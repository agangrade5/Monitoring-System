<?php

use App\Http\Controllers\Backend\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController
};
use App\Http\Controllers\Backend\{
    ActivityLogController,
    DashboardController,
    UserController,
    SettingController,
    MonitorController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('guest')->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Login Routes
            |--------------------------------------------------------------------------
            */
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
            /* Route::get('/register', [
                RegisterController::class,
                'index',
            ])->name('register');

            Route::post('/register', [
                RegisterController::class,
                'register',
            ])->name('register.submit'); */
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
    });

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [
        LoginController::class,
        'userLogin',
    ])->name('login');

    Route::post('/login/send-otp', [
        LoginController::class,
        'sendOtp',
    ])->name('login.send-otp');

    Route::get('/login/verify-otp', [
        LoginController::class,
        'showVerifyOtp',
    ])->name('login.verify');

    Route::post('/login/verify-otp', [
        LoginController::class,
        'verifyOtp',
    ])->name('login.verify.submit');
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

            Route::post('/settings/otp', [
                SettingController::class,
                'updateOtpSettings'
            ])->name('settings.otp');

            Route::post('/settings/twilio', [
                SettingController::class,
                'updateTwilioSettings'
            ])->name('settings.twilio');

            Route::post('/settings/email', [
                SettingController::class,
                'updateEmailSettings'
            ])->name('settings.email');

            Route::post('/settings/aws', [
                SettingController::class,
                'updateAwsSettings'
            ])->name('settings.aws');

            /*
            |--------------------------------------------------------------------------
            | Users Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/users', [
                UserController::class,
                'allUsers',
            ])->name('users.index');

            Route::post('/users', [
                UserController::class,
                'storeUser',
            ])->name('users.store');

            Route::get('/users/{id}/edit', [
                UserController::class,
                'editUser',
            ])->name('users.edit');

            Route::post('/users/update/{id}', [
                UserController::class,
                'updateUser',
            ])->name('users.update');

            Route::delete('/users/{id}', [
                UserController::class,
                'destroyUser',
            ])->name('users.destroy');

            /*
            |--------------------------------------------------------------------------
            | Activity Logs
            |--------------------------------------------------------------------------
            */
            Route::middleware('permission:activity-logs.view-all')
                ->get('/activity-logs', [ActivityLogController::class, 'index'])
                ->name('activity-logs.index');

            Route::middleware('permission:activity-logs.view')
                ->get('/activity-logs/{id}', [ActivityLogController::class, 'show'])
                ->name('activity-logs.show');

            Route::middleware(['permission:activity-logs.delete', 'role:admin'])
                ->delete('/activity-logs/{id}', [ActivityLogController::class, 'destroy'])
                ->name('activity-logs.destroy');

        });

    /*
    |--------------------------------------------------------------------------
    | User & Monitoring Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user|admin')
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

            Route::get('/monitor/{id}', [
                MonitorController::class,
                'show'
            ])->name('monitor.show');

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

            Route::post('/monitor/{id}/check', [
                MonitorController::class,
                'triggerCheck'
            ])->name('monitor.check');

            Route::post('/monitor/{id}/test-notification', [
                MonitorController::class,
                'sendTestNotification'
            ])->name('monitor.testNotification');

            /*
            |--------------------------------------------------------------------------
            | Activity Logs
            |--------------------------------------------------------------------------
            */
            Route::middleware('permission:activity-logs.view-all')
                ->get('/activity-logs', [ActivityLogController::class, 'index'])
                ->name('activity-logs.index');

            Route::middleware('permission:activity-logs.view')
                ->get('/activity-logs/{id}', [ActivityLogController::class, 'show'])
                ->name('activity-logs.show');

            Route::middleware(['permission:activity-logs.delete', 'role:admin'])
                ->delete('/activity-logs/{id}', [ActivityLogController::class, 'destroy'])
                ->name('activity-logs.destroy');
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

    /*
    |--------------------------------------------------------------------------
    | Change Password
    |--------------------------------------------------------------------------
    */
    Route::post('/change-password', [
        UserController::class,
        'changePassword',
    ])->name('change-password');
});
