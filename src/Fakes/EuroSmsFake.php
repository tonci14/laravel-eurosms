<?php

namespace Tonci14\LaravelEuroSMS\Fakes;

use Tonci14\LaravelEuroSMS\Exceptions\InvalidArgumentException;
use Tonci14\LaravelEuroSMS\Services\EuroSmsService;

class EuroSmsFake extends EuroSmsService
{
    public array $sentMessages = [];

    /**
     *
     */
    public function __construct()
    {
        parent::__construct([
            'url'            => 'https://fake.eurosms.com',
            'integrationID'  => 'fake-id',
            'integrationKey' => 'fake-key',
            'senderName'     => 'FakeSender',
        ]);
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param int|null $userId
     * @param string|null $senderName
     * @return void
     */
    public function send(string $phoneNumber, string $message, ?int $userId = null, ?string $senderName = null): void
    {
        $this->sentMessages[] = compact('phoneNumber', 'message', 'userId', 'senderName');
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param int|null $userId
     * @param string|null $senderName
     * @param string|null $locale
     * @param string|null $queue
     * @return void
     */
    public function sendAsync(string $phoneNumber, string $message, ?int $userId = null, ?string $senderName = null, ?string $queue = null): void
    {
        $this->sentMessages[] = compact('phoneNumber', 'message', 'userId', 'senderName', 'queue');
    }

    /**
     * @param string $phone
     * @return string
     * @throws InvalidArgumentException
     */
    public static function validatePhoneNumber(string $phone): string
    {
        return EuroSmsService::validatePhoneNumber($phone);
    }

    /**
     * @param string $phoneNumber
     * @return bool
     */
    public function assertSentTo(string $phoneNumber): bool
    {
        return collect($this->sentMessages)->contains(fn($m) => $m['phoneNumber'] === $phoneNumber);
    }

    /**
     * @return bool
     */
    public function assertNothingSent(): bool
    {
        return empty($this->sentMessages);
    }
}
