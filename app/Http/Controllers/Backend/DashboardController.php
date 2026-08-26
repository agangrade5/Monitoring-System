<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * method admin
     *
     * @return View
     */
    public function admin(): View
    {
        return view('backend.admin.dashboard', [
            'title' => 'Admin Dashboard',
        ]);
    }

    /**
     * method user
     *
     * @return View
     */
    public function user(): View
    {
        return view('backend.user.dashboard', [
            'title' => 'User Dashboard',
        ]);
    }
}
