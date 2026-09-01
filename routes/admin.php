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

            Route::get('/dashboard', [
                DashboardController::class,
                'admin',
            ])->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Users Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/users', [
                UserController::class,
                'allUsers',
            ])->name('users');

            Route::post('/users/store', [
                UserController::class,
                'storeUser'
            ])->name('users.store');

            Route::post('/users/update/{id}', [
                UserController::class,
                'updateUser'
            ])->name('users.update');

            Route::delete('/users/destroy/{id}', [
                UserController::class,
                'destroyUser'
            ])->name('users.destroy');

            /*
            |--------------------------------------------------------------------------
            | Settings Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/settings', [
                SettingController::class,
                'index'
            ])->name('settings');
            Route::get('/settings', [
                SettingController::class,
                'index'
            ])->name('settings');

            Route::post('/settings/notification/update', [
                SettingController::class,
                'updateNotification'
            ])->name('settings.notification.update');

            Route::post('/settings/smtp/update', [
                SettingController::class,
                'updateSmtp'
            ])->name('settings.smtp.update');

            Route::post('/settings/sms/update', [
                SettingController::class,
                'updateSms'
            ])->name('settings.sms.update');
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
});
