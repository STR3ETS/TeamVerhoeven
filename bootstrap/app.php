<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // jouw bestaande middleware-config…

        // ✅ Alias toevoegen
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserRole::class,
            'intake.complete' => \App\Http\Middleware\EnsureIntakeComplete::class,
        ]);

        // Stripe webhook heeft geen CSRF-token (verificatie gebeurt via de
        // Stripe-Signature header met STRIPE_WEBHOOK_SECRET)
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);

        // Eventueel kun je hier ook group-aanpassingen doen:
        // $middleware->appendToGroup('web', [ ... ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            //
        });
    })
    ->create();
