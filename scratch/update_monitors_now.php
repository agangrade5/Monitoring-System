<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Jobs\CheckSslCertificateJob;
use App\Jobs\CheckUptimeJob;
use App\Models\Monitor;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$monitors = Monitor::where('is_active', true)->get();
echo "Running SSL Check & Uptime Check for " . $monitors->count() . " active monitors...\n\n";

foreach ($monitors as $m) {
    echo "Processing Monitor #{$m->id}: {$m->name} ({$m->url})...\n";
    (new CheckSslCertificateJob($m->id))->handle();
    (new CheckUptimeJob($m->id))->handle();
    $m->refresh();
    
    $sslStatus = $m->checkResult?->ssl_status ?? 'N/A';
    echo "  -> Final Status: " . strtoupper($m->status) . " | SSL Status: " . strtoupper($sslStatus) . "\n\n";
}
