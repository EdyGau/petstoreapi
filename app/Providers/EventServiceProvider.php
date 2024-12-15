<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PetUpdated;
use App\Listeners\HandlePetUpdated;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PetUpdated::class => [
            HandlePetUpdated::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
