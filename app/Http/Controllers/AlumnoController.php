<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::with('grupo')
            ->orderBy('apellidoPaterno')
            ->orderBy('apellidoMaterno')
            ->orderBy('nombre')
            ->get();

        return view('alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        $grupos = Grupo::where('estado', true)
            ->orderBy('cicloEsc')
            ->orderBy('grado')
            ->orderBy('letraGrupo')
            ->get();

        return view('alumnos.create', compact('grupos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:20'],
            'apellidoPaterno' => ['required', 'string', 'max:35'],
            'apellidoMaterno' => ['required', 'string', 'max:45'],
            'fNac' => ['required', 'date', 'before:today'],
            'estado' => ['required', 'boolean'],
            'grupo_idGrupo' => ['required', 'exists:grupos,idGrupo'],
        ]);

        $datos['fechaRegistro'] = now()->toDateString();

        Alumno::create($datos);

        return redirect()
            ->route('alumnos.index')
            ->with('success', 'Alumno registrado correctamente.');
    }

    public function show($idAlumno)
    {
        return redirect()->route('alumnos.index');
    }

    public function edit($idAlumno)
    {
        $alumno = Alumno::findOrFail($idAlumno);

        $grupos = Grupo::where('estado', true)
            ->orderBy('cicloEsc')
            ->orderBy('grado')
            ->orderBy('letraGrupo')
            ->get();

        return view('alumnos.edit', compact('alumno', 'grupos'));
    }

    public function update(Request $request, $idAlumno)
    {
        $alumno = Alumno::findOrFail($idAlumno);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:20'],
            'apellidoPaterno' => ['required', 'string', 'max:35'],
            'apellidoMaterno' => ['required', 'string', 'max:45'],
            'fNac' => ['required', 'date', 'before:today'],
            'estado' => ['required', 'boolean'],
            'grupo_idGrupo' => ['required', 'exists:grupos,idGrupo'],
        ]);

        $alumno->update($datos);

        return redirect()
            ->route('alumnos.index')
            ->with('success', 'Alumno actualizado correctamente.');
    }

    public function destroy($idAlumno)
    {
        $alumno = Alumno::findOrFail($idAlumno);

        $alumno->delete();

        return redirect()
            ->route('alumnos.index')
            ->with('success', 'Alumno eliminado correctamente.');
    }
}