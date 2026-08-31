<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\{ForgotPasswordRequest, ResetPasswordRequest};
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Auth, Password};
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
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'success',
                'If an account exists for this email address, a password reset link has been sent.'
            );
        }

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
        $status = Password::reset(
            $request->validated(),
                function ($user, $password) {
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
            Auth::logout();
            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Your password has been reset successfully. You can now login.'
                );
        }

        return back()
            ->withErrors([
                'email' => __($status),
            ])
            ->withInput();
    }
}