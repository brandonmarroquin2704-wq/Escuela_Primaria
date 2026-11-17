<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiereRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $rol = $request->user()?->usuario?->rol?->nombre;

        abort_unless(
            $rol && in_array($rol, $roles, true),
            403,
            'No tienes acceso a esta sección.'
        );

        return $next($request);
    }
}