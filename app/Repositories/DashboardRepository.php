<?php

namespace App\Repositories;

use App\Models\{User,Monitor};
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\MonitorLogRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function __construct(
        protected ?MonitorLogRepositoryInterface $monitorLogRepository = null
    ) {
        $this->monitorLogRepository = $monitorLogRepository ?? app(MonitorLogRepositoryInterface::class);
    }
    /**
     * Get all dashboard metrics and data for admin.
     *
     * @param User $user
     * 
     * @return array
     */
    public function getAdminDashboardData(User $user): array
    {
        $data = [];
        $data['user'] = $user;
        $data['monitors'] = $monitors = Monitor::with(['user', 'settings', 'checkResult', 'logs'])->latest()->get();
        $data['activeMonitorsCount'] = $monitors->where('is_active', true)->count();
        $data['totalMonitorsCount'] = $monitors->count();

        $data['totalUsersCount'] = User::count();
        $data['activeUsersCount'] = User::where('is_active', true)->count();

        $validResponseTimes = $monitors->filter(fn($m) => !empty($m->response_time) && $m->response_time > 0);
        $data['avgResponseTime'] = $validResponseTimes->isNotEmpty()
            ? (int) round($validResponseTimes->avg('response_time'))
            : ($monitors->isNotEmpty() ? 0 : 0);

         $data['upIncidentsCount'] = $monitors->filter(fn($m) => strtolower($m->status ?? '') === 'up')->count();
         $data['downIncidentsCount'] = $downIncidentsCount = $monitors->filter(fn($m) => strtolower($m->status ?? '') === 'down')->count();
         $data['activeOutagesCount'] = $downIncidentsCount;

        $data['totalAlertsCount'] = $monitors->filter(function ($m) {
            $status = strtolower($m->status ?? '');
            $sslStatus = strtolower($m->ssl_status ?? '');
            $domainStatus = strtolower($m->domain_status ?? '');
            return $status === 'down'
                || in_array($sslStatus, ['warning', 'expired', 'invalid'])
                || in_array($domainStatus, ['warning', 'expired']);
        })->count();

       // 1. Recent outage logs for this user (only DOWN incidents, strictly capped at 10 items max)
        $data['downMonitors'] = $this->monitorLogRepository->getRecentDownLogs(null, 10);

        // 2. Recent Active Monitors
        $data['recentActiveMonitors'] = $monitors
            ->where('is_active', true)
            ->sortByDesc(fn ($m) => $m->last_checked_at ?? $m->updated_at)
            ->take(8);

        $data['title'] = 'Admin Dashboard';
        return $data;
    }

    /**
     * Get all dashboard metrics and data for user.
     *
     * @param User $user
     * 
     * @return array
     */
    public function getUserDashboardData(User $user): array
    {
        $data = [];
        $userId = $user->id;
        $query = Monitor::query()->with(['user', 'settings', 'checkResult', 'logs'])->where('user_id', $userId);

        $data['user'] = $user;
        $data['monitors'] = $monitors = $query->latest()->get();
        $data['activeMonitorsCount'] = $monitors->where('is_active', true)->count();
        $data['totalMonitorsCount'] = $monitors->count();

        $data['upIncidentsCount'] = $monitors->filter(fn($m) => strtolower($m->status ?? '') === 'up')->count();
        $data['downIncidentsCount'] = $downIncidentsCount = $monitors->filter(fn($m) => strtolower($m->status ?? '') === 'down')->count();
        $data['activeOutagesCount'] = $downIncidentsCount;

        $validResponseTimes = $monitors->filter(fn($m) => !empty($m->response_time) && $m->response_time > 0);
        $data['avgResponseTime'] = $validResponseTimes->isNotEmpty()
            ? (int) round($validResponseTimes->avg('response_time'))
            : 0;

        $data['totalAlertsCount'] = $monitors->filter(function ($m) {
            $status = strtolower($m->status ?? '');
            $sslStatus = strtolower($m->ssl_status ?? '');
            $domainStatus = strtolower($m->domain_status ?? '');
            return $status === 'down'
                || in_array($sslStatus, ['warning', 'expired', 'invalid'])
                || in_array($domainStatus, ['warning', 'expired']);
        })->count();

        // 1. Recent outage logs for this user (only DOWN incidents, strictly capped at 10 items max)
        $data['downMonitors'] = $this->monitorLogRepository->getRecentDownLogs($userId, 10);

        $data['title'] = 'User Dashboard';
        return $data;
    }
}
