<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\{ForgotPasswordRequest, ResetPasswordRequest};
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Auth, Log, Password};
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Show forgot password page.
     */
    public function index(): View
    {
        return view('backend.auth.forgot-password', [
            'title' => 'Forgot Password',
            'bodyClassName' => 'forgot-password-page',
        ]);
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(
        ForgotPasswordRequest $request
    ): RedirectResponse {

        $email = $request->validated('email');

        /*
        |--------------------------------------------------------------------------
        | Only Admin User ID 1 Can Reset Password
        |--------------------------------------------------------------------------
        */
        $user = $this->userRepository->findAdminByEmail($email);

        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'If an account exists for this email address, a password reset link has been sent.',
                ])
                ->withInput();
        }


        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            Log::info('Password reset link response', [
                'email' => $request->email,
                'user_id' => $user->id,
                'status' => $status,
            ]);
        } catch (\Throwable $e) {
            Log::error('Password reset email failed', [
                'email' => $request->email,
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }

        if ($status === Password::RESET_LINK_SENT) {

            /*
            |--------------------------------------------------------------------------
            | Activity Log - Reset Link Sent
            |--------------------------------------------------------------------------
            */
            UtilityHelper::customActivityLog(
                'auth',
                'Password reset link sent successfully.',
                $user,
                [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
            return back()->with(
                'success',
                'If an account exists for this email address, a password reset link has been sent.'
            );
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withErrors([
                    'email' => 'A password reset link was recently requested. Please wait a moment before trying again.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Activity Log - Reset Link Failed
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'auth',
            'Password reset link request failed.',
            $user,
            [
                'email' => $request->email,
                'reason' => __($status),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return back()
            ->withErrors([
                'email' => __($status),
            ])
            ->withInput();
    }

    /**
     * Show reset password page.
     */
    public function showResetForm(
        string $token
    ): View {
        return view('backend.auth.reset-password', [
            'title' => 'Reset Password',
            'token' => $token,
            'email' => request()->query('email'),
            'bodyClassName' => 'reset-password-page',
        ]);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(
        ResetPasswordRequest $request
    ): RedirectResponse {

        $email = $request->validated('email');

        /*
        |--------------------------------------------------------------------------
        | Only Admin User ID 1 Can Reset Password
        |--------------------------------------------------------------------------
        */
        $user = $this->userRepository->findAdminByEmail($email);
        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'This password reset request is not allowed.',
                ])
                ->withInput();
        }

        $status = Password::reset(
            $request->validated(),
                function ($user, $password) use ($email) {
                    if (
                        (int) $user->id !== 1 ||
                        !$user->hasRole('admin') ||
                        $user->email !== $email
                    ) {
                        return;
                    }
                    
                    $this->userRepository->updatePassword(
                        $user,
                        $password
                    );

                    $user->forceFill([
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

        if ($status === Password::PASSWORD_RESET) {

            /*
            |--------------------------------------------------------------------------
            | Set User Timezone
            |--------------------------------------------------------------------------
            */
            UtilityHelper::setUserTimezone(
                $user,
                $request->input('timezone') ?? null
            );

            /*
            |--------------------------------------------------------------------------
            | Activity Log - Password Reset Successful
            |--------------------------------------------------------------------------
            */
            UtilityHelper::customActivityLog(
                'auth',
                'Password reset successfully.',
                $user,
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );

            Auth::logout();

            return redirect()
                ->route('admin.login')
                ->with(
                    'success',
                    'Your password has been reset successfully. You can now login.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Activity Log - Password Reset Failed
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'auth',
            'Password reset failed.',
            $user,
            [
                'email' => $request->validated('email'),
                'reason' => __($status),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return back()
            ->withErrors([
                'email' => __($status),
            ])
            ->withInput();
    }
}
