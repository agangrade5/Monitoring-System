<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class MonitoringReportNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user,
        public Collection $monitors,
        public string $filePath,
        public string $frequency = 'weekly'
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
        $frequencyLabel = ucfirst($this->frequency);
        $totalMonitors = $this->monitors->count();
        $appName = config('app.name', 'Monitoring System');

        $fileName = basename($this->filePath);
        $downloadUrl = URL::temporarySignedRoute('report.download', now()->addDays(30), ['file' => $fileName]);

        $mailMessage = (new MailMessage)
            ->subject("[{$frequencyLabel} Report] Website Health & Monitoring Digest - {$appName}")
            ->view('emails.report.monitoring-report', [
                'user' => $this->user,
                'monitors' => $this->monitors,
                'frequency' => $this->frequency,
                'frequencyLabel' => $frequencyLabel,
                'totalMonitors' => $totalMonitors,
                'fileName' => $fileName,
                'downloadUrl' => $downloadUrl,
            ]);


        if (file_exists($this->filePath)) {
            $fileContent = file_get_contents($this->filePath);
            $mailMessage->attachData($fileContent, $fileName, [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'monitoring_report',
            'frequency' => $this->frequency,
            'monitors_count' => $this->monitors->count(),
            'user_id' => $this->user->id,
        ];
    }
}
