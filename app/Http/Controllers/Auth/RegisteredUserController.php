<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DatosTutor;
use App\Models\Rol;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
            $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:45'],
            'apellidoPaterno' => ['required', 'string', 'max:45'],
            'apellidoMaterno' => ['nullable', 'string', 'max:45'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150',
                'unique:users,email', 'unique:usuarios,correo'],
            'telefono' => ['required', 'digits:10'],
            'tipoResponsable' => ['required', 'in:Padre,Madre,Tutor legal,Otro'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.unique' => 'Este correo ya está registrado. Inicia sesión o usa otro correo.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'telefono.digits' => 'El teléfono debe tener 10 dígitos.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $rolTutor = Rol::where('nombre', 'Tutor')->firstOrFail();  //busca el rol por nombre, no por número

        $user = DB::transaction(function () use ($datos, $rolTutor) {
            $hash = Hash::make($datos['password']);

            $perfil = Usuario::create([
                'nombre' => $datos['nombre'],
                'apellidoPaterno' => $datos['apellidoPaterno'],
                'apellidoMaterno' => $datos['apellidoMaterno'] ?? '',
                'correo' => $datos['email'],
                'contrasena' => $hash,
                'rol_idRol' => $rolTutor->idRol,
                'fRegistro' => now()->toDateString(),
                'estado' => true,
            ]);

            DatosTutor::create([
                'usuario_idUsuario' => $perfil->idUsuario,
                'tipoResponsable' => $datos['tipoResponsable'],
                'telefono' => $datos['telefono'],
                'estado' => true,
            ]);

            return User::create([
                'name' => trim($datos['nombre'].' '.$datos['apellidoPaterno'].' '.($datos['apellidoMaterno'] ?? '')),
                'email' => $datos['email'],
                'password' => $hash,
                'usuario_idUsuario' => $perfil->idUsuario,
            ]);
        });

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}