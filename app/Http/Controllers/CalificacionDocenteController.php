<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AsignacionDocenteAsignatura;
use App\Models\Calificacion;
use App\Models\PeriodoEscolar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CalificacionDocenteController extends Controller
{
    private function docente()
    {
        $perfil = Auth::user()->usuario;

        abort_unless(
            $perfil && $perfil->rol?->nombre === 'Docente',
            403,
            'Esta sección es solo para docentes.'
        );

        return $perfil;
    }

    private function asignacionesDe($docente)
    {
        return AsignacionDocenteAsignatura::with(['grupo', 'asignatura'])
            ->where('docente_idUsuario', $docente->idUsuario)
            ->where('estado', true)
            ->whereHas('grupo', fn ($q) => $q->where('estado', true))
            ->whereHas('asignatura', fn ($q) => $q->where('estado', true));
    }

    private function cierreDe($asignacion, $periodo, $alumnosIds): ?Carbon
    {
        $valor = Calificacion::where('asignatura_idAsignatura', $asignacion->asignatura_idAsignatura)
            ->where('periodo_escolar_idPeriodo', $periodo->idPeriodo)
            ->where('estado', true)
            ->whereIn('alumno_idAlumno', $alumnosIds)
            ->min('fechaLimiteEdicion');

        return $valor ? Carbon::parse($valor) : null;
    }

    public function index(Request $request): View
    {
        $docente = $this->docente();

        $asignaciones = $this->asignacionesDe($docente)->get();
        $periodos = PeriodoEscolar::where('estado', true)->orderBy('fechaInicio')->get();

        $asignacionSel = $asignaciones->firstWhere('id', (int) $request->query('asignacion'));
        $periodoSel = $periodos->firstWhere('idPeriodo', (int) $request->query('periodo'));
                if ($periodoSel && $periodoSel->fechaInicio->isFuture()) {
            $periodoSel = null;
        }
        $alumnos = collect();
        $calificaciones = collect();
        $cierre = null;
        $cerrada = false;

        if ($asignacionSel && $periodoSel) {
            $alumnos = Alumno::where('estado', true)
                ->where('grupo_idGrupo', $asignacionSel->grupo_idGrupo)
                ->orderBy('apellidoPaterno')
                ->orderBy('apellidoMaterno')
                ->orderBy('nombre')
                ->get();

            $calificaciones = Calificacion::where('asignatura_idAsignatura', $asignacionSel->asignatura_idAsignatura)
                ->where('periodo_escolar_idPeriodo', $periodoSel->idPeriodo)
                ->whereIn('alumno_idAlumno', $alumnos->pluck('idAlumno'))
                ->get()
                ->keyBy('alumno_idAlumno');

            $cierre = $this->cierreDe($asignacionSel, $periodoSel, $alumnos->pluck('idAlumno'));
            $cerrada = $cierre && now()->greaterThanOrEqualTo($cierre);
        }

        return view('docente.calificaciones', compact(
            'asignaciones', 'periodos', 'asignacionSel', 'periodoSel',
            'alumnos', 'calificaciones', 'cierre', 'cerrada'
        ));
    }

    public function guardar(Request $request): RedirectResponse
    {
        $docente = $this->docente();

        $datos = $request->validate([
            'asignacion' => ['required', 'integer'],
            'periodo' => ['required', 'integer', 'exists:periodos_escolares,idPeriodo'],
            'calificaciones' => ['nullable', 'array'],
            'calificaciones.*' => ['nullable', 'numeric', 'between:0,10', 'decimal:0,1'],
        ], [
            'calificaciones.*.between' => 'Cada calificación debe estar entre 0 y 10.',
            'calificaciones.*.numeric' => 'Cada calificación debe ser un número.',
            'calificaciones.*.decimal' => 'Usa máximo un decimal (por ejemplo 8.5).',
        ]);

        $asignacion = $this->asignacionesDe($docente)->findOrFail($datos['asignacion']);
        $periodo = PeriodoEscolar::where('estado', true)->findOrFail($datos['periodo']);

        if ($periodo->fechaInicio->isFuture()) {
            return back()->withErrors([
                'calificaciones' => $periodo->nombre . ' inicia el ' . $periodo->fechaInicio->format('d/m/Y') . '. Aún no se pueden capturar calificaciones.',
            ]);
        }
        $alumnosGrupo = Alumno::where('estado', true)
            ->where('grupo_idGrupo', $asignacion->grupo_idGrupo)
            ->pluck('idAlumno')
            ->all();

        $cierre = $this->cierreDe($asignacion, $periodo, $alumnosGrupo);

        if ($cierre && now()->greaterThanOrEqualTo($cierre)) {
            return back()->withErrors([
                'calificaciones' => 'La captura de este trimestre cerró el ' . $cierre->format('d/m/Y H:i') . '.',
            ]);
        }

        $limite = $cierre ?? now()->addDays(7);
        $guardadas = 0;

        DB::transaction(function () use ($datos, $asignacion, $periodo, $docente, $alumnosGrupo, $limite, &$guardadas) {
            foreach (($datos['calificaciones'] ?? []) as $idAlumno => $valor) {
                if ($valor === null || $valor === '' || ! in_array((int) $idAlumno, $alumnosGrupo, true)) {
                    continue;
                }

                $existente = Calificacion::where('alumno_idAlumno', $idAlumno)
                    ->where('asignatura_idAsignatura', $asignacion->asignatura_idAsignatura)
                    ->where('periodo_escolar_idPeriodo', $periodo->idPeriodo)
                    ->first();

                if ($existente) {
                    $existente->update(['valor' => $valor]);
                } else {
                    Calificacion::create([
                        'valor' => $valor,
                        'fechaRegistro' => now(),
                        'fechaLimiteEdicion' => $limite,
                        'estado' => true,
                        'alumno_idAlumno' => $idAlumno,
                        'periodo_escolar_idPeriodo' => $periodo->idPeriodo,
                        'asignatura_idAsignatura' => $asignacion->asignatura_idAsignatura,
                        'usuario_idUsuario' => $docente->idUsuario,
                    ]);
                }

                $guardadas++;
            }
        });

        if ($guardadas === 0) {
            return back()->withErrors([
                'calificaciones' => 'Escribe al menos una calificación para guardar.',
            ]);
        }

        return redirect()
            ->route('docente.calificaciones.index', [
                'asignacion' => $asignacion->id,
                'periodo' => $periodo->idPeriodo,
            ])
            ->with('status', "Calificaciones guardadas: {$guardadas}.");
    }
}