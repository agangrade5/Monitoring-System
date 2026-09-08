<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorSetting extends Model
{
    protected $fillable = [
        'monitor_id',
        'check_uptime',
        'check_ssl',
        'check_php',
        'check_domain',
        'check_security_headers',
    ];

    protected $casts = [
        'check_uptime' => 'boolean',
        'check_ssl' => 'boolean',
        'check_php' => 'boolean',
        'check_domain' => 'boolean',
        'check_security_headers' => 'boolean',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
