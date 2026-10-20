<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Periodos escolares
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                @if (session('success'))
                    <div class="mb-4 rounded bg-green-100 p-3 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Botón para registrar un periodo --}}
                <div class="mb-5">
                    <a
                        href="{{ route('periodos-escolares.create') }}"
                        class="inline-block rounded border-2 border-black bg-white px-4 py-2 font-semibold text-black transition hover:bg-stone-100"
                    >
                        Registrar periodo escolar
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">No.</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Ciclo escolar</th>
                                <th class="border px-3 py-2 text-left">Fecha de inicio</th>
                                <th class="border px-3 py-2 text-left">Fecha de fin</th>
                                <th class="border px-3 py-2 text-left">Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($periodos as $periodo)
                                <tr>
                                    {{-- Número consecutivo, no el ID de la base de datos --}}
                                    <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                    <td class="border px-3 py-2">{{ $periodo->nombre }}</td>
                                    <td class="border px-3 py-2">{{ $periodo->cicloEscolar }}</td>
                                    <td class="border px-3 py-2">{{ $periodo->fechaInicio->format('d/m/Y') }}</td>
                                    <td class="border px-3 py-2">{{ $periodo->fechaFin->format('d/m/Y') }}</td>

                                    {{-- Estado: verde si está activo, rojo si no --}}
                                    <td class="border px-3 py-2">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $periodo->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $periodo->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border px-3 py-4 text-center text-gray-500">
                                        No hay periodos escolares registrados todavía.
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