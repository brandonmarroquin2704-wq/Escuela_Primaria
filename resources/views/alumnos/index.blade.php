<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Catálogo de alumnos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a
                    href="{{ route('alumnos.create') }}"
                    style="background-color: #2563eb; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                >
                    Registrar alumno
                </a>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-6">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-3 py-2 text-left">ID</th>
                                <th class="border px-3 py-2 text-left">Nombre completo</th>
                                <th class="border px-3 py-2 text-left">Fecha de nacimiento</th>
                                <th class="border px-3 py-2 text-left">Grupo</th>
                                <th class="border px-3 py-2 text-left">Estado</th>
                                <th class="border px-3 py-2 text-left">Fecha de registro</th>
                                <th class="border px-3 py-2 text-left">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($alumnos as $alumno)
                                <tr>
                                    <td class="border px-3 py-2">
                                        {{ $alumno->idAlumno }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $alumno->nombre }}
                                        {{ $alumno->apellidoPaterno }}
                                        {{ $alumno->apellidoMaterno }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $alumno->fNac?->format('d/m/Y') }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        @if ($alumno->grupo)
                                            {{ $alumno->grupo->grado }}° {{ $alumno->grupo->letraGrupo }}
                                            ({{ $alumno->grupo->cicloEsc }})
                                        @else
                                            Sin grupo
                                        @endif
                                    </td>

                                    <td class="border px-3 py-2">
                                        @if ($alumno->estado)
                                            <span class="font-semibold text-green-700">
                                                Activo
                                            </span>
                                        @else
                                            <span class="font-semibold text-red-700">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $alumno->fechaRegistro?->format('d/m/Y') }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        <div class="flex gap-2">
                                            <a
                                                href="{{ route('alumnos.edit', ['alumno' => $alumno->idAlumno]) }}"
                                                style="background-color: #eab308; color: #000000; padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('alumnos.destroy', ['alumno' => $alumno->idAlumno]) }}"
                                                onsubmit="return confirm('¿Deseas eliminar este alumno?');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    style="background-color: #dc2626; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-weight: 600; border: none;"
                                                >
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="border px-3 py-4 text-center text-gray-500">
                                        No hay alumnos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

