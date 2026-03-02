<?php

namespace JeffersonGoncalves\MetricsFathom\Exceptions;

class RateLimitException extends FathomException
{
    public static function exceeded(): self
    {
        return new self('Fathom API rate limit exceeded. Try again later.', 429);
    }
}
