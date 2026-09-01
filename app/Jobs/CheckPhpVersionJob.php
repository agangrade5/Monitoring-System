<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckPhpVersionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $monitorId
    ) {}

    public function handle(): void
    {
        $monitor = Monitor::find($this->monitorId);

        if (!$monitor) {
            return;
        }

        $phpVersion = PHP_VERSION;

        $monitor->update([
            'php_version' => $phpVersion,
            'php_status' => 'up',
            'php_checked_at' => now(),
        ]);
    }
}