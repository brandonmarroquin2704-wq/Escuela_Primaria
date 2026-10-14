<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CuentaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $perfil = $request->user()?->usuario;

        if (! $perfil || ! $perfil->estado) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('acceso.inicio')
                ->with('error', 'Tu cuenta está inactiva o sin perfil. Contacta al administrador.');
        }

        return $next($request);
    }
}