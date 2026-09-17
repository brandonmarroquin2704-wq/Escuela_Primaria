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

                <div class="mb-5">
                    <a
                        href="{{ route('periodos-escolares.create') }}"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        Registrar periodo escolar
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">ID</th>
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
                                    <td class="border px-3 py-2">
                                        {{ $periodo->idPeriodo }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $periodo->nombre }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $periodo->cicloEscolar }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $periodo->fechaInicio->format('d/m/Y') }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $periodo->fechaFin->format('d/m/Y') }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $periodo->estado ? 'Activo' : 'Inactivo' }}
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