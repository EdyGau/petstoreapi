<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PetStatus;

class Pet extends Model
{
    use HasFactory;

    protected $table = 'pets';

    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * Get valid statuses from the enum.
     */
    public static function validStatuses(): array
    {
        return array_map(fn($status) => $status->value, PetStatus::cases());
    }

    /**
     * @param $value
     * @return void
     */
    public function setStatusAttribute($value): void
    {
        if (!PetStatus::isValid($value)) {
            throw new \InvalidArgumentException("Invalid status: $value");
        }

        $this->attributes['status'] = $value;
    }

    /**
     * @param $value
     * @return PetStatus
     */
    public function getStatusAttribute($value): PetStatus
    {
        return PetStatus::from($value);
    }
}

