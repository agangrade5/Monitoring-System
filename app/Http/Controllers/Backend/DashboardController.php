<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ActivityLogRepositoryInterface $activityRepository
     *
     * @return void
     */
    public function __construct(
        private ActivityLogRepositoryInterface $activityRepository
    ) {}

    /**
     * method admin
     *
     * @return View
     */
    public function admin(): View
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Get recent activity logs
        |--------------------------------------------------------------------------
        */
        $recentActivityLogs =
            $this->activityRepository->getRecentLogs(
                $user->id,
                true,
                5
            );

        return view('backend.admin.dashboard', [
            'title' => 'Admin Dashboard',
            'user' => $user,
            'recentActivityLogs' => $recentActivityLogs,
        ]);
    }

    /**
     * method user
     *
     * @return View
     */
    public function user(): View
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Get recent activity logs
        |--------------------------------------------------------------------------
        */
        $recentActivityLogs =
            $this->activityRepository->getRecentLogs(
                $user->id,
                false,
                5
            );

        return view('backend.user.dashboard', [
            'title' => 'User Dashboard',
            'user' => $user,
            'recentActivityLogs' => $recentActivityLogs
        ]);
    }
}
