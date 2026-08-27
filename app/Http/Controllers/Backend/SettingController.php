<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\Contracts\SettingRepositoryInterface;
class SettingController extends Controller
{
     
    
      public function index(): View
    {

        return view('backend.admin.settings', [
            'title' => 'Settings',
            'settings' => [],
        ]);
    }
}
