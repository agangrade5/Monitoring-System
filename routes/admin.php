<?php

use App\Http\Controllers\Backend\{
    DashboardController
};
use Illuminate\Support\Facades\Route;

Route::get('/admin', function () {
    return view('admin');
});

Route::controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', 'index');
});

