@php
    $lista = $relaciones->values();
    $total = $lista->count();

    // Si el controlador pagina, el total general viene del paginador
    $totalGeneral = method_exists($relaciones, 'total') ? $relaciones->total() : $total;

    $coloresTutor = [
        'bg-blue-100 text-blue-800',
        'bg-sky-100 text-sky-800',
        'bg-indigo-100 text-indigo-800',
    ];

    $coloresAlumno = [
        'bg-emerald-100 text-emerald-800',
        'bg-teal-100 text-teal-800',
        'bg-green-100 text-green-800',
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
    $info = $lista->map(function ($r) use ($coloresTutor, $coloresAlumno, $nombreDe, $inicialesDe) {
        $g = $r->alumno?->grupo;
        $tutor = $nombreDe($r->tutor, 'Sin tutor');
        $alumno = $nombreDe($r->alumno, 'Sin alumno');

        return [
            'tutor'           => $tutor,
            'tutorIniciales'  => $inicialesDe($r->tutor),
            'tutorAvatar'     => $coloresTutor[$r->usuario_idUsuario % count($coloresTutor)],
            'alumno'          => $alumno,
            'alumnoIniciales' => $inicialesDe($r->alumno),
            'alumnoAvatar'    => $coloresAlumno[$r->alumno_idAlumno % count($coloresAlumno)],
            'grupo'           => $g ? $g->grado . '° ' . $g->letraGrupo : 'Sin grupo',
            'ciclo'           => $g ? str_replace('-', '–', (string) $g->cicloEsc) : '',
            'grupoId'         => $g ? (string) $g->idGrupo : 'sin',
            'grupoEtiqueta'   => $g ? $g->grado . '° ' . $g->letraGrupo . ' · ' . str_replace('-', '–', (string) $g->cicloEsc) : 'Sin grupo',
            'estado'          => $r->estado ? 'activa' : 'inactiva',
            'texto'           => $tutor . ' ' . $alumno,
        ];
    })->all();

    $coleccion = collect($info);
    $tutoresDistintos = $lista->pluck('usuario_idUsuario')->filter()->unique()->count();
    $alumnosDistintos = $lista->pluck('alumno_idAlumno')->filter()->unique()->count();
    $principales = $lista->where('esResponsable', true)->count();

    // Opciones del filtro de grupo, tomadas de los datos
    $opcionesGrupo = $coleccion->unique('grupoId')->sortBy('grupoEtiqueta')->values()->all();

    $tarjetas = [
        ['etiqueta' => 'Relaciones registradas', 'numero' => $totalGeneral, 'icono' => 'bg-blue-50 text-blue-700', 'color' => 'text-slate-900'],
        ['etiqueta' => 'Tutores relacionados', 'numero' => $tutoresDistintos, 'icono' => 'bg-sky-50 text-sky-700', 'color' => 'text-slate-900'],
        ['etiqueta' => 'Alumnos relacionados', 'numero' => $alumnosDistintos, 'icono' => 'bg-emerald-50 text-emerald-700', 'color' => 'text-slate-900'],
        ['etiqueta' => 'Responsables principales', 'numero' => $principales, 'icono' => 'bg-violet-50 text-violet-700', 'color' => 'text-slate-900'],
    ];

    $miUsuario = auth()->user()?->usuario;
    $puedeRegistrar = (bool) $miUsuario?->tienePermiso('Tutores y alumnos', 'Registrar');
    $puedeEditar = (bool) $miUsuario?->tienePermiso('Tutores y alumnos', 'Editar') && Route::has('tutor-alumnos.edit');
    $puedeEliminar = (bool) $miUsuario?->tienePermiso('Tutores y alumnos', 'Activar/Inactivar') && Route::has('tutor-alumnos.destroy');
    $hayAcciones = $puedeEditar || $puedeEliminar;
    $columnas = $hayAcciones ? 8 : 7;

    // Solo lo necesario para filtrar en el navegador
    $filas = $coleccion->map(fn ($f) => [
        'texto'  => $f['texto'],
        'grupo'  => $f['grupoId'],
        'estado' => $f['estado'],
    ])->values()->all();
    $filasJs = \Illuminate\Support\Js::from($filas);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            Tutores y alumnos
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
                        <span class="font-semibold text-slate-800">Tutores y alumnos</span>
                    </nav>

                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Tutores y alumnos</h1>

                    <p class="mt-1 text-base text-slate-700">
                        Administra las relaciones entre estudiantes y sus tutores responsables.
                    </p>
                </div>

                @if ($puedeRegistrar)
                    <a
                        href="{{ route('tutor-alumnos.create') }}"
                        aria-label="Relacionar tutor con alumno"
                        title="Relacionar un tutor con un alumno"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-reduce:transition-none"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1z" />
                        </svg>
                        <span class="hidden sm:inline">Relacionar tutor con alumno</span>
                        <span class="sm:hidden">Nueva relación</span>
                    </a>
                @endif
            </header>

            {{-- Resumen --}}
            <section aria-label="Resumen de relaciones" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tarjetas as $tarjeta)
                    <div class="flex items-center gap-4 rounded-xl border border-blue-100 bg-white p-5 shadow-sm transition hover:shadow-md motion-reduce:transition-none">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full {{ $tarjeta['icono'] }}" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM14.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM1.5 17a5.5 5.5 0 0 1 11 0v.5h-11V17zM13 17.5v-.5a6.5 6.5 0 0 0-1.3-3.9A4.5 4.5 0 0 1 18.5 17v.5H13z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-3xl font-extrabold {{ $tarjeta['color'] }}">{{ $tarjeta['numero'] }}</p>
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
                    est: '',
                    filas: {{ $filasJs }},
                    norm(s) {
                        return s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                    },
                    coincide(i) {
                        const f = this.filas[i];
                        return (this.grupo === '' || f.grupo === this.grupo)
                            && (this.est === '' || f.estado === this.est)
                            && this.norm(f.texto).includes(this.norm(this.q.trim()));
                    },
                    get visibles() {
                        return this.filas.filter((f, i) => this.coincide(i)).length;
                    },
                    get activos() {
                        return (this.q.trim() !== '' ? 1 : 0) + (this.grupo !== '' ? 1 : 0) + (this.est !== '' ? 1 : 0);
                    },
                    limpiar() {
                        this.q = '';
                        this.grupo = '';
                        this.est = '';
                    }
                }"
            >
                {{-- Filtros --}}
                <section aria-labelledby="titulo-filtros" class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="titulo-filtros" class="text-lg font-bold text-slate-900">Buscar relaciones</h2>

                        <span
                            x-show="activos > 0"
                            x-cloak
                            class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-900"
                            x-text="activos + (activos === 1 ? ' filtro activo' : ' filtros activos')"
                        ></span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-[2fr_1.4fr_1fr_auto] lg:items-end">
                        <div>
                            <label for="buscar-relacion" class="mb-1 block text-sm font-semibold text-slate-800">Tutor o alumno</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 3.1 8.9l3.5 3.5a1 1 0 0 0 1.4-1.4l-3.5-3.5A5 5 0 0 0 8 3zM5 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0z" clip-rule="evenodd" />
                                </svg>
                                <input
                                    id="buscar-relacion"
                                    type="search"
                                    x-model="q"
                                    placeholder="Buscar por nombre de tutor o alumno"
                                    class="h-11 w-full rounded-lg border-slate-300 pl-10 pr-3 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="filtro-grupo" class="mb-1 block text-sm font-semibold text-slate-800">Grupo del alumno</label>
                            <select id="filtro-grupo" x-model="grupo" class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                <option value="">Todos los grupos</option>
                                @foreach ($opcionesGrupo as $opcion)
                                    <option value="{{ $opcion['grupoId'] }}">{{ $opcion['grupoEtiqueta'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filtro-estado" class="mb-1 block text-sm font-semibold text-slate-800">Estado de la relación</label>
                            <select id="filtro-estado" x-model="est" class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                <option value="">Todos</option>
                                <option value="activa">Activa</option>
                                <option value="inactiva">Inactiva</option>
                            </select>
                        </div>

                        <div>
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
                            <h2 id="titulo-tabla" class="text-lg font-bold text-slate-900">Relaciones registradas</h2>
                            <p class="text-sm text-slate-600">Solo puede haber un responsable principal activo por alumno.</p>
                        </div>
                        <p class="text-sm font-medium text-slate-700" aria-live="polite" x-text="visibles + (visibles === 1 ? ' relación encontrada' : ' relaciones encontradas')"></p>
                    </div>

                    <div class="mt-4 overflow-x-auto rounded-lg border border-blue-100">
                        <table class="w-full min-w-[1000px] text-left text-slate-900">
                            <caption class="sr-only">Lista de relaciones entre tutores y alumnos</caption>

                            <thead class="bg-blue-100 text-sm font-bold uppercase tracking-wide text-blue-950">
                                <tr>
                                    <th scope="col" class="w-16 px-4 py-3">No.</th>
                                    <th scope="col" class="w-1/4 px-4 py-3">Tutor</th>
                                    <th scope="col" class="w-1/4 px-4 py-3">Alumno</th>
                                    <th scope="col" class="px-4 py-3">Grupo</th>
                                    <th scope="col" class="px-4 py-3">Parentesco</th>
                                    <th scope="col" class="px-4 py-3">Responsable</th>
                                    <th scope="col" class="px-4 py-3">Estado</th>
                                    @if ($hayAcciones)
                                        <th scope="col" class="w-48 px-4 py-3">Acciones</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-blue-100">
                                @forelse ($lista as $i => $relacion)
                                    <tr x-show="coincide({{ $i }})" class="transition odd:bg-white even:bg-slate-50 hover:bg-blue-50 motion-reduce:transition-none">
                                        <td class="px-4 py-4 tabular-nums">{{ $loop->iteration }}</td>

                                        <td class="px-4 py-4">
                                            <span class="flex items-center gap-3">
                                                <span
                                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-bold {{ $info[$i]['tutorAvatar'] }}"
                                                    role="img"
                                                    aria-label="Iniciales del tutor {{ $info[$i]['tutor'] }}"
                                                >{{ $info[$i]['tutorIniciales'] }}</span>
                                                <span class="font-bold" title="{{ $info[$i]['tutor'] }}">{{ $info[$i]['tutor'] }}</span>
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="flex items-center gap-3">
                                                <span
                                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-bold {{ $info[$i]['alumnoAvatar'] }}"
                                                    role="img"
                                                    aria-label="Iniciales del alumno {{ $info[$i]['alumno'] }}"
                                                >{{ $info[$i]['alumnoIniciales'] }}</span>
                                                <span class="font-bold" title="{{ $info[$i]['alumno'] }}">{{ $info[$i]['alumno'] }}</span>
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($relacion->alumno?->grupo)
                                                <span class="inline-flex flex-col">
                                                    <span class="w-fit rounded-lg bg-blue-50 px-2.5 py-1 text-base font-extrabold text-blue-900 ring-1 ring-blue-200">{{ $info[$i]['grupo'] }}</span>
                                                    <span class="mt-1 text-xs font-medium tabular-nums text-slate-600">{{ $info[$i]['ciclo'] }}</span>
                                                </span>
                                            @else
                                                <span class="text-slate-600">Sin grupo</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-800 ring-1 ring-slate-200">
                                                {{ $relacion->parentesco }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($relacion->esResponsable)
                                                <span class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-900 ring-1 ring-blue-200">
                                                    <span class="h-2 w-2 rounded-full bg-blue-600" aria-hidden="true"></span>
                                                    Principal
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700 ring-1 ring-slate-200">
                                                    Secundario
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($relacion->estado)
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
                                                            href="{{ route('tutor-alumnos.edit', ['id' => $relacion->id]) }}"
                                                            aria-label="Editar la relación de {{ $info[$i]['tutor'] }} con {{ $info[$i]['alumno'] }}"
                                                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
                                                        >
                                                            Editar
                                                        </a>
                                                    @endif

                                                    @if ($puedeEliminar)
                                                        <form
                                                            method="POST"
                                                            action="{{ route('tutor-alumnos.destroy', ['id' => $relacion->id]) }}"
                                                            onsubmit="return confirm('¿Deseas eliminar esta relación?');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                aria-label="Eliminar la relación de {{ $info[$i]['tutor'] }} con {{ $info[$i]['alumno'] }}"
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
                                            <p class="font-semibold">Todavía no hay relaciones entre tutores y alumnos.</p>
                                            @if ($puedeRegistrar)
                                                <a href="{{ route('tutor-alumnos.create') }}" class="mt-3 inline-block rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                                                    Crear primera relación
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse

                                @if ($total > 0)
                                    <tr x-show="visibles === 0" x-cloak>
                                        <td colspan="{{ $columnas }}" class="px-4 py-10 text-center text-slate-700">
                                            <p class="font-semibold">No encontramos relaciones que coincidan con tu búsqueda.</p>
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

                    {{-- Paginación (solo aparece si el controlador pagina) --}}
                    @if (method_exists($relaciones, 'links'))
                        <div class="mt-4">{{ $relaciones->links() }}</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>