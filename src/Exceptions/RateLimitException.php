<?php

namespace Tigusigalpa\BingX\Exceptions;

class RateLimitException extends ApiException
{
    public function __construct(string $message = "Rate limit exceeded", array $response = [])
    {
        parent::__construct($message, 'RATE_LIMIT', $response);
    }
}
