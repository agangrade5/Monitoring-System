<?php

use App\Http\Controllers\Backend\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController
};
use App\Http\Controllers\Backend\{
    DashboardController,
    UserController,
    SettingController,
    MonitorController
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
            /*
            |--------------------------------------------------------------------------
            | Dashboard Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/dashboard', [
                DashboardController::class,
                'admin',
            ])->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Settings Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/settings', [
                SettingController::class,
                'index'
            ])->name('settings');

            /*
            |--------------------------------------------------------------------------
            | Users Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/users', [
                UserController::class,
                'allUsers',
            ])->name('users');
        });

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user')
        ->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Dashboard Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/dashboard', [
                DashboardController::class,
                'user',
            ])->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Settings Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/settings', [
                SettingController::class,
                'index'
            ])->name('settings');

            /*
            |--------------------------------------------------------------------------
            | Users Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/users', function () {
                return 'Users';
            })->name('users');
Route::get('/profile', [
                UserController::class,
                'profile'
            ])->name('profile');
               /*
            |--------------------------------------------------------------------------
            | Monitors Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/monitor', [
                MonitorController::class,
                'index'
            ])->name('monitor');
            Route::get('/monitor/create', [
                MonitorController::class,
                'create'
            ])->name('monitor.create');
            Route::post('/monitor/store', [
                MonitorController::class,
                'store'
            ])->name('monitor.store');
            Route::get('/monitor/{id}/edit', [
                MonitorController::class,
                'edit'
            ])->name('monitor.edit');
            Route::post('/monitor/{id}/update', [
                MonitorController::class,
                'update'
            ])->name('monitor.update');
            Route::delete('/monitor/{id}', [
                MonitorController::class,
                'destroy'
            ])->name('monitor.destroy');
            Route::patch('/monitor/{id}/toggle', [
                MonitorController::class,
                'toggleActive'
            ])->name('monitor.toggle');
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

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/profile', [
        UserController::class,
        'updateProfile',
    ])->name('profile.update');
});
