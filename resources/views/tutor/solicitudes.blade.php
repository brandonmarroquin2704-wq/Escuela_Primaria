<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Buscar a mi hijo(a)
        </h2>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 space-y-8">

        @if (session('status'))
            <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 font-semibold">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('busqueda'))
            <div role="alert" class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 font-semibold">
                {{ $errors->first('busqueda') }}
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm space-y-5">
            <h3 class="text-lg font-bold text-slate-800">1. Elige el grupo</h3>

            <form method="GET" action="{{ route('tutor.solicitudes.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-input-label for="grupo" value="Grupo de tu hijo(a)" />
                    <select id="grupo" name="grupo" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Selecciona un grupo</option>
                        @foreach ($grupos as $grupo)
                            <option value="{{ $grupo->idGrupo }}" @selected($grupoSel && $grupoSel->idGrupo === $grupo->idGrupo)>
                                {{ $grupo->grado }}° {{ $grupo->letraGrupo }} ({{ $grupo->cicloEsc }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-primary-button>Ver alumnos</x-primary-button>
            </form>
        </section>

        @if ($grupoSel)
            <section class="rounded-2xl bg-white p-6 shadow-sm space-y-5">
                <h3 class="text-lg font-bold text-slate-800">
                    2. Elige a tu hijo(a) en {{ $grupoSel->grado }}° {{ $grupoSel->letraGrupo }}
                </h3>

                @if ($alumnos->isEmpty())
                    <p class="text-slate-500">
                        No hay alumnos disponibles en este grupo. Si no ves a tu hijo(a),
                        pide a su docente que lo registre en el grupo.
                    </p>
                @else
                    <form method="POST" action="{{ route('tutor.solicitudes.store') }}" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @foreach ($alumnos as $a)
                                <label class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 cursor-pointer hover:bg-slate-50">
                                    <input type="radio" name="alumno_idAlumno" value="{{ $a->idAlumno }}"
                                           @checked(old('alumno_idAlumno') == $a->idAlumno) required>
                                    <span class="font-semibold text-slate-800">
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

                        <h3 class="text-lg font-bold text-slate-800 pt-2">3. Confirma tus datos</h3>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="fNac" value="Fecha de nacimiento de tu hijo(a)" />
                                <x-text-input id="fNac" name="fNac" type="date" class="block mt-1 w-full" :value="old('fNac')" required />
                                <x-input-error :messages="$errors->get('fNac')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="parentesco" value="Tu parentesco" />
                                <select id="parentesco" name="parentesco" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    @foreach (['Padre', 'Madre', 'Tutor legal', 'Abuelo(a)', 'Otro'] as $p)
                                        <option value="{{ $p }}" @selected(old('parentesco') === $p)>{{ $p }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('parentesco')" class="mt-2" />
                            </div>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="esResponsable" value="1" @checked(old('esResponsable'))>
                            Soy el responsable principal del alumno
                        </label>

                        <x-primary-button>Enviar solicitud al docente</x-primary-button>
                    </form>
                @endif
            </section>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Mis solicitudes</h3>

            @forelse ($solicitudes as $s)
                <div class="flex items-center justify-between border-b border-slate-100 py-3">
                    <div>
                        <p class="font-semibold text-slate-800">
                            {{ $s->alumno->nombre }} {{ $s->alumno->apellidoPaterno }}
                        </p>
                        <p class="text-sm text-slate-500">
                            {{ $s->alumno->grupo->grado ?? '' }}° {{ $s->alumno->grupo->letraGrupo ?? '' }}
                            · enviada {{ $s->fechaSolicitud?->format('d/m/Y') }}
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-sm font-bold
                        {{ $s->estadoSolicitud === 'aprobada' ? 'bg-green-100 text-green-800' : ($s->estadoSolicitud === 'rechazada' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($s->estadoSolicitud) }}
                    </span>
                </div>
            @empty
                <p class="text-slate-500">Todavía no has enviado solicitudes.</p>
            @endforelse
        </section>
    </div>
</x-app-layout>