@php
    $lista = $usuarios->values();
    $total = $lista->count();

    $coloresAvatar = [
        'bg-blue-100 text-blue-800',
        'bg-emerald-100 text-emerald-800',
        'bg-violet-100 text-violet-800',
        'bg-sky-100 text-sky-800',
        'bg-rose-100 text-rose-800',
        'bg-teal-100 text-teal-800',
    ];

    $coloresRol = [
        'bg-blue-50 text-blue-900 ring-blue-200',
        'bg-violet-50 text-violet-900 ring-violet-200',
        'bg-sky-50 text-sky-900 ring-sky-200',
        'bg-teal-50 text-teal-900 ring-teal-200',
        'bg-indigo-50 text-indigo-900 ring-indigo-200',
    ];

    // Datos ya calculados de cada fila
    $info = $lista->map(function ($u) use ($coloresAvatar, $coloresRol) {
        $nombre = trim(preg_replace('/\s+/', ' ', $u->nombre . ' ' . $u->apellidoPaterno . ' ' . $u->apellidoMaterno));
        $rol = $u->rol?->nombre ?? 'Sin rol';

        return [
            'nombre'   => $nombre,
            'iniciales' => mb_strtoupper(mb_substr($u->nombre, 0, 1) . mb_substr($u->apellidoPaterno, 0, 1)),
            'avatar'   => $coloresAvatar[$u->idUsuario % count($coloresAvatar)],
            'rol'      => $rol,
            'rolClase' => $u->rol ? $coloresRol[crc32($rol) % count($coloresRol)] : 'bg-slate-100 text-slate-800 ring-slate-300',
            'estado'   => $u->estado ? 'activo' : 'inactivo',
            'texto'    => mb_strtolower($nombre . ' ' . $u->correo),
        ];
    })->all();

    $activos = collect($info)->where('estado', 'activo')->count();
    $inactivos = collect($info)->where('estado', 'inactivo')->count();

    $nombresRoles = collect($info)->pluck('rol')->unique()->sort()->values()->all();

    $tarjetas = [
        ['etiqueta' => 'Usuarios totales', 'numero' => $total, 'icono' => 'bg-blue-50 text-blue-700', 'color' => 'text-slate-900'],
        ['etiqueta' => 'Usuarios activos', 'numero' => $activos, 'icono' => 'bg-green-50 text-green-700', 'color' => 'text-green-700'],
        ['etiqueta' => 'Usuarios inactivos', 'numero' => $inactivos, 'icono' => 'bg-slate-100 text-slate-600', 'color' => 'text-slate-700'],
    ];

    $miUsuario = auth()->user()?->usuario;
    $puedeRegistrar = (bool) $miUsuario?->tienePermiso('Usuarios', 'Registrar');
    $puedeEditar = (bool) $miUsuario?->tienePermiso('Usuarios', 'Editar');
    $puedeEliminar = (bool) $miUsuario?->tienePermiso('Usuarios', 'Activar/Inactivar');
    $hayAcciones = $puedeEditar || $puedeEliminar;
    $columnas = $hayAcciones ? 7 : 6;

    // Solo lo necesario para filtrar en el navegador
    $filas = collect($info)->map(fn ($f) => [
        'texto' => $f['texto'],
        'rol' => $f['rol'],
        'estado' => $f['estado'],
    ])->values()->all();
    $filasJs = \Illuminate\Support\Js::from($filas);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            Catálogo de usuarios
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
                        <span class="font-semibold text-slate-800">Catálogo de usuarios</span>
                    </nav>

                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Catálogo de usuarios</h1>

                    <p class="mt-1 text-base text-slate-700">
                        Administra los usuarios, roles y accesos de la comunidad escolar.
                    </p>
                </div>

                @if ($puedeRegistrar)
                    <a
                        href="{{ route('usuarios.create') }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-reduce:transition-none"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1z" />
                        </svg>
                        Registrar usuario
                    </a>
                @endif
            </header>

            {{-- Resumen --}}
            <section aria-label="Resumen de usuarios" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @foreach ($tarjetas as $tarjeta)
                    <div class="flex items-center gap-4 rounded-xl border border-blue-100 bg-white p-5 shadow-sm transition hover:shadow-md motion-reduce:transition-none">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full {{ $tarjeta['icono'] }}" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM3 17a7 7 0 0 1 14 0v1H3v-1z" />
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
                    rol: '',
                    est: '',
                    filas: {{ $filasJs }},
                    coincide(i) {
                        const f = this.filas[i];
                        return (this.rol === '' || f.rol === this.rol)
                            && (this.est === '' || f.estado === this.est)
                            && f.texto.includes(this.q.trim().toLowerCase());
                    },
                    get visibles() {
                        return this.filas.filter((f, i) => this.coincide(i)).length;
                    },
                    get activos() {
                        return (this.q.trim() !== '' ? 1 : 0) + (this.rol !== '' ? 1 : 0) + (this.est !== '' ? 1 : 0);
                    },
                    limpiar() {
                        this.q = '';
                        this.rol = '';
                        this.est = '';
                    }
                }"
            >
                {{-- Filtros --}}
                <section aria-labelledby="titulo-filtros" class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="titulo-filtros" class="text-lg font-bold text-slate-900">Buscar usuarios</h2>

                        <span
                            x-show="activos > 0"
                            x-cloak
                            class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-900"
                            x-text="activos + (activos === 1 ? ' filtro activo' : ' filtros activos')"
                        ></span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_auto] lg:items-end">
                        <div>
                            <label for="buscar-usuario" class="mb-1 block text-sm font-semibold text-slate-800">Nombre o correo</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 3.1 8.9l3.5 3.5a1 1 0 0 0 1.4-1.4l-3.5-3.5A5 5 0 0 0 8 3zM5 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0z" clip-rule="evenodd" />
                                </svg>
                                <input
                                    id="buscar-usuario"
                                    type="search"
                                    x-model="q"
                                    placeholder="Buscar por nombre o correo"
                                    class="h-11 w-full rounded-lg border-slate-300 pl-10 pr-3 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:ring-blue-600"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="filtro-rol" class="mb-1 block text-sm font-semibold text-slate-800">Rol</label>
                            <select id="filtro-rol" x-model="rol" class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                <option value="">Todos los roles</option>
                                @foreach ($nombresRoles as $nombreRol)
                                    <option value="{{ $nombreRol }}">{{ $nombreRol }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filtro-estado" class="mb-1 block text-sm font-semibold text-slate-800">Estado</label>
                            <select id="filtro-estado" x-model="est" class="h-11 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                <option value="">Todos los estados</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
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
                        <h2 id="titulo-tabla" class="text-lg font-bold text-slate-900">Usuarios registrados</h2>
                        <p class="text-sm font-medium text-slate-700" aria-live="polite" x-text="visibles + (visibles === 1 ? ' usuario encontrado' : ' usuarios encontrados')"></p>
                    </div>

                    <div class="mt-4 overflow-x-auto rounded-lg border border-blue-100">
                        <table class="w-full min-w-[900px] text-left text-slate-900">
                            <caption class="sr-only">Lista de usuarios del sistema</caption>

                            <thead class="bg-blue-100 text-sm font-bold uppercase tracking-wide text-blue-950">
                                <tr>
                                    <th scope="col" class="w-16 px-4 py-3">No.</th>
                                    <th scope="col" class="px-4 py-3">Nombre completo</th>
                                    <th scope="col" class="px-4 py-3">Correo</th>
                                    <th scope="col" class="px-4 py-3">Rol</th>
                                    <th scope="col" class="px-4 py-3">Estado</th>
                                    <th scope="col" class="px-4 py-3">Fecha de registro</th>
                                    @if ($hayAcciones)
                                        <th scope="col" class="w-52 px-4 py-3">Acciones</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-blue-100">
                                @forelse ($lista as $i => $usuario)
                                    <tr x-show="coincide({{ $i }})" class="transition odd:bg-white even:bg-slate-50 hover:bg-blue-50 motion-reduce:transition-none">
                                        <td class="px-4 py-4 tabular-nums">{{ $loop->iteration }}</td>

                                        <td class="px-4 py-4">
                                            <span class="flex items-center gap-3">
                                                <span
                                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-bold {{ $info[$i]['avatar'] }}"
                                                    role="img"
                                                    aria-label="Iniciales de {{ $info[$i]['nombre'] }}"
                                                >{{ $info[$i]['iniciales'] }}</span>
                                                <span class="font-bold">{{ $info[$i]['nombre'] }}</span>
                                            </span>
                                        </td>

                                        <td class="max-w-[16rem] truncate px-4 py-4 text-slate-700" title="{{ $usuario->correo }}">{{ $usuario->correo }}</td>

                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold ring-1 {{ $info[$i]['rolClase'] }}">
                                                {{ $info[$i]['rol'] }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($usuario->estado)
                                                <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 ring-1 ring-green-200">
                                                    <span class="h-2 w-2 rounded-full bg-green-600" aria-hidden="true"></span>
                                                    Activo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800 ring-1 ring-red-200">
                                                    <span class="h-2 w-2 rounded-full bg-red-600" aria-hidden="true"></span>
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 tabular-nums text-slate-700">{{ $usuario->fRegistro?->format('d/m/Y') ?? '—' }}</td>

                                        @if ($hayAcciones)
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-2">
                                                    @if ($puedeEditar)
                                                        <a
                                                            href="{{ route('usuarios.edit', $usuario->idUsuario) }}"
                                                            aria-label="Editar a {{ $info[$i]['nombre'] }}"
                                                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
                                                        >
                                                            Editar
                                                        </a>
                                                    @endif

                                                    @if ($puedeEliminar)
                                                        <form
                                                            method="POST"
                                                            action="{{ route('usuarios.destroy', $usuario->idUsuario) }}"
                                                            onsubmit="return confirm('¿Deseas eliminar este usuario?');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                aria-label="Eliminar a {{ $info[$i]['nombre'] }}"
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
                                            <p class="font-semibold">Todavía no hay usuarios registrados.</p>
                                            @if ($puedeRegistrar)
                                                <a href="{{ route('usuarios.create') }}" class="mt-3 inline-block rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                                                    Registrar primer usuario
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse

                                @if ($total > 0)
                                    <tr x-show="visibles === 0" x-cloak>
                                        <td colspan="{{ $columnas }}" class="px-4 py-10 text-center text-slate-700">
                                            <p class="font-semibold">No encontramos usuarios que coincidan con tu búsqueda.</p>
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
                </section>
            </div>
        </div>
    </div>
</x-app-layout>