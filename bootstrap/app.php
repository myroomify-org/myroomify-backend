<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        $middleware->group('api', [
            Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            //'auth' => Illuminate\Auth\Middleware\Authenticate::class,
            //'can' => \Illuminate\Auth\Middleware\Authorize::class,
            //'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            //'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'ability' => CheckForAnyAbility::class,
            'abilities' => CheckAbilities::class,
            "role" => \App\Http\Middleware\RoleMiddleware::class

            //"limiter" => ThrottleRequests::class.":5,1",
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
