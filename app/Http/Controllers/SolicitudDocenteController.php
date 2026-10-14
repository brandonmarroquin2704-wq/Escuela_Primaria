<?php

namespace App\Http\Controllers;

use App\Models\SolicitudAccesoSalon;
use App\Models\TutorAlumno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudDocenteController extends Controller
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

    private function deMiGrupo($docente)
    {
        return SolicitudAccesoSalon::query()
            ->whereHas('alumno.grupo', fn ($q) => $q->where('idMaestro', $docente->idUsuario));
    }

    public function index(): View
    {
        $docente = $this->docente();

        $pendientes = $this->deMiGrupo($docente)
            ->with(['alumno.grupo', 'tutor.datosTutor'])
            ->where('estadoSolicitud', 'pendiente')
            ->oldest('fechaSolicitud')
            ->get();

        $resueltas = $this->deMiGrupo($docente)
            ->with(['alumno.grupo', 'tutor'])
            ->where('estadoSolicitud', '!=', 'pendiente')
            ->latest('fechaRespuesta')
            ->limit(20)
            ->get();

        return view('docente.solicitudes', compact('pendientes', 'resueltas'));
    }

    public function aprobar(int $solicitud): RedirectResponse
    {
        $docente = $this->docente();

        DB::transaction(function () use ($docente, $solicitud) {
            $s = $this->deMiGrupo($docente)
                ->lockForUpdate()
                ->findOrFail($solicitud);

            abort_unless($s->estadoSolicitud === 'pendiente', 409, 'Esta solicitud ya fue respondida.');

            TutorAlumno::updateOrCreate(
                [
                    'usuario_idUsuario' => $s->tutor_idUsuario,
                    'alumno_idAlumno' => $s->alumno_idAlumno,
                ],
                [
                    'parentesco' => $s->parentesco,
                    'esResponsable' => $s->esResponsable,
                    'estado' => true,
                ]
            );

            $s->update([
                'estadoSolicitud' => 'aprobada',
                'docente_validador_idUsuario' => $docente->idUsuario,
                'fechaRespuesta' => now(),
            ]);
        });

        return back()->with('status', 'Solicitud aprobada. El tutor ya puede ver a su hijo(a).');
    }

    public function rechazar(Request $request, int $solicitud): RedirectResponse
    {
        $docente = $this->docente();

        $datos = $request->validate([
            'observacionRespuesta' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($docente, $solicitud, $datos) {
            $s = $this->deMiGrupo($docente)
                ->lockForUpdate()
                ->findOrFail($solicitud);

            abort_unless($s->estadoSolicitud === 'pendiente', 409, 'Esta solicitud ya fue respondida.');

            $s->update([
                'estadoSolicitud' => 'rechazada',
                'docente_validador_idUsuario' => $docente->idUsuario,
                'fechaRespuesta' => now(),
                'observacionRespuesta' => $datos['observacionRespuesta'] ?? null,
            ]);
        });

        return back()->with('status', 'Solicitud rechazada.');
    }
}