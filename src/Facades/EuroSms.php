<?php

namespace Tonci14\LaravelEuroSMS\Facades;

use Illuminate\Support\Facades\Facade;

class EuroSms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'eurosms';
    }
}
