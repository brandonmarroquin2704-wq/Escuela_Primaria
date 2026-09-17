<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="min-h-[calc(100vh-5rem)] bg-slate-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <section
                class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-indigo-900 px-6 py-12 text-white shadow-xl sm:px-10 sm:py-16 lg:px-16"
            >
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-blue-200 sm:text-base">
                    Sistema de control escolar
                </p>

                <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">
                    Bienvenido a<br>
                    Primaria 21 De Octubre
                </h1>

                <p class="mt-6 max-w-2xl text-base leading-relaxed text-blue-100 sm:text-lg lg:text-xl">
                    Administra la información académica, los grupos, alumnos,
                    docentes y asignaturas desde un solo lugar.
                </p>

                <div class="mt-9 flex flex-wrap gap-3">
                    <a
                        href="{{ route('alumnos.index') }}"
                        class="rounded-lg bg-white px-5 py-3 text-sm font-bold text-blue-800 shadow transition hover:bg-blue-50"
                    >
                        Ver alumnos
                    </a>

                    <a
                        href="{{ route('grupos.index') }}"
                        class="rounded-lg border border-white/60 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/15"
                    >
                        Ver grupos
                    </a>
                </div>
            </section>

            <section class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <a
                    href="{{ route('usuarios.index') }}"
                    class="rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Gestión
                    </p>

                    <p class="mt-2 text-2xl font-extrabold text-blue-700">
                        Usuarios
                    </p>

                    <p class="mt-2 text-sm text-slate-600">
                        Administra cuentas y roles.
                    </p>
                </a>

                <a
                    href="{{ route('alumnos.index') }}"
                    class="rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Control escolar
                    </p>

                    <p class="mt-2 text-2xl font-extrabold text-blue-700">
                        Alumnos
                    </p>

                    <p class="mt-2 text-sm text-slate-600">
                        Consulta y registra estudiantes.
                    </p>
                </a>

                <a
                    href="{{ route('grupos.index') }}"
                    class="rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Organización
                    </p>

                    <p class="mt-2 text-2xl font-extrabold text-blue-700">
                        Grupos
                    </p>

                    <p class="mt-2 text-sm text-slate-600">
                        Gestiona grados y grupos escolares.
                    </p>
                </a>

                <a
                    href="{{ route('asignaciones-docentes.index') }}"
                    class="rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Académico
                    </p>

                    <p class="mt-2 text-2xl font-extrabold text-blue-700">
                        Materias
                    </p>

                    <p class="mt-2 text-sm text-slate-600">
                        Asigna docentes por grupo y materia.
                    </p>
                </a>
            </section>
        </div>
    </div>
</x-app-layout>