<?php

namespace Tonci14\LaravelEuroSMS\Services;

use GuzzleHttp\Client;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Tonci14\LaravelEuroSMS\Jobs\SendEuroSmsJob;

class EuroSmsService
{
    private array $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function send(string $phoneNumber, string $message): void
    {
        $phone = $this->validatePhoneNumber($phoneNumber);

        $client = new Client();
        $client->post($this->config['url'], [
            'auth' => [$this->config['username'], $this->config['password']],
            'form_params' => [
                'to' => $phone,
                'text' => $message,
            ],
        ]);
    }

    public function sendAsync(
        string $phoneNumber,
        string $message,
        ?string $locale = null,
        string $queue = 'default',
        ?int $userId = null
    ): void {
        $phone = $this->validatePhoneNumber($phoneNumber);

        SendEuroSmsJob::dispatch($phone, $message, $locale, $userId)
            ->onQueue($queue);
    }

    private function validatePhoneNumber(string $phone): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $numberProto = $phoneUtil->parse($phone, null);
        } catch (NumberParseException $e) {
            throw new \InvalidArgumentException("Invalid phone number format: $phone", 0, $e);
        }

        if (!$phoneUtil->isValidNumber($numberProto)) {
            throw new \InvalidArgumentException("Phone number is not valid: $phone");
        }

        $region = $phoneUtil->getRegionCodeForNumber($numberProto);
        $allowed = $this->config['allowed_countries'] ?? [];

        if (!in_array(strtoupper($region), $allowed, true)) {
            throw new \InvalidArgumentException("Phone number region '{$region}' is not allowed.");
        }

        return $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
    }
}
