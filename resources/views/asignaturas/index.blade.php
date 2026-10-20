@php
    // Permisos del usuario en sesión sobre este módulo
    $yo = auth()->user()->usuario;
    $puedeRegistrar = $yo->tienePermiso('Asignaturas', 'Registrar');
    $puedeEditar = $yo->tienePermiso('Asignaturas', 'Editar');
    $puedeEliminar = $yo->tienePermiso('Asignaturas', 'Eliminar');
    $hayAcciones = $puedeEditar || $puedeEliminar;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Asignaturas registradas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
            @endif

            {{-- Botón para registrar una asignatura (solo con permiso) --}}
            @if ($puedeRegistrar)
                <div class="mb-4">
                    <a
                        href="{{ route('asignaturas.create') }}"
                        class="inline-block rounded border-2 border-black bg-white px-4 py-2 font-semibold text-black transition hover:bg-stone-100"
                    >
                        Registrar asignatura
                    </a>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Buscador y filtro --}}
                    <form method="GET" action="{{ route('asignaturas.index') }}" class="mb-5 flex flex-wrap items-end gap-3">
                        <div class="min-w-[220px] flex-1">
                            <label for="buscar" class="block text-sm font-medium text-gray-700">Buscar</label>
                            <input id="buscar" type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o descripción"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select id="estado" name="estado" class="mt-1 block rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos</option>
                                <option value="1" @selected(request('estado') === '1')>Activa</option>
                                <option value="0" @selected(request('estado') === '0')>Inactiva</option>
                            </select>
                        </div>

                        <button type="submit" class="rounded border-2 border-black bg-white px-4 py-2 font-semibold text-black transition hover:bg-stone-100">
                            Buscar
                        </button>

                        @if (request()->hasAny(['buscar', 'estado']))
                            <a href="{{ route('asignaturas.index') }}" class="py-2 text-sm text-gray-600 underline hover:text-gray-900">Limpiar</a>
                        @endif
                    </form>

                    {{-- Tabla de asignaturas --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left">No.</th>
                                    <th class="border px-3 py-2 text-left">Nombre</th>
                                    <th class="border px-3 py-2 text-left">Descripción</th>
                                    <th class="border px-3 py-2 text-left">Estado</th>
                                    <th class="border px-3 py-2 text-left">Fecha de registro</th>
                                    @if ($hayAcciones)
                                        <th class="border px-3 py-2 text-left">Acciones</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($asignaturas as $asignatura)
                                    <tr>
                                        {{-- Número consecutivo que continúa entre páginas --}}
                                        <td class="border px-3 py-2">{{ $asignaturas->firstItem() + $loop->index }}</td>
                                        <td class="border px-3 py-2">{{ $asignatura->nombre }}</td>
                                        <td class="border px-3 py-2">{{ $asignatura->descripcion ?? 'Sin descripción' }}</td>

                                        {{-- Estado: verde si está activa, rojo si no --}}
                                        <td class="border px-3 py-2">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $asignatura->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $asignatura->estado ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </td>

                                        <td class="border px-3 py-2">{{ $asignatura->fechaRegistro?->format('d/m/Y H:i') }}</td>

                                        @if ($hayAcciones)
                                            <td class="border px-3 py-2">
                                                <div class="flex gap-2">
                                                    @if ($puedeEditar)
                                                        <a
                                                            href="{{ route('asignaturas.edit', $asignatura) }}"
                                                            class="inline-block rounded border-2 border-black bg-white px-3 py-1 text-sm font-semibold text-black transition hover:bg-stone-100"
                                                        >
                                                            Editar
                                                        </a>
                                                    @endif

                                                    @if ($puedeEliminar)
                                                        <form
                                                            method="POST"
                                                            action="{{ route('asignaturas.destroy', $asignatura) }}"
                                                            onsubmit="return confirm('¿Deseas eliminar esta asignatura?');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit" class="rounded border-2 border-red-600 bg-white px-3 py-1 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $hayAcciones ? 6 : 5 }}" class="border px-3 py-4 text-center text-gray-500">
                                            No se encontraron asignaturas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-4">{{ $asignaturas->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>