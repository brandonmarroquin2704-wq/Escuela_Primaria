@php
    $lista = $roles->values();
    $total = $lista->count();

    // Texto de búsqueda de cada rol (nombre y descripción)
    $filas = $lista->map(fn ($r) => [
        'texto' => mb_strtolower($r->nombre . ' ' . ($r->descripcion ?? '')),
    ])->all();

    $filasJs = \Illuminate\Support\Js::from($filas);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            Catálogo de roles
        </h2>
    </x-slot>

    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">

            @if (session('success'))
                <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Encabezado --}}
            <header>
                <nav aria-label="Ruta de navegación" class="text-sm text-slate-600">
                    Gestión escolar <span aria-hidden="true">/</span>
                    <span class="font-semibold text-slate-800">Catálogo de roles</span>
                </nav>

                <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Catálogo de roles</h1>

                <p class="mt-1 text-base text-slate-700">
                    Administra los roles y permisos disponibles para los usuarios del sistema.
                </p>
            </header>

            {{-- Resumen --}}
            <section aria-label="Resumen de roles" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                    <p class="text-3xl font-extrabold text-slate-900">{{ $total }}</p>
                    <p class="mt-1 text-sm font-medium text-slate-700">Roles registrados</p>
                </div>
            </section>

            {{-- Tabla --}}
            <section
                aria-labelledby="titulo-tabla"
                class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm shadow-blue-900/5 sm:p-6"
                x-data="{
                    q: '',
                    filas: {{ $filasJs }},
                    coincide(i) {
                        return this.filas[i].texto.includes(this.q.trim().toLowerCase());
                    },
                    get visibles() {
                        return this.filas.filter((f, i) => this.coincide(i)).length;
                    }
                }"
            >
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 id="titulo-tabla" class="text-lg font-bold text-slate-900">Roles registrados</h2>
                        <p class="text-sm text-slate-600" aria-live="polite" x-text="visibles + (visibles === 1 ? ' rol encontrado' : ' roles encontrados')"></p>
                    </div>

                    <div class="relative w-full lg:w-80">
                        <label for="buscar-rol" class="sr-only">Buscar rol por nombre o descripción</label>
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 3.1 8.9l3.5 3.5a1 1 0 0 0 1.4-1.4l-3.5-3.5A5 5 0 0 0 8 3zM5 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0z" clip-rule="evenodd" />
                        </svg>
                        <input
                            id="buscar-rol"
                            type="search"
                            x-model="q"
                            placeholder="Buscar por nombre o descripción"
                            class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600"
                        >
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto rounded-lg border border-blue-100">
                    <table class="w-full min-w-[640px] text-left text-slate-900">
                        <caption class="sr-only">Catálogo de roles del sistema</caption>

                        <thead class="bg-blue-100 text-sm font-bold uppercase tracking-wide text-blue-950">
                            <tr>
                                <th scope="col" class="w-16 px-4 py-3">No.</th>
                                <th scope="col" class="w-1/4 px-4 py-3">Nombre</th>
                                <th scope="col" class="px-4 py-3">Descripción</th>
                                <th scope="col" class="w-44 px-4 py-3">Fecha de registro</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-blue-100">
                            @forelse ($lista as $i => $rol)
                                <tr x-show="coincide({{ $i }})" class="transition odd:bg-white even:bg-slate-50 hover:bg-blue-50">
                                    <td class="px-4 py-4 tabular-nums">{{ $loop->iteration }}</td>

                                    <td class="px-4 py-4">
                                        <span class="flex items-center gap-3">
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blue-50 text-blue-700" aria-hidden="true">
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM3 17a7 7 0 0 1 14 0v1H3v-1z" />
                                                </svg>
                                            </span>
                                            <span class="text-base font-bold">{{ $rol->nombre }}</span>
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-slate-700">{{ $rol->descripcion ?? 'Sin descripción' }}</td>

                                    <td class="px-4 py-4 tabular-nums text-slate-700">{{ $rol->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-slate-700">
                                        <p class="font-semibold">Todavía no hay roles registrados.</p>
                                    </td>
                                </tr>
                            @endforelse

                            @if ($total > 0)
                                <tr x-show="visibles === 0" x-cloak>
                                    <td colspan="4" class="px-4 py-10 text-center text-slate-700">
                                        <p class="font-semibold">No encontramos roles que coincidan con tu búsqueda.</p>
                                        <button
                                            type="button"
                                            @click="q = ''"
                                            class="mt-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                        >
                                            Limpiar búsqueda
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>