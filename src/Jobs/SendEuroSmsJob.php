<?php

namespace Tonci14\LaravelEuroSMS;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEuroSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $target;
    private string $content;
    private ?string $sender;

    /**
     * @param string $target
     * @param string $content
     * @param string|null $sender
     * @param string|null $queue
     */
    public function __construct(string $target, string $content, ?string $sender = null, ?string $queue = null)
    {
        $this->onQueue($queue ?? "default");
        $this->target = $target;
        $this->content = $content;
        $this->sender = $sender;
    }

    /**
     * Execute the job.
     *
     * @param EuroSmsService $smsService
     * @return bool
     * @throws GuzzleException
     */
    public function handle(EuroSmsService $smsService): bool
    {
        return $smsService->send($this->target, $this->content, $this->sender);
    }
}
