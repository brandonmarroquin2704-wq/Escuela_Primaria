<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\SolicitudAccesoSalon;
use App\Models\TutorAlumno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class SolicitudTutorController extends Controller
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

    public function index(Request $request): View
    {
        $perfil = $this->tutor();

        $grupos = Grupo::where('estado', true)
            ->orderBy('grado')
            ->orderBy('letraGrupo')
            ->get();

        $grupoSel = $grupos->firstWhere('idGrupo', (int) $request->query('grupo'));

        $alumnos = collect();

        if ($grupoSel) {
            $yaSolicitados = SolicitudAccesoSalon::where('tutor_idUsuario', $perfil->idUsuario)
                ->pluck('alumno_idAlumno');

            $yaVinculados = TutorAlumno::where('usuario_idUsuario', $perfil->idUsuario)
                ->where('estado', true)
                ->pluck('alumno_idAlumno');

            $alumnos = Alumno::where('estado', true)
                ->where('grupo_idGrupo', $grupoSel->idGrupo)
                ->whereNotIn('idAlumno', $yaSolicitados->merge($yaVinculados)->all())
                ->orderBy('nombre')
                ->orderBy('apellidoPaterno')
                ->get();
        }

        $solicitudes = SolicitudAccesoSalon::with('alumno.grupo')
            ->where('tutor_idUsuario', $perfil->idUsuario)
            ->latest('fechaSolicitud')
            ->get();

        return view('tutor.solicitudes', compact('grupos', 'grupoSel', 'alumnos', 'solicitudes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $perfil = $this->tutor();

        $datos = $request->validate([
            'alumno_idAlumno' => ['required', 'exists:alumnos,idAlumno'],
            'fNac' => ['required', 'date', 'before:today'],
            'parentesco' => ['required', 'in:Padre,Madre,Tutor legal,Abuelo(a),Otro'],
            'esResponsable' => ['nullable', 'boolean'],
        ], [
            'alumno_idAlumno.required' => 'Selecciona a tu hijo(a) de la lista.',
            'fNac.required' => 'Escribe la fecha de nacimiento de tu hijo(a).',
        ]);

        $clave = 'buscar-hijo:' . $perfil->idUsuario;

        if (RateLimiter::tooManyAttempts($clave, 5)) {
            $seg = RateLimiter::availableIn($clave);

            return back()->withInput()->withErrors([
                'busqueda' => "Demasiados intentos. Intenta de nuevo en {$seg} segundos.",
            ]);
        }

        $alumno = Alumno::where('estado', true)
            ->where('idAlumno', $datos['alumno_idAlumno'])
            ->whereDate('fNac', $datos['fNac'])
            ->first();

        if (! $alumno) {
            RateLimiter::hit($clave, 600);

            return back()->withInput()->withErrors([
                'busqueda' => 'La fecha de nacimiento no coincide con la registrada en la escuela.',
            ]);
        }

        $yaVinculado = TutorAlumno::where('usuario_idUsuario', $perfil->idUsuario)
            ->where('alumno_idAlumno', $alumno->idAlumno)
            ->where('estado', true)
            ->exists();

        if ($yaVinculado) {
            return back()->withErrors(['busqueda' => 'Ya tienes acceso a este alumno.']);
        }

        $previa = SolicitudAccesoSalon::where('tutor_idUsuario', $perfil->idUsuario)
            ->where('alumno_idAlumno', $alumno->idAlumno)
            ->first();

        if ($previa) {
            return back()->withErrors([
                'busqueda' => 'Ya existe una solicitud para este alumno (estado: ' . $previa->estadoSolicitud . ').',
            ]);
        }

        SolicitudAccesoSalon::create([
            'tutor_idUsuario' => $perfil->idUsuario,
            'alumno_idAlumno' => $alumno->idAlumno,
            'parentesco' => $datos['parentesco'],
            'esResponsable' => $request->boolean('esResponsable'),
            'estadoSolicitud' => 'pendiente',
            'fechaSolicitud' => now(),
            'estado' => true,
        ]);

        return redirect()
            ->route('tutor.solicitudes.index')
            ->with('status', 'Solicitud enviada. El docente del grupo la revisará.');
    }
}