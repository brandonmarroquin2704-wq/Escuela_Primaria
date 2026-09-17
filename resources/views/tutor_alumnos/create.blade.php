<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Relacionar tutor con alumno
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('tutor-alumnos.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="usuario_idUsuario" class="block text-sm font-medium text-gray-700">
                                Tutor
                            </label>

                            <select
                                id="usuario_idUsuario"
                                name="usuario_idUsuario"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona un tutor</option>

                                @foreach ($tutores as $tutor)
                                    <option
                                        value="{{ $tutor->idUsuario }}"
                                        @selected(old('usuario_idUsuario') == $tutor->idUsuario)
                                    >
                                        {{ $tutor->nombre }}
                                        {{ $tutor->apellidoPaterno }}
                                        {{ $tutor->apellidoMaterno }}
                                    </option>
                                @endforeach
                            </select>

                            @error('usuario_idUsuario')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alumno_idAlumno" class="block text-sm font-medium text-gray-700">
                                Alumno
                            </label>

                            <select
                                id="alumno_idAlumno"
                                name="alumno_idAlumno"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona un alumno</option>

                                @foreach ($alumnos as $alumno)
                                    <option
                                        value="{{ $alumno->idAlumno }}"
                                        @selected(old('alumno_idAlumno') == $alumno->idAlumno)
                                    >
                                        {{ $alumno->nombre }}
                                        {{ $alumno->apellidoPaterno }}
                                        {{ $alumno->apellidoMaterno }}
                                        @if ($alumno->grupo)
                                            — {{ $alumno->grupo->grado }}° {{ $alumno->grupo->letraGrupo }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('alumno_idAlumno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="parentesco" class="block text-sm font-medium text-gray-700">
                                Parentesco
                            </label>

                            <select
                                id="parentesco"
                                name="parentesco"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="">Selecciona el parentesco</option>
                                <option value="Madre" @selected(old('parentesco') === 'Madre')>Madre</option>
                                <option value="Padre" @selected(old('parentesco') === 'Padre')>Padre</option>
                                <option value="Abuelo" @selected(old('parentesco') === 'Abuelo')>Abuelo</option>
                                <option value="Abuela" @selected(old('parentesco') === 'Abuela')>Abuela</option>
                                <option value="Tutor legal" @selected(old('parentesco') === 'Tutor legal')>Tutor legal</option>
                                <option value="Otro" @selected(old('parentesco') === 'Otro')>Otro</option>
                            </select>

                            @error('parentesco')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="esResponsable" class="block text-sm font-medium text-gray-700">
                                ¿Es responsable principal?
                            </label>

                            <select
                                id="esResponsable"
                                name="esResponsable"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="1" @selected(old('esResponsable', '1') == '1')>
                                    Sí
                                </option>

                                <option value="0" @selected(old('esResponsable') == '0')>
                                    No
                                </option>
                            </select>

                            @error('esResponsable')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">
                                Estado de la relación
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="1" @selected(old('estado', '1') == '1')>
                                    Activa
                                </option>

                                <option value="0" @selected(old('estado') == '0')>
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
                            Guardar relación
                        </button>

                        <a
                            href="{{ route('tutor-alumnos.index') }}"
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