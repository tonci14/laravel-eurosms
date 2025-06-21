<?php

namespace Tonci14\LaravelEuroSMS\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tonci14\LaravelEuroSMS\Facades\EuroSms;
use Tonci14\LaravelEuroSMS\Jobs\SendEuroSmsJob;
use Tonci14\LaravelEuroSMS\Services\EuroSmsService;

class EuroSmsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_sms_synchronously_and_logs_to_database()
    {
        EuroSms::send('+421900123456', 'Test SMS synchronne');

        $this->assertDatabaseHas('euro_sms_queue', [
            'phone'   => '+421900123456',
            'message' => 'Test SMS synchronne',
            'status'  => 'sent',
        ]);
    }

    /** @test */
    public function it_dispatches_async_sms_to_queue()
    {
        Queue::fake();

        EuroSms::sendAsync('+421900123456', 'Async test', 7, null, "sk", 'messaging');

        Queue::assertPushedOn('messaging', SendEuroSmsJob::class, function ($job) {
            return $job instanceof SendEuroSmsJob
                && $job->getUserId() === 7
                && $job->getPhoneNumber() === '+421900123456';
        });
    }

    /** @test */
    public function it_validates_invalid_number_and_throws()
    {
        $this->expectException(\InvalidArgumentException::class);

        app(EuroSmsService::class)->send('123ABC', 'test');
    }

    /** @test */
    public function it_rejects_numbers_from_disallowed_country()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/region.*not allowed/i');

        app(EuroSmsService::class)->send('+441234567890', 'UK test');
    }

    /** @test */
    public function it_logs_failed_sms_when_exception_thrown()
    {
        $job = new SendEuroSmsJob('invalid', 'Failed test', null, null, 3);

        try {
            $job->handle();
        } catch (\Throwable $throwable) {
        }

        $this->assertDatabaseHas('euro_sms_queue', [
            'phone'   => 'invalid',
            'status'  => 'failed',
            'user_id' => 3,
        ]);
    }
}
