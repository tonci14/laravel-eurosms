<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EuroSMS API prístup
    |--------------------------------------------------------------------------
    */

    'username' => env('EURO_SMS_USERNAME'),
    'password' => env('EURO_SMS_PASSWORD'),
    'url' => env('EURO_SMS_URL', 'https://api.eurosms.com/api/v1/send'),

    /*
    |--------------------------------------------------------------------------
    | Povolené krajiny pre validáciu čísla (ISO alpha-2)
    |--------------------------------------------------------------------------
    | Napr. ['SK', 'CZ', 'AT']
    */

    'allowed_countries' => ['SK', 'CZ', 'AT'],

];
