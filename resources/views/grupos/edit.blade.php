<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar grupo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form
                    method="POST"
                    action="{{ route('grupos.update', ['grupo' => $grupo->idGrupo]) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="grado" class="block text-sm font-medium text-gray-700">
                                Grado
                            </label>

                            <select
                                id="grado"
                                name="grado"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona un grado</option>

                                @for ($grado = 1; $grado <= 6; $grado++)
                                    <option
                                        value="{{ $grado }}"
                                        @selected(old('grado', $grupo->grado) == $grado)
                                    >
                                        {{ $grado }}°
                                    </option>
                                @endfor
                            </select>

                            @error('grado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="letraGrupo" class="block text-sm font-medium text-gray-700">
                                Letra del grupo
                            </label>

                            <input
                                type="text"
                                id="letraGrupo"
                                name="letraGrupo"
                                value="{{ old('letraGrupo', $grupo->letraGrupo) }}"
                                maxlength="1"
                                required
                                style="text-transform: uppercase;"
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('letraGrupo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="cicloEsc" class="block text-sm font-medium text-gray-700">
                                Ciclo escolar
                            </label>

                            <input
                                type="text"
                                id="cicloEsc"
                                name="cicloEsc"
                                value="{{ old('cicloEsc', $grupo->cicloEsc) }}"
                                maxlength="25"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('cicloEsc')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="idMaestro" class="block text-sm font-medium text-gray-700">
                                Maestro responsable
                            </label>

                            <select
                                id="idMaestro"
                                name="idMaestro"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona un docente</option>

                                @foreach ($maestros as $maestro)
                                    <option
                                        value="{{ $maestro->idUsuario }}"
                                        @selected(old('idMaestro', $grupo->idMaestro) == $maestro->idUsuario)
                                    >
                                        {{ $maestro->nombre }}
                                        {{ $maestro->apellidoPaterno }}
                                        {{ $maestro->apellidoMaterno }}
                                    </option>
                                @endforeach
                            </select>

                            @error('idMaestro')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="escuela_idEscuela" class="block text-sm font-medium text-gray-700">
                                Escuela
                            </label>

                            <select
                                id="escuela_idEscuela"
                                name="escuela_idEscuela"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona una escuela</option>

                                @foreach ($escuelas as $escuela)
                                    <option
                                        value="{{ $escuela->idEscuela }}"
                                        @selected(old('escuela_idEscuela', $grupo->escuela_idEscuela) == $escuela->idEscuela)
                                    >
                                        {{ $escuela->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            @error('escuela_idEscuela')
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
                                    @selected(old('estado', $grupo->estado ? '1' : '0') == '1')
                                >
                                    Activo
                                </option>

                                <option
                                    value="0"
                                    @selected(old('estado', $grupo->estado ? '1' : '0') == '0')
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
                            Actualizar grupo
                        </button>

                        <a
                            href="{{ route('grupos.index') }}"
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