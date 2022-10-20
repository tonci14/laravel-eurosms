<?php
declare(strict_types=1);

namespace Tonci14\LaravelEuroSMS\Exceptions;

use RuntimeException;

final class SmsSentFailedException extends RuntimeException
{
    /**
     * SmsSentFailedException constructor.
     */
    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
