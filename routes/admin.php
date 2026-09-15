<?php

use App\Http\Controllers\Backend\Auth\{
    LoginController,
    TwoFactorController,
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
| Shared Route Closures (Reusable for Admin and User)
|--------------------------------------------------------------------------
| These route closures are defined only once and reused for both
| Admin and User route groups.
|
| When used inside the Admin group, the URL will be:
| /admin/monitor
| because the parent group already has the 'admin' prefix.
|
| When used inside the User group, the URL will be:
| /monitor
| because the User group does not have a URL prefix.
|--------------------------------------------------------------------------
*/
$monitorRoutes = function () {
    Route::prefix('monitor')
        ->name('monitor.')
        ->group(function () {

            Route::get('/', [
                MonitorController::class,
                'index',
            ])->name('index');

            Route::get('/create', [
                MonitorController::class,
                'create',
            ])->name('create');

            Route::post('/store', [
                MonitorController::class,
                'store',
            ])->name('store');

            Route::get('/{id}', [
                MonitorController::class,
                'show',
            ])->name('show');

            Route::get('/{id}/edit', [
                MonitorController::class,
                'edit',
            ])->name('edit');

            Route::post('/{id}/update', [
                MonitorController::class,
                'update',
            ])->name('update');

            Route::delete('/{id}', [
                MonitorController::class,
                'destroy',
            ])->name('destroy');

            Route::patch('/{id}/toggle', [
                MonitorController::class,
                'toggleActive',
            ])->name('toggle');

            Route::post('/{id}/check', [
                MonitorController::class,
                'triggerCheck',
            ])->name('check');

            Route::post('/{id}/test-notification', [
                MonitorController::class,
                'sendTestNotification',
            ])->name('testNotification');

        });
};

/*
|--------------------------------------------------------------------------
| Shared Route Closures (Reusable for Admin and User)
|--------------------------------------------------------------------------
| These route closures are defined only once and reused for both
| Admin and User route groups.
|
| When used inside the Admin group, the URL will be:
| /admin/settings
| because the parent group already has the 'admin' prefix.
|
| When used inside the User group, the URL will be:
| /settings
| because the User group does not have a URL prefix.
|--------------------------------------------------------------------------
*/
$settingsRoutes = function (bool $isAdmin = false) {
    Route::prefix('settings')
        ->name('settings.')
        ->group(function () use ($isAdmin) {

            // Common routes - Admin + User dono
            Route::get('/', [
                SettingController::class,
                'index'
            ])->name('index');

            Route::post('/notifications', [
                SettingController::class,
                'updateNotificationSettings'
            ])->name('notifications');

            Route::post('/report', [
                SettingController::class,
                'updateReportSettings'
            ])->name('report');

            // Admin-only routes
            if ($isAdmin) {
                Route::post('/otp', [
                    SettingController::class,
                    'updateOtpSettings'
                ])->name('otp');

                Route::post('/twilio', [
                    SettingController::class,
                    'updateTwilioSettings'
                ])->name('twilio');

                Route::post('/email', [
                    SettingController::class,
                    'updateEmailSettings'
                ])->name('email');

                Route::post('/aws', [
                    SettingController::class,
                    'updateAwsSettings'
                ])->name('aws');

                // 2FA Routes
                Route::prefix('two-fa')->name('twoFa.')->group(function () {
                    Route::get('/setup', [
                        SettingController::class,
                        'twoFaSetup'
                    ])->name('setup');

                    Route::post('/enable', [
                        SettingController::class,
                        'twoFaEnable'
                    ])->name('enable');

                    Route::post('/disable', [
                        SettingController::class,
                        'twoFaDisable'
                    ])->name('disable');
                });
            }

        });
};

/*
|--------------------------------------------------------------------------
| Shared Route Closures (Reusable for Admin and User)
|--------------------------------------------------------------------------
| These route closures are defined only once and reused for both
| Admin and User route groups.
|
| When used inside the Admin group, the URL will be:
| /admin/activity-logs
| because the parent group already has the 'admin' prefix.
|
| When used inside the User group, the URL will be:
| /activity-logs
| because the User group does not have a URL prefix.
|--------------------------------------------------------------------------
*/
$activityLogRoutes = function (string $indexPermission) {
    Route::prefix('activity-logs')
        ->name('activity-logs.')
        ->group(function () use ($indexPermission) {

            Route::middleware("permission:{$indexPermission}")
                ->get('/', [
                    ActivityLogController::class,
                    'index',
                ])
                ->name('index');

            Route::middleware('permission:activity-logs.view')
                ->get('/{id}', [
                    ActivityLogController::class,
                    'show',
                ])
                ->name('show');

            Route::middleware([
                'permission:activity-logs.delete',
                'role:admin',
            ])->delete('/{id}', [
                ActivityLogController::class,
                'destroy',
            ])->name('destroy');

        });
};

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
            | 2FA Routes
            |--------------------------------------------------------------------------
            */
            Route::get('twoFa-show', [
                TwoFactorController::class,
                'twoFaShow'
            ])->name('twoFa.show');
            Route::post('twoFa-verify', [
                TwoFactorController::class,
                'twoFaVerify'
            ])->name('twoFa.verify');

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
        return redirect()->route('login.index');
    });

    Route::prefix('login')->name('login.')->group(function () {
        Route::get('/', [
            LoginController::class,
            'userLogin',
        ])->name('index');

        Route::post('/send-otp', [
            LoginController::class,
            'sendOtp',
        ])->name('send-otp');

        Route::get('/verify-otp', [
            LoginController::class,
            'showVerifyOtp',
        ])->name('verify');

        Route::post('/verify-otp', [
            LoginController::class,
            'verifyOtp',
        ])->name('verify.submit');

        Route::post('/resend-otp', [
            LoginController::class,
            'resendOtp',
        ])->name('resend-otp');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () use ($monitorRoutes, $activityLogRoutes, $settingsRoutes) {
    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () use ($monitorRoutes, $activityLogRoutes, $settingsRoutes) {
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
            | Users Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('users')->name('users.')->group(function () {

                Route::get('/', [
                    UserController::class,
                    'allUsers',
                ])->name('index');

                Route::post('/', [
                    UserController::class,
                    'storeUser',
                ])->name('store');

                Route::get('/{id}/edit', [
                    UserController::class,
                    'editUser',
                ])->name('edit');

                Route::post('/update/{id}', [
                    UserController::class,
                    'updateUser',
                ])->name('update');

                Route::delete('/{id}', [
                    UserController::class,
                    'destroyUser',
                ])->name('destroy');

            });

            /*
            |--------------------------------------------------------------------------
            | Settings Routes -> Common + Admin-only (otp, twilio, email, aws, two-fa)
            |--------------------------------------------------------------------------
            */
            $settingsRoutes(true);

            /*
            |--------------------------------------------------------------------------
            | Monitors Routes -> URL: /admin/monitor
            |--------------------------------------------------------------------------
            */
            $monitorRoutes();

            /*
            |--------------------------------------------------------------------------
            | Activity Logs -> URL: /admin/activity-logs
            |--------------------------------------------------------------------------
            */
            $activityLogRoutes('activity-logs.view-all');
        });

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user')
        ->group(function () use ($monitorRoutes, $activityLogRoutes, $settingsRoutes) {
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
            | Settings Routes -> Common only (index, notifications, report)
            |--------------------------------------------------------------------------
            */
            $settingsRoutes();

            /*
            |--------------------------------------------------------------------------
            | Monitors Routes -> URL: /monitor
            |--------------------------------------------------------------------------
            */
            $monitorRoutes();

            /*
            |--------------------------------------------------------------------------
            | Activity Logs -> URL: /activity-logs
            |--------------------------------------------------------------------------
            */
            $activityLogRoutes('activity-logs.view');
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
