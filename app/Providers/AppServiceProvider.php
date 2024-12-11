<?php

namespace App\Providers;

use App\Clients\PetStoreClient;
use App\Services\PetService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(PetStoreClient::class, function ($app) {
            return new PetStoreClient();
        });

        $this->app->singleton(PetService::class, function ($app) {
            return new PetService($app->make(PetStoreClient::class));
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
