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

    /**
     * @param string $phoneNumber
     * @param string $message
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $phoneNumber, string $message): void
    {
        $phone = $this->validatePhoneNumber($phoneNumber);

        $client = new Client();
        $client->post($this->config['url'], self::buildRequest($phone, $message, $this->config['senderName']));
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param string|null $locale
     * @param string $queue
     * @param int|null $userId
     * @return void
     */
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

    /**
     * @param string $phone
     * @return string
     */
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

    /**
     * @param string $targetNumber
     * @param string $content
     * @param string|null $senderName
     * @return array
     * @phpstan-return array{
     *     headers: array{Accept: 'application/text'},
     *     data: array{
     *              action: 'send1SMSHTTP',
     *              i: string,
     *              s: string,
     *              sender: string|null,
     *              number: string,
     *              msg: string
     *      }
     *  }
     */
    private function buildRequest(string $targetNumber, string $content, ?string $senderName = null): array
    {
        $md5 = md5($this->config['integrationKey'] . $targetNumber);
        $sign = substr($md5, 10, 11);

        $headers = [
            'Accept' => 'application/text',
        ];

        $data = [
            'action' => 'send1SMSHTTP',
            'i' => $this->config['integrationID'],
            's' => $sign,
            'sender' => $senderName,
            'number' => $targetNumber,
            'msg' => $content
        ];

        return [
            'headers' => $headers,
            'data' => $data,
        ];
    }
}
