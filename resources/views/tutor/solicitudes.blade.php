@php
    $perfil = auth()->user()?->usuario;
    $nombreTutor = $perfil ? trim(preg_replace('/\s+/', ' ', $perfil->nombre . ' ' . $perfil->apellidoPaterno . ' ' . $perfil->apellidoMaterno)) : 'Tutor';
    $primerNombre = $perfil?->nombre ?? 'Tutor';
    $inicialesTutor = $perfil ? mb_strtoupper(mb_substr($perfil->nombre, 0, 1) . mb_substr($perfil->apellidoPaterno, 0, 1)) : 'T';

    $totalSolicitudes = $solicitudes->count();
    $pendientes = $solicitudes->where('estadoSolicitud', 'pendiente')->count();
    $aprobadas = $solicitudes->where('estadoSolicitud', 'aprobada')->count();
    $rechazadas = $solicitudes->where('estadoSolicitud', 'rechazada')->count();

    // Apariencia de cada estado: siempre con texto, nunca solo color
    $estados = [
        'pendiente' => [
            'texto' => 'Pendiente',
            'clase' => 'bg-amber-100 text-amber-900 ring-amber-200',
            'punto' => 'bg-amber-500',
            'ayuda' => 'Tu solicitud está en revisión. El docente del grupo la atenderá pronto.',
        ],
        'aprobada' => [
            'texto' => 'Aprobada',
            'clase' => 'bg-green-100 text-green-800 ring-green-200',
            'punto' => 'bg-green-600',
            'ayuda' => 'Tu solicitud fue aprobada.',
        ],
        'rechazada' => [
            'texto' => 'Rechazada',
            'clase' => 'bg-red-100 text-red-800 ring-red-200',
            'punto' => 'bg-red-600',
            'ayuda' => 'Tu solicitud fue rechazada. Consulta con la escuela para conocer más detalles.',
        ],
    ];
    $estadoPorDefecto = [
        'texto' => 'En revisión',
        'clase' => 'bg-blue-100 text-blue-900 ring-blue-200',
        'punto' => 'bg-blue-600',
        'ayuda' => 'Tu solicitud se está revisando.',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Buscar a mi hijo(a)
        </h2>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">

        {{-- Encabezado --}}
        <header>
            <nav aria-label="Ruta de navegación" class="text-sm text-slate-600">
                <a href="{{ route('dashboard') }}" class="font-medium text-blue-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">Inicio</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-slate-800">Buscar a mi hijo(a)</span>
            </nav>

            <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Buscar a mi hijo(a)</h1>
            <p class="mt-1 text-base text-slate-700">
                Hola, {{ $primerNombre }}. Consulta la información escolar de tus hijos desde un solo lugar.
            </p>
        </header>

        @if (session('status'))
            <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('busqueda'))
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-semibold text-red-700">
                {{ $errors->first('busqueda') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Columna principal --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Paso 1: grupo --}}
                <section aria-labelledby="titulo-grupo" class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex items-start gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-blue-50 text-blue-700" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM14.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM1.5 17a5.5 5.5 0 0 1 11 0v.5h-11V17zM13 17.5v-.5a6.5 6.5 0 0 0-1.3-3.9A4.5 4.5 0 0 1 18.5 17v.5H13z" />
                            </svg>
                        </span>
                        <div>
                            <h2 id="titulo-grupo" class="text-xl font-bold text-slate-900">1. ¿Qué grupo deseas consultar?</h2>
                            <p class="mt-1 text-slate-700">Selecciona el grupo donde está registrado tu hijo(a) para encontrarlo.</p>
                        </div>
                    </div>

                    @if ($grupos->isEmpty())
                        <p class="mt-6 rounded-lg bg-slate-50 px-4 py-3 text-slate-700">No tienes grupos disponibles para consultar.</p>
                    @else
                        <form
                            method="GET"
                            action="{{ route('tutor.solicitudes.index') }}"
                            class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end"
                            x-data="{ grupo: '{{ $grupoSel?->idGrupo }}', cargando: false }"
                            @submit="cargando = true"
                        >
                            <div class="flex-1">
                                <label for="grupo" class="mb-1 block text-sm font-semibold text-slate-800">Grupo de tu hijo(a)</label>
                                <select
                                    id="grupo"
                                    name="grupo"
                                    x-model="grupo"
                                    required
                                    class="h-12 w-full rounded-lg border-slate-300 text-base text-slate-900 focus:border-blue-600 focus:ring-blue-600"
                                >
                                    <option value="">Selecciona un grupo</option>
                                    @foreach ($grupos as $grupo)
                                        <option value="{{ $grupo->idGrupo }}" @selected($grupoSel && $grupoSel->idGrupo === $grupo->idGrupo)>
                                            {{ $grupo->grado }}° {{ $grupo->letraGrupo }} · Ciclo {{ str_replace('-', '–', (string) $grupo->cicloEsc) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button
                                type="submit"
                                :disabled="grupo === '' || cargando"
                                class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-blue-700 px-6 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
                            >
                                <span x-show="!cargando">Ver alumnos</span>
                                <span x-show="cargando" x-cloak>Buscando…</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10.3 4.3a1 1 0 0 1 1.4 0l5 5a1 1 0 0 1 0 1.4l-5 5a1 1 0 0 1-1.4-1.4L13.6 11H4a1 1 0 1 1 0-2h9.6l-3.3-3.3a1 1 0 0 1 0-1.4z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    @endif

                    @unless ($grupoSel)
                        <p class="mt-4 text-sm text-slate-600">Comienza seleccionando el grupo de tu hijo(a).</p>
                    @endunless
                </section>

                {{-- Paso 2 y 3: alumno y datos --}}
                @if ($grupoSel)
                    <section aria-labelledby="titulo-alumno" class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm sm:p-8">
                        <h2 id="titulo-alumno" class="text-xl font-bold text-slate-900">
                            2. Elige a tu hijo(a) en {{ $grupoSel->grado }}° {{ $grupoSel->letraGrupo }}
                        </h2>

                        @if ($alumnos->isEmpty())
                            <p class="mt-4 rounded-lg bg-slate-50 px-4 py-3 text-slate-700">
                                No hay alumnos disponibles en este grupo. Si no ves a tu hijo(a),
                                pide a su docente que lo registre en el grupo.
                            </p>
                        @else
                            <form method="POST" action="{{ route('tutor.solicitudes.store') }}" class="mt-4 space-y-6">
                                @csrf

                                <fieldset>
                                    <legend class="sr-only">Alumnos disponibles</legend>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        @foreach ($alumnos as $a)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-blue-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-blue-600 motion-reduce:transition-none">
                                                <input
                                                    type="radio"
                                                    name="alumno_idAlumno"
                                                    value="{{ $a->idAlumno }}"
                                                    class="text-blue-700 focus:ring-blue-600"
                                                    @checked(old('alumno_idAlumno') == $a->idAlumno)
                                                    required
                                                >
                                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-800" aria-hidden="true">
                                                    {{ mb_strtoupper(mb_substr($a->nombre, 0, 1) . mb_substr($a->apellidoPaterno, 0, 1)) }}
                                                </span>
                                                <span class="font-semibold text-slate-900">
                                                    {{ $a->nombre }}
                                                    {{ mb_substr($a->apellidoPaterno, 0, 1) }}.
                                                    @if ($a->apellidoMaterno)
                                                        {{ mb_substr($a->apellidoMaterno, 0, 1) }}.
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('alumno_idAlumno')" class="mt-2" />
                                </fieldset>

                                <div>
                                    <h2 class="text-xl font-bold text-slate-900">3. Confirma tus datos</h2>
                                    <p class="mt-1 text-sm text-slate-600">Esto nos ayuda a comprobar que eres su familiar.</p>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="fNac" class="mb-1 block text-sm font-semibold text-slate-800">Fecha de nacimiento de tu hijo(a)</label>
                                        <input
                                            id="fNac"
                                            name="fNac"
                                            type="date"
                                            value="{{ old('fNac') }}"
                                            required
                                            class="h-12 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600"
                                        >
                                        <x-input-error :messages="$errors->get('fNac')" class="mt-2" />
                                    </div>

                                    <div>
                                        <label for="parentesco" class="mb-1 block text-sm font-semibold text-slate-800">Tu parentesco</label>
                                        <select
                                            id="parentesco"
                                            name="parentesco"
                                            required
                                            class="h-12 w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-600 focus:ring-blue-600"
                                        >
                                            @foreach (['Padre', 'Madre', 'Tutor legal', 'Abuelo(a)', 'Otro'] as $p)
                                                <option value="{{ $p }}" @selected(old('parentesco') === $p)>{{ $p }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('parentesco')" class="mt-2" />
                                    </div>
                                </div>

                                <label class="flex items-center gap-2 text-sm text-slate-800">
                                    <input type="checkbox" name="esResponsable" value="1" class="rounded border-slate-300 text-blue-700 focus:ring-blue-600" @checked(old('esResponsable'))>
                                    Soy el responsable principal del alumno
                                </label>

                                <button
                                    type="submit"
                                    class="inline-flex h-12 w-full items-center justify-center rounded-lg bg-blue-700 px-6 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 sm:w-auto motion-reduce:transition-none"
                                >
                                    Enviar solicitud al docente
                                </button>
                            </form>
                        @endif
                    </section>
                @endif

                {{-- Mis solicitudes --}}
                <section aria-labelledby="titulo-solicitudes" class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="titulo-solicitudes" class="text-xl font-bold text-slate-900">Mis solicitudes</h2>
                        @if ($totalSolicitudes > 0)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{{ $totalSolicitudes }}</span>
                        @endif
                    </div>

                    <div class="mt-4 space-y-4">
                        @forelse ($solicitudes as $s)
                            @php
                                $est = $estados[$s->estadoSolicitud] ?? $estadoPorDefecto;
                                $alumno = $s->alumno;
                                $g = $alumno?->grupo;
                                $nombreAlumno = $alumno ? trim($alumno->nombre . ' ' . $alumno->apellidoPaterno) : 'Alumno no disponible';
                                $inicialesAlumno = $alumno ? mb_strtoupper(mb_substr($alumno->nombre, 0, 1) . mb_substr($alumno->apellidoPaterno, 0, 1)) : '—';
                            @endphp

                            <article class="rounded-xl border border-slate-200 p-4 transition hover:shadow-md motion-reduce:transition-none">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-800" role="img" aria-label="Iniciales de {{ $nombreAlumno }}">{{ $inicialesAlumno }}</span>
                                        <div>
                                            <p class="text-lg font-bold text-slate-900">{{ $nombreAlumno }}</p>
                                            @if ($g)
                                                <p class="text-sm text-slate-600">Grupo {{ $g->grado }}° {{ $g->letraGrupo }} · Ciclo {{ str_replace('-', '–', (string) $g->cicloEsc) }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <span class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold ring-1 {{ $est['clase'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $est['punto'] }}" aria-hidden="true"></span>
                                        {{ $est['texto'] }}
                                    </span>
                                </div>

                                <dl class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                                    <div>
                                        <dt class="font-semibold text-slate-600">Solicitud creada</dt>
                                        <dd class="text-slate-900">{{ $s->fechaSolicitud?->format('d/m/Y') ?? '—' }}</dd>
                                    </div>
                                    @if ($s->fechaRespuesta)
                                        <div>
                                            <dt class="font-semibold text-slate-600">Respuesta</dt>
                                            <dd class="text-slate-900">{{ $s->fechaRespuesta->format('d/m/Y') }}</dd>
                                        </div>
                                    @endif
                                    @if ($s->parentesco)
                                        <div>
                                            <dt class="font-semibold text-slate-600">Parentesco</dt>
                                            <dd class="text-slate-900">{{ $s->parentesco }}</dd>
                                        </div>
                                    @endif
                                </dl>

                                <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                    {{ $est['ayuda'] }}
                                    @if ($s->observacionRespuesta)
                                        <span class="mt-1 block"><span class="font-semibold">Comentario del docente:</span> {{ $s->observacionRespuesta }}</span>
                                    @endif
                                </p>
                            </article>
                        @empty
                            <p class="rounded-lg bg-slate-50 px-4 py-6 text-center text-slate-700">No tienes solicitudes recientes.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Columna lateral --}}
            <aside class="space-y-6" aria-label="Resumen familiar">
                <section class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-blue-100 text-lg font-bold text-blue-800" role="img" aria-label="Iniciales de {{ $nombreTutor }}">{{ $inicialesTutor }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-lg font-bold text-slate-900" title="{{ $nombreTutor }}">{{ $nombreTutor }}</p>
                            <p class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="h-2 w-2 rounded-full bg-green-600" aria-hidden="true"></span>
                                Sesión activa
                            </p>
                        </div>
                    </div>

                    <dl class="mt-5 grid grid-cols-2 gap-3 text-center">
                        <div class="rounded-xl bg-blue-50 px-3 py-3">
                            <dd class="text-2xl font-extrabold text-slate-900">{{ $totalSolicitudes }}</dd>
                            <dt class="text-xs font-medium text-slate-700">Solicitudes</dt>
                        </div>
                        <div class="rounded-xl bg-green-50 px-3 py-3">
                            <dd class="text-2xl font-extrabold text-slate-900">{{ $aprobadas }}</dd>
                            <dt class="text-xs font-medium text-slate-700">Aprobadas</dt>
                        </div>
                        <div class="rounded-xl bg-amber-50 px-3 py-3">
                            <dd class="text-2xl font-extrabold text-slate-900">{{ $pendientes }}</dd>
                            <dt class="text-xs font-medium text-slate-700">Pendientes</dt>
                        </div>
                        <div class="rounded-xl bg-red-50 px-3 py-3">
                            <dd class="text-2xl font-extrabold text-slate-900">{{ $rechazadas }}</dd>
                            <dt class="text-xs font-medium text-slate-700">Rechazadas</dt>
                        </div>
                    </dl>

                    @if (Route::has('profile.edit'))
                        <a
                            href="{{ route('profile.edit') }}"
                            class="mt-5 inline-flex h-11 w-full items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-4 font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
                        >
                            Mi cuenta
                        </a>
                    @endif
                </section>

                <section class="rounded-2xl border border-blue-100 bg-blue-50 p-6">
                    <h2 class="text-base font-bold text-blue-950">¿Necesitas ayuda?</h2>
                    <p class="mt-2 text-sm text-blue-950">
                        Si no encuentras a tu hijo(a), verifica el grupo seleccionado. Cuando envíes la solicitud,
                        el docente del grupo la revisará.
                    </p>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>