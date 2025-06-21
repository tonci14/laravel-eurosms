<?php

namespace Tonci14\LaravelEuroSMS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tonci14\LaravelEuroSMS\Facades\EuroSms;
use Tonci14\LaravelEuroSMS\Models\SmsMessage;
use Throwable;

class SendEuroSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $phoneNumber;
    protected string $content;
    protected string $locale;
    public ?int$userId;

    public function __construct(string $phoneNumber, string $content, ?string $locale = null, ?int $userId = null)
    {
        $this->phoneNumber = $phoneNumber;
        $this->content = $content;
        $this->locale = $locale;
        $this->userId = $userId;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $status = 'sent';
        $error = null;

        try {
            EuroSms::send($this->phoneNumber, $this->content);
        } catch (Throwable $e) {
            $status = 'failed';
            $error = $e->getMessage();
        }

        SmsMessage::create([
            'user_id' => $this->userId,
            'phone' => $this->phoneNumber,
            'message' => $this->content,
            'status' => $status,
            'error' => $error,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
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
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
