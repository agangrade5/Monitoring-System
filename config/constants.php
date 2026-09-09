<?php

return [

    /**
     * Default pagination limit
     */
    'pagination_limit' => [
        'defaultPagination' => 10,
    ],

    /**
     * Date format to be used for the application
     */
    'date_format' => [
        'admin_display' => 'd-m-Y h:i:s A',
    ],

    /**
     * System settings configuration
     */
    'settings' => [
        'twilio' => [
            'enable_twilio' => false,
            'twilio_account_sid' => '',
            'twilio_auth_token' => '',
            'twilio_from_number' => '',
        ],
        'mail' => [
            'mail_mailer' => 'smtp',
            'mail_host' => '',
            'mail_port' => 587,
            'mail_encryption' => 'tls',
            'mail_username' => '',
            'mail_password' => '',
            'mail_from_address' => '',
            'mail_from_name' => 'Monitoring System',
        ],
        'aws' => [
            'aws_access_key_id' => '',
            'aws_secret_access_key' => '',
            'aws_default_region' => 'us-east-1',
            'aws_bucket' => '',
        ],
        'otp' => [
            'max_time' => 90, // in seconds
            'otp_length' => 6, // in digits
            'is_default' => true, // true=Fixed OTP or false=Dynamic OTP
            'default' => '999999',
        ],
    ],
];
