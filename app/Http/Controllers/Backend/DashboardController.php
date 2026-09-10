<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DashboardRepositoryInterface $dashboardRepository
     * /**
     * Admin Dashboard View
     * @param ActivityLogRepositoryInterface $activityRepository
     *
     * @return void
     */
     
    public function __construct(
        protected DashboardRepositoryInterface $dashboardRepository,
        protected ActivityLogRepositoryInterface $activityRepository
    ) {}
        
        

    /**
     * method admin
     *
     * @return View
     */
    public function admin(): View
    {
        $user = Auth::user();
        $data = $this->dashboardRepository->getAdminDashboardData($user);
        /*
        |--------------------------------------------------------------------------
        | Get recent activity logs
        |--------------------------------------------------------------------------
        */
        $data['recentActivityLogs'] =
            $this->activityRepository->getRecentLogs(
                $user->id,
                true,
                5
            );

        return view('backend.admin.dashboard', $data);
    }

    /**
     * User Dashboard View
     *
     * @return View
     */
    public function user(): View
    {
        $user = Auth::user();
        $data = $this->dashboardRepository->getUserDashboardData($user);

        /*
        |--------------------------------------------------------------------------
        | Get recent activity logs
        |--------------------------------------------------------------------------
        */
         $data['recentActivityLogs'] =
            $this->activityRepository->getRecentLogs(
                $user->id,
                false,
                5
            );

        return view('backend.user.dashboard', $data);
    }
}
