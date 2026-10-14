<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Captura de calificaciones
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded bg-green-100 px-4 py-3 text-green-800 font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded bg-red-100 px-4 py-3 text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="rounded bg-blue-50 px-4 py-3 text-sm text-blue-900">
                Al capturar el primer alumno de un trimestre se abre un plazo de 7 días para
                capturar y corregir calificaciones. Al terminar el plazo, el trimestre se cierra
                y las calificaciones se publican en la boleta de los tutores.
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                @if ($asignaciones->isEmpty())
                    <p class="text-gray-500">
                        No tienes materias asignadas. Pide al administrador que te asigne
                        una materia en tu grupo.
                    </p>
                @else
                    <form method="GET" action="{{ route('docente.calificaciones.index') }}"
                          class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
                        <div>
                            <x-input-label for="asignacion" value="Grupo y materia" />
                            <select id="asignacion" name="asignacion" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Selecciona</option>
                                @foreach ($asignaciones as $a)
                                    <option value="{{ $a->id }}" @selected($asignacionSel && $asignacionSel->id === $a->id)>
                                        {{ $a->grupo->grado }}° {{ $a->grupo->letraGrupo }} — {{ $a->asignatura->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="periodo" value="Trimestre" />
                            <select id="periodo" name="periodo" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Selecciona</option>
                                @foreach ($periodos as $p)
                                                                        <option value="{{ $p->idPeriodo }}"
                                            @selected($periodoSel && $periodoSel->idPeriodo === $p->idPeriodo)
                                            @disabled($p->fechaInicio->isFuture())>
                                        {{ $p->nombre }} ({{ $p->cicloEscolar }})
                                        @if ($p->fechaInicio->isFuture())
                                            — inicia el {{ $p->fechaInicio->format('d/m/Y') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <x-primary-button>Ver alumnos</x-primary-button>
                    </form>
                @endif
            </div>

            @if ($asignacionSel && $periodoSel)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        {{ $asignacionSel->grupo->grado }}° {{ $asignacionSel->grupo->letraGrupo }}
                        — {{ $asignacionSel->asignatura->nombre }}
                        — {{ $periodoSel->nombre }}
                    </h3>

                    @if ($cerrada)
                        <div class="mb-4 rounded bg-gray-100 px-4 py-3 text-sm text-gray-700 font-semibold">
                            Captura cerrada el {{ $cierre->format('d/m/Y H:i') }}.
                            Las calificaciones de este trimestre ya están publicadas.
                        </div>
                    @elseif ($cierre)
                        <div class="mb-4 rounded bg-yellow-50 px-4 py-3 text-sm text-yellow-800 font-semibold">
                            Puedes capturar y corregir hasta el {{ $cierre->format('d/m/Y H:i') }}.
                        </div>
                    @endif

                    @if ($alumnos->isEmpty())
                        <p class="text-gray-500">Este grupo no tiene alumnos activos.</p>
                    @else
                        <form method="POST" action="{{ route('docente.calificaciones.guardar') }}">
                            @csrf
                            <input type="hidden" name="asignacion" value="{{ $asignacionSel->id }}">
                            <input type="hidden" name="periodo" value="{{ $periodoSel->idPeriodo }}">

                            <table class="min-w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-3 py-2 text-left">Alumno</th>
                                        <th class="border px-3 py-2 text-left">Calificación (0-10)</th>
                                        <th class="border px-3 py-2 text-left">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alumnos as $a)
                                        @php($c = $calificaciones->get($a->idAlumno))
                                        <tr>
                                            <td class="border px-3 py-2">
                                                {{ $a->apellidoPaterno }} {{ $a->apellidoMaterno }} {{ $a->nombre }}
                                            </td>
                                            <td class="border px-3 py-2">
                                                <input type="number" step="0.1" min="0" max="10"
                                                       name="calificaciones[{{ $a->idAlumno }}]"
                                                       value="{{ old('calificaciones.'.$a->idAlumno, $c ? number_format($c->valor, 1, '.', '') : '') }}"
                                                       @disabled($cerrada)
                                                       class="w-28 border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                                            </td>
                                            <td class="border px-3 py-2 text-sm">
                                                @if (! $c)
                                                    <span class="text-gray-500">Sin calificación</span>
                                                @elseif ($cerrada)
                                                    <span class="font-semibold text-green-700">Publicada</span>
                                                @else
                                                    <span class="font-semibold text-yellow-700">En revisión</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-4">
                                @if ($cerrada)
                                    <button type="button" disabled
                                            class="rounded-md bg-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-500 cursor-not-allowed">
                                        Captura cerrada
                                    </button>
                                @else
                                    <x-primary-button>Guardar calificaciones</x-primary-button>
                                @endif
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>