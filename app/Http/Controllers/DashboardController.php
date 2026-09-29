<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user()->usuario;

        if (! $usuario || ! $usuario->rol) {
            abort(403, 'Tu cuenta no está vinculada a un perfil escolar con rol activo.');
        }

        return view('dashboard', [
            'usuarioEscolar' => $usuario,
            'rol' => $usuario->rol->nombre,
        ]);
    }
}