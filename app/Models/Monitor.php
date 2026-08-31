<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    
     protected $fillable = [
        'name',
        'email',
        'mobile',
        'url',
        'ip_address',
         'user_id',
        'type',
        'status',
        'check_interval',
        'last_checked_at',
        'last_up_at',
        'last_down_at',
        'uptime_percentage',
        'is_active',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'last_up_at' => 'datetime',
        'last_down_at' => 'datetime',
        'uptime_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
