<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar usuario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form
                    method="POST"
                    action="{{ route('usuarios.update', ['usuario' => $usuario->idUsuario]) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre', $usuario->nombre) }}"
                                maxlength="45"
                                required
                                autofocus
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="apellidoPaterno" class="block text-sm font-medium text-gray-700">
                                Apellido paterno
                            </label>

                            <input
                                type="text"
                                id="apellidoPaterno"
                                name="apellidoPaterno"
                                value="{{ old('apellidoPaterno', $usuario->apellidoPaterno) }}"
                                maxlength="45"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('apellidoPaterno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="apellidoMaterno" class="block text-sm font-medium text-gray-700">
                                Apellido materno
                            </label>

                            <input
                                type="text"
                                id="apellidoMaterno"
                                name="apellidoMaterno"
                                value="{{ old('apellidoMaterno', $usuario->apellidoMaterno) }}"
                                maxlength="45"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('apellidoMaterno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="correo" class="block text-sm font-medium text-gray-700">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="correo"
                                name="correo"
                                value="{{ old('correo', $usuario->correo) }}"
                                maxlength="150"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('correo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contrasena" class="block text-sm font-medium text-gray-700">
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                id="contrasena"
                                name="contrasena"
                                minlength="8"
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                Déjalo vacío si no deseas cambiar la contraseña.
                            </p>

                            @error('contrasena')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirmar nueva contraseña
                            </label>

                            <input
                                type="password"
                                id="contrasena_confirmation"
                                name="contrasena_confirmation"
                                minlength="8"
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                        </div>

                        <div>
                            <label for="rol_idRol" class="block text-sm font-medium text-gray-700">
                                Rol
                            </label>

                            <select
                                id="rol_idRol"
                                name="rol_idRol"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona un rol</option>

                                @foreach ($roles as $rol)
                                    <option
                                        value="{{ $rol->idRol }}"
                                        @selected(old('rol_idRol', $usuario->rol_idRol) == $rol->idRol)
                                    >
                                        {{ $rol->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            @error('rol_idRol')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">
                                Estado
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option
                                    value="1"
                                    @selected(old('estado', $usuario->estado ? '1' : '0') == '1')
                                >
                                    Activo
                                </option>

                                <option
                                    value="0"
                                    @selected(old('estado', $usuario->estado ? '1' : '0') == '0')
                                >
                                    Inactivo
                                </option>
                            </select>

                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            style="background-color: #2563eb; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; border: none;"
                        >
                            Actualizar usuario
                        </button>

                        <a
                            href="{{ route('usuarios.index') }}"
                            style="background-color: #6b7280; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>