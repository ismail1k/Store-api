<?php

return [
    //Sandbox
    'sandbox' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID'),
        'secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET'),
    ],
    //Live
    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID'),
        'secret' => env('PAYPAL_LIVE_CLIENT_SECRET'),
    ],

    //Settings
    'settings' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'http' => [
            'ConnectionTimeOut' => 5000,
        ],
    ],
];