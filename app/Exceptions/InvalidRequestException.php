<?php

namespace App\Exceptions;

use Exception;

class InvalidRequestException extends Exception
{
    public function __construct(string $message = "Invalid request.")
    {
        parent::__construct($message);
    }
}
