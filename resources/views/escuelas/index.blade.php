<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Escuelas registradas
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
                        href="{{ route('escuelas.create') }}"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        Registrar escuela
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">ID</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Clave CCT</th>
                                <th class="border px-3 py-2 text-left">Municipio</th>
                                <th class="border px-3 py-2 text-left">Teléfono</th>
                                <th class="border px-3 py-2 text-left">Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($escuelas as $escuela)
                                <tr>
                                    <td class="border px-3 py-2">
                                        {{ $escuela->idEscuela }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $escuela->nombre }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $escuela->claveCentroTrabajo }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $escuela->municipio }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $escuela->telefono }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $escuela->estado ? 'Activa' : 'Inactiva' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border px-3 py-4 text-center text-gray-500">
                                        No hay escuelas registradas todavía.
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