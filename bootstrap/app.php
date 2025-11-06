<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\AutoMarkAbsentCommand;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'hr' => \App\Http\Middleware\HRMiddleware::class,
            'employee' => \App\Http\Middleware\EmployeeMiddleware::class,
        ]);
    })
    ->withCommands([
        AutoMarkAbsentCommand::class, // Register the command here
    ])
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Auto-mark absent employees daily at 6 PM
        $schedule->command('attendance:auto-mark-absent')
                 ->dailyAt('18:00')
                 ->timezone('Asia/Kolkata') // Adjust to your timezone
                 ->description('Automatically mark absent employees at the end of the day');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
