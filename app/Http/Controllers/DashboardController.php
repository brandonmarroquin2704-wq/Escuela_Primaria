<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use App\Models\TutorAlumno;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user()->usuario;

        if (! $usuario || ! $usuario->rol) {
            abort(403, 'Tu cuenta no está vinculada a un perfil escolar con rol activo.');
        }

        $hijos = collect();

        if ($usuario->rol->nombre === 'Tutor') {
            $hijos = TutorAlumno::with('alumno.grupo')
                ->where('usuario_idUsuario', $usuario->idUsuario)
                ->where('estado', true)
                ->whereHas('alumno', fn ($q) => $q->where('estado', true))
                ->get();
        }

        return view('dashboard', [
            'usuarioEscolar' => $usuario,
            'rol' => $usuario->rol->nombre,
            'escuela' => Escuela::first(),
            'puedeEditarEscuela' => $usuario->tienePermiso('Escuelas', 'Editar'),
            'hijos' => $hijos,
        ]);
    }
}