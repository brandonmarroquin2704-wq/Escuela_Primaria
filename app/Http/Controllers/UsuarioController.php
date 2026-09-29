<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'correo' => [
                'required',
                'string',
                'email',
                'max:150',
                'unique:usuarios,correo',
                'unique:users,email',
            ],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
            'rol_idRol' => ['required', 'exists:roles,idRol'],
            'estado' => ['required', 'boolean'],
        ]);

        $usuario = DB::transaction(function () use ($datos) {
            $contrasenaCifrada = Hash::make($datos['contrasena']);

            $usuario = Usuario::create([
                'nombre' => $datos['nombre'],
                'apellidoPaterno' => $datos['apellidoPaterno'],
                'apellidoMaterno' => $datos['apellidoMaterno'],
                'correo' => $datos['correo'],
                'contrasena' => $contrasenaCifrada,
                'rol_idRol' => $datos['rol_idRol'],
                'estado' => $datos['estado'],
                'fRegistro' => now()->toDateString(),
            ]);

            User::create([
                'name' => trim("{$datos['nombre']} {$datos['apellidoPaterno']} {$datos['apellidoMaterno']}"),
                'email' => $datos['correo'],
                'password' => $contrasenaCifrada,
                'usuario_idUsuario' => $usuario->idUsuario,
            ]);

            return $usuario;
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario y cuenta de acceso registrados correctamente.');
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
                Rule::unique('usuarios', 'correo')->ignore($usuario->idUsuario, 'idUsuario'),
                Rule::unique('users', 'email')->ignore($usuario->cuentaAcceso?->id),
            ],
            'rol_idRol' => ['required', 'exists:roles,idRol'],
            'estado' => ['required', 'boolean'],
        ];

        if ($request->filled('contrasena')) {
            $reglas['contrasena'] = ['string', 'min:8', 'confirmed'];
        }

        $datos = $request->validate($reglas);

        DB::transaction(function () use ($usuario, $datos, $request) {
            $contrasenaCifrada = null;

            if ($request->filled('contrasena')) {
                $contrasenaCifrada = Hash::make($datos['contrasena']);
                $datos['contrasena'] = $contrasenaCifrada;
            } else {
                unset($datos['contrasena']);
            }

            $usuario->update($datos);

            $cuenta = $usuario->cuentaAcceso;

            if ($cuenta) {
                $cambiosCuenta = [
                    'name' => trim("{$datos['nombre']} {$datos['apellidoPaterno']} {$datos['apellidoMaterno']}"),
                    'email' => $datos['correo'],
                ];

                if ($contrasenaCifrada) {
                    $cambiosCuenta['password'] = $contrasenaCifrada;
                }

                $cuenta->update($cambiosCuenta);
            }
        });

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