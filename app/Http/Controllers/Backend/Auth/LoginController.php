<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\{LoginRequest, UserLoginRequest, VerifyOtpRequest};
use App\Notifications\SendOtpNotification;
use App\Repositories\Contracts\UserRepositoryInterface;
//use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\TwilioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Auth, RateLimiter, Session};
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Constructor
     *
     * @param UserRepositoryInterface $userRepository
     * @param SettingRepositoryInterface $settingRepository
     * @param TwilioService $twilioService
     *
     * @return void
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        //private readonly SettingRepositoryInterface $settingRepository,
        private readonly TwilioService $twilioService,
    ) {
    }

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
     * Method userLogin
     *
     * @return View
     */
    public function userLogin(): View
    {
        return view('backend.auth.user-login', [
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
     * Method sendOtp
     *
     * @param UserLoginRequest $request
     *
     * @return RedirectResponse
     */
    public function sendOtp(UserLoginRequest $request): RedirectResponse
    {
        $type = $request->input('login_type');

        $value = $type === 'email'
            ? $request->input('email')
            : $request->input('phone');


        /*
         * Rate limiter key
         */
        $key = 'login-otp:' . $request->ip() . ':' . $value;

        /*
         * Maximum 3 OTP requests per 10 minutes
         */
        if (RateLimiter::tooManyAttempts($key, 3)) {

            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withErrors([
                    $type => "Too many OTP requests. Please try again in "
                        . ceil($seconds / 60)
                        . " minute(s).",
                ])
                ->withInput();
        }

        RateLimiter::hit(
            $key,
            10 * 60 // 10 minutes
        );

        /*
         * Find user
         */
        $user = $type === 'email'
            ? $this->userRepository->findByEmail($value)
            : $this->userRepository->findByPhone($value);

        if (!$user) {
            return back()
                ->withErrors([
                    $type => 'No account found with these details.',
                ])
                ->withInput();
        }

        /*
         * OTP settings
         */
        //$otpDetails = $this->settingRepository->getOtpDetails();

        $otp = UtilityHelper::generateOtp();

        /*
         * Store OTP in session
         */
        Session::put('login_otp', [
            'user_id' => $user->id,
            'type' => $type,
            'value' => $value,
            'otp' => $otp,
            'expires_at' => now()->addSeconds(
                60 //$otpDetails['otp']['max_time']
            ),
        ]);

        /*
         * Send OTP
         */
        if ($type === 'phone') {

            $this->twilioService->sendOtp(
                $value,
                $otp
            );
        } else {

            /*
             * Email OTP service
             *
             * Twilio SendGrid / Laravel Mail can be used here.
             */
            $user->notify(
                new SendOtpNotification(
                    otp: $otp,
                    otpExpireTime: 60 //$otpDetails['otp']['max_time']
                )
            );
        }

        return redirect()
            ->route('login.verify')
            ->with('success', 'OTP sent successfully.');
    }

    /**
     * Method showVerifyOtp
     *
     * @return View|RedirectResponse
     */
    public function showVerifyOtp(): View|RedirectResponse
    {
        if (!session()->has('login_otp')) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'login' => 'Please request a new OTP.',
                ]);
        }

        $otpData = session('login_otp');

        return view('backend.auth.verify-otp', [
            'title' => 'Verify OTP',
            'bodyClassName' => 'login-page',
            'expiresAt' => $otpData['expires_at'],
        ]);
    }

    /**
     * Method verifyOtp
     *
     * @param VerifyOtpRequest $request
     *
     * @return RedirectResponse
     */
    public function verifyOtp(
        VerifyOtpRequest $request
    ): RedirectResponse {

        $otpData = session('login_otp');

        if (!$otpData) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' => 'OTP session expired. Please request a new OTP.',
                ]);
        }

        /*
        * Check expiry
        */
        if (now()->greaterThan(
            \Carbon\Carbon::parse($otpData['expires_at'])
        )) {

            session()->forget('login_otp');

            return back()
                ->withErrors([
                    'otp' => 'OTP has expired. Please request a new OTP.',
                ]);
        }

        $otp = implode('', $request->input('otp'));

        if (!hash_equals(
            (string) $otpData['otp'],
            (string) $otp
        )) {

            return back()
                ->withErrors([
                    'otp' => 'Invalid OTP.',
                ]);
        }

        $user = $this->userRepository->findById(
            $otpData['user_id']
        );

        if (!$user) {
            session()->forget('login_otp');

            return redirect()
                ->route('login')
                ->withErrors([
                    'login' => 'User account not found.',
                ]);
        }

        /*
        * Login
        */
        auth()->login($user);

        request()->session()->regenerate();

        /*
        * Remove OTP
        */
        session()->forget('login_otp');

        /*
        * Activity Log
        */
        UtilityHelper::customActivityLog(
            'auth',
            'User logged in successfully using OTP.',
            $user,
            [
                'user_id' => $user->id,
                'login_type' => $otpData['type'],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

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
            ->route('admin.login')
            ->with(
                'success',
                'You have been logged out successfully.'
            );
    }
}
