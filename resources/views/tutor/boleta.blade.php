<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Boleta de {{ $hijo->nombre }} {{ $hijo->apellidoPaterno }}
            — {{ $hijo->grupo->grado }}° {{ $hijo->grupo->letraGrupo }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-6">

            <div class="rounded bg-blue-50 px-4 py-3 text-sm text-blue-900">
                Las calificaciones aparecen aquí cuando termina el periodo de revisión del docente.
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto p-6">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-3 py-2 text-left">Materia</th>
                            @foreach ($periodos as $p)
                                <th class="border px-3 py-2 text-center">{{ $p->nombre }}</th>
                            @endforeach
                            <th class="border px-3 py-2 text-center">Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($filas as $fila)
                            <tr>
                                <td class="border px-3 py-2 font-semibold">{{ $fila['nombre'] }}</td>

                                @foreach ($periodos as $p)
                                    <td class="border px-3 py-2 text-center">
                                        @if ($fila['notas']->has($p->idPeriodo))
                                            {{ number_format($fila['notas'][$p->idPeriodo]->valor, 1) }}
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="border px-3 py-2 text-center font-bold">
                                    @if ($fila['promedio'] !== null)
                                        {{ number_format($fila['promedio'], 1) }}
                                        @if ($fila['parcial'])
                                            <span class="text-xs font-normal text-yellow-700">(parcial)</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $periodos->count() + 2 }}" class="border px-3 py-4 text-center text-gray-500">
                                    Este grupo aún no tiene materias asignadas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if ($promedioGeneral !== null)
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td class="border px-3 py-2 font-bold" colspan="{{ $periodos->count() + 1 }}">
                                    Promedio general
                                </td>
                                <td class="border px-3 py-2 text-center font-bold text-blue-700">
                                    {{ number_format($promedioGeneral, 1) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <a href="{{ route('dashboard') }}" class="text-sm text-blue-700 underline">
                Volver al panel
            </a>
        </div>
    </div>
</x-app-layout>