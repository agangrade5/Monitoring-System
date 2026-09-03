<?php

namespace App\Repositories;

use App\Models\Monitor;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

class DashboardRepository implements DashboardRepositoryInterface
{
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
        $data['users'] = $user;
        $data['monitors'] = $monitors = Monitor::with('user')->latest()->get();
        $data['activeMonitorsCount'] = $monitors->where('is_active', true)->count();
        $data['totalMonitorsCount'] = $monitors->count();

        $data['totalUsersCount'] = User::where('is_deleted', false)->count();
        $data['activeUsersCount'] = User::where('is_deleted', false)->where('is_active', true)->count();

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

        // 1. Monitors that recently went down or have critical status
         $data['downMonitors'] = $monitors
            ->filter(fn ($m) => strtolower(trim($m->status ?? '')) === 'down')
            ->sortByDesc(fn ($m) => $m->last_down_at ?? $m->updated_at);

        // 2. Recent Active Monitors
         $data['recentActiveMonitors'] = $monitors
            ->where('is_active', true)
            ->sortByDesc(fn ($m) => $m->last_checked_at ?? $m->updated_at)
            ->take(8);

        // 3. Recent system and user activities with pagination
        if (class_exists(Activity::class)) {
            $data['recentActivities'] = Activity::with('causer')
                ->latest()
                ->paginate(10)
                ->withQueryString();
        } else {
            $data['recentActivities'] = new LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }
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
        $query = Monitor::query()->where('user_id', $userId);

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

        // 1. Monitors that recently went down or have critical status
        $data['downMonitors'] = $monitors
            ->filter(fn ($m) => strtolower(trim($m->status ?? '')) === 'down')
            ->sortByDesc(fn ($m) => $m->last_down_at ?? $m->updated_at);

        // 2. Recent user activities for this logged-in user
        $data['recentActivities'] = class_exists(Activity::class)
            ? Activity::with('causer')->where('causer_id', $userId)->latest()->take(10)->get()
            : collect();
        $data['title'] = 'User Dashboard';
        return $data;
    }
}
