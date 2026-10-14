<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar datos de la escuela
        </h2>
    </x-slot>

    @php
        $campos = [
            ['nombre', 'Nombre de la escuela', 'text', 100, 'md:col-span-2'],
            ['claveCentroTrabajo', 'Clave del centro de trabajo', 'text', 25, ''],
            ['telefono', 'Teléfono', 'text', 25, ''],
            ['correo', 'Correo electrónico', 'email', 45, 'md:col-span-2'],
            ['direccion', 'Dirección', 'text', 200, 'md:col-span-2'],
            ['municipio', 'Municipio', 'text', 80, ''],
            ['codigoPostal', 'Código postal', 'text', 15, ''],
        ];
    @endphp

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('escuelas.update', $escuela) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach ($campos as [$nombre, $etiqueta, $tipo, $max, $clase])
                            <div class="{{ $clase }}">
                                <label for="{{ $nombre }}" class="block text-sm font-medium text-gray-700">
                                    {{ $etiqueta }}
                                </label>
                                <input
                                    type="{{ $tipo }}"
                                    id="{{ $nombre }}"
                                    name="{{ $nombre }}"
                                    value="{{ old($nombre, $escuela->$nombre) }}"
                                    maxlength="{{ $max }}"
                                    required
                                    class="mt-1 block w-full rounded border-gray-300"
                                >
                                @error($nombre)
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select id="estado" name="estado" required class="mt-1 block w-full rounded border-gray-300">
                                <option value="1" @selected(old('estado', (string) (int) $escuela->estado) === '1')>Activa</option>
                                <option value="0" @selected(old('estado', (string) (int) $escuela->estado) === '0')>Inactiva</option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Guardar cambios
                        </button>
                        <a href="{{ route('dashboard') }}" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>