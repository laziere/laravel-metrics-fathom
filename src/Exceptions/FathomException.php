<?php

namespace JeffersonGoncalves\MetricsFathom\Exceptions;

use Exception;

class FathomException extends Exception
{
    public static function fromResponse(int $statusCode, string $message): self
    {
        return new self("Fathom API error ({$statusCode}): {$message}", $statusCode);
    }
}
