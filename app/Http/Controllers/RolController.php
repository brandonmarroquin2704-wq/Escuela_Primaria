<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::orderBy('idRol')->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:25',
                'unique:roles,nombre',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:45',
            ],
        ]);

        Rol::create($datos);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol registrado correctamente.');
    }

    public function show($idRol)
    {
        return redirect()->route('roles.index');
    }

    public function edit($idRol)
    {
        $rol = Rol::findOrFail($idRol);

        return view('roles.edit', compact('rol'));
    }

    public function update(Request $request, $idRol)
    {
        $rol = Rol::findOrFail($idRol);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:25',
                Rule::unique('roles', 'nombre')->ignore($rol->idRol, 'idRol'),
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:45',
            ],
        ]);

        $rol->update($datos);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy($idRol)
    {
        $rol = Rol::findOrFail($idRol);

        $rol->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}