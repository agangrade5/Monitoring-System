<?php

use App\Http\Controllers\Backend\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController
};
use App\Http\Controllers\Backend\{
    DashboardController,
    UserController,
    SettingController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Login Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [
        LoginController::class,
        'index',
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login',
    ])->name('login.submit');
    /*
    |--------------------------------------------------------------------------
    | Register Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/register', [
        RegisterController::class,
        'index',
    ])->name('register');

    Route::post('/register', [
        RegisterController::class,
        'register',
    ])->name('register.submit');
    /*
    |--------------------------------------------------------------------------
    | Forgot Password Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/forgot-password', [
        ForgotPasswordController::class,
        'index',
    ])->name('password.request');

    Route::post('/forgot-password', [
        ForgotPasswordController::class,
        'sendResetLink',
    ])->name('password.email');

    Route::get('/reset-password/{token}', [
        ForgotPasswordController::class,
        'showResetForm',
    ])->name('password.reset');

    Route::post('/reset-password', [
        ForgotPasswordController::class,
        'resetPassword',
    ])->name('password.update');
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

            Route::get('/users', [
                UserController::class,
                'allUsers',
            ])->name('users');

            Route::get('/settings', [
                SettingController::class,
                'index'
            ])->name('settings');

            Route::post('/settings/notification/update', [
                SettingController::class,
                'updateNotification'
            ])->name('settings.notification.update');

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
