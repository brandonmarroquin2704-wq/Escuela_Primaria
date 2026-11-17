<x-app-layout>
    <x-slot name="header">
        <h2 class="truncate text-lg font-bold text-[#1E3A5F]">
            Registrar usuario
        </h2>
    </x-slot>

    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-6">

            {{-- Encabezado --}}
            <header>
                <nav aria-label="Ruta de navegación" class="text-sm text-[#667085]">
                    <a href="{{ route('usuarios.index') }}" class="font-medium text-[#2563A6] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6]">Usuarios</a>
                    <span aria-hidden="true">/</span>
                    <span class="font-semibold text-[#263548]">Registrar usuario</span>
                </nav>

                <h1 class="mt-2 text-2xl font-extrabold text-[#1E3A5F] sm:text-[28px] sm:leading-9">Registrar usuario</h1>
                <p class="mt-1 text-base text-[#667085]">
                    Completa la información para registrar un nuevo usuario del sistema.
                </p>
            </header>

            @if (session('error'))
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-semibold text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <p class="font-semibold">Revisa los campos marcados antes de continuar.</p>
                    <p class="text-sm">Hay {{ $errors->count() }} {{ $errors->count() === 1 ? 'error' : 'errores' }} en el formulario.</p>
                </div>
            @endif

            {{-- Tarjeta del formulario --}}
            <div class="overflow-hidden rounded-2xl border border-[#DCE6F2] bg-white shadow-sm shadow-[#1E3A5F]/5">
                <form
                    method="POST"
                    action="{{ route('usuarios.store') }}"
                    x-data="{ cargando: false }"
                    @submit="cargando = true"
                    class="divide-y divide-[#E6EDF5]"
                >
                    @csrf

                    {{-- Información personal --}}
                    <fieldset class="p-6 sm:p-8">
                        <legend class="flex items-center gap-3 text-base font-bold text-[#1E3A5F]">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#EAF3FC] text-[#2563A6]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 17a7 7 0 0 1 14 0v.5H3V17z" /></svg>
                            </span>
                            Información personal
                        </legend>

                        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="nombre" class="mb-1 block text-sm font-semibold text-[#263548]">Nombre</label>
                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    value="{{ old('nombre') }}"
                                    maxlength="45"
                                    required
                                    autofocus
                                    @error('nombre') aria-invalid="true" aria-describedby="error-nombre" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-[#263548] placeholder:text-[#667085] focus:border-[#2563A6] focus:ring-[#2563A6] @error('nombre') border-red-500 @enderror"
                                >
                                @error('nombre')
                                    <p id="error-nombre" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="apellidoPaterno" class="mb-1 block text-sm font-semibold text-[#263548]">Apellido paterno</label>
                                <input
                                    type="text"
                                    id="apellidoPaterno"
                                    name="apellidoPaterno"
                                    value="{{ old('apellidoPaterno') }}"
                                    maxlength="45"
                                    required
                                    @error('apellidoPaterno') aria-invalid="true" aria-describedby="error-apellidoPaterno" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('apellidoPaterno') border-red-500 @enderror"
                                >
                                @error('apellidoPaterno')
                                    <p id="error-apellidoPaterno" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="apellidoMaterno" class="mb-1 block text-sm font-semibold text-[#263548]">Apellido materno</label>
                                <input
                                    type="text"
                                    id="apellidoMaterno"
                                    name="apellidoMaterno"
                                    value="{{ old('apellidoMaterno') }}"
                                    maxlength="45"
                                    required
                                    @error('apellidoMaterno') aria-invalid="true" aria-describedby="error-apellidoMaterno" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('apellidoMaterno') border-red-500 @enderror"
                                >
                                @error('apellidoMaterno')
                                    <p id="error-apellidoMaterno" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="correo" class="mb-1 block text-sm font-semibold text-[#263548]">Correo electrónico</label>
                                <input
                                    type="email"
                                    id="correo"
                                    name="correo"
                                    value="{{ old('correo') }}"
                                    maxlength="150"
                                    required
                                    @error('correo') aria-invalid="true" aria-describedby="error-correo" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('correo') border-red-500 @enderror"
                                >
                                @error('correo')
                                    <p id="error-correo" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Seguridad --}}
                    <fieldset class="p-6 sm:p-8">
                        <legend class="flex items-center gap-3 text-base font-bold text-[#1E3A5F]">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#F0EEFA] text-[#4B3F9E]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0 1 10 0v2a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2zm8-2v2H7V7a3 3 0 0 1 6 0z" clip-rule="evenodd" /></svg>
                            </span>
                            Seguridad
                        </legend>

                        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="contrasena" class="mb-1 block text-sm font-semibold text-[#263548]">Contraseña</label>
                                <input
                                    type="password"
                                    id="contrasena"
                                    name="contrasena"
                                    minlength="8"
                                    required
                                    aria-describedby="ayuda-contrasena @error('contrasena') error-contrasena @enderror"
                                    @error('contrasena') aria-invalid="true" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('contrasena') border-red-500 @enderror"
                                >
                                <p id="ayuda-contrasena" class="mt-1 text-sm text-[#667085]">Mínimo 8 caracteres.</p>
                                @error('contrasena')
                                    <p id="error-contrasena" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contrasena_confirmation" class="mb-1 block text-sm font-semibold text-[#263548]">Confirmar contraseña</label>
                                <input
                                    type="password"
                                    id="contrasena_confirmation"
                                    name="contrasena_confirmation"
                                    minlength="8"
                                    required
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6]"
                                >
                            </div>
                        </div>
                    </fieldset>

                    {{-- Permisos y estado --}}
                    <fieldset class="p-6 sm:p-8">
                        <legend class="flex items-center gap-3 text-base font-bold text-[#1E3A5F]">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#EAF3FC] text-[#2563A6]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1.5 3 4.500v4.700c0 4.300 2.900 8.100 7 9.300 4.100-1.200 7-5 7-9.300V4.500l-7-3zm3.700 6.700a1 1 0 0 0-1.400-1.400L9 10.100 7.700 8.800a1 1 0 0 0-1.400 1.400l2 2a1 1 0 0 0 1.400 0l4-4z" clip-rule="evenodd" /></svg>
                            </span>
                            Permisos y estado
                        </legend>

                        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="rol_idRol" class="mb-1 block text-sm font-semibold text-[#263548]">Rol</label>
                                <select
                                    id="rol_idRol"
                                    name="rol_idRol"
                                    required
                                    @error('rol_idRol') aria-invalid="true" aria-describedby="error-rol" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('rol_idRol') border-red-500 @enderror"
                                >
                                    <option value="">Selecciona un rol</option>

                                    @foreach ($roles as $rol)
                                        <option
                                            value="{{ $rol->idRol }}"
                                            @selected(old('rol_idRol') == $rol->idRol)
                                        >
                                            {{ $rol->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rol_idRol')
                                    <p id="error-rol" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="estado" class="mb-1 block text-sm font-semibold text-[#263548]">Estado</label>
                                <select
                                    id="estado"
                                    name="estado"
                                    required
                                    @error('estado') aria-invalid="true" aria-describedby="error-estado" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('estado') border-red-500 @enderror"
                                >
                                    <option value="1" @selected(old('estado', '1') == '1')>
                                        Activo
                                    </option>

                                    <option value="0" @selected(old('estado') == '0')>
                                        Inactivo
                                    </option>
                                </select>
                                @error('estado')
                                    <p id="error-estado" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Botones --}}
                    <div class="flex flex-col-reverse gap-3 bg-[#F5F8FC] px-6 py-5 sm:flex-row sm:items-center sm:justify-end sm:px-8">
                        <a
                            href="{{ route('usuarios.index') }}"
                            class="inline-flex h-12 items-center justify-center rounded-lg border border-[#C9D5E3] bg-white px-6 font-semibold text-[#263548] transition hover:bg-[#EAF3FC] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6] focus-visible:ring-offset-2 motion-reduce:transition-none"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            :disabled="cargando"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-[#2563A6] px-6 font-semibold text-white shadow-sm transition hover:bg-[#1E3A5F] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 motion-reduce:transition-none"
                        >
                            <svg x-show="cargando" x-cloak class="h-5 w-5 animate-spin motion-reduce:animate-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                            </svg>
                            <span x-show="!cargando">Guardar usuario</span>
                            <span x-show="cargando" x-cloak>Guardando…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>