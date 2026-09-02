<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DashboardRepositoryInterface $dashboardRepository
     */
    public function __construct(
        protected DashboardRepositoryInterface $dashboardRepository
    ) {}

    /**
     * Admin Dashboard View
     *
     * @return View
     */
    public function admin(): View
    {
        $data = $this->dashboardRepository->getAdminDashboardData(Auth::user());

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

        return view('backend.user.dashboard', $data);
    }
}
