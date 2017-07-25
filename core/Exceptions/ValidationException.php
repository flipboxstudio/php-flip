<?php

namespace Core\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public $errors;

    public function __construct(string $message, int $code, array $errors)
    {
        parent::__construct($message, $code);

        $this->errors = $errors;
    }
}
