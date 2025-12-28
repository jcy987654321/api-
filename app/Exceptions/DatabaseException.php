<?php

namespace App\Exceptions;

use Exception;

class DatabaseException extends Exception
{
    protected $statusCode;

    public function __construct(string $message = "Database error occurred", int $statusCode = 500)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
