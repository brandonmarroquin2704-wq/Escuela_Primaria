<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mi perfil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900">Mis datos</h3>

                    <dl class="mt-4 space-y-2 text-sm text-gray-700">
                        <div>
                            <dt class="font-semibold">Nombre</dt>
                            <dd>{{ $perfil?->nombre }} {{ $perfil?->apellidoPaterno }} {{ $perfil?->apellidoMaterno }}</dd>
                        </div>

                        <div>
                            <dt class="font-semibold">Correo</dt>
                            <dd>{{ $user->email }}</dd>
                        </div>

                        <div>
                            <dt class="font-semibold">Rol</dt>
                            <dd>{{ $perfil?->rol?->nombre }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 text-xs text-gray-500">
                        Si necesitas corregir estos datos, solicítalo al administrador.
                    </p>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>