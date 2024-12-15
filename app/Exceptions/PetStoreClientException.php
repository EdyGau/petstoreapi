<?php

namespace App\Exceptions;

use Exception;

class PetStoreClientException extends Exception
{
    public function __construct(string $message = "Error with external PetStore API.")
    {
        parent::__construct($message);
    }
}
