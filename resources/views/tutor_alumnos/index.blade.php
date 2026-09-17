<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tutores y alumnos
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
                    href="{{ route('tutor-alumnos.create') }}"
                    style="background-color: #2563eb; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                >
                    Relacionar tutor con alumno
                </a>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-6">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-3 py-2 text-left">ID</th>
                                <th class="border px-3 py-2 text-left">Tutor</th>
                                <th class="border px-3 py-2 text-left">Alumno</th>
                                <th class="border px-3 py-2 text-left">Grupo</th>
                                <th class="border px-3 py-2 text-left">Parentesco</th>
                                <th class="border px-3 py-2 text-left">Responsable</th>
                                <th class="border px-3 py-2 text-left">Estado</th>
                                <th class="border px-3 py-2 text-left">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($relaciones as $relacion)
                                <tr>
                                    <td class="border px-3 py-2">
                                        {{ $relacion->id }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $relacion->tutor?->nombre }}
                                        {{ $relacion->tutor?->apellidoPaterno }}
                                        {{ $relacion->tutor?->apellidoMaterno }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $relacion->alumno?->nombre }}
                                        {{ $relacion->alumno?->apellidoPaterno }}
                                        {{ $relacion->alumno?->apellidoMaterno }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        @if ($relacion->alumno?->grupo)
                                            {{ $relacion->alumno->grupo->grado }}°
                                            {{ $relacion->alumno->grupo->letraGrupo }}
                                            ({{ $relacion->alumno->grupo->cicloEsc }})
                                        @else
                                            Sin grupo
                                        @endif
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $relacion->parentesco }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        @if ($relacion->esResponsable)
                                            <span class="font-semibold text-blue-700">
                                                Sí
                                            </span>
                                        @else
                                            <span class="text-gray-600">
                                                No
                                            </span>
                                        @endif
                                    </td>

                                    <td class="border px-3 py-2">
                                        @if ($relacion->estado)
                                            <span class="font-semibold text-green-700">
                                                Activa
                                            </span>
                                        @else
                                            <span class="font-semibold text-red-700">
                                                Inactiva
                                            </span>
                                        @endif
                                    </td>

                                    <td class="border px-3 py-2">
                                        <div class="flex gap-2">
                                            <a
                                                href="{{ route('tutor-alumnos.edit', ['tutor_alumno' => $relacion->id]) }}"
                                                style="background-color: #eab308; color: #000000; padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('tutor-alumnos.destroy', ['tutor_alumno' => $relacion->id]) }}"
                                                onsubmit="return confirm('¿Deseas eliminar esta relación?');"
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
                                    <td colspan="8" class="border px-3 py-4 text-center text-gray-500">
                                        No hay relaciones tutor-alumno registradas.
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