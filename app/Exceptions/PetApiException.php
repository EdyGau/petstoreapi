<?php

namespace App\Exceptions;

use Exception;

class PetApiException extends Exception
{
    /**
     *
     * @param array $response
     * @return self
     */
    public static function forFailedResponse(array $response): self
    {
        $statusCode = $response['code'] ?? 'Unknown Status Code';
        $message = $response['message'] ?? 'Unknown API Error';

        return new self("API Error [{$statusCode}]: {$message}");
    }

    /**
     *
     * @param string $message
     * @param int $code
     * @return self
     */
    public static function forGenericError(string $message = 'An unknown error occurred.', int $code = 0): self
    {
        return new self("API Error: {$message}", $code);
    }
}
