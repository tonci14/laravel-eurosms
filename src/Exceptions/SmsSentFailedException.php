<?php
declare(strict_types=1);

namespace App\Exceptions;

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
