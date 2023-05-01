<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::fake([
            env('AUTHORIZATION_API_URL') =>
                Http::response(['message' => 'Autorizado']),
            env('NOTIFY_API_URL') =>
                Http::response(['message' => 'Success'])
        ]);
    }
}
