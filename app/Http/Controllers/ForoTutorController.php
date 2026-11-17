<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Comunicado;
use App\Models\TutorAlumno;
use Illuminate\Http\Request;

class ForoTutorController extends Controller
{
    public function index(Request $request)
    {
        $tutorId = $request->user()->usuario->idUsuario;

        $alumnoIds = TutorAlumno::where('usuario_idUsuario', $tutorId)
            ->where('estado', true)
            ->pluck('alumno_idAlumno');

        $grupoIds = Alumno::whereIn('idAlumno', $alumnoIds)
            ->where('estado', true)
            ->pluck('grupo_idGrupo')
            ->filter()
            ->unique();

        $comunicados = Comunicado::with(['asignacion.grupo', 'asignacion.asignatura', 'docente'])
            ->whereHas('asignacion', fn ($q) => $q->whereIn('grupo_idGrupo', $grupoIds))
            ->latest()
            ->paginate(10);

        return view('tutor.foro.index', compact('comunicados'));
    }
}
