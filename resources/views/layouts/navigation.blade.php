<nav
    x-data="{ menuAbierto: window.innerWidth >= 768, gestionAbierta: true }"
    @resize.window="menuAbierto = window.innerWidth >= 768"
    class="min-h-screen bg-[#F5F8FC]"
>
    @php
        // Usuario en sesión y nombre de su rol
        $miUsuario = auth()->user()?->usuario;
        $rolNombre = $miUsuario?->rol?->nombre;

        // Iconos
        $iconos = [
            'inicio'   => 'M10.7 2.3a1 1 0 0 0-1.4 0l-7 7A1 1 0 0 0 3 11h1v6a1 1 0 0 0 1 1h3v-4h4v4h3a1 1 0 0 0 1-1v-6h1a1 1 0 0 0 .7-1.7l-7-7z',
            'solicitud'=> 'M3 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8h-4l-1 2H8l-1-2H3V4zm0 10h3.4l1 2h5.2l1-2H17v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-2z',
            'lapiz'    => 'M13.600 2.400a2 2 0 0 1 2.800 2.800L7 14.600 3 15.500l.9-4 9.700-9.100z',
            'buscar'   => 'M8 3a5 5 0 1 0 3.100 8.900l3.500 3.500a1 1 0 0 0 1.400-1.400l-3.500-3.500A5 5 0 0 0 8 3zM5 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0z',
            'gestion'  => 'M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z',
            'usuario'  => 'M10 9a3.500 3.500 0 1 0 0-7 3.500 3.500 0 0 0 0 7zM3 17a7 7 0 0 1 14 0v.5H3V17z',
            'boleta'   => 'M5 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7.400a2 2 0 0 0-.6-1.400L12.600 2.600A2 2 0 0 0 11.200 2H5zm2 8h6v1.500H7V10zm0 3h6v1.500H7V13z',
            'salir'    => 'M3 4a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H5v10h5a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1V4zm10.300 2.300a1 1 0 0 1 1.400 0l3 3a1 1 0 0 1 0 1.400l-3 3a1 1 0 0 1-1.400-1.400L14.600 11H8a1 1 0 1 1 0-2h6.600l-1.300-1.300a1 1 0 0 1 0-1.400z',
        ];

        // Clases de un enlace; el activo lleva fondo azul claro e indicador lateral
        $link = fn ($activo, $extra = '') => 'relative flex items-center gap-3 rounded-lg px-4 py-3 text-base transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6] motion-reduce:transition-none '
            . ($activo
                ? 'bg-[#EAF3FC] font-bold text-[#1E3A5F] before:absolute before:inset-y-2 before:left-0 before:w-1 before:rounded-r before:bg-[#2563A6] '
                : 'font-semibold text-[#263548] hover:bg-[#F5F8FC] ')
            . $extra;

        // Enlaces principales: [ruta, patrón para resaltar, texto, icono]
        $enlaces = [
            ['dashboard', 'dashboard', 'Dashboard', 'inicio'],
            ...match ($rolNombre) {
                'Docente' => [
                    ['docente.solicitudes.index', 'docente.solicitudes.*', 'Solicitudes de acceso', 'solicitud'],
                    ['docente.calificaciones.index', 'docente.calificaciones.*', 'Capturar calificaciones', 'lapiz'],
                    ['docente.foro.index', 'docente.foro.*', 'Foro de avisos', 'solicitud'],
                ],
                'Tutor' => [
                    ['tutor.solicitudes.index', 'tutor.solicitudes.*', 'Buscar a mi hijo(a)', 'buscar'],
                    ['tutor.foro.index', 'tutor.foro.*', 'Avisos de los docentes', 'solicitud'],
                ],
                default => [],
            },
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
        class="fixed inset-y-0 left-0 z-40 flex w-80 max-w-[85vw] flex-col border-r border-[#DCE6F2] bg-white text-[#263548] shadow-xl transition-transform duration-300 motion-reduce:transition-none"
        :class="menuAbierto ? 'translate-x-0' : '-translate-x-full'"
        aria-label="Menú principal"
    >
        {{-- Nombre de la escuela --}}
        <div class="border-b border-[#DCE6F2] bg-[#F5F8FC] px-6 py-6">
            <a href="{{ route('dashboard') }}" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6]">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#667085]">Sistema escolar</p>
                <h1 class="mt-2 text-2xl font-extrabold leading-tight text-[#1E3A5F]">
                    Primaria<br>21 De Octubre
                </h1>
            </a>
        </div>

        {{-- Enlaces de navegación --}}
        <div class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
            @foreach ($enlaces as $enlace)
                <a
                    href="{{ route($enlace[0]) }}"
                    @click="if (window.innerWidth < 768) menuAbierto = false"
                    class="{{ $link(request()->routeIs($enlace[1])) }}"
                    @if (request()->routeIs($enlace[1])) aria-current="page" @endif
                >
                    <svg class="h-5 w-5 shrink-0 {{ request()->routeIs($enlace[1]) ? 'text-[#2563A6]' : 'text-[#667085]' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="{{ $iconos[$enlace[3]] }}" /></svg>
                    {{ $enlace[2] }}
                </a>
            @endforeach

            {{-- Boletas de los hijos (solo tutores) --}}
            @if ($hijos->isNotEmpty())
                <p class="px-4 pb-1 pt-5 text-xs font-bold uppercase tracking-wider text-[#667085]">
                    Boletas de mis hijos
                </p>

                @foreach ($hijos as $vinculo)
                    @php $boletaActiva = request()->routeIs('tutor.boleta.show') && (int) request()->route('alumno') === (int) $vinculo->alumno_idAlumno; @endphp
                    <a
                        href="{{ route('tutor.boleta.show', $vinculo->alumno_idAlumno) }}"
                        @click="if (window.innerWidth < 768) menuAbierto = false"
                        class="{{ $link($boletaActiva, 'truncate') }}"
                        @if ($boletaActiva) aria-current="page" @endif
                    >
                        <svg class="h-5 w-5 shrink-0 {{ $boletaActiva ? 'text-[#2563A6]' : 'text-[#667085]' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="{{ $iconos['boleta'] }}" /></svg>
                        <span class="truncate">{{ $vinculo->alumno->nombre }} {{ $vinculo->alumno->apellidoPaterno }}</span>
                    </a>
                @endforeach
            @endif

            {{-- Gestión escolar (desplegable, según permisos) --}}
            @if ($enlacesGestion->isNotEmpty())
                <div class="pt-2">
                    <button
                        type="button"
                        @click="gestionAbierta = !gestionAbierta"
                        :aria-expanded="gestionAbierta.toString()"
                        class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left text-base font-semibold text-[#263548] transition hover:bg-[#F5F8FC] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6] motion-reduce:transition-none"
                    >
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-[#667085]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="{{ $iconos['gestion'] }}" /></svg>
                            Gestión escolar
                        </span>
                        <svg class="h-4 w-4 text-[#667085] transition-transform duration-200 motion-reduce:transition-none" :class="{ 'rotate-180': gestionAbierta }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.300 7.300a1 1 0 0 1 1.400 0L10 10.600l3.300-3.300a1 1 0 1 1 1.400 1.400l-4 4a1 1 0 0 1-1.400 0l-4-4a1 1 0 0 1 0-1.400z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="gestionAbierta" x-transition class="mt-1 space-y-1 border-l-2 border-[#DCE6F2] pl-3 ml-6">
                        @foreach ($enlacesGestion as $modulo => $prefijo)
                            @php $moduloActivo = request()->routeIs($prefijo . '.*'); @endphp
                            <a
                                href="{{ route($prefijo . '.index') }}"
                                @click="if (window.innerWidth < 768) menuAbierto = false"
                                class="{{ $link($moduloActivo, '!py-2') }}"
                                @if ($moduloActivo) aria-current="page" @endif
                            >
                                {{ $modulo }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pt-2">
                @php $cuentaActiva = request()->routeIs('profile.*'); @endphp
                <a
                    href="{{ route('profile.edit') }}"
                    @click="if (window.innerWidth < 768) menuAbierto = false"
                    class="{{ $link($cuentaActiva) }}"
                    @if ($cuentaActiva) aria-current="page" @endif
                >
                    <svg class="h-5 w-5 shrink-0 {{ $cuentaActiva ? 'text-[#2563A6]' : 'text-[#667085]' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="{{ $iconos['usuario'] }}" /></svg>
                    Mi cuenta
                </a>
            </div>
        </div>

        {{-- Usuario en sesión y cierre de sesión --}}
        <div class="border-t border-[#DCE6F2] bg-[#F5F8FC] p-4">
            <div class="mb-3 rounded-lg bg-[#F0EEFA] px-4 py-3">
                <p class="truncate text-base font-semibold text-[#1E3A5F]">{{ Auth::user()->name }}</p>
                <p class="mt-1 flex items-center gap-2 text-sm text-[#667085]">
                    <span class="h-2 w-2 rounded-full bg-green-600" aria-hidden="true"></span>
                    Sesión activa
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-base font-bold text-red-700 transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 motion-reduce:transition-none"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="{{ $iconos['salir'] }}" /></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Fondo oscuro que cierra el menú al tocarlo (solo móvil) --}}
    <div
        x-show="menuAbierto"
        x-transition.opacity
        @click="menuAbierto = false"
        class="fixed inset-0 z-30 bg-[#1E3A5F]/50 md:hidden"
    ></div>

    {{-- Contenido principal; se recorre cuando el menú está abierto --}}
    <div class="min-h-screen transition-all duration-300 motion-reduce:transition-none" :class="menuAbierto ? 'md:pl-80' : 'md:pl-0'">
        {{-- Barra superior fija --}}
        <header class="sticky top-0 z-20 border-b border-[#DCE6F2] bg-white/95 shadow-sm backdrop-blur">
            <div class="flex h-20 items-center gap-4 px-4 sm:px-6 lg:px-8">
                {{-- Botón para abrir o cerrar el menú --}}
                <button
                    type="button"
                    @click="menuAbierto = !menuAbierto"
                    class="rounded-lg bg-[#1E3A5F] px-3 py-2 text-xl font-bold text-white shadow transition hover:bg-[#2563A6] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6] focus-visible:ring-offset-2 motion-reduce:transition-none"
                    aria-label="Abrir o cerrar menú"
                    :aria-expanded="menuAbierto.toString()"
                >
                    ☰
                </button>

                {{-- Título de la sección --}}
                <div class="min-w-0 flex-1">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h2 class="truncate text-lg font-bold text-[#1E3A5F]">Primaria 21 De Octubre</h2>
                    @endisset
                </div>

                {{-- Fecha actual --}}
                <div class="hidden text-right sm:block">
                    <p class="text-xs font-medium uppercase tracking-wider text-[#667085]">Sistema de control escolar</p>
                    <p class="text-sm font-semibold text-[#263548]">{{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </header>

        <main>{{ $slot }}</main>
    </div>
</nav>