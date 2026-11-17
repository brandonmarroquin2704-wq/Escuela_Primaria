@php
    $nombreCorto = trim($hijo->nombre . ' ' . $hijo->apellidoPaterno);
    $nombreCompleto = trim(preg_replace('/\s+/', ' ', $hijo->nombre . ' ' . $hijo->apellidoPaterno . ' ' . $hijo->apellidoMaterno));
    $iniciales = mb_strtoupper(mb_substr($hijo->nombre, 0, 1) . mb_substr($hijo->apellidoPaterno, 0, 1));

    $grupo = $hijo->grupo ? $hijo->grupo->grado . '° ' . $hijo->grupo->letraGrupo : 'Sin grupo';
    $ciclo = $hijo->grupo ? str_replace('-', '–', (string) $hijo->grupo->cicloEsc) : null;

    $totalMaterias = $filas->count();
    $evaluadas = $filas->whereNotNull('promedio')->count();
    $sinCalificar = $totalMaterias - $evaluadas;

    // ¿Hay alguna celda sin calificación publicada?
    $hayPendientes = $filas->contains(function ($f) use ($periodos) {
        return $periodos->contains(fn ($p) => ! $f['notas']->has($p->idPeriodo));
    });
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Boleta de {{ $nombreCorto }} — {{ $grupo }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">

        {{-- Encabezado --}}
        <header>
            <nav aria-label="Ruta de navegación" class="text-sm text-slate-600">
                <a href="{{ route('dashboard') }}" class="font-medium text-blue-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">Inicio</a>
                <span aria-hidden="true">/</span>
                <span>Boletas</span>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-slate-800">{{ $nombreCorto }}</span>
            </nav>

            <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Boleta de calificaciones</h1>
            <p class="mt-1 text-lg font-semibold text-slate-800">
                {{ $nombreCorto }}
                <span class="font-medium text-slate-600">· {{ $grupo }}@if ($ciclo) · Ciclo escolar {{ $ciclo }}@endif</span>
            </p>
            <p class="mt-1 text-base text-slate-700">Consulta el avance académico y las calificaciones del alumno.</p>
        </header>

        {{-- Información del alumno --}}
        <section aria-label="Información del alumno" class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-emerald-100 text-lg font-bold text-emerald-800" role="img" aria-label="Iniciales de {{ $nombreCompleto }}">{{ $iniciales }}</span>
                    <div class="min-w-0">
                        <p class="break-words text-xl font-extrabold text-slate-900">{{ $nombreCompleto }}</p>
                        <p class="text-sm text-slate-700">{{ $grupo }}@if ($ciclo) · Ciclo escolar {{ $ciclo }}@endif</p>
                    </div>
                </div>

                @if ($hijo->estado)
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 ring-1 ring-green-200">
                        <span class="h-2 w-2 rounded-full bg-green-600" aria-hidden="true"></span>
                        Alumno activo
                    </span>
                @endif
            </div>
        </section>

        {{-- Resumen --}}
        <section aria-label="Resumen académico" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                <p class="text-3xl font-extrabold text-slate-900">
                    {{ $promedioGeneral !== null ? number_format($promedioGeneral, 1) : 'Pendiente' }}
                </p>
                <p class="text-sm font-medium text-slate-700">Promedio general</p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                <p class="text-3xl font-extrabold text-slate-900">{{ $evaluadas }}</p>
                <p class="text-sm font-medium text-slate-700">Materias con calificación</p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                <p class="text-3xl font-extrabold text-slate-900">{{ $sinCalificar }}</p>
                <p class="text-sm font-medium text-slate-700">Materias sin calificación</p>
            </div>
        </section>

        {{-- Aviso --}}
        <div role="note" class="flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-blue-950">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM9 9a1 1 0 0 0 0 2v3a1 1 0 0 0 1 1h1a1 1 0 1 0 0-2v-3a1 1 0 0 0-1-1H9z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm">
                Las calificaciones estarán disponibles cuando el docente termine el periodo de revisión correspondiente.
                @if ($hayPendientes && $totalMaterias > 0)
                    Algunas calificaciones todavía no están disponibles.
                @endif
            </p>
        </div>

        {{-- Calificaciones --}}
        <section aria-labelledby="titulo-calificaciones" class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm sm:p-6">
            <h2 id="titulo-calificaciones" class="text-lg font-bold text-slate-900">Calificaciones por materia</h2>

            @if ($filas->isEmpty())
                <p class="mt-4 rounded-lg bg-slate-50 px-4 py-6 text-center text-slate-700">
                    Este grupo aún no tiene materias asignadas.
                </p>
            @else
                {{-- Tabla (tabletas y escritorio) --}}
                <div class="mt-4 hidden overflow-x-auto rounded-lg border border-blue-100 md:block">
                    <table class="w-full text-slate-900">
                        <caption class="sr-only">Calificaciones de {{ $nombreCompleto }} por materia y periodo</caption>

                        <thead class="bg-blue-100 text-sm font-bold uppercase tracking-wide text-blue-950">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left">Materia</th>
                                @foreach ($periodos as $p)
                                    <th scope="col" class="px-4 py-3 text-center">{{ $p->nombre }}</th>
                                @endforeach
                                <th scope="col" class="px-4 py-3 text-center">Promedio</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-blue-100">
                            @foreach ($filas as $fila)
                                <tr class="transition odd:bg-white even:bg-slate-50 hover:bg-blue-50 motion-reduce:transition-none">
                                    <th scope="row" class="px-4 py-4 text-left text-base font-bold">{{ $fila['nombre'] }}</th>

                                    @foreach ($periodos as $p)
                                        <td class="px-4 py-4 text-center">
                                            @if ($fila['notas']->has($p->idPeriodo))
                                                <span class="text-lg font-semibold tabular-nums">{{ number_format($fila['notas'][$p->idPeriodo]->valor, 1) }}</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">Pendiente</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td class="px-4 py-4 text-center">
                                        @if ($fila['promedio'] !== null)
                                            <span class="text-lg font-extrabold tabular-nums text-blue-900">{{ number_format($fila['promedio'], 1) }}</span>
                                            @if ($fila['parcial'])
                                                <span class="block text-xs font-medium text-amber-800">Parcial</span>
                                            @endif
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        @if ($promedioGeneral !== null)
                            <tfoot>
                                <tr class="border-t-2 border-blue-300 bg-blue-50">
                                    <th scope="row" class="px-4 py-4 text-left text-base font-extrabold text-blue-950" colspan="{{ $periodos->count() + 1 }}">
                                        Promedio general
                                    </th>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex rounded-lg bg-blue-700 px-3 py-1 text-xl font-extrabold tabular-nums text-white">{{ number_format($promedioGeneral, 1) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Tarjetas (móvil) --}}
                <div class="mt-4 space-y-3 md:hidden">
                    @foreach ($filas as $fila)
                        <article class="rounded-xl border border-blue-100 p-4">
                            <h3 class="text-base font-extrabold text-slate-900">{{ $fila['nombre'] }}</h3>

                            <dl class="mt-3 space-y-2 text-sm">
                                @foreach ($periodos as $p)
                                    <div class="flex items-center justify-between gap-3">
                                        <dt class="font-medium text-slate-700">{{ $p->nombre }}</dt>
                                        <dd>
                                            @if ($fila['notas']->has($p->idPeriodo))
                                                <span class="text-base font-semibold tabular-nums text-slate-900">{{ number_format($fila['notas'][$p->idPeriodo]->valor, 1) }}</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">Pendiente</span>
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach

                                <div class="flex items-center justify-between gap-3 border-t border-blue-100 pt-2">
                                    <dt class="font-bold text-slate-900">Promedio</dt>
                                    <dd>
                                        @if ($fila['promedio'] !== null)
                                            <span class="text-lg font-extrabold tabular-nums text-blue-900">{{ number_format($fila['promedio'], 1) }}</span>
                                            @if ($fila['parcial'])
                                                <span class="ml-1 text-xs font-medium text-amber-800">(parcial)</span>
                                            @endif
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">Pendiente</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </article>
                    @endforeach

                    @if ($promedioGeneral !== null)
                        <div class="flex items-center justify-between rounded-xl border-2 border-blue-300 bg-blue-50 px-4 py-3">
                            <span class="font-extrabold text-blue-950">Promedio general</span>
                            <span class="rounded-lg bg-blue-700 px-3 py-1 text-xl font-extrabold tabular-nums text-white">{{ number_format($promedioGeneral, 1) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Leyenda --}}
                <ul class="mt-4 space-y-1 text-sm text-slate-700">
                    <li><span class="font-semibold">Pendiente:</span> la calificación aún no está disponible porque el docente no ha terminado su revisión.</li>
                    <li><span class="font-semibold">Parcial:</span> el promedio de la materia se calculó solo con las calificaciones ya disponibles.</li>
                </ul>
            @endif
        </section>

        <a
            href="{{ route('dashboard') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-reduce:transition-none"
        >
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9.7 15.7a1 1 0 0 1-1.4 0l-5-5a1 1 0 0 1 0-1.4l5-5a1 1 0 0 1 1.4 1.4L6.4 9H16a1 1 0 1 1 0 2H6.4l3.3 3.3a1 1 0 0 1 0 1.4z" clip-rule="evenodd" />
            </svg>
            Volver a mi panel
        </a>
    </div>
</x-app-layout>