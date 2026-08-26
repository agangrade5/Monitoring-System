<?php

use App\Http\Controllers\Backend\Auth\{
    LoginController,
    RegisterController
};
use App\Http\Controllers\Backend\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [
        LoginController::class,
        'index',
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login',
    ])->name('login.submit');

    Route::get('/register', [
        RegisterController::class,
        'index',
    ])->name('register');

    Route::post('/register', [
        RegisterController::class,
        'register',
    ])->name('register.submit');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [
                DashboardController::class,
                'admin',
            ])->name('dashboard');

        });

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user')
        ->group(function () {

            Route::get('/dashboard', [
                DashboardController::class,
                'user',
            ])->name('dashboard');

            Route::get('/profile', function () {
                return 'Profile';
            })->name('profile');

            Route::get('/users', function () {
                return 'Users';
            })->name('users');

        });

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [
        LoginController::class,
        'logout',
    ])->name('logout');
});
