<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
{
    // Valida la búsqueda y los filtros.
    $filtros = $request->validate([
        'buscar' => ['nullable', 'string', 'max:100'],
        'grupo' => ['nullable', 'integer', 'exists:grupos,idGrupo'],
        'estado' => ['nullable', 'in:0,1'],
    ]);

    $alumnos = Alumno::with('grupo')
        // Busca por nombre o apellidos.
        ->when($request->filled('buscar'), function ($consulta) use ($filtros) {
            $texto = '%' . trim($filtros['buscar']) . '%';

            $consulta->where(function ($consulta) use ($texto) {
                $consulta->where('nombre', 'like', $texto)
                    ->orWhere('apellidoPaterno', 'like', $texto)
                    ->orWhere('apellidoMaterno', 'like', $texto);
            });
        })
        // Filtra por grupo y estado.
        ->when(
            $request->filled('grupo'),
            fn ($consulta) => $consulta->where('grupo_idGrupo', $filtros['grupo'])
        )
        ->when(
            $request->filled('estado'),
            fn ($consulta) => $consulta->where('estado', $filtros['estado'])
        )
        // Ordena por apellidos y nombre.
        ->orderBy('apellidoPaterno')
        ->orderBy('apellidoMaterno')
        ->orderBy('nombre')
        ->orderBy('idAlumno')
        ->paginate(10)
        ->withQueryString();

    // Incluye grupos activos e inactivos para consultar el historial.
    $grupos = Grupo::orderByDesc('cicloEsc')
        ->orderBy('grado')
        ->orderBy('letraGrupo')
        ->get();

    return view('alumnos.index', compact('alumnos', 'grupos'));
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