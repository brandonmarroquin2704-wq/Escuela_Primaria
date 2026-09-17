<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\TutorAlumno;
use App\Models\Usuario;
use Illuminate\Http\Request;

class TutorAlumnoController extends Controller
{
    public function index()
    {
        $relaciones = TutorAlumno::with(['tutor', 'alumno.grupo'])
            ->orderBy('alumno_idAlumno')
            ->orderByDesc('esResponsable')
            ->get();

        return view('tutor_alumnos.index', compact('relaciones'));
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

    public function destroy($id)
    {
        $relacion = TutorAlumno::findOrFail($id);

        $relacion->delete();

        return redirect()
            ->route('tutor-alumnos.index')
            ->with('success', 'Relación tutor-alumno eliminada correctamente.');
    }
}