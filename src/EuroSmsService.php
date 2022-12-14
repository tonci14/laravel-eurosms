<?php

namespace Tonci14\LaravelEuroSMS;

use Tonci14\LaravelEuroSMS\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Tonci14\LaravelEuroSMS\Jobs\SendEuroSmsJob;

class EuroSmsService
{
    const API_URL = "https://as.eurosms.com/sms/Sender";
    const MAX_SENDER_NAME_LENGTH = 11;

    private string $integrationKey;
    private string $integrationID;
    private string $defaultSenderName;

    /**
     * @param string $integrationKey
     * @param string $integrationID
     * @param string $defaultSenderName
     */
    public function __construct(
        string $integrationKey,
        string $integrationID,
        string $defaultSenderName
    )
    {
        $this->integrationKey = $integrationKey;
        $this->integrationID = $integrationID;
        $this->defaultSenderName = $defaultSenderName;
    }

    /**
     * @param string $targetNumber
     * @param string $message
     * @param string|null $senderName
     * @return bool
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function send(string $targetNumber, string $message, ?string $senderName = null): bool
    {
        $senderName = strtolower($senderName ?? $this->defaultSenderName);
        self::validateSender($senderName);

        $requestData = $this->buildRequest($targetNumber, $message, $senderName);

        $client = new Client(['verify' => false]);
        $result = $client->get(
            self::API_URL . "?" . http_build_query($requestData['data']), $requestData['headers']
        );
        return $result->getStatusCode() == 200;
    }

    /**
     * @param string $targetNumber
     * @param string $message
     * @param string|null $senderName
     * @param string|null $queue
     * @return bool
     * @throws InvalidArgumentException
     */
    public function sendAsync(string $targetNumber, string $message, ?string $senderName = null, ?string $queue = null): bool
    {
        $senderName = strtolower($senderName ?? $this->defaultSenderName);
        self::validateSender($senderName);

        dispatch(new SendEuroSmsJob($targetNumber, $message, $senderName, $queue));
        return true;
    }

    /**
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    private static function validateSender(string $value): void
    {
        if(strlen($value) > self::MAX_SENDER_NAME_LENGTH){
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
        $md5 = md5($this->integrationKey . $targetNumber);
        $sign = substr($md5, 10, 11);

        $headers = [
            'Accept' => 'application/text',
        ];

        $data = [
            'action' => 'send1SMSHTTP',
            'i' => $this->integrationID,
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
