<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Asignaturas registradas
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
                        href="{{ route('asignaturas.create') }}"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        Registrar asignatura
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">ID</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Descripción</th>
                                <th class="border px-3 py-2 text-left">Estado</th>
                                <th class="border px-3 py-2 text-left">Fecha de registro</th>
                                <th class="border px-3 py-2 text-left">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($asignaturas as $asignatura)
                                <tr>
                                    <td class="border px-3 py-2">
                                        {{ $asignatura->idAsignatura }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $asignatura->nombre }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $asignatura->descripcion ?? 'Sin descripción' }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $asignatura->estado ? 'Activa' : 'Inactiva' }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $asignatura->fechaRegistro?->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="border px-3 py-2">
 <td class="border px-3 py-2">
    <div class="flex gap-2">
        <a
            href="{{ route('asignaturas.edit', $asignatura) }}"
            style="background-color: #c5c5c6; color: #000000; padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none;"
        >
            Editar
        </a>

        <form
            method="POST"
            action="{{ route('asignaturas.destroy', $asignatura) }}"
            onsubmit="return confirm('¿Deseas eliminar esta asignatura?');"
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
                                    <td colspan="6" class="border px-3 py-4 text-center text-gray-500">
                                        No hay asignaturas registradas todavía.
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