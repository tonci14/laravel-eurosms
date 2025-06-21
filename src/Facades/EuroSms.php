<?php

namespace Tonci14\LaravelEuroSMS\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for EuroSMS service.
 *
 * @method static void send(string $phoneNumber, string $message, int|null $userId = null, string|null $senderName = null)
 * @method static void sendAsync(string $phoneNumber, string $message, int|null $userId = null, string|null $senderName = null, string|null $locale = null, string $queue = 'default')
 *
 * @see \Tonci14\LaravelEuroSMS\Services\EuroSmsService
 */
class EuroSms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'eurosms';
    }
}
