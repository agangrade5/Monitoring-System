<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitor;
use App\Jobs\CheckDomainExpiryJob;

$monitors = Monitor::whereNotNull('url')->get();
foreach ($monitors as $monitor) {
    echo "Running CheckDomainExpiryJob for Monitor ID: {$monitor->id} ({$monitor->url})...\n";
    CheckDomainExpiryJob::dispatchSync($monitor->id);
    
    $res = $monitor->fresh()->checkResult;
    echo " -> Domain Status: " . ($res->domain_status ?? 'null') . "\n";
    echo " -> Domain Expires At: " . ($res->domain_expires_at ? $res->domain_expires_at->format('Y-m-d') : 'null') . "\n";
    echo " -> Domain Registrar: " . ($res->domain_registrar ?? 'null') . "\n\n";
}
