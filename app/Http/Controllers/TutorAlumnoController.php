<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\TutorAlumno;
use App\Models\Usuario;
use Illuminate\Http\Request;

class TutorAlumnoController extends Controller
{
    public function index(Request $request)
    {
        // Valida la búsqueda y los filtros.
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'grupo' => ['nullable', 'integer', 'exists:grupos,idGrupo'],
            'estado' => ['nullable', 'in:0,1'],
        ]);

        $consulta = TutorAlumno::with(['tutor', 'alumno.grupo'])
            // Busca coincidencias en el tutor o el alumno.
            ->when($request->filled('buscar'), function ($consulta) use ($filtros) {
                $texto = '%' . trim($filtros['buscar']) . '%';

                $buscarPersona = fn ($persona) => $persona
                    ->where('nombre', 'like', $texto)
                    ->orWhere('apellidoPaterno', 'like', $texto)
                    ->orWhere('apellidoMaterno', 'like', $texto);

                $consulta->where(fn ($personas) => $personas
                    ->whereHas('tutor', $buscarPersona)
                    ->orWhereHas('alumno', $buscarPersona));
            })
            // Filtra por el grupo del alumno.
            ->when($request->filled('grupo'), function ($consulta) use ($filtros) {
                $consulta->whereHas('alumno', fn ($alumno) => $alumno
                    ->where('grupo_idGrupo', $filtros['grupo']));
            })
            ->when(
                $request->filled('estado'),
                fn ($consulta) => $consulta->where('estado', $filtros['estado'])
            );

        // Totales sobre todo el resultado filtrado, no solo la página actual.
        $totales = [
            'total' => (clone $consulta)->count(),
            'activos' => (clone $consulta)->where('estado', true)->count(),
            'inactivos' => (clone $consulta)->where('estado', false)->count(),
        ];

        $relaciones = $consulta
            ->orderBy('alumno_idAlumno')
            ->orderByDesc('esResponsable')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        // Incluye todos los grupos para consultar relaciones anteriores.
        $grupos = Grupo::orderByDesc('cicloEsc')
            ->orderBy('grado')
            ->orderBy('letraGrupo')
            ->get();

        return view('tutor_alumnos.index', compact('relaciones', 'grupos', 'totales'));
    }

    public function create()
    {
        $tutores = Usuario::where('estado', true)
            ->whereHas('rol', function ($query) {
                $query->where('nombre', 'Tutor');
            })
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        $alumnos = Alumno::where('estado', true)
            ->with('grupo')
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        return view('tutor_alumnos.create', compact('tutores', 'alumnos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'usuario_idUsuario' => ['required', 'exists:usuarios,idUsuario'],
            'alumno_idAlumno' => ['required', 'exists:alumnos,idAlumno'],
            'parentesco' => ['required', 'string', 'max:25'],
            'esResponsable' => ['required', 'boolean'],
            'estado' => ['required', 'boolean'],
        ]);

        $tutorDuplicado = TutorAlumno::where('usuario_idUsuario', $datos['usuario_idUsuario'])
            ->where('alumno_idAlumno', $datos['alumno_idAlumno'])
            ->exists();

        if ($tutorDuplicado) {
            return back()
                ->withErrors([
                    'usuario_idUsuario' => 'Este tutor ya está relacionado con el alumno seleccionado.',
                ])
                ->withInput();
        }

        if ($datos['estado'] && $datos['esResponsable']) {
            TutorAlumno::where('alumno_idAlumno', $datos['alumno_idAlumno'])
                ->where('estado', true)
                ->update(['esResponsable' => false]);
        }

        TutorAlumno::create($datos);

        return redirect()
            ->route('tutor-alumnos.index')
            ->with('success', 'Tutor relacionado con el alumno correctamente.');
    }

    public function show($id)
    {
        return redirect()->route('tutor-alumnos.index');
    }

    public function edit($id)
    {
        $relacion = TutorAlumno::findOrFail($id);

        $tutores = Usuario::where('estado', true)
            ->whereHas('rol', function ($query) {
                $query->where('nombre', 'Tutor');
            })
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        $alumnos = Alumno::where('estado', true)
            ->with('grupo')
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        return view('tutor_alumnos.edit', compact('relacion', 'tutores', 'alumnos'));
    }

    public function update(Request $request, $id)
    {
        $relacion = TutorAlumno::findOrFail($id);

        $datos = $request->validate([
            'usuario_idUsuario' => ['required', 'exists:usuarios,idUsuario'],
            'alumno_idAlumno' => ['required', 'exists:alumnos,idAlumno'],
            'parentesco' => ['required', 'string', 'max:25'],
            'esResponsable' => ['required', 'boolean'],
            'estado' => ['required', 'boolean'],
        ]);

        $tutorDuplicado = TutorAlumno::where('usuario_idUsuario', $datos['usuario_idUsuario'])
            ->where('alumno_idAlumno', $datos['alumno_idAlumno'])
            ->where('id', '!=', $relacion->id)
            ->exists();

        if ($tutorDuplicado) {
            return back()
                ->withErrors([
                    'usuario_idUsuario' => 'Este tutor ya está relacionado con el alumno seleccionado.',
                ])
                ->withInput();
        }

        if ($datos['estado'] && $datos['esResponsable']) {
            TutorAlumno::where('alumno_idAlumno', $datos['alumno_idAlumno'])
                ->where('estado', true)
                ->where('id', '!=', $relacion->id)
                ->update(['esResponsable' => false]);
        }

        $relacion->update($datos);

        return redirect()
            ->route('tutor-alumnos.index')
            ->with('success', 'Relación tutor-alumno actualizada correctamente.');
    }

    // Inactiva o reactiva la relación; no se borra para conservar el historial.
    public function destroy($id)
    {
        $relacion = TutorAlumno::findOrFail($id);
        $nuevoEstado = ! $relacion->estado;

        // Al reactivar un responsable, solo puede haber uno activo por alumno.
        if ($nuevoEstado && $relacion->esResponsable) {
            TutorAlumno::where('alumno_idAlumno', $relacion->alumno_idAlumno)
                ->where('estado', true)
                ->where('id', '!=', $relacion->id)
                ->update(['esResponsable' => false]);
        }

        $relacion->update(['estado' => $nuevoEstado]);

        return redirect()
            ->route('tutor-alumnos.index')
            ->with('success', $nuevoEstado
                ? 'Relación tutor-alumno reactivada correctamente.'
                : 'Relación tutor-alumno inactivada correctamente.');
    }
}