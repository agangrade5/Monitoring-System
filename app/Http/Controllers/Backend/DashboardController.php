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
        $data = $this->dashboardRepository->getAdminDashboardData(Auth::user());
        $user = Auth::user();

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
            $data['user'] = $user;
            $data['title'] = 'Admin Dashboard';

        return view('backend.admin.dashboard', $data);
    }

    /**
     * User Dashboard View
     *
     * @return View
     */
    public function user(): View
    {
        $data = $this->dashboardRepository->getUserDashboardData(Auth::user());
        $user = Auth::user();

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
            $data['user'] = $user;
            $data['title'] = 'User Dashboard';

        return view('backend.user.dashboard', $data);
    }
}
