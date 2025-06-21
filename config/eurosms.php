<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EuroSMS API prístup
    |--------------------------------------------------------------------------
    */

    'url'            => env('EURO_SMS_URL', 'https://as.eurosms.com/sms/Sender'),
    'integrationKey' => env('EUROSMS_KEY', null),
    'integrationID'  => env('EUROSMS_ID', null),
    'senderName'     => env('SMS_SENDER_NAME', null),

    /*
    |--------------------------------------------------------------------------
    | Povolené krajiny pre validáciu čísla (ISO alpha-2)
    |--------------------------------------------------------------------------
    | Napr. ['SK', 'CZ', 'AT']
    */

    'allowed_countries' => ['SK', 'CZ', 'AT'],

];
