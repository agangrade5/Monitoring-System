<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorCheckResult extends Model
{
    protected $fillable = [
        'monitor_id',
        'php_version',
        'php_status',
        'php_checked_at',
        'domain_status',
        'domain_expires_at',
        'domain_days_remaining',
        'domain_registrar',
        'domain_checked_at',
        'ssl_status',
        'ssl_enabled',
        'ssl_days_remaining',
        'ssl_expires_at',
        'ssl_issuer',
        'security_grade',
        'server_info',
        'open_ports',
        'security_headers',
    ];

    protected $casts = [
        'ssl_expires_at' => 'datetime',
        'ssl_enabled' => 'boolean',
        'domain_expires_at' => 'datetime',
        'domain_checked_at' => 'datetime',
        'php_checked_at' => 'datetime',
        'security_headers' => 'array',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
