<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar asignatura
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('asignaturas.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre de la asignatura
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                maxlength="85"
                                required
                                autofocus
                                placeholder="Ejemplo: Matemáticas"
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700">
                                Descripción
                            </label>

                            <textarea
                                id="descripcion"
                                name="descripcion"
                                rows="4"
                                maxlength="200"
                                placeholder="Descripción opcional de la asignatura"
                                class="mt-1 block w-full rounded border-gray-300"
                            >{{ old('descripcion') }}</textarea>

                            @error('descripcion')
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
                                    Activa
                                </option>

                                <option value="0" @selected(old('estado') === '0')>
                                    Inactiva
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
                            Guardar asignatura
                        </button>

                        <a
                            href="{{ route('asignaturas.index') }}"
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