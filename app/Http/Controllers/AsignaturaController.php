<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use Illuminate\Http\Request;

class AsignaturaController extends Controller
{
    /**
     * Muestra todas las asignaturas registradas.
     */
    public function index()
    {
        $asignaturas = Asignatura::orderBy('nombre')->get();

        return view('asignaturas.index', compact('asignaturas'));
    }

    /**
     * Muestra el formulario para registrar una asignatura.
     */
    public function create()
    {
        return view('asignaturas.create');
    }

    /**
     * Guarda una nueva asignatura.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:85'],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'estado' => ['required', 'boolean'],
        ]);

        $datos['fechaRegistro'] = now();
        $datos['fechaActualizacion'] = now();

        Asignatura::create($datos);

        return redirect()
            ->route('asignaturas.index')
            ->with('success', 'Asignatura registrada correctamente.');
    }

    /**
     * Muestra una asignatura específica.
     */
    public function show(Asignatura $asignatura)
    {
        return view('asignaturas.show', compact('asignatura'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Asignatura $asignatura)
    {
        return view('asignaturas.edit', compact('asignatura'));
    }

    /**
     * Actualiza una asignatura.
     */
    public function update(Request $request, Asignatura $asignatura)
    {
$datos = $request->validate([
    'nombre' => [
        'required',
        'string',
        'max:85',
        'unique:asignaturas,nombre,' .
            $asignatura->idAsignatura .
            ',idAsignatura',
    ],
    'descripcion' => ['nullable', 'string', 'max:200'],
    'estado' => ['required', 'boolean'],
]);

        $datos['fechaActualizacion'] = now();

        $asignatura->update($datos);

        return redirect()
            ->route('asignaturas.index')
            ->with('success', 'Asignatura actualizada correctamente.');
    }

    /**
     * Elimina una asignatura.
     */
    public function destroy(Asignatura $asignatura)
    {
        $asignatura->delete();

        return redirect()
            ->route('asignaturas.index')
            ->with('success', 'Asignatura eliminada correctamente.');
    }
}