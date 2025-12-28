<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected $errors;

    protected $statusCode;

    public function __construct(string $message = 'Validation failed', array $errors = [], int $statusCode = 422)
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
