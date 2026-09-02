<?php

namespace App\Services;

use App\Models\Monitor;
use App\Models\User;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use App\Jobs\{
    CheckUptimeJob,
    CheckSslCertificateJob,
    CheckPhpVersionJob,
    CheckDomainExpiryJob,
    CheckSecurityHeadersJob
};
use App\Notifications\TestMonitorAlertNotification;
use Illuminate\Support\Facades\Notification;

class MonitorService
{
    /**
     * Constructor to inject the Monitor Repository.
     *
     * @param MonitorRepositoryInterface $monitorRepository
     */
    public function __construct(
        protected MonitorRepositoryInterface $monitorRepository
    ) {}

    /**
     * Run all background health checks (Uptime, SSL, PHP Version, Domain Expiry, Security Headers).
     *
     * @param int $id
     * @return Monitor|null
     */
    public function runAllChecks(int $id): ?Monitor
    {
        $monitor = $this->monitorRepository->findById($id);

        CheckUptimeJob::dispatchSync($id);
        CheckSslCertificateJob::dispatchSync($id);
        CheckPhpVersionJob::dispatchSync($id);
        CheckDomainExpiryJob::dispatchSync($id);
        CheckSecurityHeadersJob::dispatchSync($id);

        return $monitor;
    }

    /**
     * Send a test notification alert email for a specific monitor.
     *
     * @param int $id
     * @param User|null $causer
     * @return array
     */
    public function sendTestNotification(int $id, ?User $causer = null): array
    {
        $monitor = $this->monitorRepository->findById($id);

        if (!$monitor) {
            return [
                'success' => false,
                'message' => 'Monitor not found.',
                'code' => 404,
            ];
        }

        $recipientEmail = $monitor->email ?: ($causer?->email ?? null);

        if (!$recipientEmail) {
            return [
                'success' => false,
                'message' => 'No recipient email configured for this monitor or user account.',
                'code' => 422,
            ];
        }

        try {
            Notification::route('mail', $recipientEmail)
                ->notify(new TestMonitorAlertNotification($monitor, $causer));

            if (function_exists('activity') && $causer) {
                activity('monitor')
                    ->causedBy($causer)
                    ->performedOn($monitor)
                    ->log("Sent test notification for monitor: {$monitor->name} to {$recipientEmail}");
            }

            return [
                'success' => true,
                'message' => "Test notification sent successfully to {$recipientEmail}.",
                'code' => 200,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to send test notification: ' . $e->getMessage(),
                'code' => 500,
            ];
        }
    }
}
