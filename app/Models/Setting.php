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
    ];

    protected $casts = [
        'email_notification' => 'boolean',
        'sms_notification' => 'boolean',
        'two_factor_authentication' => 'boolean',
    ];
}
