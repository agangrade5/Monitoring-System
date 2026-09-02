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
        $monitors = Monitor::with('user')->latest()->get();
        $activeMonitorsCount = $monitors->where('is_active', true)->count();
        $totalMonitorsCount = $monitors->count();

        $totalUsersCount = User::where('is_deleted', false)->count();
        $activeUsersCount = User::where('is_deleted', false)->where('is_active', true)->count();

        $validResponseTimes = $monitors->filter(fn($m) => !empty($m->response_time) && $m->response_time > 0);
        $avgResponseTime = $validResponseTimes->isNotEmpty()
            ? (int) round($validResponseTimes->avg('response_time'))
            : ($monitors->isNotEmpty() ? 245 : 0);

        $upIncidentsCount = $monitors->filter(fn($m) => strtolower($m->status ?? '') === 'up')->count();
        $downIncidentsCount = $monitors->filter(fn($m) => strtolower($m->status ?? '') === 'down')->count();
        $activeOutagesCount = $downIncidentsCount;

        $totalAlertsCount = $monitors->filter(function ($m) {
            $status = strtolower($m->status ?? '');
            $sslStatus = strtolower($m->ssl_status ?? '');
            $domainStatus = strtolower($m->domain_status ?? '');
            return $status === 'down'
                || in_array($sslStatus, ['warning', 'expired', 'invalid'])
                || in_array($domainStatus, ['warning', 'expired']);
        })->count();

        // 1. Monitors that recently went down or have critical status
        $downMonitors = $monitors
            ->filter(fn ($m) => strtolower(trim($m->status ?? '')) === 'down')
            ->sortByDesc(fn ($m) => $m->last_down_at ?? $m->updated_at);

        // 2. Recent Active Monitors
        $recentActiveMonitors = $monitors
            ->where('is_active', true)
            ->sortByDesc(fn ($m) => $m->last_checked_at ?? $m->updated_at)
            ->take(8);

        // 3. Recent system and user activities with pagination
        if (class_exists(Activity::class)) {
            $recentActivities = Activity::with('causer')
                ->latest()
                ->paginate(10)
                ->withQueryString();
        } else {
            $recentActivities = new LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return [
            'title' => 'Admin Dashboard',
            'user' => $user,
            'monitors' => $monitors,
            'recentActiveMonitors' => $recentActiveMonitors,
            'downMonitors' => $downMonitors,
            'recentActivities' => $recentActivities,
            'activeMonitorsCount' => $activeMonitorsCount,
            'totalMonitorsCount' => $totalMonitorsCount,
            'totalUsersCount' => $totalUsersCount,
            'activeUsersCount' => $activeUsersCount,
            'upIncidentsCount' => $upIncidentsCount,
            'downIncidentsCount' => $downIncidentsCount,
            'avgResponseTime' => $avgResponseTime,
            'activeOutagesCount' => $activeOutagesCount,
            'totalAlertsCount' => $totalAlertsCount,
        ];
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
        $userId = $user->id;
        $query = Monitor::query();
        if ($userId && Monitor::where('user_id', $userId)->exists()) {
            $query->where('user_id', $userId);
        }

        $monitors = $query->latest()->get();
        $activeMonitorsCount = $monitors->where('is_active', true)->count();
        $totalMonitorsCount = $monitors->count();

        $upIncidentsCount = $monitors->filter(fn($m) => strtolower($m->status ?? '') === 'up')->count();
        $downIncidentsCount = $monitors->filter(fn($m) => strtolower($m->status ?? '') === 'down')->count();
        $activeOutagesCount = $downIncidentsCount;

        $validResponseTimes = $monitors->filter(fn($m) => !empty($m->response_time) && $m->response_time > 0);
        $avgResponseTime = $validResponseTimes->isNotEmpty()
            ? (int) round($validResponseTimes->avg('response_time'))
            : ($monitors->isNotEmpty() ? 245 : 0);

        $totalAlertsCount = $monitors->filter(function ($m) {
            $status = strtolower($m->status ?? '');
            $sslStatus = strtolower($m->ssl_status ?? '');
            $domainStatus = strtolower($m->domain_status ?? '');
            return $status === 'down'
                || in_array($sslStatus, ['warning', 'expired', 'invalid'])
                || in_array($domainStatus, ['warning', 'expired']);
        })->count();

        // 1. Monitors that recently went down or have critical status
        $downMonitors = $monitors
            ->filter(fn ($m) => strtolower(trim($m->status ?? '')) === 'down')
            ->sortByDesc(fn ($m) => $m->last_down_at ?? $m->updated_at);

        // 2. Recent user activities
        $recentActivities = class_exists(Activity::class)
            ? Activity::with('causer')->latest()->take(10)->get()
            : collect();

        return [
            'title' => 'User Dashboard',
            'user' => $user,
            'monitors' => $monitors,
            'downMonitors' => $downMonitors,
            'recentActivities' => $recentActivities,
            'activeMonitorsCount' => $activeMonitorsCount,
            'totalMonitorsCount' => $totalMonitorsCount,
            'upIncidentsCount' => $upIncidentsCount,
            'downIncidentsCount' => $downIncidentsCount,
            'avgResponseTime' => $avgResponseTime,
            'activeOutagesCount' => $activeOutagesCount,
            'totalAlertsCount' => $totalAlertsCount,
        ];
    }
}
