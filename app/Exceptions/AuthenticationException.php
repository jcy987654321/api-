<?php

namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    protected $statusCode;

    public function __construct(string $message = 'Unauthenticated', int $statusCode = 401)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
