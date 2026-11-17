@php
    $lista = $asignaciones->values();
    $total = $lista->count();
    $totalGeneral = method_exists($asignaciones, 'total') ? $asignaciones->total() : $total;

    $coloresDocente = [
        'bg-blue-100 text-blue-800',
        'bg-sky-100 text-sky-800',
        'bg-indigo-100 text-indigo-800',
    ];

    $nombreDe = function ($persona, $vacio) {
        if (! $persona) {
            return $vacio;
        }
        return trim(preg_replace('/\s+/', ' ', $persona->nombre . ' ' . $persona->apellidoPaterno . ' ' . $persona->apellidoMaterno));
    };

    $inicialesDe = function ($persona) {
        if (! $persona) {
            return '—';
        }
        return mb_strtoupper(mb_substr($persona->nombre, 0, 1) . mb_substr($persona->apellidoPaterno, 0, 1));
    };

    // Datos ya calculados de cada fila
    $info = $lista->map(function ($a) use ($coloresDocente, $nombreDe, $inicialesDe) {
        $g = $a->grupo;
        $docente = $nombreDe($a->docente, 'Sin docente');
        $asignatura = $a->asignatura?->nombre ?? 'Sin asignatura';
        $ciclo = $g ? str_replace('-', '–', (string) $g->cicloEsc) : '';

        return [
            'grupo'            => $g ? $g->grado . '° ' . $g->letraGrupo : 'Sin grupo',
            'ciclo'            => $ciclo,
            'grupoId'          => $g ? (string) $g->idGrupo : 'sin',
            'grupoEtiqueta'    => $g ? $g->grado . '° ' . $g->letraGrupo . ' · ' . $ciclo : 'Sin grupo',
            'cicloId'          => $ciclo !== '' ? $ciclo : 'sin',
            'asignatura'       => $asignatura,
            'docente'          => $docente,
            'docenteId'        => $a->docente_idUsuario ? (string) $a->docente_idUsuario : 'sin',
            'docenteIniciales' => $inicialesDe($a->docente),
            'docenteAvatar'    => $coloresDocente[((int) $a->docente_idUsuario) % count($coloresDocente)],
            'estado'           => $a->estado ? 'activa' : 'inactiva',
            'texto'            => $docente . ' ' . $asignatura,
        ];
    })->all();

    $coleccion = collect($info);
    $docentesDistintos = $lista->pluck('docente_idUsuario')->filter()->unique()->count();
    $materiasDistintas = $lista->pluck('asignatura_idAsignatura')->filter()->unique()->count();
    $gruposDistintos = $lista->pluck('grupo_idGrupo')->filter()->unique()->count();
    $activas = $lista->where('estado', true)->count();

    // Opciones de filtros tomadas de los datos
    $opcionesGrupo = $coleccion->unique('grupoId')->sortBy('grupoEtiqueta')->values()->all();
    $opcionesCiclo = $coleccion->where('ciclo', '!=', '')->unique('cicloId')->sortBy('ciclo')->values()->all();
    $opcionesDocente = $coleccion->unique('docenteId')->sortBy('docente')->values()->all();

    $tarjetas = [
        ['etiqueta' => 'Asignaciones registradas', 'numero' => $totalGeneral, 'icono' => 'bg-blue-50 text-blue-700'],
        ['etiqueta' => 'Docentes asignados', 'numero' => $docentesDistintos, 'icono' => 'bg-sky-50 text-sky-700'],
        ['etiqueta' => 'Materias cubiertas', 'numero' => $materiasDistintas, 'icono' => 'bg-emerald-50 text-emerald-700'],
        ['etiqueta' => 'Grupos atendidos', 'numero' => $gruposDistintos, 'icono' => 'bg-violet-50 text-violet-700'],
        ['etiqueta' => 'Asignaciones activas', 'numero' => $activas, 'icono' => 'bg-green-50 text-green-700'],
    ];

    $miUsuario = auth()->user()?->usuario;
    $puedeRegistrar = (bool) $miUsuario?->tienePermiso('Asignación de materias', 'Registrar');
    $puedeEditar = (bool) $miUsuario?->tienePermiso('Asignación de materias', 'Editar') && Route::has('asignaciones-docentes.edit');
    $puedeEliminar = (bool) $miUsuario?->tienePermiso('Asignación de materias', 'Activar/Inactivar') && Route::has('asignaciones-docentes.destroy');
    $hayAcciones = $puedeEditar || $puedeEliminar;
    $columnas = $hayAcciones ? 6 : 5;

    // Solo lo necesario para filtrar en el navegador
    $filas = $coleccion->map(fn ($f) => [
        'texto'   => $f['texto'],
        'grupo'   => $f['grupoId'],
        'ciclo'   => $f['cicloId'],
        'docente' => $f['docenteId'],
        'estado'  => $f['estado'],
    ])->values()->all();
    $filasJs = \Illuminate\Support\Js::from($filas);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            Asignación de materias a docentes
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
                        <span class="font-semibold text-slate-800">Asignaciones académicas</span>
                    </nav>

                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Asignación de materias a docentes</h1>

                    <p class="mt-1 text-base text-slate-700">
                        Administra las materias, docentes y grupos asignados en cada ciclo escolar.
                    </p>
                </div>

                @if ($puedeRegistrar)
                    <a
                        href="{{ route('asignaciones-docentes.create') }}"
                        aria-label="Crear una nueva asignación de materia"
                        title="Asignar una materia a un docente y un grupo"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 active:bg-blue-900 motion-reduce:transition-none"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1z" />
                        </svg>
                        <span class="hidden sm:inline">Asignar materia</span>
                        <span class="sm:hidden">Nueva asignación</span>
                    </a>
                @endif
            </header>

            {{-- Resumen --}}
            <section aria-label="Resumen de asignaciones" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($tarjetas as $tarjeta)
                    <div class="flex items-center gap-4 rounded-xl border border-blue-100 bg-white p-5 shadow-sm transition hover:shadow-md motion-reduce:transition-none">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full {{ $tarjeta['icono'] }}" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 4.8C7.9 4 6.4 3.6 4.5 3.6c-.9 0-1.8.1-2.5.3a1 1 0 0 0-.7 1v9.6c0 .6.6 1.1 1.2.9.6-.2 1.3-.2 2-.2 1.5 0 2.7.4 3.5 1V4.8zm2 0v10.2c.8-.6 2-1 3.5-1 .7 0 1.4 0 2 .2.6.2 1.2-.3 1.2-.9V4.9a1 1 0 0 0-.7-1c-.7-.2-1.6-.3-2.5-.3-1.9 0-3.4.4-4.5 1.2z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $tarjeta['numero'] }}</p>
                            <p class="text-sm font-medium text-slate-700">{{ $tarjeta['etiqueta'] }}</p>
                        </div>
                    </div>
                @endforeach
            </section>

            <div
                class="space-y-6"
                x-data="{
                    q: '',
                    grupo: '',
                    ciclo: '',
                    docente: '',
                    est: '',
                    filas: {{ $filasJs }},
                    norm(s) {
                        return s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                    },
                    coincide(i) {
                        const f = this.filas[i];
                        return (this.grupo === '' || f.grupo === this.grupo)
                            && (this.ciclo === '' || f.ciclo === this.ciclo)
                            && (this.docente === '' || f.docente === this.docente)
                            && (this.est === '' || f.estado === this.est)
                            && this.norm(f.texto).includes(this.norm(this.q.trim()));
                    },
                    get visibles() {
                        return this.filas.filter((f, i) => this.coincide(i)).length;
                    },
                    get activos() {
                        return (this.q.trim() !== '' ? 1 : 0) + (this.grupo !== '' ? 1 : 0) + (this.ciclo !== '' ? 1 : 0)
                            + (this.docente !== '' ? 1 : 0) + (this.est !== '' ? 1 : 0);
                    },
                    limpiar() {
                        this.q = '';
                        this.grupo = '';
                        this.ciclo = '';
                        this.docente = '';
                        this.est = '';
                    }
                }"
            >
                {{-- Filtros --}}
                <section aria-labelledby="titulo-filtros" class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="titulo-filtros" class="text-lg font-bold text-slate-900">Buscar asignaciones</h2>

                        <span
                            x-show="activos > 0"
                            x-cloak
                            class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-900"
                            x-text="activos + (activos === 1 ? ' filtro activo' : ' filtros activos')"
                        ></span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="buscar-asignacion" class="mb-1 block text-sm font-semibold text-slate-800">Docente o asignatura</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 3.1 8.9l3.5 3.5a1 1 0 0 0 1.4-1.4l-3.5-3.5A5 5 0 0 0 8 3zM5 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0z" clip-rule="evenodd" />
                                </svg>
                                <input
                                    id="buscar-asignacion"
                                    type="search"
                                    x-model="q"
                                    placeholder="Buscar por docente o asignatura"
                                    class="h-11 w-full rounded-lg border-slate-300 pl-10 pr-3 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="filtro-grupo" class="mb-1 block text-sm font-semibold text-slate-800">Grupo</label>
                            <select id="filtro-grupo" x-model="grupo" class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                <option value="">Todos los grupos</option>
                                @foreach ($opcionesGrupo as $opcion)
                                    <option value="{{ $opcion['grupoId'] }}">{{ $opcion['grupoEtiqueta'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filtro-ciclo" class="mb-1 block text-sm font-semibold text-slate-800">Ciclo escolar</label>
                            <select id="filtro-ciclo" x-model="ciclo" class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                <option value="">Todos los ciclos</option>
                                @foreach ($opcionesCiclo as $opcion)
                                    <option value="{{ $opcion['cicloId'] }}">{{ $opcion['ciclo'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filtro-docente" class="mb-1 block text-sm font-semibold text-slate-800">Docente</label>
                            <select id="filtro-docente" x-model="docente" class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                <option value="">Todos los docentes</option>
                                @foreach ($opcionesDocente as $opcion)
                                    <option value="{{ $opcion['docenteId'] }}">{{ $opcion['docente'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filtro-estado" class="mb-1 block text-sm font-semibold text-slate-800">Estado de la asignación</label>
                            <select id="filtro-estado" x-model="est" class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                <option value="">Todos</option>
                                <option value="activa">Activa</option>
                                <option value="inactiva">Inactiva</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                type="button"
                                @click="limpiar()"
                                :disabled="activos === 0"
                                class="h-11 w-full rounded-lg border border-blue-200 bg-blue-50 px-4 font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
                            >
                                Limpiar filtros
                            </button>
                        </div>
                    </div>
                </section>

                {{-- Tabla --}}
                <section aria-labelledby="titulo-tabla" class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm shadow-blue-900/5 sm:p-6">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 id="titulo-tabla" class="text-lg font-bold text-slate-900">Asignaciones registradas</h2>
                            <p class="text-sm text-slate-600">Cada asignatura se asigna una sola vez por grupo.</p>
                        </div>
                        <p class="text-sm font-medium text-slate-700" aria-live="polite" x-text="visibles + (visibles === 1 ? ' asignación encontrada' : ' asignaciones encontradas')"></p>
                    </div>

                    <div class="mt-4 overflow-x-auto rounded-lg border border-blue-100">
                        <table class="w-full min-w-[900px] text-left text-slate-900">
                            <caption class="sr-only">Lista de asignaciones de materias a docentes por grupo</caption>

                            <thead class="bg-blue-100 text-sm font-bold uppercase tracking-wide text-blue-950">
                                <tr>
                                    <th scope="col" class="w-16 px-4 py-3">No.</th>
                                    <th scope="col" class="px-4 py-3">Grupo</th>
                                    <th scope="col" class="w-1/4 px-4 py-3">Asignatura</th>
                                    <th scope="col" class="w-1/4 px-4 py-3">Docente</th>
                                    <th scope="col" class="px-4 py-3">Estado</th>
                                    @if ($hayAcciones)
                                        <th scope="col" class="w-48 px-4 py-3">Acciones</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-blue-100">
                                @forelse ($lista as $i => $asignacion)
                                    <tr x-show="coincide({{ $i }})" class="transition odd:bg-white even:bg-slate-50 hover:bg-blue-50 motion-reduce:transition-none">
                                        <td class="px-4 py-4 tabular-nums" title="ID de la asignación: {{ $asignacion->id }}">{{ $loop->iteration }}</td>

                                        <td class="px-4 py-4">
                                            @if ($asignacion->grupo)
                                                <span class="inline-flex flex-col">
                                                    <span class="w-fit rounded-lg bg-blue-50 px-2.5 py-1 text-base font-extrabold text-blue-900 ring-1 ring-blue-200">{{ $info[$i]['grupo'] }}</span>
                                                    <span class="mt-1 text-xs font-medium tabular-nums text-slate-600">Ciclo {{ $info[$i]['ciclo'] }}</span>
                                                </span>
                                            @else
                                                <span class="text-slate-600">Sin grupo</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-5 w-5 shrink-0 text-blue-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M9 4.8C7.9 4 6.4 3.6 4.5 3.6c-.9 0-1.8.1-2.5.3a1 1 0 0 0-.7 1v9.6c0 .6.6 1.1 1.2.9.6-.2 1.3-.2 2-.2 1.5 0 2.7.4 3.5 1V4.8zm2 0v10.2c.8-.6 2-1 3.5-1 .7 0 1.4 0 2 .2.6.2 1.2-.3 1.2-.9V4.9a1 1 0 0 0-.7-1c-.7-.2-1.6-.3-2.5-.3-1.9 0-3.4.4-4.5 1.2z" />
                                                </svg>
                                                <span class="text-base font-bold" title="{{ $info[$i]['asignatura'] }}">{{ $info[$i]['asignatura'] }}</span>
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="flex items-center gap-3">
                                                <span
                                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-bold {{ $info[$i]['docenteAvatar'] }}"
                                                    role="img"
                                                    aria-label="Iniciales del docente {{ $info[$i]['docente'] }}"
                                                >{{ $info[$i]['docenteIniciales'] }}</span>
                                                <span class="font-semibold" title="{{ $info[$i]['docente'] }}">{{ $info[$i]['docente'] }}</span>
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($asignacion->estado)
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

                                        @if ($hayAcciones)
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-2">
                                                    @if ($puedeEditar)
                                                        <a
                                                            href="{{ route('asignaciones-docentes.edit', ['id' => $asignacion->id]) }}"
                                                            aria-label="Editar la asignación de {{ $info[$i]['asignatura'] }} al grupo {{ $info[$i]['grupo'] }}"
                                                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
                                                        >
                                                            Editar
                                                        </a>
                                                    @endif

                                                    @if ($puedeEliminar)
                                                        <form
                                                            method="POST"
                                                            action="{{ route('asignaciones-docentes.destroy', ['id' => $asignacion->id]) }}"
                                                            onsubmit="return confirm('¿Deseas eliminar esta asignación?');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                aria-label="Eliminar la asignación de {{ $info[$i]['asignatura'] }} al grupo {{ $info[$i]['grupo'] }}"
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
                                            <p class="font-semibold">Todavía no hay asignaciones registradas.</p>
                                            @if ($puedeRegistrar)
                                                <a href="{{ route('asignaciones-docentes.create') }}" class="mt-3 inline-block rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                                                    Crear primera asignación
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse

                                @if ($total > 0)
                                    <tr x-show="visibles === 0" x-cloak>
                                        <td colspan="{{ $columnas }}" class="px-4 py-10 text-center text-slate-700">
                                            <p class="font-semibold">No encontramos asignaciones que coincidan con tu búsqueda.</p>
                                            <button
                                                type="button"
                                                @click="limpiar()"
                                                class="mt-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                            >
                                                Limpiar filtros
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if (method_exists($asignaciones, 'links'))
                        <div class="mt-4">{{ $asignaciones->links() }}</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>