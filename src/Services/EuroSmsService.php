<?php

namespace Tonci14\LaravelEuroSMS\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Tonci14\LaravelEuroSMS\Exceptions\InvalidArgumentException;
use Tonci14\LaravelEuroSMS\Jobs\SendEuroSmsJob;
use Tonci14\LaravelEuroSMS\Models\SmsMessage;

class EuroSmsService
{
    const MAX_SENDER_NAME_LENGTH = 11;

    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param int|null $userId
     * @param string|null $senderName
     * @return void
     * @throws GuzzleException
     */
    public function send(string $phoneNumber, string $message, ?int $userId = null, ?string $senderName = null): void
    {
        $phone = self::validatePhoneNumber($phoneNumber);
        $this->validateConfiguration();

        if ($phone !== "+421900000000") {
            $requestData = $this->buildRequest($phone, $message, $senderName);

            $client = new Client();
            $result = $client->get(
                $this->config['url'] . "?" . http_build_query($requestData['data']), $requestData['headers']
            );
            $sent = $result->getStatusCode() == 200;
            $error = !$sent ? $result->getBody()->getContents() : null;
        } else {
            $sent = true;
            $error = "Fake phone number";
        }

        if (!$sent) {
            SmsMessage::create([
                'user_id' => $userId,
                'phone'   => $phone,
                'message' => $message,
                'status'  => 'sent',
                'error'   => $error,
                'sent_at' => null,
            ]);
            throw new \Exception("Failed to send sms with error: \n" . $error);
        }

        SmsMessage::create([
            'user_id' => $userId,
            'phone'   => $phone,
            'message' => $message,
            'status'  => 'sent',
            'error'   => $error,
            'sent_at' => now(),
        ]);
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param int|null $userId
     * @param string|null $senderName
     * @param string|null $queue
     * @return void
     */
    public function sendAsync(
        string  $phoneNumber,
        string  $message,
        ?int    $userId = null,
        ?string $senderName = null,
        ?string $queue = null
    ): void
    {
        $phone = self::validatePhoneNumber($phoneNumber);
        $this->validateConfiguration();

        dispatch(new SendEuroSmsJob($phone, $message, $senderName, $userId))
            ->onQueue($queue ?? $this->config['queue']);
    }

    /**
     * @param string $phone
     * @return string
     * @throws InvalidArgumentException
     */
    public static function validatePhoneNumber(string $phone): string
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
        $allowed = config('eurosms.allowed_countries') ?? [];
        if (!empty($allowed) && !in_array(strtoupper($region), $allowed, true)) {
            throw new \InvalidArgumentException("Phone number region '{$region}' is not allowed.");
        }

        return $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    private function validateConfiguration(): void
    {
        foreach (['url', 'integrationID', 'integrationKey', 'senderName'] as $key) {
            if (empty($this->config[$key])) {
                throw new \InvalidArgumentException("Missing EuroSMS config value: {$key}");
            }
        }

        if (strlen($this->config['senderName']) > self::MAX_SENDER_NAME_LENGTH) {
            throw new InvalidArgumentException("MAX_SENDER_LENGTH_IS_" . self::MAX_SENDER_NAME_LENGTH, 400);
        }
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
            'i'      => $this->config['integrationID'],
            's'      => $sign,
            'sender' => $senderName ?? $this->config['senderName'],
            'number' => $targetNumber,
            'msg'    => $content,
        ];

        return [
            'headers' => $headers,
            'data'    => $data,
        ];
    }
}
