<?php

declare(strict_types=1);

namespace Tonci14\LaravelEuroSMS\Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Tonci14\LaravelEuroSMS\EuroSmsService;
use Tonci14\LaravelEuroSMS\Exceptions\InvalidArgumentException;

class TestInit extends TestCase
{
    private EuroSmsService $service;

    /**
     * @return void
     * @throws Exception
     */
    private function testInit(): void
    {
        $this->service = new EuroSmsService("TEST_KEY", "TEST_ID", "TEST_SENDER");
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSenderValidation(): void
    {
        $this->testInit();
        try {
            $this->service->sendAsync("", "", 0, "123456789123456789");
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("MAX_SENDER_LENGTH_IS_" . EuroSmsService::MAX_SENDER_NAME_LENGTH, $exception->getMessage());
        }
    }
}
