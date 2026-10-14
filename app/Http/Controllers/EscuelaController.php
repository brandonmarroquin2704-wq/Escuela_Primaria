<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use Illuminate\Http\Request;

class EscuelaController extends Controller
{
    /**
     * Muestra el formulario para registrar la escuela.
     */
    public function create()
    {
        if (Escuela::exists()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'La escuela ya está registrada. Puedes editar sus datos desde el inicio.');
        }

        return view('escuelas.create');
    }

    /**
     * Guarda la escuela (solo se permite una).
     */
    public function store(Request $request)
    {
        if (Escuela::exists()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'La escuela ya está registrada.');
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'claveCentroTrabajo' => [
                'required',
                'string',
                'max:25',
                'unique:escuelas,claveCentroTrabajo',
                // Opcional: formato real de la CCT (ej. 07DPR0001A)
                // 'regex:/^[0-9]{2}[A-Z]{3}[0-9]{4}[A-Z]$/',
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
            ->route('dashboard')
            ->with('success', 'Escuela registrada correctamente.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Escuela $escuela)
    {
        return view('escuelas.edit', compact('escuela'));
    }

    /**
     * Actualiza los datos de la escuela.
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
                // 'regex:/^[0-9]{2}[A-Z]{3}[0-9]{4}[A-Z]$/',
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
            ->route('dashboard')
            ->with('success', 'Datos de la escuela actualizados correctamente.');
    }
}