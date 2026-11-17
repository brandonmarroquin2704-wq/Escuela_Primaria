@php
    $hoy = now()->startOfDay();

    $estadoDe = function ($p) use ($hoy) {
        if (! $p->estado) {
            return 'inactivo';
        }
        if ($p->fechaFin->lt($hoy)) {
            return 'finalizado';
        }
        return 'activo';
    };

    $estilos = [
        'activo'     => ['Activo',     'bg-green-100 text-green-800 ring-green-200',  'bg-green-600'],
        'proximo'    => ['Próximo',    'bg-blue-100 text-blue-900 ring-blue-200',     'bg-blue-600'],
        'finalizado' => ['Finalizado', 'bg-slate-200 text-slate-800 ring-slate-300',  'bg-slate-500'],
        'inactivo'   => ['Inactivo',   'bg-red-100 text-red-800 ring-red-200',        'bg-red-600'],
    ];

    $lista = $periodos->values();

    // Datos ya calculados de cada fila (estado, etiqueta y clases)
    $info = $lista->map(function ($p) use ($estadoDe, $estilos) {
        $e = $estadoDe($p);
        return [
            'estado'   => $e,
            'etiqueta' => $estilos[$e][0],
            'clases'   => $estilos[$e][1],
            'punto'    => $estilos[$e][2],
            'texto'    => mb_strtolower($p->nombre . ' ' . $p->cicloEscolar . ' ' . $p->fechaInicio->format('d/m/Y') . ' ' . $p->fechaFin->format('d/m/Y')),
        ];
    })->all();

    $total = $lista->count();
    $activos = collect($info)->where('estado', 'activo')->count();
    $finalizados = collect($info)->where('estado', 'finalizado')->count();

    $tarjetas = [
        ['etiqueta' => 'Periodos registrados', 'numero' => $total, 'color' => 'text-slate-900'],
        ['etiqueta' => 'Periodos activos', 'numero' => $activos, 'color' => 'text-green-700'],
        ['etiqueta' => 'Periodos finalizados', 'numero' => $finalizados, 'color' => 'text-slate-700'],
    ];

    $puedeEliminar = (bool) auth()->user()?->usuario?->tienePermiso('Periodos escolares', 'Activar/Inactivar');
    $columnas = $puedeEliminar ? 7 : 6;

    // Solo lo necesario para buscar y filtrar en el navegador
    $filas = collect($info)->map(fn ($f) => ['texto' => $f['texto'], 'estado' => $f['estado']])->values()->all();
    $filasJs = \Illuminate\Support\Js::from($filas);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            Periodos escolares
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

            <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <nav aria-label="Ruta de navegación" class="text-sm text-slate-600">
                        Gestión escolar <span aria-hidden="true">/</span>
                        <span class="font-semibold text-slate-800">Periodos escolares</span>
                    </nav>

                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Periodos escolares</h1>

                    <p class="mt-1 text-base text-slate-700">
                        Consulta, registra y administra los periodos académicos de la escuela.
                    </p>

                    <p class="mt-1 text-sm font-semibold text-blue-800">
                        {{ $total }} {{ $total === 1 ? 'periodo registrado' : 'periodos registrados' }}
                    </p>
                </div>

                <a
                    href="{{ route('periodos-escolares.create') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1z" />
                    </svg>
                    Registrar periodo escolar
                </a>
            </header>

            <section aria-label="Resumen de periodos" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @foreach ($tarjetas as $tarjeta)
                    <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                        <p class="text-3xl font-extrabold {{ $tarjeta['color'] }}">{{ $tarjeta['numero'] }}</p>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $tarjeta['etiqueta'] }}</p>
                    </div>
                @endforeach
            </section>

            <section
                aria-labelledby="titulo-tabla"
                class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm shadow-blue-900/5 sm:p-6"
                x-data="{
                    q: '',
                    est: 'todos',
                    filas: {{ $filasJs }},
                    coincide(i) {
                        const f = this.filas[i];
                        return (this.est === 'todos' || f.estado === this.est) && f.texto.includes(this.q.trim().toLowerCase());
                    },
                    get visibles() {
                        return this.filas.filter((f, i) => this.coincide(i)).length;
                    }
                }"
            >
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <h2 id="titulo-tabla" class="text-lg font-bold text-slate-900">Periodos registrados</h2>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative">
                            <label for="buscar-periodo" class="sr-only">Buscar periodo escolar</label>
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 3.1 8.9l3.5 3.5a1 1 0 0 0 1.4-1.4l-3.5-3.5A5 5 0 0 0 8 3zM5 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0z" clip-rule="evenodd" />
                            </svg>
                            <input
                                id="buscar-periodo"
                                type="search"
                                x-model="q"
                                placeholder="Buscar periodo..."
                                class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600 sm:w-64"
                            >
                        </div>

                        <div>
                            <label for="filtro-estado" class="sr-only">Filtrar por estado</label>
                            <select
                                id="filtro-estado"
                                x-model="est"
                                class="w-full rounded-lg border-slate-300 py-2 text-slate-900 focus:border-blue-600 focus:ring-blue-600 sm:w-44"
                            >
                                <option value="todos">Todos los estados</option>
                                <option value="activo">Activos</option>
                                <option value="finalizado">Finalizados</option>
                                <option value="inactivo">Inactivos</option>
                            </select>
                        </div>
                    </div>
                </div>

                <p class="sr-only" aria-live="polite" x-text="visibles + ' periodos encontrados'"></p>

                <div class="mt-4 overflow-x-auto rounded-lg border border-blue-100">
                    <table class="w-full min-w-[720px] text-left text-slate-900">
                        <caption class="sr-only">Lista de periodos escolares</caption>

                        <thead class="bg-blue-100 text-sm font-bold uppercase tracking-wide text-blue-950">
                            <tr>
                                <th scope="col" class="w-16 px-4 py-3">No.</th>
                                <th scope="col" class="w-1/3 px-4 py-3">Nombre</th>
                                <th scope="col" class="px-4 py-3">Ciclo escolar</th>
                                <th scope="col" class="px-4 py-3">Fecha de inicio</th>
                                <th scope="col" class="px-4 py-3">Fecha de fin</th>
                                <th scope="col" class="px-4 py-3">Estado</th>
                                @if ($puedeEliminar)
                                    <th scope="col" class="px-4 py-3 text-right">Acciones</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-blue-100">
                            @forelse ($lista as $i => $periodo)
                                <tr x-show="coincide({{ $i }})" class="transition odd:bg-white even:bg-slate-50 hover:bg-blue-50">
                                    <td class="px-4 py-4 tabular-nums">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-4 font-semibold">{{ $periodo->nombre }}</td>
                                    <td class="px-4 py-4">{{ $periodo->cicloEscolar }}</td>
                                    <td class="px-4 py-4 tabular-nums">{{ $periodo->fechaInicio->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4 tabular-nums">{{ $periodo->fechaFin->format('d/m/Y') }}</td>

                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold ring-1 {{ $info[$i]['clases'] }}">
                                            <span class="h-2 w-2 rounded-full {{ $info[$i]['punto'] }}" aria-hidden="true"></span>
                                            {{ $info[$i]['etiqueta'] }}
                                        </span>
                                    </td>

                                    @if ($puedeEliminar)
                                        <td class="px-4 py-4 text-right">
                                            <form method="POST" action="{{ route('periodos-escolares.destroy', $periodo) }}" onsubmit="return confirm('¿Eliminar este periodo escolar? Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" aria-label="Eliminar el periodo {{ $periodo->nombre }}" class="rounded-md px-3 py-1.5 text-sm font-semibold text-red-700 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $columnas }}" class="px-4 py-10 text-center text-slate-700">
                                        <p class="font-semibold">Aún no hay periodos escolares registrados.</p>
                                        <p class="mt-1 text-sm">Usa el botón «Registrar periodo escolar» para crear el primero.</p>
                                    </td>
                                </tr>
                            @endforelse

                            @if ($total > 0)
                                <tr x-show="visibles === 0" x-cloak>
                                    <td colspan="{{ $columnas }}" class="px-4 py-10 text-center text-slate-700">
                                        <p class="font-semibold">No se encontraron periodos con esos criterios.</p>
                                        <p class="mt-1 text-sm">Prueba con otra búsqueda o cambia el filtro de estado.</p>
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