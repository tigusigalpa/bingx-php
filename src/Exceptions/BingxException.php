<?php

namespace Tigusigalpa\BingX\Exceptions;

use Exception;

class BingxException extends Exception
{
    protected array $response = [];
    
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, array $response = [])
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }
    
    public function getResponse(): array
    {
        return $this->response;
    }
}
