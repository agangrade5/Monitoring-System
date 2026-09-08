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
     * OTP Configuration
     */
    'otp' => [
        'max_time' => 60, // time in minutes
        'otp_length' => 6, // in digits
        'is_default' => true, // true=Fixed OTP or false=Dynamic OTP
        'default' => 999999,
    ],
];
