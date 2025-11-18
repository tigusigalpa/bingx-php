<?php

namespace Tigusigalpa\BingX\Exceptions;

class AuthenticationException extends ApiException
{
    public function __construct(string $message = "Authentication failed", array $response = [])
    {
        parent::__construct($message, 'AUTH_ERROR', $response);
    }
}
