<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Monitor;
use App\Jobs\CheckSslCertificateJob;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$monitors = Monitor::where('is_active', true)->get();

foreach ($monitors as $m) {
    echo "Running CheckSslCertificateJob for Monitor #{$m->id}: {$m->name} ({$m->url})...\n";
    (new CheckSslCertificateJob($m->id))->handle();
    $m->refresh();
    $res = $m->checkResult;
    echo "  -> SSL Status: " . ($res ? $res->ssl_status : 'N/A') . " (Expires in: " . ($res ? $res->ssl_days_remaining : 'N/A') . " days)\n\n";
}
