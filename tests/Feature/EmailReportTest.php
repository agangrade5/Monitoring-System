<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\Monitor;
use App\Models\EmailLog;
use App\Services\EmailReportService;
use App\Services\ReportExportService;
use App\Notifications\MonitoringReportNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test all user eligibility condition scenarios as requested.
     */
    public function test_user_eligibility_scenarios(): void
    {
        $service = app(EmailReportService::class);

        // 1. User with enabled=false (should be false for both weekly and monthly)
        $userDisabled = User::factory()->create(['is_active' => true]);
        Setting::create([
            'user_id' => $userDisabled->id,
            'type' => 'report',
            'value' => json_encode(['report_email' => ['enabled' => false, 'weekly' => true, 'monthly' => true]]),
        ]);
        $this->assertFalse($service->isUserEligibleForReport($userDisabled, 'weekly'));
        $this->assertFalse($service->isUserEligibleForReport($userDisabled, 'monthly'));

        // 2. User with enabled=true, weekly=true, monthly=true (true for both)
        $userBoth = User::factory()->create(['is_active' => true]);
        Setting::create([
            'user_id' => $userBoth->id,
            'type' => 'report',
            'value' => json_encode(['report_email' => ['enabled' => true, 'weekly' => true, 'monthly' => true]]),
        ]);
        $this->assertTrue($service->isUserEligibleForReport($userBoth, 'weekly'));
        $this->assertTrue($service->isUserEligibleForReport($userBoth, 'monthly'));

        // 3. User with enabled=true, weekly=true, monthly=false (true for weekly, false for monthly)
        $userWeeklyOnly = User::factory()->create(['is_active' => true]);
        Setting::create([
            'user_id' => $userWeeklyOnly->id,
            'type' => 'report',
            'value' => json_encode(['report_email' => ['enabled' => true, 'weekly' => true, 'monthly' => false]]),
        ]);
        $this->assertTrue($service->isUserEligibleForReport($userWeeklyOnly, 'weekly'));
        $this->assertFalse($service->isUserEligibleForReport($userWeeklyOnly, 'monthly'));

        // 4. User with enabled=true, weekly=false, monthly=true (false for weekly, true for monthly)
        $userMonthlyOnly = User::factory()->create(['is_active' => true]);
        Setting::create([
            'user_id' => $userMonthlyOnly->id,
            'type' => 'report',
            'value' => json_encode(['report_email' => ['enabled' => true, 'weekly' => false, 'monthly' => true]]),
        ]);
        $this->assertFalse($service->isUserEligibleForReport($userMonthlyOnly, 'weekly'));
        $this->assertTrue($service->isUserEligibleForReport($userMonthlyOnly, 'monthly'));

        // 5. User with enabled=true, weekly=false, monthly=false (Fallback to monthly report!)
        $userFallback = User::factory()->create(['is_active' => true]);
        Setting::create([
            'user_id' => $userFallback->id,
            'type' => 'report',
            'value' => json_encode(['report_email' => ['enabled' => true, 'weekly' => false, 'monthly' => false]]),
        ]);
        $this->assertFalse($service->isUserEligibleForReport($userFallback, 'weekly'));
        $this->assertTrue($service->isUserEligibleForReport($userFallback, 'monthly')); // Default fallback

        // 6. User with no setting record
        $userNoSetting = User::factory()->create(['is_active' => true]);
        $this->assertFalse($service->isUserEligibleForReport($userNoSetting, 'weekly'));
        $this->assertFalse($service->isUserEligibleForReport($userNoSetting, 'monthly'));
    }

    /**
     * Test Excel file generation with multiple worksheets and valid content.
     */
    public function test_excel_file_generation(): void
    {
        $exportService = app(ReportExportService::class);
        $user = User::factory()->create(['name' => 'Report Test User', 'is_active' => true]);

        $monitor = Monitor::create([
            'user_id' => $user->id,
            'name' => 'Production Server Test',
            'url' => 'https://example.com',
            'status' => 'up',
            'uptime_percentage' => 99.95,
            'is_active' => true,
        ]);

        $filePath = $exportService->generateUserReportExcel($user, collect([$monitor]), 'weekly');

        $this->assertFileExists($filePath);
        $this->assertStringEndsWith('.xlsx', $filePath);

        // Verify spreadsheet can be loaded
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($filePath);

        $sheetNames = $spreadsheet->getSheetNames();
        $this->assertContains('Overview Summary', $sheetNames);
        $this->assertContains('Monitors Detail', $sheetNames);
        $this->assertContains('Incident Logs', $sheetNames);

        // Clean up
        $exportService->cleanupFile($filePath);
        $this->assertFileDoesNotExist($filePath);
    }

    /**
     * Test artisan command execution.
     */
    public function test_artisan_command_execution(): void
    {
        Notification::fake();

        $user = User::factory()->create(['is_active' => true]);
        Setting::create([
            'user_id' => $user->id,
            'type' => 'report',
            'value' => json_encode(['report_email' => ['enabled' => true, 'weekly' => true, 'monthly' => true]]),
        ]);

        $this->artisan('reports:send-email', ['--frequency' => 'weekly', '--user_id' => $user->id])
            ->assertExitCode(0);

        Notification::assertSentTo($user, MonitoringReportNotification::class);
    }

    /**
     * Test signed download route for Excel report.
     */
    public function test_signed_download_route(): void
    {
        $exportService = app(ReportExportService::class);
        $user = User::factory()->create(['is_active' => true]);
        $filePath = $exportService->generateUserReportExcel($user, collect(), 'monthly');
        $fileName = basename($filePath);

        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute('report.download', now()->addMinutes(10), ['file' => $fileName]);

        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', "attachment; filename={$fileName}");

        $exportService->cleanupFile($filePath);
    }
}

