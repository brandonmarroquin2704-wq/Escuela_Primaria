<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar periodo escolar
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('periodos-escolares.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre del periodo
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                maxlength="45"
                                required
                                autofocus
                                placeholder="Ejemplo: Primer periodo"
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="cicloEscolar" class="block text-sm font-medium text-gray-700">
                                Ciclo escolar
                            </label>

                            <input
                                type="text"
                                id="cicloEscolar"
                                name="cicloEscolar"
                                value="{{ old('cicloEscolar') }}"
                                maxlength="40"
                                required
                                placeholder="Ejemplo: 2026-2027"
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('cicloEscolar')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="fechaInicio" class="block text-sm font-medium text-gray-700">
                                Fecha de inicio
                            </label>

                            <input
                                type="date"
                                id="fechaInicio"
                                name="fechaInicio"
                                value="{{ old('fechaInicio') }}"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('fechaInicio')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="fechaFin" class="block text-sm font-medium text-gray-700">
                                Fecha de fin
                            </label>

                            <input
                                type="date"
                                id="fechaFin"
                                name="fechaFin"
                                value="{{ old('fechaFin') }}"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('fechaFin')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">
                                Estado
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="1" @selected(old('estado', '1') == '1')>
                                    Activo
                                </option>

                                <option value="0" @selected(old('estado') === '0')>
                                    Inactivo
                                </option>
                            </select>

                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button
                            type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                        >
                            Guardar periodo
                        </button>

                        <a
                            href="{{ route('periodos-escolares.index') }}"
                            class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>