<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(function () {
            $regla = Password::min(10)->letters()->mixedCase()->numbers();

            // La consulta de filtraciones requiere internet; solo en producción
            return app()->isProduction() ? $regla->uncompromised() : $regla;
        });
    }
}