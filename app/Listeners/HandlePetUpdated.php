<?php

namespace App\Listeners;

use App\Events\PetUpdated;
use Illuminate\Support\Facades\Log;

class HandlePetUpdated
{
    public function handle(PetUpdated $event): void
    {
        Log::info('Pet updated - listener.', ['pet' => $event->pet]);
    }
}
