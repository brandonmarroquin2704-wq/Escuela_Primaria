<nav
    x-data="{ menuAbierto: window.innerWidth >= 768, gestionAbierta: true }"
    @resize.window="menuAbierto = window.innerWidth >= 768"
    class="min-h-screen bg-stone-100"
>
    @php
        // Usuario en sesión y nombre de su rol
        $miUsuario = auth()->user()?->usuario;
        $rolNombre = $miUsuario?->rol?->nombre;

        // Clase para marcar el elemento activo del menú
        $activa = 'bg-crema-oscuro shadow-sm';

        // Clases de un enlace; se resalta si $activo es true
        $link = fn ($activo, $extra = '') => "block rounded-lg px-4 py-3 text-lg font-semibold transition hover:bg-crema-oscuro $extra " . ($activo ? $activa : '');

        // Enlaces principales: [ruta, patrón para resaltar, texto, icono opcional]
        $enlaces = [
            ['dashboard', 'dashboard', 'Dashboard', '⌂'],
            ...match ($rolNombre) {
                'Docente' => [
                    ['docente.solicitudes.index', 'docente.solicitudes.*', 'Solicitudes de acceso'],
                    ['docente.calificaciones.index', 'docente.calificaciones.*', 'Capturar calificaciones'],
                ],
                'Tutor' => [
                    ['tutor.solicitudes.index', 'tutor.solicitudes.*', 'Buscar a mi hijo(a)'],
                ],
                default => [],
            },
            ['escuelas.show', 'escuelas.*', 'Información de la escuela'],
        ];

        // Hijos activos del tutor (solo si el rol es Tutor)
        $hijos = $rolNombre === 'Tutor'
            ? \App\Models\TutorAlumno::with('alumno')
                ->where('usuario_idUsuario', $miUsuario->idUsuario)
                ->where('estado', true)
                ->whereHas('alumno', fn ($q) => $q->where('estado', true))
                ->get()
            : collect();

        // Módulos de gestión (nombre del permiso => prefijo de ruta) que el usuario puede consultar
        $enlacesGestion = collect([
            'Periodos escolares' => 'periodos-escolares',
            'Roles' => 'roles',
            'Usuarios' => 'usuarios',
            'Asignaturas' => 'asignaturas',
            'Grupos' => 'grupos',
            'Alumnos' => 'alumnos',
            'Tutores y alumnos' => 'tutor-alumnos',
            'Asignación de materias' => 'asignaciones-docentes',
        ])->filter(fn ($prefijo, $modulo) => $miUsuario?->tienePermiso($modulo, 'Consultar'));
    @endphp

    {{-- Menú lateral --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-80 flex-col border-r border-stone-300 bg-crema text-black shadow-xl transition-transform duration-300"
        :class="menuAbierto ? 'translate-x-0' : '-translate-x-full'"
    >
        {{-- Nombre de la escuela --}}
        <div class="border-b border-stone-300 px-6 py-7">
            <a href="{{ route('dashboard') }}" class="block">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-neutral-600">Sistema escolar</p>
                <h1 class="mt-2 text-2xl font-extrabold leading-tight text-black">
                    Primaria<br>21 De Octubre
                </h1>
            </a>
        </div>

        {{-- Enlaces de navegación --}}
        <div class="flex-1 space-y-2 overflow-y-auto px-4 py-6">
            @foreach ($enlaces as $enlace)
                <a href="{{ route($enlace[0]) }}" class="{{ $link(request()->routeIs($enlace[1]), 'flex items-center gap-3') }}">
                    @if (isset($enlace[3]))
                        <span>{{ $enlace[3] }}</span>
                    @endif
                    {{ $enlace[2] }}
                </a>
            @endforeach

            {{-- Boletas de los hijos (solo tutores) --}}
            @if ($hijos->isNotEmpty())
                <p class="px-4 pt-3 text-sm font-semibold uppercase tracking-wider text-neutral-600">
                    Boletas de mis hijos
                </p>

                @foreach ($hijos as $vinculo)
                    <a
                        href="{{ route('tutor.boleta.show', $vinculo->alumno_idAlumno) }}"
                        class="{{ $link(request()->routeIs('tutor.boleta.show') && (int) request()->route('alumno') === (int) $vinculo->alumno_idAlumno, 'truncate') }}"
                    >
                        {{ $vinculo->alumno->nombre }} {{ $vinculo->alumno->apellidoPaterno }}
                    </a>
                @endforeach
            @endif

            {{-- Gestión escolar (desplegable, según permisos) --}}
            @if ($enlacesGestion->isNotEmpty())
                <div>
                    <button
                        type="button"
                        @click="gestionAbierta = !gestionAbierta"
                        class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left text-lg font-semibold transition hover:bg-crema-oscuro"
                    >
                        <span class="flex items-center gap-3"><span>▦</span> Gestión escolar</span>
                        <span class="transition-transform duration-200" :class="{ 'rotate-180': gestionAbierta }">⌄</span>
                    </button>

                    <div x-show="gestionAbierta" x-transition class="mt-1 space-y-1 border-l border-stone-300 pl-3">
                        @foreach ($enlacesGestion as $modulo => $prefijo)
                            <a
                                href="{{ route($prefijo . '.index') }}"
                                class="block rounded-lg px-4 py-2 text-lg text-neutral-800 transition hover:bg-crema-oscuro {{ request()->routeIs($prefijo . '.*') ? $activa . ' font-bold text-black' : '' }}"
                            >
                                {{ $modulo }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.*'), 'border border-stone-300') }}">
                Mi cuenta
            </a>
        </div>

        {{-- Usuario en sesión y cierre de sesión --}}
        <div class="border-t border-stone-300 p-4">
            <div class="mb-3 rounded-lg bg-crema-oscuro px-4 py-3">
                <p class="truncate text-base font-semibold text-black">{{ Auth::user()->name }}</p>
                <p class="mt-1 text-sm text-neutral-600">Sesión activa</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-500 px-4 py-3 text-lg font-bold text-white transition hover:bg-red-600"
                >
                    <span>↪</span> Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Fondo oscuro que cierra el menú al tocarlo (solo móvil) --}}
    <div
        x-show="menuAbierto"
        x-transition.opacity
        @click="menuAbierto = false"
        class="fixed inset-0 z-30 bg-slate-950/50 md:hidden"
    ></div>

    {{-- Contenido principal; se recorre cuando el menú está abierto --}}
    <div class="min-h-screen transition-all duration-300" :class="menuAbierto ? 'md:pl-80' : 'md:pl-0'">
        {{-- Barra superior fija --}}
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
            <div class="flex h-20 items-center gap-4 px-4 sm:px-6 lg:px-8">
                {{-- Botón para abrir o cerrar el menú --}}
                <button
                    type="button"
                    @click="menuAbierto = !menuAbierto"
                    class="rounded-lg bg-stone-700 px-3 py-2 text-xl font-bold text-white shadow transition hover:bg-stone-900"
                    aria-label="Abrir o cerrar menú"
                >
                    ☰
                </button>

                {{-- NOMBRE DE LA ESCUELA --}}
                <div class="min-w-0 flex-1">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h2 class="truncate text-lg font-bold text-slate-800">Primaria 21 De Octubre</h2>
                    @endisset
                </div>

                {{-- Fecha actual --}}
                <div class="hidden text-right sm:block">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Sistema de control escolar</p>
                    <p class="text-sm font-semibold text-slate-700">{{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </header>

        <main>{{ $slot }}</main>
    </div>
</nav>