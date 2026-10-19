<?php

namespace App\Http\Controllers;

use App\Models\Escuela;
use Illuminate\Http\Request;

class EscuelaController extends Controller
{
    public function show()
    {
        $perfil = auth()->user()->usuario;

        return view('escuelas.show', [
            'escuela' => Escuela::first(),
            'puedeEditar' => $perfil->tienePermiso('Escuelas', 'Editar'),
            'puedeRegistrar' => $perfil->tienePermiso('Escuelas', 'Registrar'),
        ]);
    }

    public function create()
    {
        if (Escuela::exists()) {
            return redirect()
                ->route('escuelas.show')
                ->with('error', 'La escuela ya está registrada. Puedes editar sus datos desde esta pantalla.');
        }

        return view('escuelas.create');
    }

    public function store(Request $request)
    {
        if (Escuela::exists()) {
            return redirect()
                ->route('escuelas.show')
                ->with('error', 'La escuela ya está registrada.');
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'claveCentroTrabajo' => ['required', 'string', 'max:25', 'unique:escuelas,claveCentroTrabajo'],
            'telefono' => ['required', 'string', 'max:25'],
            'correo' => ['required', 'email', 'max:45', 'unique:escuelas,correo'],
            'direccion' => ['required', 'string', 'max:200'],
            'municipio' => ['required', 'string', 'max:80'],
            'codigoPostal' => ['required', 'string', 'max:15'],
            'estado' => ['required', 'boolean'],
        ]);

        $datos['fechaRegistro'] = now();
        $datos['fechaActualizacion'] = now();

        Escuela::create($datos);

        return redirect()
            ->route('escuelas.show')
            ->with('success', 'Escuela registrada correctamente.');
    }

    public function edit(Escuela $escuela)
    {
        return view('escuelas.edit', compact('escuela'));
    }

    public function update(Request $request, Escuela $escuela)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'claveCentroTrabajo' => [
                'required',
                'string',
                'max:25',
                'unique:escuelas,claveCentroTrabajo,' . $escuela->idEscuela . ',idEscuela',
            ],
            'telefono' => ['required', 'string', 'max:25'],
            'correo' => [
                'required',
                'email',
                'max:45',
                'unique:escuelas,correo,' . $escuela->idEscuela . ',idEscuela',
            ],
            'direccion' => ['required', 'string', 'max:200'],
            'municipio' => ['required', 'string', 'max:80'],
            'codigoPostal' => ['required', 'string', 'max:15'],
            'estado' => ['required', 'boolean'],
        ]);

        $datos['fechaActualizacion'] = now();

        $escuela->update($datos);

        return redirect()
            ->route('escuelas.show')
            ->with('success', 'Datos de la escuela actualizados correctamente.');
    }
}