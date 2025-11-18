<?php

namespace Tigusigalpa\BingX\Exceptions;

class ApiException extends BingxException
{
    protected string $errorCode;
    
    public function __construct(string $message, string $errorCode, array $response = [])
    {
        parent::__construct($message, 0, null, $response);
        $this->errorCode = $errorCode;
    }
    
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
