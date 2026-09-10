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

        /**
         * Security configuration
         */
            
        'securityHeaders' => [
                'strict-transport-security' => [
                    'name' => 'Strict-Transport-Security (HSTS)',
                    'description' => 'Forces secure HTTPS connections and prevents SSL stripping attacks.',
                    'present' => false, 
                    'value' => null,
                ],
                'content-security-policy' => [
                    'name' => 'Content-Security-Policy (CSP)',
                    'description' => 'Mitigates Cross-Site Scripting (XSS) and malicious data injection.',
                    'present' => false,
                    'value' => null,
                ],
                'x-frame-options' => [
                    'name' => 'X-Frame-Options',
                    'description' => 'Prevents Clickjacking by controlling iframe embedding.',
                    'present' => false,
                    'value' => null,
                ],
                'x-content-type-options' => [
                    'name' => 'X-Content-Type-Options',
                    'description' => 'Blocks MIME-type sniffing to prevent malicious script execution.',
                    'present' => false,
                    'value' => null,
                ],
                'referrer-policy' => [
                    'name' => 'Referrer-Policy',
                    'description' => 'Controls referrer information sent in HTTP requests.',
                    'present' => false,
                    'value' => null,
                ],
                'permissions-policy' => [
                    'name' => 'Permissions-Policy',
                    'description' => 'Restricts browser permissions (Camera, Geolocation, Microphone).',
                    'present' => false,
                    'value' => null,
                ],
        ],

      
];
