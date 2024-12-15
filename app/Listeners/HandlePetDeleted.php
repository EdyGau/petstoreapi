<?php

namespace App\Listeners;

use App\Events\PetDeleted;
use Illuminate\Support\Facades\Log;

class HandlePetDeleted
{
    public function handle(PetDeleted $event): void
    {
        Log::info('Pet deleted - listener.', ['pet' => $event->pet]);
    }
}
