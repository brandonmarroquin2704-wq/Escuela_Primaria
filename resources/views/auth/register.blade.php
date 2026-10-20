<x-guest-layout ancho="sm:max-w-3xl">
    <h2 class="mb-4 text-lg font-semibold text-gray-800">Registro de padre o tutor</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Una columna en celular y dos en pantallas más grandes --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            {{-- Fila 1: nombre y apellido paterno --}}
            <div>
                <x-input-label for="nombre" value="Nombre(s)" />
                <x-text-input id="nombre" class="mt-1 block w-full" type="text" name="nombre" :value="old('nombre')" required autofocus />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="apellidoPaterno" value="Apellido paterno" />
                <x-text-input id="apellidoPaterno" class="mt-1 block w-full" type="text" name="apellidoPaterno" :value="old('apellidoPaterno')" required />
                <x-input-error :messages="$errors->get('apellidoPaterno')" class="mt-2" />
            </div>

            {{-- Fila 2: apellido materno y teléfono --}}
            <div>
                <x-input-label for="apellidoMaterno" value="Apellido materno" />
                <x-text-input id="apellidoMaterno" class="mt-1 block w-full" type="text" name="apellidoMaterno" :value="old('apellidoMaterno')" />
                <x-input-error :messages="$errors->get('apellidoMaterno')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="telefono" value="Teléfono (10 dígitos)" />
                <x-text-input id="telefono" class="mt-1 block w-full" type="text" name="telefono" :value="old('telefono')" inputmode="numeric" maxlength="10" required />
                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
            </div>

            {{-- Fila 3: correo y tipo de responsable --}}
            <div>
                <x-input-label for="email" value="Correo electrónico" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="tipoResponsable" value="Eres" />
                <select id="tipoResponsable" name="tipoResponsable" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @foreach (['Padre', 'Madre', 'Tutor legal', 'Otro'] as $opcion)
                        <option value="{{ $opcion }}" @selected(old('tipoResponsable') === $opcion)>{{ $opcion }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('tipoResponsable')" class="mt-2" />
            </div>

            {{-- Fila 4: contraseña y confirmación --}}
            <div>
                <x-input-label for="password" value="Contraseña" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        {{-- Acciones --}}
        <div class="mt-6 flex items-center justify-end">
            <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login', ['tipo' => 'tutor']) }}">¿Ya tienes cuenta?</a>
            <x-primary-button class="ms-4">Registrarme</x-primary-button>
        </div>
    </form>
</x-guest-layout>