<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $perfil = $user->usuario?->load(['rol', 'datosTutor']);

        return view('profile.edit', compact('user', 'perfil'));
    }

    public function password(): View
    {
        return view('profile.password');
    }
}