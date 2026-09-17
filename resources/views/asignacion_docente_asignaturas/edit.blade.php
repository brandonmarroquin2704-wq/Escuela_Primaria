<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar asignación de materia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form
                    method="POST"
                    action="{{ route('asignaciones-docentes.update', ['asignaciones_docente' => $asignacion->id]) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                                        @selected(old('grupo_idGrupo', $asignacion->grupo_idGrupo) == $grupo->idGrupo)
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
                            <label for="asignatura_idAsignatura" class="block text-sm font-medium text-gray-700">
                                Asignatura
                            </label>

                            <select
                                id="asignatura_idAsignatura"
                                name="asignatura_idAsignatura"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona una asignatura</option>

                                @foreach ($asignaturas as $asignatura)
                                    <option
                                        value="{{ $asignatura->idAsignatura }}"
                                        @selected(old('asignatura_idAsignatura', $asignacion->asignatura_idAsignatura) == $asignatura->idAsignatura)
                                    >
                                        {{ $asignatura->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            @error('asignatura_idAsignatura')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="docente_idUsuario" class="block text-sm font-medium text-gray-700">
                                Docente que imparte la asignatura
                            </label>

                            <select
                                id="docente_idUsuario"
                                name="docente_idUsuario"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona un docente</option>

                                @foreach ($docentes as $docente)
                                    <option
                                        value="{{ $docente->idUsuario }}"
                                        @selected(old('docente_idUsuario', $asignacion->docente_idUsuario) == $docente->idUsuario)
                                    >
                                        {{ $docente->nombre }}
                                        {{ $docente->apellidoPaterno }}
                                        {{ $docente->apellidoMaterno }}
                                    </option>
                                @endforeach
                            </select>

                            @error('docente_idUsuario')
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
                                    @selected(old('estado', $asignacion->estado ? '1' : '0') == '1')
                                >
                                    Activa
                                </option>

                                <option
                                    value="0"
                                    @selected(old('estado', $asignacion->estado ? '1' : '0') == '0')
                                >
                                    Inactiva
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
                            Actualizar asignación
                        </button>

                        <a
                            href="{{ route('asignaciones-docentes.index') }}"
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