<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
     protected $fillable = [
        'email_notification',
        'sms_notification',
        'two_factor_authentication',
        'mode',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'sms_provider',
        'sms_api_key',
        'sms_api_secret',
        'sms_from_number',
    ];

    protected $casts = [
        'email_notification' => 'boolean',
        'sms_notification' => 'boolean',
        'two_factor_authentication' => 'boolean',
    ];
}
