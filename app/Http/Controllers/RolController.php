<?php

namespace App\Http\Controllers;

use App\Models\Rol;

class RolController extends Controller
{
    
    //Muestra el catálogo de roles (solo consulta).
     
    public function index()
    {
        $roles = Rol::orderBy('idRol')->get();

        return view('roles.index', compact('roles'));
    }
}