<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mi cuenta
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $perfil?->rol?->nombre === 'Tutor' ? 'Datos del padre o tutor' : 'Datos del docente' }}
                </h3>

                <dl class="mt-4 grid grid-cols-1 gap-4 text-sm text-gray-700 sm:grid-cols-2">
                    <div>
                        <dt class="font-semibold">account_box Nombre completo</dt>
                        <dd>{{ $perfil?->nombre }} {{ $perfil?->apellidoPaterno }} {{ $perfil?->apellidoMaterno }}</dd>
                    </div>

                    <div>
                        <dt class="font-semibold">Correo electrónico</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>

                    <div>
                        <dt class="font-semibold">Rol</dt>
                        <dd>{{ $perfil?->rol?->nombre }}</dd>
                    </div>

                    <div>
                        <dt class="font-semibold">Fecha de registro</dt>
                        <dd>{{ $perfil?->fRegistro?->format('d/m/Y') }}</dd>
                    </div>

                    @if ($perfil?->datosTutor)
                        <div>
                            <dt class="font-semibold">Tipo de responsable</dt>
                            <dd>{{ $perfil->datosTutor->tipoResponsable }}</dd>
                        </div>

                        <div>
                            <dt class="font-semibold">Teléfono</dt>
                            <dd>{{ $perfil->datosTutor->telefono }}</dd>
                        </div>
                    @endif
                </dl>

                <p class="mt-4 text-xs text-gray-500">
                    Si algún dato es incorrecto, solicita la corrección al administrador.
                </p>
            </div>

            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">Seguridad</h3>

                <p class="mt-1 text-sm text-gray-600">
                    Cambia tu contraseña de acceso al sistema.
                </p>

                <div class="mt-4 flex items-center gap-3">
                    <a
                        href="{{ route('profile.password') }}"
                        style="background-color: #2563eb; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                    >
                        Actualizar contraseña
                    </a>

                    <a
                        href="{{ route('dashboard') }}"
                        style="background-color: #6b7280; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                    >
                        Volver al panel
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>