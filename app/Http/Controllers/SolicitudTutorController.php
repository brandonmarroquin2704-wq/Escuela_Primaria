<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\SolicitudAccesoSalon;
use App\Models\TutorAlumno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SolicitudTutorController extends Controller
{
    private const MAX_PENDIENTES = 5;

    private function tutor()
    {
        return Auth::user()->usuario;
    }

    private function normalizar(string $texto): string
    {
        return trim(preg_replace('/\s+/', ' ', Str::lower(Str::ascii($texto))));
    }

    private function reglasNombre(): array
    {
        return [
            'required',
            'string',
            'max:120',
            'regex:/^[\pL\s.\'-]+$/u',
            function ($atributo, $valor, $fallar) {
                if (count(preg_split('/\s+/', trim((string) $valor))) < 2) {
                    $fallar('Escribe el nombre completo, con nombre y apellidos.');
                }
            },
        ];
    }

    private function mensajesNombre(): array
    {
        return [
            'nombre.required' => 'Escribe el nombre completo de tu hijo(a).',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre.max' => 'El nombre es demasiado largo.',
        ];
    }

    private function buscarAlumno(int $grupoId, string $nombre): ?Alumno
    {
        $buscado = $this->normalizar($nombre);

        $coincidencias = Alumno::where('estado', true)
            ->where('grupo_idGrupo', $grupoId)
            ->get()
            ->filter(fn (Alumno $a) => $this->normalizar(
                $a->nombre . ' ' . $a->apellidoPaterno . ' ' . $a->apellidoMaterno
            ) === $buscado);

        return $coincidencias->count() === 1 ? $coincidencias->first() : null;
    }

    private function mensajeSolicitudPrevia(SolicitudAccesoSalon $previa): string
    {
        if ($previa->estadoSolicitud === 'rechazada') {
            return 'Tu solicitud anterior para este alumno fue rechazada. '
                . 'Puedes volver a enviarla desde «Mis solicitudes».';
        }

        return 'Ya existe una solicitud para este alumno (estado: ' . $previa->estadoSolicitud . ').';
    }

    private function pendientesDe(int $tutorId): int
    {
        return SolicitudAccesoSalon::where('tutor_idUsuario', $tutorId)
            ->where('estadoSolicitud', 'pendiente')
            ->count();
    }

    public function index(Request $request): View
    {
        $perfil = $this->tutor();

        $grupos = Grupo::where('estado', true)
            ->orderBy('grado')
            ->orderBy('letraGrupo')
            ->get();

        $grupoSel = $grupos->firstWhere('idGrupo', (int) $request->query('grupo'));

        $alumno = null;
        $nombreBuscado = null;
        $aviso = null;

        if ($grupoSel && $request->filled('nombre')) {
            $request->validate(['nombre' => $this->reglasNombre()], $this->mensajesNombre());

            $nombreBuscado = trim((string) $request->query('nombre'));
            $clave = 'buscar-nombre:' . $perfil->idUsuario;

            if (RateLimiter::tooManyAttempts($clave, 10)) {
                $aviso = 'Demasiadas búsquedas. Intenta de nuevo en '
                    . RateLimiter::availableIn($clave) . ' segundos.';
            } else {
                $encontrado = $this->buscarAlumno($grupoSel->idGrupo, $nombreBuscado);

                if (! $encontrado) {
                    RateLimiter::hit($clave, 600);
                    Log::warning('tutor.busqueda_fallida', [
                        'usuario' => $perfil->idUsuario,
                        'grupo' => $grupoSel->idGrupo,
                        'ip' => $request->ip(),
                    ]);
                    $aviso = 'No encontramos un alumno con ese nombre en este grupo. '
                        . 'Verifica que escribiste el nombre y los apellidos completos.';
                } elseif (TutorAlumno::where('usuario_idUsuario', $perfil->idUsuario)
                    ->where('alumno_idAlumno', $encontrado->idAlumno)
                    ->where('estado', true)
                    ->exists()) {
                    $aviso = 'Ya tienes acceso a este alumno.';
                } elseif ($previa = SolicitudAccesoSalon::where('tutor_idUsuario', $perfil->idUsuario)
                    ->where('alumno_idAlumno', $encontrado->idAlumno)
                    ->first()) {
                    $aviso = $this->mensajeSolicitudPrevia($previa);
                } else {
                    $alumno = $encontrado;
                }
            }
        }

        $solicitudes = SolicitudAccesoSalon::with('alumno.grupo')
            ->where('tutor_idUsuario', $perfil->idUsuario)
            ->latest('fechaSolicitud')
            ->get();

        return view('tutor.solicitudes', compact(
            'grupos', 'grupoSel', 'alumno', 'nombreBuscado', 'aviso', 'solicitudes'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $perfil = $this->tutor();

        $datos = $request->validate([
            'grupo' => ['required', 'integer', Rule::exists('grupos', 'idGrupo')->where('estado', true)],
            'nombre' => $this->reglasNombre(),
            'alumno_idAlumno' => ['required', 'integer'],
            'parentesco' => ['required', 'in:Padre,Madre,Tutor legal,Abuelo(a),Otro'],
            'esResponsable' => ['nullable', 'boolean'],
        ], $this->mensajesNombre() + [
            'alumno_idAlumno.required' => 'Busca y selecciona a tu hijo(a) primero.',
        ]);

        $clave = 'buscar-hijo:' . $perfil->idUsuario;

        if (RateLimiter::tooManyAttempts($clave, 5)) {
            $seg = RateLimiter::availableIn($clave);

            return back()->withInput()->withErrors([
                'busqueda' => "Demasiados intentos. Intenta de nuevo en {$seg} segundos.",
            ]);
        }

        $alumno = $this->buscarAlumno((int) $datos['grupo'], $datos['nombre']);

        if (! $alumno || $alumno->idAlumno !== (int) $datos['alumno_idAlumno']) {
            RateLimiter::hit($clave, 600);

            return back()->withInput()->withErrors([
                'busqueda' => 'No pudimos validar los datos del alumno. Vuelve a buscarlo.',
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
            return back()->withErrors(['busqueda' => $this->mensajeSolicitudPrevia($previa)]);
        }

        if ($this->pendientesDe($perfil->idUsuario) >= self::MAX_PENDIENTES) {
            return back()->withErrors([
                'busqueda' => 'Ya tienes ' . self::MAX_PENDIENTES . ' solicitudes pendientes. Espera a que el docente las revise.',
            ]);
        }

        SolicitudAccesoSalon::create([
            'tutor_idUsuario' => $perfil->idUsuario,
            'alumno_idAlumno' => $alumno->idAlumno,
            'parentesco' => $datos['parentesco'],
            'esResponsable' => $request->boolean('esResponsable'),
            'estadoSolicitud' => 'pendiente',
            'intentos' => 1,
            'fechaSolicitud' => now(),
            'estado' => true,
        ]);

        Log::info('tutor.solicitud_creada', [
            'usuario' => $perfil->idUsuario,
            'alumno' => $alumno->idAlumno,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('tutor.solicitudes.index')
            ->with('status', 'Solicitud enviada. El docente del grupo la revisará.');
    }

    // Vuelve a enviar una solicitud rechazada (máximo MAX_INTENTOS por alumno).
    public function reenviar(Request $request, int $solicitud): RedirectResponse
    {
        $perfil = $this->tutor();

        // Solo se encuentran solicitudes propias; las ajenas dan 404.
        $s = SolicitudAccesoSalon::with('alumno')
            ->where('tutor_idUsuario', $perfil->idUsuario)
            ->findOrFail($solicitud);

        if ($s->estadoSolicitud !== 'rechazada') {
            return back()->withErrors(['busqueda' => 'Solo puedes volver a enviar solicitudes rechazadas.']);
        }

        if ($s->intentos >= SolicitudAccesoSalon::MAX_INTENTOS) {
            return back()->withErrors([
                'busqueda' => 'Alcanzaste el máximo de intentos para este alumno. Acude a la escuela para resolverlo.',
            ]);
        }

        if (! $s->alumno || ! $s->alumno->estado) {
            return back()->withErrors(['busqueda' => 'Este alumno ya no está disponible.']);
        }

        if ($this->pendientesDe($perfil->idUsuario) >= self::MAX_PENDIENTES) {
            return back()->withErrors([
                'busqueda' => 'Ya tienes ' . self::MAX_PENDIENTES . ' solicitudes pendientes. Espera a que el docente las revise.',
            ]);
        }

        $s->update([
            'estadoSolicitud' => 'pendiente',
            'intentos' => $s->intentos + 1,
            'fechaSolicitud' => now(),
            'fechaRespuesta' => null,
            'observacionRespuesta' => null,
            'docente_validador_idUsuario' => null,
        ]);

        Log::info('tutor.solicitud_reenviada', [
            'usuario' => $perfil->idUsuario,
            'solicitud' => $s->idSolicitudAccesoSalon,
            'intento' => $s->intentos,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('tutor.solicitudes.index')
            ->with('status', 'Solicitud enviada de nuevo. El docente del grupo la revisará.');
    }
}