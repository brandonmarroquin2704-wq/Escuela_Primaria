@php
    $editando = $comunicado->exists;
    $campo = 'block w-full rounded-lg border border-[#DCE6F2] px-4 py-3 text-base text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6]';
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="truncate text-lg font-bold text-[#1E3A5F]">{{ $editando ? 'Editar comunicado' : 'Nuevo comunicado' }}</h2>
    </x-slot>

    <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6">
        <form method="POST"
              action="{{ $editando ? route('docente.foro.update', $comunicado) : route('docente.foro.store') }}"
              x-data="{ tipo: '{{ old('tipo', $comunicado->tipo ?? 'anuncio') }}', enviando: false }"
              @submit="if (enviando) { $event.preventDefault(); return; } enviando = true"
              class="space-y-5 rounded-lg border border-[#DCE6F2] bg-white p-4 sm:p-6">
            @csrf
            @if ($editando) @method('PUT') @endif

            <div>
                <label for="asignacion_id" class="mb-1 block text-sm font-bold text-[#1E3A5F]">Grupo y materia</label>
                <select id="asignacion_id" name="asignacion_id" required class="{{ $campo }}">
                    <option value="">Selecciona…</option>
                    @foreach ($asignaciones as $a)
                        <option value="{{ $a->id }}" @selected(old('asignacion_id', $comunicado->asignacion_id) == $a->id)>
                            {{ $a->grupo?->grado }}° {{ $a->grupo?->letraGrupo }} · {{ $a->asignatura?->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('asignacion_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tipo" class="mb-1 block text-sm font-bold text-[#1E3A5F]">Tipo</label>
                <select id="tipo" name="tipo" x-model="tipo" required class="{{ $campo }}">
                    @foreach (\App\Models\Comunicado::TIPOS as $valor => $texto)
                        <option value="{{ $valor }}">{{ $texto }}</option>
                    @endforeach
                </select>
                @error('tipo') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="titulo" class="mb-1 block text-sm font-bold text-[#1E3A5F]">Título</label>
                <input id="titulo" name="titulo" type="text" maxlength="120" required
                       value="{{ old('titulo', $comunicado->titulo) }}" class="{{ $campo }}">
                @error('titulo') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="contenido" class="mb-1 block text-sm font-bold text-[#1E3A5F]">Mensaje</label>
                <textarea id="contenido" name="contenido" rows="6" maxlength="2000" required class="{{ $campo }}">{{ old('contenido', $comunicado->contenido) }}</textarea>
                @error('contenido') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div x-show="tipo === 'tarea'" x-cloak>
                <label for="fecha_entrega" class="mb-1 block text-sm font-bold text-[#1E3A5F]">Fecha de entrega</label>
                <input id="fecha_entrega" name="fecha_entrega" type="date" min="{{ now()->toDateString() }}"
                       value="{{ old('fecha_entrega', $comunicado->fecha_entrega?->toDateString()) }}" class="{{ $campo }}">
                @error('fecha_entrega') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <p class="rounded-lg bg-[#F5F8FC] px-4 py-3 text-sm text-[#667085]">
                Los tutores solo podrán leer este comunicado. Verán automáticamente una nota indicando que no se admiten respuestas y que se comuniquen con usted para dudas o comentarios.
            </p>

            <div class="flex flex-col gap-2 sm:flex-row">
                <button type="submit"
                        :disabled="enviando"
                        class="flex min-h-12 items-center justify-center rounded-lg bg-[#1E3A5F] px-5 py-3 font-bold text-white hover:bg-[#2563A6] disabled:cursor-not-allowed disabled:opacity-70">
                    {{ $editando ? 'Guardar cambios' : 'Publicar' }}
                </button>
                <a href="{{ route('docente.foro.index') }}"
                   class="flex min-h-12 items-center justify-center rounded-lg border border-[#DCE6F2] px-5 py-3 font-bold text-[#263548] hover:bg-[#F5F8FC]">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>