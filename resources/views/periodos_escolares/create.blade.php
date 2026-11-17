<x-app-layout>
    <x-slot name="header">
        <h2 class="truncate text-lg font-bold text-[#1E3A5F]">
            Registrar periodo escolar
        </h2>
    </x-slot>

    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-6">

            {{-- Encabezado --}}
            <header>
                <nav aria-label="Ruta de navegación" class="text-sm text-[#667085]">
                    <a href="{{ route('periodos-escolares.index') }}" class="font-medium text-[#2563A6] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6]">Periodos escolares</a>
                    <span aria-hidden="true">/</span>
                    <span class="font-semibold text-[#263548]">Registrar periodo escolar</span>
                </nav>

                <h1 class="mt-2 text-2xl font-extrabold text-[#1E3A5F] sm:text-[28px] sm:leading-9">Registrar periodo escolar</h1>
                <p class="mt-1 text-base text-[#667085]">
                    Completa la información para registrar un nuevo periodo escolar.
                </p>
            </header>

            @if (session('error'))
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-semibold text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <p class="font-semibold">Revisa los campos marcados antes de continuar.</p>
                    <p class="text-sm">Hay {{ $errors->count() }} {{ $errors->count() === 1 ? 'error' : 'errores' }} en el formulario.</p>
                </div>
            @endif

            {{-- Tarjeta del formulario --}}
            <div class="overflow-hidden rounded-2xl border border-[#DCE6F2] bg-white shadow-sm shadow-[#1E3A5F]/5">
                <form
                    method="POST"
                    action="{{ route('periodos-escolares.store') }}"
                    x-data="{ cargando: false }"
                    @submit="cargando = true"
                    class="divide-y divide-[#E6EDF5]"
                >
                    @csrf

                    {{-- Información general --}}
                    <fieldset class="p-6 sm:p-8">
                        <legend class="flex items-center gap-3 text-base font-bold text-[#1E3A5F]">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#EAF3FC] text-[#2563A6]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M5 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7.400a2 2 0 0 0-.6-1.400L12.600 2.600A2 2 0 0 0 11.200 2H5zm2 8h6v1.500H7V10zm0 3h6v1.500H7V13z" /></svg>
                            </span>
                            Información general
                        </legend>

                        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="nombre" class="mb-1 block text-sm font-semibold text-[#263548]">Nombre del periodo</label>
                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    value="{{ old('nombre') }}"
                                    maxlength="45"
                                    required
                                    autofocus
                                    placeholder="Ejemplo: Primer periodo"
                                    @error('nombre') aria-invalid="true" aria-describedby="error-nombre" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-base font-medium text-[#263548] placeholder:text-[#667085] focus:border-[#2563A6] focus:ring-[#2563A6] @error('nombre') border-red-500 @enderror"
                                >
                                @error('nombre')
                                    <p id="error-nombre" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cicloEscolar" class="mb-1 block text-sm font-semibold text-[#263548]">Ciclo escolar</label>
                                <input
                                    type="text"
                                    id="cicloEscolar"
                                    name="cicloEscolar"
                                    value="{{ old('cicloEscolar') }}"
                                    maxlength="40"
                                    required
                                    placeholder="Ejemplo: 2026-2027"
                                    @error('cicloEscolar') aria-invalid="true" aria-describedby="error-cicloEscolar" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-base font-medium text-[#263548] placeholder:text-[#667085] focus:border-[#2563A6] focus:ring-[#2563A6] @error('cicloEscolar') border-red-500 @enderror"
                                >
                                @error('cicloEscolar')
                                    <p id="error-cicloEscolar" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Fechas --}}
                    <fieldset class="p-6 sm:p-8">
                        <legend class="flex items-center gap-3 text-base font-bold text-[#1E3A5F]">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#F0EEFA] text-[#4B3F9E]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 0 1 1 1v1h6V3a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1V3a1 1 0 0 1 1-1zM4 9v7h12V9H4z" clip-rule="evenodd" /></svg>
                            </span>
                            Fechas
                        </legend>

                        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="fechaInicio" class="mb-1 block text-sm font-semibold text-[#263548]">Fecha de inicio</label>
                                <input
                                    type="date"
                                    id="fechaInicio"
                                    name="fechaInicio"
                                    value="{{ old('fechaInicio') }}"
                                    required
                                    @error('fechaInicio') aria-invalid="true" aria-describedby="error-fechaInicio" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-base text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('fechaInicio') border-red-500 @enderror"
                                >
                                @error('fechaInicio')
                                    <p id="error-fechaInicio" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="fechaFin" class="mb-1 block text-sm font-semibold text-[#263548]">Fecha de fin</label>
                                <input
                                    type="date"
                                    id="fechaFin"
                                    name="fechaFin"
                                    value="{{ old('fechaFin') }}"
                                    required
                                    aria-describedby="ayuda-fechaFin @error('fechaFin') error-fechaFin @enderror"
                                    @error('fechaFin') aria-invalid="true" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-base text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('fechaFin') border-red-500 @enderror"
                                >
                                <p id="ayuda-fechaFin" class="mt-1 text-sm text-[#667085]">Debe ser posterior a la fecha de inicio.</p>
                                @error('fechaFin')
                                    <p id="error-fechaFin" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Estado --}}
                    <fieldset class="p-6 sm:p-8">
                        <legend class="flex items-center gap-3 text-base font-bold text-[#1E3A5F]">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#EAF3FC] text-[#2563A6]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm3.700-9.300a1 1 0 0 0-1.400-1.400L9 10.600 7.700 9.300a1 1 0 0 0-1.400 1.400l2 2a1 1 0 0 0 1.400 0l4-4z" clip-rule="evenodd" /></svg>
                            </span>
                            Estado del periodo
                        </legend>

                        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="estado" class="mb-1 block text-sm font-semibold text-[#263548]">Estado</label>
                                <select
                                    id="estado"
                                    name="estado"
                                    required
                                    @error('estado') aria-invalid="true" aria-describedby="error-estado" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] text-base text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('estado') border-red-500 @enderror"
                                >
                                    <option value="1" @selected(old('estado', '1') == '1')>
                                        Activo
                                    </option>

                                    <option value="0" @selected(old('estado') === '0')>
                                        Inactivo
                                    </option>
                                </select>
                                @error('estado')
                                    <p id="error-estado" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Botones --}}
                    <div class="flex flex-col-reverse gap-3 bg-[#F5F8FC] px-6 py-5 sm:flex-row sm:items-center sm:justify-end sm:px-8">
                        <a
                            href="{{ route('periodos-escolares.index') }}"
                            class="inline-flex h-12 items-center justify-center rounded-lg border border-[#C9D5E3] bg-white px-6 font-semibold text-[#263548] transition hover:bg-[#EAF3FC] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6] focus-visible:ring-offset-2 motion-reduce:transition-none"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            :disabled="cargando"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-[#2563A6] px-6 font-semibold text-white shadow-sm transition hover:bg-[#1E3A5F] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 motion-reduce:transition-none"
                        >
                            <svg x-show="cargando" x-cloak class="h-5 w-5 animate-spin motion-reduce:animate-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                            </svg>
                            <span x-show="!cargando">Guardar periodo</span>
                            <span x-show="cargando" x-cloak>Guardando…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>