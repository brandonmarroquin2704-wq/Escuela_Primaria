<nav
    x-data="{ menuAbierto: window.innerWidth >= 768, gestionAbierta: true }"
    @resize.window="menuAbierto = window.innerWidth >= 768"
    class="min-h-screen bg-slate-100"
>
    @php
        $miUsuario = auth()->user()?->usuario;

        // Módulo (permiso) => prefijo de la ruta
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

    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-80 flex-col bg-gradient-to-b from-blue-800 via-blue-700 to-indigo-900 text-white shadow-2xl transition-transform duration-300"
        :class="menuAbierto ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="border-b border-blue-500 px-6 py-7">
            <a href="{{ route('dashboard') }}" class="block">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-blue-200">
                    Sistema escolar
                </p>

                <h1 class="mt-2 text-2xl font-extrabold leading-tight">
                    Primaria<br>
                    21 De Octubre
                </h1>
            </a>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6">
            <a
                href="{{ route('dashboard') }}"
                class="mb-2 flex items-center gap-3 rounded-lg px-4 py-3 text-lg font-semibold transition hover:bg-white/15 {{ request()->routeIs('dashboard') ? 'bg-white/20 shadow' : '' }}"
            >
                <span>⌂</span>
                Dashboard
            </a>

            @if ($enlacesGestion->isNotEmpty())
                <button
                    type="button"
                    @click="gestionAbierta = !gestionAbierta"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left text-lg font-semibold transition hover:bg-white/15"
                >
                    <span class="flex items-center gap-3">
                        <span>▦</span>
                        Gestión escolar
                    </span>

                    <span
                        class="transition-transform duration-200"
                        :class="{ 'rotate-180': gestionAbierta }"
                    >
                        ⌄
                    </span>
                </button>

                <div
                    x-show="gestionAbierta"
                    x-transition
                    class="mt-1 space-y-1 border-l border-blue-400/60 pl-3"
                >
                    @foreach ($enlacesGestion as $modulo => $prefijo)
                        <a
                            href="{{ route($prefijo . '.index') }}"
                            class="block rounded-lg px-4 py-2 text-lg text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs($prefijo . '.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                        >
                            {{ $modulo }}
                        </a>
                    @endforeach
                </div>
            @endif

            <a
                href="{{ route('profile.edit') }}"
                class="mt-3 block rounded-lg bg-white/10 px-4 py-3 text-lg font-semibold transition hover:bg-white/20 {{ request()->routeIs('profile.*') ? 'bg-white/20 shadow' : '' }}"
            >
                Mi cuenta
            </a>
        </div>

        <div class="border-t border-blue-500 p-4">
            <div class="mb-3 rounded-lg bg-white/10 px-4 py-3">
                <p class="truncate text-base font-semibold">
                    {{ Auth::user()->name }}
                </p>

                <p class="mt-1 text-sm text-blue-200">
                    Sesión activa
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-500 px-4 py-3 text-lg font-bold text-white transition hover:bg-red-600"
                >
                    <span>↪</span>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <div
        x-show="menuAbierto"
        x-transition.opacity
        @click="menuAbierto = false"
        class="fixed inset-0 z-30 bg-slate-950/50 md:hidden"
    ></div>

    <div
        class="min-h-screen transition-all duration-300"
        :class="menuAbierto ? 'md:pl-80' : 'md:pl-0'"
    >
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
            <div class="flex h-20 items-center gap-4 px-4 sm:px-6 lg:px-8">
                <button
                    type="button"
                    @click="menuAbierto = !menuAbierto"
                    class="rounded-lg bg-blue-700 px-3 py-2 text-xl font-bold text-white shadow transition hover:bg-blue-800"
                    aria-label="Abrir o cerrar menú"
                >
                    ☰
                </button>

                <div class="min-w-0 flex-1">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h2 class="truncate text-lg font-bold text-slate-800">
                            Primaria 21 De Octubre
                        </h2>
                    @endisset
                </div>

                <div class="hidden text-right sm:block">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                        Sistema de control escolar
                    </p>
                    <p class="text-sm font-semibold text-slate-700">
                        {{ now()->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>
    </div>
</nav>