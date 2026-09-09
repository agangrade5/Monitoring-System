<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\{LoginRequest, UserLoginRequest, VerifyOtpRequest};
use App\Notifications\SendOtpNotification;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\TwilioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Auth, Log, RateLimiter, Session};
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
        private readonly SettingRepositoryInterface $settingRepository,
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
    public function sendOtp(
        UserLoginRequest $request
    ): RedirectResponse {

        $type = $request->input('login_type');

        /*
        |--------------------------------------------------------------------------
        | Get Login Value
        |--------------------------------------------------------------------------
        */

        if ($type === 'email') {

            $value = $request->input('email');

        } else {

            $countryCode =
                $request->input('country_code');

            $phone =
                $request->input('phone');

            $value = $countryCode . $phone;
        }

        /*
        |--------------------------------------------------------------------------
        | Find User
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | OTP Settings
        |--------------------------------------------------------------------------
        */

        $otpDetails =
            $this->settingRepository
                ->getSettingArray('otp');

        $maxTime = (int) (
            $otpDetails['max_time'] ?? 90
        );

        /*
        |--------------------------------------------------------------------------
        | Generate OTP
        |--------------------------------------------------------------------------
        */

        $otp = UtilityHelper::generateOtp(
            $otpDetails
        );

        /*
        |--------------------------------------------------------------------------
        | Store OTP Session
        |--------------------------------------------------------------------------
        */

        Session::put('login_otp', [
            'user_id' => $user->id,
            'type' => $type,
            'value' => $value,
            'otp' => $otp,
            'expires_at' => now()->addSeconds(
                $maxTime
            ),
            /*
            | Wrong OTP attempts
            */
            'attempts' => 0,
            /*
            | Maximum allowed wrong attempts
            */
            'max_attempts' => 3,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Send OTP
        |--------------------------------------------------------------------------
        */
        try {
            if ($type === 'phone') {
                $this->twilioService->sendOtp(
                    phone: $value,
                    otp: $otp,
                    expireTime: $maxTime
                );
            } else {
                $user->notify(
                    new SendOtpNotification(
                        otp: $otp,
                        otpExpireTime: $maxTime
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::error('OTP delivery failed.', [
                'user_id' => $user->id,
                'type' => $type,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unable to send OTP. Please try again later.');
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect Verify Page
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('login.verify')
            ->with(
                'success',
                'OTP sent successfully.'
            );
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
                    'login' =>
                        'Please request a new OTP.',
                ]);
        }

        $otpData = session('login_otp');

        return view('backend.auth.verify-otp', [
            'title' => 'Verify OTP',
            'bodyClassName' => 'login-page',
            'expiresAt' =>
                $otpData['expires_at'],
            'attempts' =>
                $otpData['attempts'] ?? 0,
            'maxAttempts' =>
                $otpData['max_attempts'] ?? 3,
            'remainingAttempts' =>
                max(
                    0,
                    ($otpData['max_attempts'] ?? 3)
                    - ($otpData['attempts'] ?? 0)
                ),
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

        /*
        |--------------------------------------------------------------------------
        | OTP Session Check
        |--------------------------------------------------------------------------
        */

        if (!$otpData) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' =>
                        'OTP session expired. Please request a new OTP.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get Attempts
        |--------------------------------------------------------------------------
        */

        $attempts =
            (int) ($otpData['attempts'] ?? 0);

        $maxAttempts =
            (int) ($otpData['max_attempts'] ?? 3);

        /*
        |--------------------------------------------------------------------------
        | Already blocked
        |--------------------------------------------------------------------------
        */

        if ($attempts >= $maxAttempts) {

            return back()
                ->withErrors([
                    'otp' =>
                        'Maximum OTP attempts exceeded. Please resend OTP.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Expiry
        |--------------------------------------------------------------------------
        */

        if (
            now()->greaterThan(
                \Carbon\Carbon::parse(
                    $otpData['expires_at']
                )
            )
        ) {

            return back()
                ->withErrors([
                    'otp' =>
                        'OTP has expired. Please resend OTP.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Submitted OTP
        |--------------------------------------------------------------------------
        */

        $otp =
            implode(
                '',
                $request->input('otp')
            );

        /*
        |--------------------------------------------------------------------------
        | Invalid OTP
        |--------------------------------------------------------------------------
        */

        if (
            !hash_equals(
                (string) $otpData['otp'],
                (string) $otp
            )
        ) {

            $attempts++;

            /*
            | Update session
            */
            session()->put(
                'login_otp.attempts',
                $attempts
            );

            $remaining =
                max(
                    0,
                    $maxAttempts - $attempts
                );

            /*
            |--------------------------------------------------------------------------
            | Maximum Attempts Reached
            |--------------------------------------------------------------------------
            */

            if ($attempts >= $maxAttempts) {

                return back()
                    ->withErrors([
                        'otp' =>
                            'Invalid OTP. Maximum attempts reached. Please resend OTP.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Attempts Remaining
            |--------------------------------------------------------------------------
            */

            return back()
                ->withErrors([
                    'otp' =>
                        "Invalid OTP. {$remaining} attempt"
                        . ($remaining === 1 ? '' : 's')
                        . " remaining.",
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Find User
        |--------------------------------------------------------------------------
        */

        $user = $this->userRepository->findById(
            $otpData['user_id']
        );

        if (!$user) {

            session()->forget('login_otp');

            return redirect()
                ->route('login')
                ->withErrors([
                    'login' =>
                        'User account not found.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        request()
            ->session()
            ->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Remove OTP Session
        |--------------------------------------------------------------------------
        */

        session()->forget('login_otp');

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        UtilityHelper::customActivityLog(
            'auth',
            'User logged in successfully using OTP.',
            $user,
            [
                'user_id' =>
                    $user->id,

                'login_type' =>
                    $otpData['type'],

                'ip' =>
                    $request->ip(),

                'user_agent' =>
                    $request->userAgent(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Login successful!'
            );
    }

    /**
     * Method resendOtp
     *
     * @return RedirectResponse
     */
    public function resendOtp(): RedirectResponse
    {
        $otpData = session('login_otp');

        /*
        |--------------------------------------------------------------------------
        | OTP Session Check
        |--------------------------------------------------------------------------
        */

        if (!$otpData) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'login' =>
                        'OTP session expired. Please login again.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get OTP Settings
        |--------------------------------------------------------------------------
        */

        $otpDetails =
            $this->settingRepository
                ->getSettingArray('otp');

        $maxTime = (int) (
            $otpDetails['max_time'] ?? 90
        );

        /*
        |--------------------------------------------------------------------------
        | Generate New OTP
        |--------------------------------------------------------------------------
        */

        $otp = UtilityHelper::generateOtp(
            $otpDetails
        );

        /*
        |--------------------------------------------------------------------------
        | Reset OTP Session
        |--------------------------------------------------------------------------
        */

        session()->put(
            'login_otp',
            [
                'user_id' =>
                    $otpData['user_id'],
                'type' =>
                    $otpData['type'],
                'value' =>
                    $otpData['value'],
                'otp' =>
                    $otp,
                'expires_at' =>
                    now()->addSeconds($maxTime),
                /*
                | Reset wrong attempts
                */
                'attempts' =>
                    0,
                'max_attempts' =>
                    3,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Send New OTP
        |--------------------------------------------------------------------------
        */

        $user = $this->userRepository->findById(
            $otpData['user_id']
        );

        if (!$user) {

            session()->forget('login_otp');

            return redirect()
                ->route('login')
                ->withErrors([
                    'login' =>
                        'User account not found.',
                ]);
        }

        if ($otpData['type'] === 'phone') {

            $this->twilioService->sendOtp(
                phone: $otpData['value'],
                otp: $otp,
                expireTime: $maxTime
            );

        } else {

            $user->notify(
                new SendOtpNotification(
                    otp: $otp,
                    otpExpireTime: $maxTime
                )
            );
        }

        return redirect()
            ->route('login.verify')
            ->with(
                'success',
                'A new OTP has been sent successfully.'
            );
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
