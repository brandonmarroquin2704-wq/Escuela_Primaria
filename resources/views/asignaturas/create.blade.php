<x-app-layout>
    <x-slot name="header">
        <h2 class="truncate text-lg font-bold text-[#1E3A5F]">
            Registrar asignatura
        </h2>
    </x-slot>

    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl space-y-6">

            {{-- Encabezado --}}
            <header>
                <nav aria-label="Ruta de navegación" class="text-sm text-[#667085]">
                    <a href="{{ route('asignaturas.index') }}" class="font-medium text-[#2563A6] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6]">Asignaturas</a>
                    <span aria-hidden="true">/</span>
                    <span class="font-semibold text-[#263548]">Registrar asignatura</span>
                </nav>

                <h1 class="mt-2 text-2xl font-extrabold text-[#1E3A5F] sm:text-[28px] sm:leading-9">Registrar asignatura</h1>
                <p class="mt-1 text-base text-[#667085]">
                    Completa la información para registrar una nueva asignatura.
                </p>
            </header>

            @if (session('error'))
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-semibold text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <p class="font-semibold">Revisa la información antes de continuar.</p>
                    <p class="text-sm">Hay {{ $errors->count() }} {{ $errors->count() === 1 ? 'error' : 'errores' }} en el formulario.</p>
                </div>
            @endif

            {{-- Tarjeta del formulario --}}
            <div class="overflow-hidden rounded-2xl border border-[#DCE6F2] bg-white shadow-sm shadow-[#1E3A5F]/5">
                <form
                    method="POST"
                    action="{{ route('asignaturas.store') }}"
                    x-data="{ cargando: false }"
                    @submit="cargando = true"
                    class="divide-y divide-[#E6EDF5]"
                >
                    @csrf

                    {{-- Información general --}}
                    <fieldset class="p-6 sm:p-8">
                        <legend class="flex items-center gap-3 text-base font-bold text-[#1E3A5F]">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#EAF3FC] text-[#2563A6]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.800C7.900 4 6.400 3.600 4.500 3.600c-.9 0-1.800.1-2.500.3a1 1 0 0 0-.7 1v9.600c0 .6.6 1.100 1.200.9.6-.2 1.300-.2 2-.2 1.500 0 2.700.4 3.500 1V4.800zm2 0v10.200c.8-.6 2-1 3.500-1 .7 0 1.400 0 2 .2.6.2 1.200-.3 1.200-.9V4.900a1 1 0 0 0-.7-1c-.7-.2-1.600-.3-2.500-.3-1.900 0-3.400.4-4.500 1.200z" /></svg>
                            </span>
                            Información general
                        </legend>

                        <div class="mt-6 grid grid-cols-1 gap-5">
                            <div>
                                <label for="nombre" class="mb-1 block text-sm font-semibold text-[#263548]">Nombre de la asignatura</label>
                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    value="{{ old('nombre') }}"
                                    maxlength="85"
                                    required
                                    autofocus
                                    placeholder="Ejemplo: Matemáticas"
                                    @error('nombre') aria-invalid="true" aria-describedby="error-nombre" @enderror
                                    class="h-12 w-full rounded-lg border-[#C9D5E3] bg-white text-base font-medium text-[#263548] placeholder:text-[#667085] focus:border-[#2563A6] focus:ring-[#2563A6] @error('nombre') border-red-500 @enderror"
                                >
                                @error('nombre')
                                    <p id="error-nombre" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="descripcion" class="mb-1 block text-sm font-semibold text-[#263548]">Descripción</label>
                                <textarea
                                    id="descripcion"
                                    name="descripcion"
                                    rows="4"
                                    maxlength="200"
                                    placeholder="Descripción opcional de la asignatura"
                                    aria-describedby="ayuda-descripcion @error('descripcion') error-descripcion @enderror"
                                    @error('descripcion') aria-invalid="true" @enderror
                                    class="min-h-[7rem] w-full resize-y rounded-lg border-[#C9D5E3] bg-white text-base text-[#263548] placeholder:text-[#667085] focus:border-[#2563A6] focus:ring-[#2563A6] @error('descripcion') border-red-500 @enderror"
                                >{{ old('descripcion') }}</textarea>
                                <p id="ayuda-descripcion" class="mt-1 text-sm text-[#667085]">Opcional. Máximo 200 caracteres.</p>
                                @error('descripcion')
                                    <p id="error-descripcion" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Estado --}}
                    <fieldset class="p-6 sm:p-8">
                        <legend class="flex items-center gap-3 text-base font-bold text-[#1E3A5F]">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#F0EEFA] text-[#4B3F9E]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm3.700-9.300a1 1 0 0 0-1.400-1.400L9 10.600 7.700 9.300a1 1 0 0 0-1.400 1.400l2 2a1 1 0 0 0 1.400 0l4-4z" clip-rule="evenodd" /></svg>
                            </span>
                            Estado
                        </legend>

                        <div class="mt-6 max-w-sm">
                            <label for="estado" class="mb-1 block text-sm font-semibold text-[#263548]">Estado</label>
                            <select
                                id="estado"
                                name="estado"
                                required
                                @error('estado') aria-invalid="true" aria-describedby="error-estado" @enderror
                                class="h-12 w-full rounded-lg border-[#C9D5E3] bg-white text-base text-[#263548] focus:border-[#2563A6] focus:ring-[#2563A6] @error('estado') border-red-500 @enderror"
                            >
                                <option value="1" @selected(old('estado', '1') == '1')>
                                    Activa
                                </option>

                                <option value="0" @selected(old('estado') === '0')>
                                    Inactiva
                                </option>
                            </select>
                            @error('estado')
                                <p id="error-estado" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </fieldset>

                    {{-- Botones --}}
                    <div class="flex flex-col-reverse gap-3 bg-[#F5F8FC] px-6 py-5 sm:flex-row sm:items-center sm:justify-end sm:px-8">
                        <a
                            href="{{ route('asignaturas.index') }}"
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
                            <span x-show="!cargando">Guardar asignatura</span>
                            <span x-show="cargando" x-cloak>Guardando…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 