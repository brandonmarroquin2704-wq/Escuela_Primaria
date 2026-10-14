<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Registro de padre o tutor
    </h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="nombre" value="Nombre(s)" />
            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required autofocus />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="apellidoPaterno" value="Apellido paterno" />
            <x-text-input id="apellidoPaterno" class="block mt-1 w-full" type="text" name="apellidoPaterno" :value="old('apellidoPaterno')" required />
            <x-input-error :messages="$errors->get('apellidoPaterno')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="apellidoMaterno" value="Apellido materno (opcional)" />
            <x-text-input id="apellidoMaterno" class="block mt-1 w-full" type="text" name="apellidoMaterno" :value="old('apellidoMaterno')" />
            <x-input-error :messages="$errors->get('apellidoMaterno')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="telefono" value="Teléfono (10 dígitos)" />
            <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono')" inputmode="numeric" maxlength="10" required />
            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="tipoResponsable" value="Eres" />
            <select id="tipoResponsable" name="tipoResponsable" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                @foreach (['Padre', 'Madre', 'Tutor legal', 'Otro'] as $opcion)
                    <option value="{{ $opcion }}" @selected(old('tipoResponsable') === $opcion)>{{ $opcion }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('tipoResponsable')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login', ['tipo' => 'tutor']) }}">
                ¿Ya tienes cuenta?
            </a>

            <x-primary-button class="ms-4">
                Registrarme
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>