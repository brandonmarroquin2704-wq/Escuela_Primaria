<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Solicitudes de acceso
        </h2>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 space-y-8">

        @if (session('status'))
            <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-4">
                Pendientes ({{ $pendientes->count() }})
            </h3>

            @forelse ($pendientes as $s)
                <div class="border border-slate-200 rounded-xl p-4 mb-4 space-y-3">
                    <div>
                        <p class="font-bold text-slate-800">
                            Alumno: {{ $s->alumno->nombre }}
                            {{ $s->alumno->apellidoPaterno }}
                            {{ $s->alumno->apellidoMaterno }}
                            <span class="font-normal text-slate-500">
                                ({{ $s->alumno->grupo->grado }}° {{ $s->alumno->grupo->letraGrupo }})
                            </span>
                        </p>
                        <p class="text-sm text-slate-600 mt-1">
                            Solicita: {{ $s->tutor->nombre }}
                            {{ $s->tutor->apellidoPaterno }}
                            {{ $s->tutor->apellidoMaterno }}
                            · {{ $s->parentesco }}
                            @if ($s->esResponsable) · Responsable principal @endif
                        </p>
                        <p class="text-sm text-slate-600">
                            Correo: {{ $s->tutor->correo }}
                            @if ($s->tutor->datosTutor)
                                · Tel: {{ $s->tutor->datosTutor->telefono }}
                            @endif
                        </p>
                        <p class="text-xs text-slate-400">
                            Enviada el {{ $s->fechaSolicitud?->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                        <form method="POST" action="{{ route('docente.solicitudes.aprobar', $s->idSolicitudAccesoSalon) }}">
                            @csrf
                            <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white hover:bg-green-700">
                                Aprobar
                            </button>
                        </form>

                        <form method="POST" action="{{ route('docente.solicitudes.rechazar', $s->idSolicitudAccesoSalon) }}" class="flex flex-1 gap-2">
                            @csrf
                            <input type="text" name="observacionRespuesta" maxlength="255"
                                   placeholder="Motivo (opcional)"
                                   class="flex-1 border-gray-300 rounded-md shadow-sm text-sm">
                            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                Rechazar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-slate-500">No tienes solicitudes pendientes.</p>
            @endforelse
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Respondidas recientemente</h3>

            @forelse ($resueltas as $s)
                <div class="flex items-center justify-between border-b border-slate-100 py-3">
                    <div>
                        <p class="font-semibold text-slate-800">
                            {{ $s->alumno->nombre }} {{ $s->alumno->apellidoPaterno }}
                            <span class="font-normal text-slate-500">← {{ $s->tutor->nombre }} {{ $s->tutor->apellidoPaterno }}</span>
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ $s->fechaRespuesta?->format('d/m/Y H:i') }}
                            @if ($s->observacionRespuesta) · {{ $s->observacionRespuesta }} @endif
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-sm font-bold
                        {{ $s->estadoSolicitud === 'aprobada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($s->estadoSolicitud) }}
                    </span>
                </div>
            @empty
                <p class="text-slate-500">Aún no has respondido solicitudes.</p>
            @endforelse
        </section>
    </div>
</x-app-layout>