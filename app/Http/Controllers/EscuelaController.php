<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use Illuminate\Http\Request;

class EscuelaController extends Controller
{
    /**
     * Muestra todas las escuelas registradas.
     */
    public function index()
    {
        $escuelas = Escuela::orderBy('nombre')->get();

        return view('escuelas.index', compact('escuelas'));
    }

    /**
     * Muestra el formulario para registrar una escuela.
     */
    public function create()
    {
        return view('escuelas.create');
    }

    /**
     * Guarda una nueva escuela.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'claveCentroTrabajo' => [
                'required',
                'string',
                'max:25',
                'unique:escuelas,claveCentroTrabajo',
            ],
            'telefono' => ['required', 'string', 'max:25'],
            'correo' => [
                'required',
                'email',
                'max:45',
                'unique:escuelas,correo',
            ],
            'direccion' => ['required', 'string', 'max:200'],
            'municipio' => ['required', 'string', 'max:80'],
            'codigoPostal' => ['required', 'string', 'max:15'],
            'estado' => ['required', 'boolean'],
        ]);

        $datos['fechaRegistro'] = now();
        $datos['fechaActualizacion'] = now();

        Escuela::create($datos);

        return redirect()
            ->route('escuelas.index')
            ->with('success', 'Escuela registrada correctamente.');
    }

    /**
     * Muestra una escuela específica.
     */
    public function show(Escuela $escuela)
    {
        return view('escuelas.show', compact('escuela'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Escuela $escuela)
    {
        return view('escuelas.edit', compact('escuela'));
    }

    /**
     * Actualiza una escuela existente.
     */
    public function update(Request $request, Escuela $escuela)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'claveCentroTrabajo' => [
                'required',
                'string',
                'max:25',
                'unique:escuelas,claveCentroTrabajo,' .
                    $escuela->idEscuela .
                    ',idEscuela',
            ],
            'telefono' => ['required', 'string', 'max:25'],
            'correo' => [
                'required',
                'email',
                'max:45',
                'unique:escuelas,correo,' .
                    $escuela->idEscuela .
                    ',idEscuela',
            ],
            'direccion' => ['required', 'string', 'max:200'],
            'municipio' => ['required', 'string', 'max:80'],
            'codigoPostal' => ['required', 'string', 'max:15'],
            'estado' => ['required', 'boolean'],
        ]);

        $datos['fechaActualizacion'] = now();

        $escuela->update($datos);

        return redirect()
            ->route('escuelas.index')
            ->with('success', 'Escuela actualizada correctamente.');
    }

    /**
     * Elimina una escuela.
     */
    public function destroy(Escuela $escuela)
    {
        $escuela->delete();

        return redirect()
            ->route('escuelas.index')
            ->with('success', 'Escuela eliminada correctamente.');
    }
}