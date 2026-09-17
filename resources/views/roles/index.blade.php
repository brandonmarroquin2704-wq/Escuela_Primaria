<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Catálogo de roles
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
                    href="{{ route('roles.create') }}"
                    style="background-color: #2563eb; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                >
                    Registrar rol
                </a>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-6">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-3 py-2 text-left">ID</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Descripción</th>
                                <th class="border px-3 py-2 text-left">Fecha de registro</th>
                                <th class="border px-3 py-2 text-left">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($roles as $rol)
                                <tr>
                                    <td class="border px-3 py-2">
                                        {{ $rol->idRol }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $rol->nombre }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $rol->descripcion ?? 'Sin descripción' }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        {{ $rol->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        <div class="flex gap-2">
                                            <a
                                                href="{{ route('roles.edit', ['role' => $rol->idRol]) }}"
                                                style="background-color: #eab308; color: #000000; padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('roles.destroy', ['role' => $rol->idRol]) }}"
                                                onsubmit="return confirm('¿Deseas eliminar este rol?');"
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
                                    <td colspan="5" class="border px-3 py-4 text-center text-gray-500">
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