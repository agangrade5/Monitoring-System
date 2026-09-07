<?php

namespace App\Notifications;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestMonitorAlertNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Monitor $monitor,
        public ?User $causer = null
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
        $showUrl = route('monitor.show', $this->monitor->id);

        return (new MailMessage)
            ->subject('[Test Alert] Monitor Test Notification: ' . $this->monitor->name)
            ->view('emails.monitor.test-alert', [
                'monitor' => $this->monitor,
                'user' => $this->causer ?? auth()->user(),
                'url' => $showUrl,
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
            'monitor_id' => $this->monitor->id,
            'monitor_name' => $this->monitor->name,
            'url' => $this->monitor->url,
            'type' => 'test_notification',
        ];
    }
}
