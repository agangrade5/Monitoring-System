<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Method index
     *
     * @return View
     */
    public function index(): View
    {
        return view('backend.auth.login', [
            'title' => 'Login',
            'bodyClassName' => 'login-page'
        ]);
    }

    /**
     * Method login
     *
     * @param LoginRequest $request
     *
     * @return RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only([
            'email',
            'password',
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            /*
            |--------------------------------------------------------------------------
            | Activity Log - login Failed
            |--------------------------------------------------------------------------
            */
            UtilityHelper::customActivityLog(
                'auth',
                'Failed login attempt.',
                null,
                [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
            return back()
                ->withErrors([
                    'email' => 'The provided credentials are incorrect.',
                ])
                ->withInput($request->only('email', 'remember'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Set timezone
        |--------------------------------------------------------------------------
        */
        $timezone = $request->input('timezone');
        $timezoneAliases = [
            'Asia/Calcutta' => 'Asia/Kolkata',
        ];
        $timezone = $timezoneAliases[$timezone] ?? $timezone;
        try {
            new \DateTimeZone($timezone);

            $user->timezone = $timezone;
            $user->save();

        } catch (\Exception $e) {
            // Invalid timezone - keep existing timezone
        }
        $user->refresh();
        session([
            'user_timezone' => $user->timezone
                ?? config('app.timezone', 'UTC'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Activity Log - Loggin Success
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'auth',
            $user->hasRole('admin')
                ? 'Admin logged in successfully.'
                : 'User logged in successfully.',
            $user,
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'remember' => $remember,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */
        if ($user->hasRole('admin')) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Login successful!');
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Login successful!');
    }

    /**
     * Method logout
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'auth',
            $user->hasRole('admin')
                ? 'Admin logged out successfully.'
                : 'User logged out successfully.',
            $user,
            [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        );

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'You have been logged out successfully.'
            );
    }
}
