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
        'status',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'last_up_at' => 'datetime',
        'last_down_at' => 'datetime',
        'uptime_percentage' => 'decimal:2',
        'is_active' => 'boolean',
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

    /**
     * Get monitor check settings options.
     */
    public function settings()
    {
        return $this->hasOne(MonitorSetting::class)->withDefault([
            'check_uptime' => false,
            'check_ssl' => false,
            'check_php' => false,
            'check_domain' => false,
            'check_security_headers' => false,
        ]);
    }

    /**
     * Get monitor health check results.
     */
    public function checkResult()
    {
        return $this->hasOne(MonitorCheckResult::class)->withDefault();
    }

    /**
     * Dynamic magic getter to support relation property fallbacks seamlessly.
     */
    public function __get($key)
    {
        $value = parent::__get($key);
        if ($value !== null) {
            return $value;
        }

        // Fallback to settings
        if (in_array($key, ['check_uptime', 'check_ssl', 'check_php', 'check_domain', 'check_security_headers'])) {
            return $this->settings->{$key} ?? true;
        }

        // Fallback to checkResult
        if (in_array($key, [
            'ssl_status', 'ssl_enabled', 'ssl_days_remaining', 'ssl_expires_at', 'ssl_issuer',
            'php_version', 'php_status', 'php_checked_at',
            'domain_status', 'domain_expires_at', 'domain_checked_at',
            'security_grade', 'server_info', 'open_ports', 'security_headers'
        ])) {
            return $this->checkResult->{$key} ?? null;
        }

        return null;
    }
}
