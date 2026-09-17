<?php

namespace App\Services;

use App\Models\User;
use App\Models\Monitor;
use App\Models\MonitorLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ReportExportService
{
    /**
     * Generate user-specific monitoring report Excel (.xlsx) file.
     *
     * @param User $user
     * @param Collection $monitors
     * @param string $frequency ('weekly' or 'monthly')
     * @return string Absolute file path of generated Excel file
     */
    public function generateUserReportExcel(User $user, Collection $monitors, string $frequency = 'weekly'): string
    {
        $spreadsheet = new Spreadsheet();
        $frequencyLabel = ucfirst($frequency);
        $generatedAt = now()->format('d-m-Y H:i:s T');
        $reportTitle = config('app.name', 'Monitoring System') . " - {$frequencyLabel} Health & Uptime Report";

        // ----------------------------------------------------
        // SHEET 1: Summary Overview
        // ----------------------------------------------------
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Overview Summary');

        // Styles
        $primaryColor = '0D6EFD';
        $darkColor = '1E293B';
        $lightGray = 'F8FAFC';
        $borderColor = 'CBD5E1';

        // Title Block
        $summarySheet->setCellValue('A1', $reportTitle);
        $summarySheet->mergeCells('A1:F2');
        $summarySheet->getStyle('A1:F2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $primaryColor],
            ],
        ]);

        // User & Report Details
        $summarySheet->setCellValue('A4', 'Report Type:');
        $summarySheet->setCellValue('B4', "{$frequencyLabel} Monitoring Digest");
        $summarySheet->setCellValue('D4', 'Recipient Name:');
        $summarySheet->setCellValue('E4', $user->name);

        $summarySheet->setCellValue('A5', 'Generated On:');
        $summarySheet->setCellValue('B5', $generatedAt);
        $summarySheet->setCellValue('D5', 'Recipient Email:');
        $summarySheet->setCellValue('E5', $user->email);

        $summarySheet->getStyle('A4:A5')->getFont()->setBold(true);
        $summarySheet->getStyle('D4:D5')->getFont()->setBold(true);

        // Metrics Calculation
        $totalMonitors = $monitors->count();
        $upMonitors = $monitors->where('status', 'up')->count();
        $downMonitors = $monitors->where('status', 'down')->count();
        $avgUptime = $totalMonitors > 0 ? round($monitors->avg('uptime_percentage') ?? 100, 2) : 100;
        $sslIssues = $monitors->filter(function ($m) {
            $status = strtolower($m->ssl_status ?? '');
            return in_array($status, ['expired', 'invalid']);
        })->count();

        // Metrics KPI Boxes
        $kpiHeaders = [
            ['cell' => 'A7:B7', 'label' => 'Total Monitors', 'valueCell' => 'A8:B8', 'value' => $totalMonitors, 'color' => '0284C7'],
            ['cell' => 'C7:D7', 'label' => 'Operational (UP)', 'valueCell' => 'C8:D8', 'value' => $upMonitors, 'color' => '16A34A'],
            ['cell' => 'E7:F7', 'label' => 'Incidents (DOWN)', 'valueCell' => 'E8:F8', 'value' => $downMonitors, 'color' => 'DC2626'],
        ];

        foreach ($kpiHeaders as $kpi) {
            $summarySheet->mergeCells($kpi['cell']);
            $summarySheet->mergeCells($kpi['valueCell']);

            $summarySheet->setCellValue(explode(':', $kpi['cell'])[0], $kpi['label']);
            $summarySheet->setCellValue(explode(':', $kpi['valueCell'])[0], $kpi['value']);

            $summarySheet->getStyle($kpi['cell'])->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $kpi['color']]],
            ]);

            $summarySheet->getStyle($kpi['valueCell'])->applyFromArray([
                'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => $darkColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
            ]);
        }

        // Additional Summary Stats Table
        $summarySheet->setCellValue('A10', 'System Health Indicators');
        $summarySheet->mergeCells('A10:F10');
        $summarySheet->getStyle('A10:F10')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $darkColor]],
        ]);

        $stats = [
            ['Metric' => 'Average Fleet Uptime', 'Value' => "{$avgUptime}%"],
            ['Metric' => 'SSL Certificate Issues Detected', 'Value' => (string) $sslIssues],
            ['Metric' => 'Overall Status', 'Value' => $downMonitors === 0 ? 'All Systems Operational' : "{$downMonitors} Service(s) Requiring Attention"],
        ];

        $row = 11;
        foreach ($stats as $stat) {
            $summarySheet->setCellValue("A{$row}", $stat['Metric']);
            $summarySheet->mergeCells("A{$row}:C{$row}");
            $summarySheet->setCellValue("D{$row}", $stat['Value']);
            $summarySheet->mergeCells("D{$row}:F{$row}");

            $summarySheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $row % 2 === 0 ? $lightGray : 'FFFFFF']],
            ]);
            $summarySheet->getStyle("A{$row}")->getFont()->setBold(true);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $summarySheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 2: Monitors Detail List
        // ----------------------------------------------------
        $monitorsSheet = $spreadsheet->createSheet();
        $monitorsSheet->setTitle('Monitors Detail');

        $headers = [
            'A1' => '#',
            'B1' => 'Monitor Name',
            'C1' => 'URL / Endpoint',
            'D1' => 'Current Status',
            'E1' => 'Uptime (%)',
            'F1' => 'Check Interval',
            'G1' => 'SSL Status',
            'H1' => 'SSL Days Left',
            'I1' => 'SSL Issuer',
            'J1' => 'Domain Status',
            'K1' => 'Domain Expiry',
            'L1' => 'Security Grade',
            'M1' => 'PHP Version',
            'N1' => 'Last Checked At',
            'O1' => 'Last Up At',
            'P1' => 'Last Down At',
        ];

        foreach ($headers as $cell => $header) {
            $monitorsSheet->setCellValue($cell, $header);
        }

        $monitorsSheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $darkColor],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '475569']],
            ],
        ]);
        $monitorsSheet->getRowDimension(1)->setRowHeight(28);

        $mRow = 2;
        $index = 1;
        foreach ($monitors as $monitor) {
            $statusUpper = strtoupper($monitor->status ?? 'UNKNOWN');
            $isUp = strtolower($monitor->status ?? '') === 'up';

            $monitorsSheet->setCellValue("A{$mRow}", $index++);
            $monitorsSheet->setCellValue("B{$mRow}", $monitor->name);
            $monitorsSheet->setCellValue("C{$mRow}", $monitor->url);
            $monitorsSheet->setCellValue("D{$mRow}", $statusUpper);
            $monitorsSheet->setCellValue("E{$mRow}", number_format((float) ($monitor->uptime_percentage ?? 100), 2) . '%');
            $monitorsSheet->setCellValue("F{$mRow}", ($monitor->check_interval ?? 5) . ' min');
            $monitorsSheet->setCellValue("G{$mRow}", ucfirst($monitor->ssl_status ?? 'N/A'));
            $monitorsSheet->setCellValue("H{$mRow}", $monitor->ssl_days_remaining !== null ? $monitor->ssl_days_remaining . ' days' : 'N/A');
            $monitorsSheet->setCellValue("I{$mRow}", $monitor->ssl_issuer ?? 'N/A');
            $monitorsSheet->setCellValue("J{$mRow}", ucfirst($monitor->domain_status ?? 'N/A'));
            $monitorsSheet->setCellValue("K{$mRow}", $monitor->domain_expires_at ? $monitor->domain_expires_at->format('d-m-Y') : 'N/A');
            $monitorsSheet->setCellValue("L{$mRow}", $monitor->security_grade ?? 'N/A');
            $monitorsSheet->setCellValue("M{$mRow}", $monitor->php_version ?? 'N/A');
            $monitorsSheet->setCellValue("N{$mRow}", $monitor->last_checked_at ? $monitor->last_checked_at->format('d-m-Y H:i:s') : 'N/A');
            $monitorsSheet->setCellValue("O{$mRow}", $monitor->last_up_at ? $monitor->last_up_at->format('d-m-Y H:i:s') : 'N/A');
            $monitorsSheet->setCellValue("P{$mRow}", $monitor->last_down_at ? $monitor->last_down_at->format('d-m-Y H:i:s') : 'N/A');

            // Row style
            $rowBg = ($mRow % 2 === 0) ? $lightGray : 'FFFFFF';
            $monitorsSheet->getStyle("A{$mRow}:P{$mRow}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Center-align specific columns
            $monitorsSheet->getStyle("A{$mRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $monitorsSheet->getStyle("D{$mRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $monitorsSheet->getStyle("E{$mRow}:H{$mRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $monitorsSheet->getStyle("J{$mRow}:M{$mRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Status color highlight
            $statusTextColor = $isUp ? '16A34A' : 'DC2626';
            $monitorsSheet->getStyle("D{$mRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $statusTextColor]],
            ]);

            $mRow++;
        }

        foreach (range('A', 'P') as $col) {
            $monitorsSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 3: Outage & Incident Logs
        // ----------------------------------------------------
        $logsSheet = $spreadsheet->createSheet();
        $logsSheet->setTitle('Incident Logs');

        $logHeaders = [
            'A1' => '#',
            'B1' => 'Monitor Name',
            'C1' => 'Target URL',
            'D1' => 'Status',
            'E1' => 'HTTP Code',
            'F1' => 'Response Time',
            'G1' => 'Reason / Error Message',
            'H1' => 'Recorded At',
        ];

        foreach ($logHeaders as $cell => $header) {
            $logsSheet->setCellValue($cell, $header);
        }

        $logsSheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $darkColor]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '475569']]],
        ]);
        $logsSheet->getRowDimension(1)->setRowHeight(28);

        // Fetch recent logs for user's monitors
        $monitorIds = $monitors->pluck('id')->toArray();
        $recentLogs = !empty($monitorIds)
            ? MonitorLog::with('monitor')
                ->whereIn('monitor_id', $monitorIds)
                ->latest('checked_at')
                ->limit(100)
                ->get()
            : collect();

        $lRow = 2;
        $lIndex = 1;
        if ($recentLogs->isEmpty()) {
            $logsSheet->setCellValue('A2', 'No recent incident or check logs found for this period.');
            $logsSheet->mergeCells('A2:H2');
            $logsSheet->getStyle('A2:H2')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
            ]);
            $logsSheet->getRowDimension(2)->setRowHeight(24);
        } else {
            foreach ($recentLogs as $log) {
                $statusUpper = strtoupper($log->status ?? 'UNKNOWN');
                $isLogUp = strtolower($log->status ?? '') === 'up';

                $logsSheet->setCellValue("A{$lRow}", $lIndex++);
                $logsSheet->setCellValue("B{$lRow}", $log->monitor?->name ?? 'N/A');
                $logsSheet->setCellValue("C{$lRow}", $log->monitor?->url ?? 'N/A');
                $logsSheet->setCellValue("D{$lRow}", $statusUpper);
                $logsSheet->setCellValue("E{$lRow}", $log->http_status_code ?? 'N/A');
                $logsSheet->setCellValue("F{$lRow}", $log->response_time !== null ? $log->response_time . ' ms' : 'N/A');
                $logsSheet->setCellValue("G{$lRow}", $log->reason ?? $log->error_message ?? 'N/A');
                $logsSheet->setCellValue("H{$lRow}", $log->checked_at ? $log->checked_at->format('d-m-Y H:i:s') : 'N/A');

                $rowBg = ($lRow % 2 === 0) ? $lightGray : 'FFFFFF';
                $logsSheet->getStyle("A{$lRow}:H{$lRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $logsSheet->getStyle("A{$lRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $logsSheet->getStyle("D{$lRow}:F{$lRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $logsSheet->getStyle("H{$lRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $statusTextColor = $isLogUp ? '16A34A' : 'DC2626';
                $logsSheet->getStyle("D{$lRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $statusTextColor]],
                ]);

                $lRow++;
            }
        }

        foreach (range('A', 'H') as $col) {
            $logsSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet back to Overview
        $spreadsheet->setActiveSheetIndex(0);

        // Ensure storage directory exists
        $storageDir = storage_path('app/reports');
        if (!File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        $cleanUsername = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name);
        $fileName = "Monitoring_Report_{$cleanUsername}_{$frequency}_" . now()->format('Ymd_His') . ".xlsx";
        $filePath = "{$storageDir}/{$fileName}";

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    /**
     * Safely delete temporary generated report file.
     *
     * @param string $filePath
     * @return void
     */
    public function cleanupFile(string $filePath): void
    {
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
}
