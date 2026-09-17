<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')
            ->orderBy('idUsuario')
            ->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Rol::orderBy('nombre')->get();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:45'],
            'apellidoPaterno' => ['required', 'string', 'max:45'],
            'apellidoMaterno' => ['required', 'string', 'max:45'],
            'correo' => ['required', 'string', 'email', 'max:150', 'unique:usuarios,correo'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
            'rol_idRol' => ['required', 'exists:roles,idRol'],
            'estado' => ['required', 'boolean'],
        ]);

        $datos['contrasena'] = Hash::make($datos['contrasena']);
        $datos['fRegistro'] = now()->toDateString();

        Usuario::create($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function show($idUsuario)
    {
        return redirect()->route('usuarios.index');
    }

    public function edit($idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);
        $roles = Rol::orderBy('nombre')->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);

        $reglas = [
            'nombre' => ['required', 'string', 'max:45'],
            'apellidoPaterno' => ['required', 'string', 'max:45'],
            'apellidoMaterno' => ['required', 'string', 'max:45'],
            'correo' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('usuarios', 'correo')
                    ->ignore($usuario->idUsuario, 'idUsuario'),
            ],
            'rol_idRol' => ['required', 'exists:roles,idRol'],
            'estado' => ['required', 'boolean'],
        ];

        if ($request->filled('contrasena')) {
            $reglas['contrasena'] = ['string', 'min:8', 'confirmed'];
        }

        $datos = $request->validate($reglas);

        if ($request->filled('contrasena')) {
            $datos['contrasena'] = Hash::make($datos['contrasena']);
        } else {
            unset($datos['contrasena']);
        }

        $usuario->update($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);

        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}