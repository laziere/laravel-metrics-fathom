<?php

namespace JeffersonGoncalves\MetricsFathom\Exceptions;

class AuthenticationException extends FathomException
{
    public static function missingToken(): self
    {
        return new self('Fathom API token is not configured. Set FATHOM_API_TOKEN in your .env file.');
    }

    public static function invalidToken(): self
    {
        return new self('The provided Fathom API token is invalid.', 401);
    }
}
