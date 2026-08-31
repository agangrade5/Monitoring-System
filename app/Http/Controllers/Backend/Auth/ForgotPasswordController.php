<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\ForgotPasswordRequest;
use App\Http\Requests\Backend\Auth\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
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
                'Password reset link has been sent to your email address.'
            );
        }

        return back()
            ->withErrors([
                'email' => __($status),
            ])
            ->withInput();
    }

    /**
     * Show password reset form.
     */
    public function showResetForm(
        string $token
    ): View|RedirectResponse {
        return view('backend.auth.reset-password', [
            'title' => 'Reset Password',
            'token' => $token,
            'email' => request()->query('email'),
            'bodyClassName' => 'reset-password-page',
        ]);
    }

    /**
     * Reset password.
     */
    public function resetPassword(
        ResetPasswordRequest $request
    ): RedirectResponse {
        $status = Password::reset(
            $request->validated(),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
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
