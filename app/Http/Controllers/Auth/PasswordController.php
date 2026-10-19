<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Escribe tu contraseña actual.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.required' => 'Escribe la nueva contraseña.',
            'password.confirmed' => 'Las contraseñas nuevas no coinciden.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La nueva contraseña debe incluir al menos una letra.',
            'password.mixed' => 'La nueva contraseña debe incluir mayúsculas y minúsculas.',
            'password.numbers' => 'La nueva contraseña debe incluir al menos un número.',
            'password.symbols' => 'La nueva contraseña debe incluir al menos un símbolo.',
            'password.uncompromised' => 'Esa contraseña apareció en filtraciones de datos. Elige otra.',
        ]);

        $hash = Hash::make($validated['password']);

        DB::transaction(function () use ($request, $hash) {
            $request->user()->update(['password' => $hash]);
            $request->user()->usuario?->update(['contrasena' => $hash]);
        });

        return redirect()
            ->route('profile.password')
            ->with('status', 'password-updated');
    }
}