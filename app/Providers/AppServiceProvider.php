<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
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
        Paginator::useBootstrapFive();
        // Ini yang bikin semua asset pakai https saat di ngrok
        if (env('APP_ENV') === 'local' || str_ends_with(request()->getHost(), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }
    }
}
