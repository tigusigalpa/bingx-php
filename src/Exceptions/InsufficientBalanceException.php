<?php

namespace Tigusigalpa\BingX\Exceptions;

class InsufficientBalanceException extends ApiException
{
    public function __construct(string $message = "Insufficient balance", array $response = [])
    {
        parent::__construct($message, 'INSUFFICIENT_BALANCE', $response);
    }
}
