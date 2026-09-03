<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Repositories\Contracts\DashboardRepositoryInterface;
=======
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Http\Request;
>>>>>>> 3776bb96d58a0da1399ce299f38c50432b8ccbc9
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
<<<<<<< HEAD
     * @param DashboardRepositoryInterface $dashboardRepository
     */
    public function __construct(
        protected DashboardRepositoryInterface $dashboardRepository
    ) {}

    /**
     * Admin Dashboard View
=======
     * @param ActivityLogRepositoryInterface $activityRepository
     *
     * @return void
     */
    public function __construct(
        private ActivityLogRepositoryInterface $activityRepository
    ) {}

    /**
     * method admin
>>>>>>> 3776bb96d58a0da1399ce299f38c50432b8ccbc9
     *
     * @return View
     */
    public function admin(): View
    {
<<<<<<< HEAD
        $data = $this->dashboardRepository->getAdminDashboardData(Auth::user());

        return view('backend.admin.dashboard', $data);
=======
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
>>>>>>> 3776bb96d58a0da1399ce299f38c50432b8ccbc9
    }

    /**
     * User Dashboard View
     *
     * @return View
     */
    public function user(): View
    {
<<<<<<< HEAD
        $data = $this->dashboardRepository->getUserDashboardData(Auth::user());

        return view('backend.user.dashboard', $data);
=======
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
>>>>>>> 3776bb96d58a0da1399ce299f38c50432b8ccbc9
    }
}
