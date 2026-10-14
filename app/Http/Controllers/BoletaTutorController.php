<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AsignacionDocenteAsignatura;
use App\Models\Calificacion;
use App\Models\PeriodoEscolar;
use App\Models\TutorAlumno;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BoletaTutorController extends Controller
{
    private function tutor()
    {
        $perfil = Auth::user()->usuario;

        abort_unless(
            $perfil && $perfil->rol?->nombre === 'Tutor',
            403,
            'Esta sección es solo para padres o tutores.'
        );

        return $perfil;
    }

    public function show(int $alumno): View
    {
        $tutor = $this->tutor();

        TutorAlumno::where('usuario_idUsuario', $tutor->idUsuario)
            ->where('alumno_idAlumno', $alumno)
            ->where('estado', true)
            ->firstOrFail();

        $hijo = Alumno::with('grupo')->where('estado', true)->findOrFail($alumno);

        $periodos = PeriodoEscolar::where('estado', true)
            ->where('cicloEscolar', $hijo->grupo->cicloEsc)
            ->orderBy('fechaInicio')
            ->get();

        $asignaturas = AsignacionDocenteAsignatura::with('asignatura')
            ->where('grupo_idGrupo', $hijo->grupo_idGrupo)
            ->where('estado', true)
            ->get()
            ->pluck('asignatura')
            ->filter()
            ->unique('idAsignatura')
            ->sortBy('nombre');

        $publicadas = Calificacion::publicadas()
            ->where('alumno_idAlumno', $hijo->idAlumno)
            ->whereIn('periodo_escolar_idPeriodo', $periodos->pluck('idPeriodo'))
            ->get()
            ->groupBy('asignatura_idAsignatura');

        $filas = $asignaturas->map(function ($asignatura) use ($publicadas, $periodos) {
            $notas = $publicadas->get($asignatura->idAsignatura, collect())
                ->keyBy('periodo_escolar_idPeriodo');

            $valores = $notas->pluck('valor')->map(fn ($v) => (float) $v);

            return [
                'nombre' => $asignatura->nombre,
                'notas' => $notas,
                'promedio' => $valores->isNotEmpty() ? $valores->avg() : null,
                'parcial' => $valores->isNotEmpty() && $valores->count() < $periodos->count(),
            ];
        });

        $promedioGeneral = $filas->whereNotNull('promedio')->avg('promedio');

        return view('tutor.boleta', compact('hijo', 'periodos', 'filas', 'promedioGeneral'));
    }
}