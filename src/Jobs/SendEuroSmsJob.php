<?php

namespace Tonci14\LaravelEuroSMS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tonci14\LaravelEuroSMS\Facades\EuroSms;

class SendEuroSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $phoneNumber;
    protected string $content;
    protected ?string $senderName;
    public ?int$userId;

    /**
     * @param string $phoneNumber
     * @param string $content
     * @param string|null $senderName
     * @param int|null $userId
     */
    public function __construct(string $phoneNumber, string $content, ?string $senderName = null, ?int $userId = null)
    {
        $this->phoneNumber = $phoneNumber;
        $this->content = $content;
        $this->senderName = $senderName;
        $this->userId = $userId;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        EuroSms::send($this->phoneNumber, $this->content, $this->userId, $this->senderName);
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
