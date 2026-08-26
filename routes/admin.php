<?php

use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\{
    DashboardController
};
use Illuminate\Support\Facades\Route;

Route::controller(LoginController::class)
    ->group(
        function (): void {
            // login secton
            Route::get('/', 'index')->name('login');
            Route::post('/', 'login')->name('login.submit');
        }
    );

Route::get('/admin', function () {
    return view('admin');
});

Route::controller(DashboardController::class)
    ->group(
        function (): void {
            // dashboard secton
            Route::get('/dashboard', 'index')->name('dashboard');
        }
    );

