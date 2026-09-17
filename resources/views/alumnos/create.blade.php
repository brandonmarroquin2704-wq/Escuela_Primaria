<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar alumno
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('alumnos.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                maxlength="20"
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
                                value="{{ old('apellidoPaterno') }}"
                                maxlength="35"
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
                                value="{{ old('apellidoMaterno') }}"
                                maxlength="45"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('apellidoMaterno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fNac" class="block text-sm font-medium text-gray-700">
                                Fecha de nacimiento
                            </label>

                            <input
                                type="date"
                                id="fNac"
                                name="fNac"
                                value="{{ old('fNac') }}"
                                max="{{ now()->subDay()->format('Y-m-d') }}"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('fNac')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="grupo_idGrupo" class="block text-sm font-medium text-gray-700">
                                Grupo
                            </label>

                            <select
                                id="grupo_idGrupo"
                                name="grupo_idGrupo"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona un grupo</option>

                                @foreach ($grupos as $grupo)
                                    <option
                                        value="{{ $grupo->idGrupo }}"
                                        @selected(old('grupo_idGrupo') == $grupo->idGrupo)
                                    >
                                        {{ $grupo->grado }}° {{ $grupo->letraGrupo }}
                                        — {{ $grupo->cicloEsc }}
                                    </option>
                                @endforeach
                            </select>

                            @error('grupo_idGrupo')
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
                                <option value="1" @selected(old('estado', '1') == '1')>
                                    Activo
                                </option>

                                <option value="0" @selected(old('estado') == '0')>
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
                            Guardar alumno
                        </button>

                        <a
                            href="{{ route('alumnos.index') }}"
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