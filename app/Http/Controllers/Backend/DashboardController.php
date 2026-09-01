<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * method admin
     *
     * @return View
     */
    public function admin(): View
    {
        $user = Auth::user();
        return view('backend.admin.dashboard', [
            'title' => 'Admin Dashboard',
            'user' => $user
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
        return view('backend.user.dashboard', [
            'title' => 'User Dashboard',
            'user' => $user
        ]);
    }
}
