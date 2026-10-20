<x-guest-layout>
    {{-- Mensaje de estado (por ejemplo, contraseña restablecida) --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @php($esTutor = $tipo === 'tutor')

    {{-- Título según el tipo de acceso --}}
    <h2 class="text-xl font-bold text-stone-800">
        {{ $esTutor ? 'Acceso para padres o tutores' : 'Acceso para personal de la escuela' }}
    </h2>

    {{-- Enlace de registro (solo tutores) --}}
    @if ($esTutor)
        <p class="mt-1 text-sm text-stone-600">
            ¿Aún no tienes cuenta?
            <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">Regístrate aquí</a>
        </p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="tipo" value="{{ $tipo }}">

        {{-- Correo electrónico --}}
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Contraseña --}}
        <div>
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Mantener la sesión iniciada --}}
        <label for="recordarme" class="inline-flex items-center">
            <input id="recordarme" type="checkbox" name="remember" class="rounded border-stone-300 text-emerald-700 shadow-sm focus:ring-emerald-600">
            <span class="ms-2 text-sm text-stone-600">Mantener sesión iniciada</span>
        </label>

        {{-- Acciones --}}
        <div class="flex items-center justify-end gap-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-stone-600 underline hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 rounded-md">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                Iniciar sesión
            </button>
        </div>
    </form>
</x-guest-layout>