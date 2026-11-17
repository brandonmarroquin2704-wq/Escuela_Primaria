@php
    // Permisos del usuario en sesión sobre este módulo
    $yo = auth()->user()->usuario;
    $puedeRegistrar = $yo->tienePermiso('Asignaturas', 'Registrar');
    $puedeEditar = $yo->tienePermiso('Asignaturas', 'Editar');
    $puedeEliminar = $yo->tienePermiso('Asignaturas', 'Eliminar');
    $hayAcciones = $puedeEditar || $puedeEliminar;
    $columnas = $hayAcciones ? 6 : 5;

    // Totales generales (no dependen de la página ni del filtro)
    $totalGeneral = \App\Models\Asignatura::count();
    $totalActivas = \App\Models\Asignatura::where('estado', true)->count();
    $totalInactivas = $totalGeneral - $totalActivas;

    $tarjetas = [
        ['etiqueta' => 'Asignaturas registradas', 'numero' => $totalGeneral, 'icono' => 'bg-blue-50 text-blue-700', 'color' => 'text-slate-900'],
        ['etiqueta' => 'Asignaturas activas', 'numero' => $totalActivas, 'icono' => 'bg-green-50 text-green-700', 'color' => 'text-green-700'],
        ['etiqueta' => 'Asignaturas inactivas', 'numero' => $totalInactivas, 'icono' => 'bg-slate-100 text-slate-600', 'color' => 'text-slate-700'],
    ];

    // Filtros activos (para los mensajes de la tabla)
    $hayFiltros = request()->filled('buscar') || request()->filled('estado');
    $encontradas = $asignaturas->total();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            Asignaturas registradas
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
            <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <nav aria-label="Ruta de navegación" class="text-sm text-slate-600">
                        Gestión escolar <span aria-hidden="true">/</span>
                        <span class="font-semibold text-slate-800">Asignaturas</span>
                    </nav>

                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Asignaturas registradas</h1>

                    <p class="mt-1 text-base text-slate-700">
                        Consulta y administra las asignaturas disponibles para la comunidad escolar.
                    </p>
                </div>

                @if ($puedeRegistrar)
                    <a
                        href="{{ route('asignaturas.create') }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-reduce:transition-none"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1z" />
                        </svg>
                        Registrar asignatura
                    </a>
                @endif
            </header>

            {{-- Resumen --}}
            <section aria-label="Resumen de asignaturas" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @foreach ($tarjetas as $tarjeta)
                    <div class="flex items-center gap-4 rounded-xl border border-blue-100 bg-white p-5 shadow-sm transition hover:shadow-md motion-reduce:transition-none">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full {{ $tarjeta['icono'] }}" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 4.8C7.7 3.7 5.9 3 4 3H2.5A1.5 1.5 0 0 0 1 4.5v10A1.5 1.5 0 0 0 2.5 16H4c1.9 0 3.7.7 5 1.8V4.8zM11 17.8c1.3-1.1 3.1-1.8 5-1.8h1.5a1.5 1.5 0 0 0 1.5-1.5v-10A1.5 1.5 0 0 0 17.5 3H16c-1.9 0-3.7.7-5 1.8v13z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-3xl font-extrabold {{ $tarjeta['color'] }}">{{ $tarjeta['numero'] }}</p>
                            <p class="text-sm font-medium text-slate-700">{{ $tarjeta['etiqueta'] }}</p>
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- Búsqueda en vivo: pide la misma ruta al servidor y actualiza solo los resultados --}}
            <div
                class="space-y-6"
                x-data="{
                    q: {{ \Illuminate\Support\Js::from((string) request('buscar', '')) }},
                    est: {{ \Illuminate\Support\Js::from((string) request('estado', '')) }},
                    cargando: false,
                    error: false,
                    seq: 0,
                    get filtrosActivos() {
                        return (this.q.trim() !== '' ? 1 : 0) + (this.est !== '' ? 1 : 0);
                    },
                    limpiar() {
                        this.q = '';
                        this.est = '';
                        this.buscar();
                    },
                    async buscar() {
                        const turno = ++this.seq;
                        this.cargando = true;
                        const params = new URLSearchParams();
                        if (this.q.trim() !== '') params.set('buscar', this.q.trim());
                        if (this.est !== '') params.set('estado', this.est);
                        const url = '{{ route('asignaturas.index') }}' + (params.toString() ? '?' + params.toString() : '');
                        try {
                            const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            if (!r.ok) throw new Error('fallo');
                            const doc = new DOMParser().parseFromString(await r.text(), 'text/html');
                            const nuevo = doc.getElementById('resultados');
                            if (!nuevo) throw new Error('sin resultados');
                            if (turno !== this.seq) return;
                            document.getElementById('resultados').innerHTML = nuevo.innerHTML;
                            history.replaceState(null, '', url);
                            this.error = false;
                        } catch (e) {
                            if (turno === this.seq) this.error = true;
                        }
                        if (turno === this.seq) this.cargando = false;
                    }
                }"
            >
                {{-- Buscador y filtro --}}
                <section aria-labelledby="titulo-filtros" class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="titulo-filtros" class="text-lg font-bold text-slate-900">Buscar asignaturas</h2>

                        <span
                            x-show="filtrosActivos > 0"
                            x-cloak
                            class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-900"
                            x-text="filtrosActivos + (filtrosActivos === 1 ? ' filtro activo' : ' filtros activos')"
                        ></span>
                    </div>

                    <form
                        method="GET"
                        action="{{ route('asignaturas.index') }}"
                        class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-[2fr_1fr_auto_auto] lg:items-end"
                        @submit.prevent="buscar()"
                    >
                        <div>
                            <label for="buscar" class="mb-1 flex items-center justify-between text-sm font-semibold text-slate-800">
                                <span>Nombre de la asignatura</span>
                                <span x-show="cargando" x-cloak class="text-xs font-medium text-blue-700">Buscando…</span>
                            </label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 3.1 8.9l3.5 3.5a1 1 0 0 0 1.4-1.4l-3.5-3.5A5 5 0 0 0 8 3zM5 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0z" clip-rule="evenodd" />
                                </svg>
                                <input
                                    id="buscar"
                                    type="search"
                                    name="buscar"
                                    x-model="q"
                                    @input.debounce.300ms="buscar()"
                                    autocomplete="off"
                                    placeholder="Buscar por nombre de la asignatura"
                                    class="h-11 w-full rounded-lg border-slate-300 pl-10 pr-3 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="estado" class="mb-1 block text-sm font-semibold text-slate-800">Estado</label>
                            <select
                                id="estado"
                                name="estado"
                                x-model="est"
                                @change="buscar()"
                                class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600"
                            >
                                <option value="">Todos los estados</option>
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>

                        <button
                            type="submit"
                            :disabled="cargando"
                            class="h-11 rounded-lg bg-blue-700 px-5 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 motion-reduce:transition-none"
                        >
                            Buscar asignaturas
                        </button>

                        <a
                            href="{{ route('asignaturas.index') }}"
                            x-show="filtrosActivos > 0"
                            x-cloak
                            @click.prevent="limpiar()"
                            class="inline-flex h-11 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-4 font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
                        >
                            Limpiar filtros
                        </a>
                    </form>

                    <p x-show="error" x-cloak role="alert" class="mt-3 text-sm font-semibold text-red-700">
                        No fue posible actualizar los resultados. Intenta nuevamente.
                    </p>
                </section>

                {{-- Tabla --}}
                <section id="resultados" aria-labelledby="titulo-tabla" class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm shadow-blue-900/5 sm:p-6">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <h2 id="titulo-tabla" class="text-lg font-bold text-slate-900">Asignaturas registradas</h2>
                        <p class="text-sm font-medium text-slate-700" aria-live="polite">
                            {{ $encontradas }} {{ $encontradas === 1 ? 'asignatura encontrada' : 'asignaturas encontradas' }}
                        </p>
                    </div>

                    <div class="mt-4 overflow-x-auto rounded-lg border border-blue-100">
                        <table class="w-full min-w-[820px] text-left text-slate-900">
                            <caption class="sr-only">Lista de asignaturas</caption>

                            <thead class="bg-blue-100 text-sm font-bold uppercase tracking-wide text-blue-950">
                                <tr>
                                    <th scope="col" class="w-16 px-4 py-3">No.</th>
                                    <th scope="col" class="w-1/4 px-4 py-3">Nombre</th>
                                    <th scope="col" class="px-4 py-3">Descripción</th>
                                    <th scope="col" class="px-4 py-3">Estado</th>
                                    <th scope="col" class="w-44 px-4 py-3">Fecha de registro</th>
                                    @if ($hayAcciones)
                                        <th scope="col" class="w-48 px-4 py-3">Acciones</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-blue-100">
                                @forelse ($asignaturas as $asignatura)
                                    <tr class="transition odd:bg-white even:bg-slate-50 hover:bg-blue-50 motion-reduce:transition-none">
                                        {{-- Número consecutivo que continúa entre páginas --}}
                                        <td class="px-4 py-4 tabular-nums">{{ $asignaturas->firstItem() + $loop->index }}</td>

                                        <td class="px-4 py-4">
                                            <span class="flex items-center gap-3">
                                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blue-50 text-blue-700" aria-hidden="true">
                                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9 4.8C7.7 3.7 5.9 3 4 3H2.5A1.5 1.5 0 0 0 1 4.5v10A1.5 1.5 0 0 0 2.5 16H4c1.9 0 3.7.7 5 1.8V4.8zM11 17.8c1.3-1.1 3.1-1.8 5-1.8h1.5a1.5 1.5 0 0 0 1.5-1.5v-10A1.5 1.5 0 0 0 17.5 3H16c-1.9 0-3.7.7-5 1.8v13z" />
                                                    </svg>
                                                </span>
                                                <span class="font-bold">{{ $asignatura->nombre }}</span>
                                            </span>
                                        </td>

                                        <td class="px-4 py-4 text-slate-700">
                                            <span class="line-clamp-2" title="{{ $asignatura->descripcion }}">{{ $asignatura->descripcion ?? 'Sin descripción' }}</span>
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($asignatura->estado)
                                                <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 ring-1 ring-green-200">
                                                    <span class="h-2 w-2 rounded-full bg-green-600" aria-hidden="true"></span>
                                                    Activa
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800 ring-1 ring-red-200">
                                                    <span class="h-2 w-2 rounded-full bg-red-600" aria-hidden="true"></span>
                                                    Inactiva
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 tabular-nums text-slate-700">{{ $asignatura->fechaRegistro?->format('d/m/Y H:i') ?? '—' }}</td>

                                        @if ($hayAcciones)
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-2">
                                                    @if ($puedeEditar)
                                                        <a
                                                            href="{{ route('asignaturas.edit', $asignatura) }}"
                                                            aria-label="Editar la asignatura {{ $asignatura->nombre }}"
                                                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
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

                                                            <button
                                                                type="submit"
                                                                aria-label="Eliminar la asignatura {{ $asignatura->nombre }}"
                                                                class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-700 transition hover:bg-red-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 motion-reduce:transition-none"
                                                            >
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
                                        <td colspan="{{ $columnas }}" class="px-4 py-10 text-center text-slate-700">
                                            @if ($hayFiltros)
                                                <p class="font-semibold">No encontramos asignaturas que coincidan con tu búsqueda.</p>
                                                <a
                                                    href="{{ route('asignaturas.index') }}"
                                                    class="mt-3 inline-block rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                                >
                                                    Limpiar filtros
                                                </a>
                                            @else
                                                <p class="font-semibold">Todavía no hay asignaturas registradas.</p>
                                                @if ($puedeRegistrar)
                                                    <a
                                                        href="{{ route('asignaturas.create') }}"
                                                        class="mt-3 inline-block rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                                    >
                                                        Registrar primera asignatura
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-4">{{ $asignaturas->links() }}</div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>