<?php

namespace Database\Seeders;

use App\Models\Monitor;
use Illuminate\Database\Seeder;

class MonitorSeeder extends Seeder
{
    public function run(): void
    {
        Monitor::create([
            'name' => 'Google Website',
            'email' => '2Ct7sssU@example.com',
             'user_id' => 2,
            'url' => 'https://www.google.com',
            'ip_address' => null,
            'type' => 'website',
            'status' => 'up',
            'check_interval' => 60,
            'last_checked_at' => now(),
            'last_up_at' => now()->subMinutes(2),
            'last_down_at' => null,
            'uptime_percentage' => 99.99,
            'is_active' => true,
        ]);

        Monitor::create([
            'name' => 'Laravel API',
             'user_id' => 2,
             'email' => '2CtdsU@example.com',
            'url' => 'https://example.com/api/health',
            'ip_address' => null,
            'type' => 'website',
            'status' => 'up',
            'check_interval' => 30,
            'last_checked_at' => now()->subMinute(),
            'last_up_at' => now()->subMinute(),
            'last_down_at' => null,
            'uptime_percentage' => 99.95,
            'is_active' => true,
        ]);

        Monitor::create([
            'name' => 'Production Server',
             'user_id' => 2,
             'email' => '2Ct7sU@example.com',
            'url' => null,
            'ip_address' => '192.168.1.100',
            'type' => 'website',
            'status' => 'down',
            'check_interval' => 60,
            'last_checked_at' => now()->subMinutes(5),
            'last_up_at' => now()->subHour(),
            'last_down_at' => now()->subMinutes(5),
            'uptime_percentage' => 98.50,
            'is_active' => true,
        ]);

        Monitor::create([
            'name' => 'Payment API',
            'email' => '2Ct7U@example.com',
    
            'url' => 'https://example.com/payment',
             'user_id' => 2,
            'ip_address' => null,
            'type' => 'website',
            'status' => 'down',
            'check_interval' => 120,
            'last_checked_at' => null,
            'last_up_at' => null,
            'last_down_at' => null,
            'uptime_percentage' => 100.00,
            'is_active' => false,
        ]);
    }
}