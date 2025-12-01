<?php

use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\SetLocale;
use CubeAgency\FilamentRedirects\Http\Middleware\FilamentRouteRedirectMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cookie\Middleware\EncryptCookies as LaravelEncryptCookies;
use App\Http\Middleware\HandleInertiaRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->web([
                HandleInertiaRequests::class,
            ])
            ->remove([
                LaravelEncryptCookies::class,
            ])
            ->append([
                EncryptCookies::class,
                SetLocale::class,
                FilamentRouteRedirectMiddleware::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
