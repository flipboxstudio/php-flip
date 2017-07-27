<?php

namespace Core\Exceptions;

use Exception;

class TransformerException extends Exception
{
    public $data;

    public function __construct(string $message, int $code, $data)
    {
        parent::__construct($message, $code);

        $this->data = $data;
    }
}
