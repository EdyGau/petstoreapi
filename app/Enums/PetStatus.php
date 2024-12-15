<?php

namespace App\Enums;

enum PetStatus: string
{
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case SOLD = 'sold';

    /**
     * Status validation.
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, array_column(self::cases(), 'value'));
    }
}
