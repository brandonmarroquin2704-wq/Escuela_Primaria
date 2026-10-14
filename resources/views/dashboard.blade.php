<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Panel de {{ $rol }}
        </h2>
    </x-slot>


    <div class="min-h-[calc(100vh-5rem)] bg-slate-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">

            {{-- NUEVO: avisos de éxito o error --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <section
                class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-indigo-900 px-6 py-12 text-white shadow-xl sm:px-10 sm:py-16 lg:px-16"
            >
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-blue-200 sm:text-base">
                    Sistema de control escolar
                </p>


                <div class="mt-5 rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-blue-200">
                            Sesión iniciada
                        </p>


                        <p class="mt-1 text-xl font-bold text-white sm:text-2xl">
                            Bienvenido: {{ $usuarioEscolar->nombre }}
                            {{ $usuarioEscolar->apellidoPaterno }}
                            {{ $usuarioEscolar->apellidoMaterno }}
                        </p>
                    </div>


                    <p class="mt-3 inline-flex w-fit rounded-full bg-white/20 px-4 py-2 text-sm font-bold text-white sm:mt-0">
                        ({{ $rol }})
                    </p>
                </div>


                <h1 class="mt-8 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">
                    Bienvenido a<br>
                    Primaria 21 De Octubre
                </h1>


                @if ($rol === 'Administrador')
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
                @elseif ($rol === 'Director')
                    <p class="mt-6 max-w-2xl text-base leading-relaxed text-blue-100 sm:text-lg lg:text-xl">
                        Consulta la información institucional y académica autorizada,
                        publica avisos y revisa los comentarios del sistema.
                    </p>


                    <p class="mt-8 text-sm font-semibold text-blue-100">
                        Las opciones correspondientes a Dirección se agregarán en el siguiente avance.
                    </p>
                @elseif ($rol === 'Docente')
                    <p class="mt-6 max-w-2xl text-base leading-relaxed text-blue-100 sm:text-lg lg:text-xl">
                        Consulta tus grupos asignados, atiende solicitudes de acceso al
                        salón virtual y gestiona la información académica autorizada.
                    </p>


                                        <div class="mt-9 flex flex-wrap gap-3">
                        <a href="{{ route('docente.solicitudes.index') }}"
                           class="rounded-lg bg-white px-5 py-3 text-sm font-bold text-blue-800 shadow transition hover:bg-blue-50">
                            Solicitudes de acceso
                        </a>

                        <a href="{{ route('docente.calificaciones.index') }}"
                           class="rounded-lg border border-white/60 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                            Capturar calificaciones
                        </a>
                    </div>


                @elseif ($rol === 'Tutor')
                    <p class="mt-6 max-w-2xl text-base leading-relaxed text-blue-100 sm:text-lg lg:text-xl">
                        Consulta el estado de tus solicitudes y la información escolar
                        autorizada de tus hijos.
                    </p>


                    <div class="mt-9">
                        <a
                            href="{{ route('tutor.solicitudes.index') }}"
                            class="rounded-lg bg-white px-5 py-3 text-sm font-bold text-blue-800 shadow transition hover:bg-blue-50"
                        >
                            Buscar a mi hijo(a)
                        </a>
                    </div>

                                    @if ($hijos->isNotEmpty())
                        <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($hijos as $vinculo)
                                <a href="{{ route('tutor.boleta.show', $vinculo->alumno_idAlumno) }}"
                                   class="rounded-xl border border-white/30 bg-white/10 p-4 transition hover:bg-white/20">
                                    <p class="text-lg font-bold text-white">
                                        {{ $vinculo->alumno->nombre }} {{ $vinculo->alumno->apellidoPaterno }}
                                    </p>
                                    <p class="text-sm text-blue-100">
                                        {{ $vinculo->alumno->grupo->grado }}° {{ $vinculo->alumno->grupo->letraGrupo }}
                                        · Ver boleta
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endif
            </section>


            {{-- NUEVO: datos de la escuela, debajo de la bienvenida --}}
            <x-escuela-info
                :escuela="$escuela"
                :puedeEditar="$puedeEditarEscuela"
                :puedeRegistrar="$usuarioEscolar->tienePermiso('Escuelas', 'Registrar')"
            />


            @if ($rol === 'Administrador')
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
            @endif
        </div>
    </div>
</x-app-layout>