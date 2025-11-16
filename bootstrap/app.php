<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Rulează comanda de trimitere mementouri zilnic la ora 18:00
        $schedule->command('appointments:send-reminders')->dailyAt('18:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
