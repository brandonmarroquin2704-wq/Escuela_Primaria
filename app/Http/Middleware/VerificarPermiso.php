<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    public function handle(
        Request $request,
        Closure $next,
        string $modulo,
        string $permiso
    ): Response {
        $cuenta = $request->user();
        $perfil = $cuenta?->usuario;

        if (! $perfil || ! $perfil->rol) {
            abort(403, 'Tu cuenta no está vinculada a un perfil escolar con rol activo.');
        }

        if (! $perfil->estado) {
            abort(403, 'Tu cuenta está inactiva. Contacta al administrador.');
        }

        if (! $perfil->tienePermiso($modulo, $permiso)) {
            abort(403, "No tienes permiso para {$permiso} en el módulo {$modulo}.");
        }

        return $next($request);
    }
}