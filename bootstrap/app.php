<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);

    $middleware->redirectGuestsTo(fn () => url('/auth'));
})

->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin'    => \App\Http\Middleware\EnsureUserIsAdmin::class,
        'customer' => \App\Http\Middleware\EnsureUserIsCustomer::class,
    ]);

    $middleware->redirectGuestsTo(fn () => url('/auth'));
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

    