<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailReportService;

class SendEmailReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send-email
                            {--frequency=weekly : Frequency to process (weekly or monthly)}
                            {--user_id= : Optional specific user ID to send report to}
                            {--force : Bypass settings check and force send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch scheduled email monitoring reports with Excel attachments to eligible users';

    /**
     * Execute the console command.
     */
    public function handle(EmailReportService $emailReportService): int
    {
        $frequency = strtolower((string) $this->option('frequency'));
        if (!in_array($frequency, ['weekly', 'monthly'])) {
            $this->error("Invalid frequency '{$frequency}'. Allowed values: weekly, monthly.");
            return self::FAILURE;
        }

        $userId = $this->option('user_id') ? (int) $this->option('user_id') : null;
        $force = (bool) $this->option('force');

        $this->info("------------------------------------------------------------");
        $this->info(" Starting Email Report Job - " . ucfirst($frequency) . " Schedule");
        $this->info(" Mode: " . ($userId ? "Single User ID: {$userId}" : "All Eligible Users"));
        $this->info(" Force Send: " . ($force ? "Yes" : "No (Strict Settings Filter)"));
        $this->info("------------------------------------------------------------");

        $result = $emailReportService->processReports($frequency, $userId, $force);

        $this->table(
            ['Frequency', 'Total Eligible', 'Successfully Sent', 'Failed'],
            [
                [
                    ucfirst($result['frequency']),
                    $result['total_eligible'],
                    $result['sent_count'],
                    $result['failed_count'],
                ]
            ]
        );

        if (!empty($result['errors'])) {
            $this->warn("Errors encountered during processing:");
            foreach ($result['errors'] as $error) {
                $this->error(" - {$error}");
            }
        }

        $this->info("Email report job completed successfully.");
        return self::SUCCESS;
    }
}
