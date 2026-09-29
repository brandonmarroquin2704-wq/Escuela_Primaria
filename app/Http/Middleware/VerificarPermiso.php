<?php

namespace App\Http\Middleware;

use App\Models\RolPermiso;
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

        if (! $cuenta || ! $cuenta->usuario || ! $cuenta->usuario->rol) {
            abort(403, 'Tu cuenta no está vinculada a un perfil escolar con rol activo.');
        }

        $tienePermiso = RolPermiso::query()
            ->where('rol_idRol', $cuenta->usuario->rol->idRol)
            ->where('estado', true)
            ->whereHas('modulo', function ($query) use ($modulo) {
                $query->where('nombre', $modulo)
                    ->where('estado', true);
            })
            ->whereHas('permiso', function ($query) use ($permiso) {
                $query->where('nombre', $permiso)
                    ->where('estado', true);
            })
            ->exists();

        if (! $tienePermiso) {
            abort(
                403,
                "No tienes permiso para {$permiso} en el módulo {$modulo}."
            );
        }

        return $next($request);
    }
}