<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     *
     * @return void
     */
    public function __construct(
        protected string $token
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::info('Custom password reset notification triggered', [
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $url = url(
            route('admin.password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false)
        );

        Log::info('Password reset URL generated', [
            'email' => $notifiable->getEmailForPasswordReset(),
            'url' => $url
        ]);

        return (new MailMessage)
            ->subject('Reset Your Password - ' . config('app.name'))
            ->view('emails.auth.password-reset', [
                'user' => $notifiable,
                'url' => $url,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
