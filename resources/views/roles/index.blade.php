<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Catálogo de roles
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-6">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-3 py-2 text-left">No.</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Descripción</th>
                                <th class="border px-3 py-2 text-left">Fecha de registro</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($roles as $rol)
                                <tr>
                                    <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                    <td class="border px-3 py-2">{{ $rol->nombre }}</td>
                                    <td class="border px-3 py-2">{{ $rol->descripcion ?? 'Sin descripción' }}</td>
                                    <td class="border px-3 py-2">{{ $rol->created_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border px-3 py-4 text-center text-gray-500">
                                        No hay roles registrados.
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