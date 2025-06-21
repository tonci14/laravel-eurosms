<?php

namespace Tonci14\LaravelEuroSMS\Facades;

use Illuminate\Support\Facades\Facade;
use Tonci14\LaravelEuroSMS\Fakes\EuroSmsFake;

/**
 * Facade for EuroSMS service.
 *
 * @method static void send(string $phoneNumber, string $message, int|null $userId = null, string|null $senderName = null)
 * @method static void sendAsync(string $phoneNumber, string $message, int|null $userId = null, string|null $senderName = null, string|null $queue = null)
 *
 * @see \Tonci14\LaravelEuroSMS\Services\EuroSmsService
 */
class EuroSms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'eurosms';
    }

    public static function fake(): EuroSmsFake
    {
        $fake = new EuroSmsFake();
        static::swap($fake);

        return $fake;
    }

}
