<?php

namespace Tonci14\LaravelEuroSMS\Fakes;

use Tonci14\LaravelEuroSMS\Services\EuroSmsService;

class EuroSmsFake extends EuroSmsService
{
    public array $sentMessages = [];

    public function __construct()
    {
        parent::__construct([
            'url'            => 'https://fake.eurosms.com',
            'integrationID'  => 'fake-id',
            'integrationKey' => 'fake-key',
            'senderName'     => 'FakeSender',
        ]);
    }

    public function send(string $phoneNumber, string $message, ?int $userId = null, ?string $senderName = null): void
    {
        $this->sentMessages[] = compact('phoneNumber', 'message', 'userId', 'senderName');
    }

    public function sendAsync(string $phoneNumber, string $message, ?int $userId = null, ?string $senderName = null, ?string $locale = null, ?string $queue = null): void
    {
        $this->sentMessages[] = compact('phoneNumber', 'message', 'userId', 'senderName', 'locale', 'queue');
    }

    public function assertSentTo(string $phoneNumber): bool
    {
        return collect($this->sentMessages)->contains(fn($m) => $m['phoneNumber'] === $phoneNumber);
    }

    public function assertNothingSent(): bool
    {
        return empty($this->sentMessages);
    }
}
