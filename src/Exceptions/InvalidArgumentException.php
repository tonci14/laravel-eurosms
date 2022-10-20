<?php
declare(strict_types=1);

namespace Tonci14\LaravelEuroSMS\Exceptions;

use RuntimeException;

final class InvalidArgumentException extends RuntimeException
{
    /**
     * RecordNotFoundException constructor.
     */
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
