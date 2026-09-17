<?php

namespace App\Services;

use App\Models\User;
use App\Models\Monitor;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\EmailLogRepositoryInterface;
use App\Notifications\MonitoringReportNotification;
use App\Helpers\UtilityHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailReportService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected SettingRepositoryInterface $settingRepository,
        protected ReportExportService $reportExportService,
        protected MailConfigService $mailConfigService,
        protected ?EmailLogRepositoryInterface $emailLogRepository = null
    ) {
        $this->emailLogRepository = $emailLogRepository ?? app(EmailLogRepositoryInterface::class);
    }

    /**
     * Determine if a user is eligible for a specific report frequency (weekly or monthly).
     *
     * Evaluation Rules:
     * 1. If 'enabled' is false or missing -> ineligible (false).
     * 2. If 'enabled' is true:
     *    - 'weekly' frequency: eligible if 'weekly' is true.
     *    - 'monthly' frequency: eligible if 'monthly' is true OR (neither 'weekly' nor 'monthly' is true -> fallback to monthly).
     *
     * @param User $user
     * @param string $frequency ('weekly' or 'monthly')
     * @return bool
     */
    public function isUserEligibleForReport(User $user, string $frequency = 'weekly'): bool
    {
        // 1. User must be active and have an email
        if (!$user->is_active || empty($user->email)) {
            return false;
        }

        // 2. Fetch user's report setting from SettingRepository
        $reportData = $this->settingRepository->getSettingArray('report', [], $user->id);

        if (empty($reportData) || !isset($reportData['report_email'])) {
            return false;
        }

        $reportEmail = $reportData['report_email'];
        $enabled = (bool) ($reportEmail['enabled'] ?? false);
        $weekly = (bool) ($reportEmail['weekly'] ?? false);
        $monthly = (bool) ($reportEmail['monthly'] ?? false);

        // If enabled is not true, do not send report
        if (!$enabled) {
            return false;
        }

        $frequency = strtolower($frequency);

        if ($frequency === 'weekly') {
            return $weekly === true;
        }

        if ($frequency === 'monthly') {
            // If monthly is explicitly true, or if both weekly and monthly are false (fallback default)
            if ($monthly === true) {
                return true;
            }

            if ($weekly === false && $monthly === false) {
                return true; // Default fallback to monthly
            }

            return false;
        }

        return false;
    }

    /**
     * Get all active users who are eligible for the given report frequency.
     *
     * @param string $frequency ('weekly' or 'monthly')
     * @return Collection<int, User>
     */
    public function getEligibleUsersForReport(string $frequency = 'weekly'): Collection
    {
        $users = User::where('is_active', true)->get();

        return $users->filter(fn (User $user) => $this->isUserEligibleForReport($user, $frequency));
    }

    /**
     * Send email report with distinct Excel attachment to a specific user.
     *
     * @param User $user
     * @param string $frequency ('weekly' or 'monthly')
     * @param bool $force (Bypass eligibility check for manual testing)
     * @return bool
     */
    public function sendReportToUser(User $user, string $frequency = 'weekly', bool $force = false): bool
    {
        if (!$force && !$this->isUserEligibleForReport($user, $frequency)) {
            return false;
        }

        // Apply dynamic SMTP configuration
        $this->mailConfigService->apply();

        // Retrieve distinct monitors belonging to this user
        $monitors = Monitor::where('user_id', $user->id)
            ->with(['settings', 'checkResult'])
            ->get();

        $filePath = null;

        try {
            // Generate distinct Excel file for user
            $filePath = $this->reportExportService->generateUserReportExcel($user, $monitors, $frequency);

            // Send notification with Excel attachment
            $user->notify(new MonitoringReportNotification($user, $monitors, $filePath, $frequency));

            $frequencyLabel = ucfirst($frequency);
            $subject = "[{$frequencyLabel} Report] Website Health & Monitoring Digest - " . config('app.name', 'Monitoring System');

            // Record in Email Logs
            $this->emailLogRepository->create([
                'monitor_id' => null,
                'user_id' => $user->id,
                'recipient_email' => $user->email,
                'alert_type' => 'scheduled_report',
                'subject' => $subject,
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => [
                    'frequency' => $frequency,
                    'monitors_count' => $monitors->count(),
                    'file_name' => basename($filePath),
                ],
            ]);

            // Record in Activity Log
            UtilityHelper::customActivityLog(
                'report',
                "Dispatched {$frequencyLabel} Excel monitoring report to {$user->email}.",
                $user,
                [
                    'frequency' => $frequency,
                    'monitors_count' => $monitors->count(),
                    'recipient' => $user->email,
                ]
            );

            return true;
        } catch (Throwable $e) {
            Log::error("Failed to send {$frequency} report to user {$user->id} ({$user->email}): " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $this->emailLogRepository->create([
                'monitor_id' => null,
                'user_id' => $user->id,
                'recipient_email' => $user->email,
                'alert_type' => 'scheduled_report',
                'subject' => "[{$frequency} Report] Website Health & Monitoring Digest",
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
                'metadata' => [
                    'frequency' => $frequency,
                ],
            ]);

            return false;
        }
    }

    /**
     * Process and dispatch reports for all eligible users or a specific user.
     *
     * @param string $frequency ('weekly' or 'monthly')
     * @param ?int $specificUserId
     * @param bool $force
     * @return array
     */
    public function processReports(string $frequency = 'weekly', ?int $specificUserId = null, bool $force = false): array
    {
        $frequency = strtolower($frequency);
        if (!in_array($frequency, ['weekly', 'monthly'])) {
            $frequency = 'weekly';
        }

        if ($specificUserId) {
            $user = User::find($specificUserId);
            if (!$user) {
                return [
                    'total_eligible' => 0,
                    'sent_count' => 0,
                    'failed_count' => 1,
                    'errors' => ["User with ID {$specificUserId} not found."],
                ];
            }
            $eligibleUsers = collect([$user]);
        } else {
            $eligibleUsers = $this->getEligibleUsersForReport($frequency);
        }

        $sentCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($eligibleUsers as $user) {
            $success = $this->sendReportToUser($user, $frequency, $force);
            if ($success) {
                $sentCount++;
            } else {
                $failedCount++;
                $errors[] = "Failed to dispatch to {$user->email} (ID: {$user->id})";
            }
        }

        return [
            'frequency' => $frequency,
            'total_eligible' => $eligibleUsers->count(),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'errors' => $errors,
        ];
    }
}
