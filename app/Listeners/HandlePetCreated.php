<?php

namespace App\Listeners;

use App\Events\PetCreated;
use Illuminate\Support\Facades\Log;

class HandlePetCreated
{
    public function handle(PetCreated $event): void
    {
        Log::info('Pet created - listener.', ['pet' => $event->pet]);
    }
}
