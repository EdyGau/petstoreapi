<?php

namespace App\Events;

use App\DTO\PetDTO;
use Illuminate\Foundation\Events\Dispatchable;

class PetUpdated
{
    use Dispatchable;

    public PetDTO $pet;

    public function __construct(PetDTO $pet)
    {
        $this->pet = $pet;
    }
}
