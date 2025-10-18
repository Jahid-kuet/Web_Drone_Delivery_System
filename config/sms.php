<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configure SMS gateway for sending OTP and notifications
    | Supported gateways: sslwireless, bulk_sms_bd, alpha_net, mimsms, log
    |
    */

    'enabled' => env('SMS_ENABLED', false),

    'default_gateway' => env('SMS_GATEWAY', 'log'),

    /*
    |--------------------------------------------------------------------------
    | SMS Gateways Configuration
    |--------------------------------------------------------------------------
    */

    'gateways' => [

        /**
         * SSL Wireless (Most popular in Bangladesh)
         * Website: https://sslwireless.com/
         * API Docs: https://smsplus.sslwireless.com/developer-api
         */
        'sslwireless' => [
            'api_token' => env('SMS_SSLWIRELESS_API_TOKEN', ''),
            'sid' => env('SMS_SSLWIRELESS_SID', ''),
            'sender_id' => env('SMS_SSLWIRELESS_SENDER_ID', 'DroneDelivery'),
        ],

        /**
         * BulkSMS Bangladesh (GreenWeb)
         * Website: https://greenweb.com.bd/
         */
        'bulk_sms_bd' => [
            'api_token' => env('SMS_BULKSMS_API_TOKEN', ''),
        ],

        /**
         * Alpha Net SMS
         * Website: https://icombd.com/
         */
        'alpha_net' => [
            'api_key' => env('SMS_ALPHANET_API_KEY', ''),
            'sender_id' => env('SMS_ALPHANET_SENDER_ID', 'DroneDelivery'),
        ],

        /**
         * MIM SMS
         * Website: https://mimsms.com/
         */
        'mimsms' => [
            'api_key' => env('SMS_MIMSMS_API_KEY', ''),
            'sender_id' => env('SMS_MIMSMS_SENDER_ID', 'DroneDelivery'),
        ],

        /**
         * Log driver (for development/testing)
         * Messages are logged to storage/logs/laravel.log
         */
        'log' => [
            // No configuration needed
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Settings
    |--------------------------------------------------------------------------
    */

    'otp' => [
        'length' => 6,
        'expiry_minutes' => 10,
        'max_attempts' => 3,
        'cooldown_seconds' => 60, // Wait 60 seconds before resending
    ],

    'notifications' => [
        'delivery_status_updates' => env('SMS_NOTIFY_DELIVERY_STATUS', true),
        'otp_generation' => env('SMS_NOTIFY_OTP', true),
    ],

];
