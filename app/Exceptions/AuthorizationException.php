<?php

namespace App\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    protected $statusCode;

    public function __construct(string $message = 'This action is unauthorized.', int $statusCode = 403)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
