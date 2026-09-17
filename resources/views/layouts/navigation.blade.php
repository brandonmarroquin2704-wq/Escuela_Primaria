<nav
    x-data="{ menuAbierto: window.innerWidth >= 768, gestionAbierta: true }"
    @resize.window="menuAbierto = window.innerWidth >= 768"
    class="min-h-screen bg-slate-100"
>
    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-gradient-to-b from-blue-800 via-blue-700 to-indigo-900 text-white shadow-2xl transition-transform duration-300"
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
                class="mb-2 flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition hover:bg-white/15 {{ request()->routeIs('dashboard') ? 'bg-white/20 shadow' : '' }}"
            >
                <span class="text-lg">⌂</span>
                Dashboard
            </a>

            <button
                type="button"
                @click="gestionAbierta = !gestionAbierta"
                class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left text-sm font-semibold transition hover:bg-white/15"
            >
                <span class="flex items-center gap-3">
                    <span class="text-lg">▦</span>
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
                <a
                    href="{{ route('escuelas.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('escuelas.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Escuelas
                </a>

                <a
                    href="{{ route('periodos-escolares.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('periodos-escolares.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Periodos escolares
                </a>

                <a
                    href="{{ route('roles.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('roles.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Roles
                </a>

                <a
                    href="{{ route('usuarios.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('usuarios.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Usuarios
                </a>

                <a
                    href="{{ route('asignaturas.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('asignaturas.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Asignaturas
                </a>

                <a
                    href="{{ route('grupos.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('grupos.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Grupos
                </a>

                <a
                    href="{{ route('alumnos.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('alumnos.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Alumnos
                </a>

                <a
                    href="{{ route('tutor-alumnos.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('tutor-alumnos.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Tutores y alumnos
                </a>

                <a
                    href="{{ route('asignaciones-docentes.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm text-blue-100 transition hover:bg-white/15 hover:text-white {{ request()->routeIs('asignaciones-docentes.*') ? 'bg-white/20 font-semibold text-white' : '' }}"
                >
                    Asignación de materias
                </a>
            </div>
        </div>

        <div class="border-t border-blue-500 p-4">
            <div class="mb-3 rounded-lg bg-white/10 px-4 py-3">
                <p class="truncate text-sm font-semibold">
                    {{ Auth::user()->name }}
                </p>

                <p class="mt-1 text-xs text-blue-200">
                    Sesión activa
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-red-600"
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
        :class="menuAbierto ? 'md:pl-72' : 'md:pl-0'"
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