<x-guest-layout>
    {{-- Instrucciones --}}
    <p class="mb-4 text-sm text-stone-600">
        ¿Olvidaste tu contraseña? Escribe tu correo electrónico y te enviaremos un enlace para que elijas una nueva.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Correo electrónico --}}
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <x-primary-button>Enviar enlace de recuperación</x-primary-button>
        </div>
    </form>
</x-guest-layout>