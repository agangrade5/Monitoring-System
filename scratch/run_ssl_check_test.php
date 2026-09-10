<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitor;
use App\Jobs\CheckSslCertificateJob;

$monitors = Monitor::where('url', 'like', 'https://%')->get();
foreach ($monitors as $monitor) {
    echo "Running CheckSslCertificateJob for Monitor ID: {$monitor->id} ({$monitor->url})...\n";
    CheckSslCertificateJob::dispatchSync($monitor->id);
    
    $res = $monitor->fresh()->checkResult;
    echo " -> SSL Status: " . ($res->ssl_status ?? 'null') . "\n";
    echo " -> SSL Issuer: " . ($res->ssl_issuer ?? 'null') . "\n";
    echo " -> Days Remaining: " . ($res->ssl_days_remaining ?? 'null') . "\n\n";
}
