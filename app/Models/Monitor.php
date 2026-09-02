<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    
   protected $fillable = [
    'user_id',
    'name',
    'email',
    'mobile',
    'url',
    'check_interval',
    'last_checked_at',
    'last_up_at',
    'last_down_at',
    'uptime_percentage',
    'is_active',
    'ssl_enabled',
    'ssl_expires_at',
    'ssl_days_remaining',
    'ssl_status',
    'ssl_issuer',
     'php_version',
    'php_status',
    'php_checked_at',
    'domain_expires_at',
'domain_status',
'domain_checked_at',
'security_headers',
'status'
];

 protected $casts = [
    'last_checked_at' => 'datetime',
    'last_up_at' => 'datetime',
    'last_down_at' => 'datetime',
    'ssl_expires_at' => 'datetime',
    'uptime_percentage' => 'decimal:2',
    'ssl_enabled' => 'boolean',
    'is_active' => 'boolean',
    'domain_expires_at' => 'datetime',
'domain_checked_at' => 'datetime',
'php_checked_at' => 'datetime',
'security_headers' => 'array',
];

    /**
     * Get open ports as an array for UI badges.
     */
    public function getPortsListAttribute(): array
    {
        if (empty($this->open_ports)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->open_ports))));
    }

    /**
     * Get domain expiry relative human diff.
     */
    public function getDomainExpiryDiffAttribute(): ?string
    {
        if (!$this->domain_expires_at) {
            return null;
        }

        return $this->domain_expires_at->diffForHumans();
    }

    /**
     * Get the user that owns the monitor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
