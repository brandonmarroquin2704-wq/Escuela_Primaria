<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar escuela
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('escuelas.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre de la escuela
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                maxlength="100"
                                required
                                autofocus
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="claveCentroTrabajo" class="block text-sm font-medium text-gray-700">
                                Clave del centro de trabajo
                            </label>

                            <input
                                type="text"
                                id="claveCentroTrabajo"
                                name="claveCentroTrabajo"
                                value="{{ old('claveCentroTrabajo') }}"
                                maxlength="25"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('claveCentroTrabajo')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700">
                                Teléfono
                            </label>

                            <input
                                type="text"
                                id="telefono"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                maxlength="25"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('telefono')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="correo" class="block text-sm font-medium text-gray-700">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="correo"
                                name="correo"
                                value="{{ old('correo') }}"
                                maxlength="45"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('correo')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="direccion" class="block text-sm font-medium text-gray-700">
                                Dirección
                            </label>

                            <input
                                type="text"
                                id="direccion"
                                name="direccion"
                                value="{{ old('direccion') }}"
                                maxlength="200"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('direccion')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="municipio" class="block text-sm font-medium text-gray-700">
                                Municipio
                            </label>

                            <input
                                type="text"
                                id="municipio"
                                name="municipio"
                                value="{{ old('municipio') }}"
                                maxlength="80"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('municipio')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="codigoPostal" class="block text-sm font-medium text-gray-700">
                                Código postal
                            </label>

                            <input
                                type="text"
                                id="codigoPostal"
                                name="codigoPostal"
                                value="{{ old('codigoPostal') }}"
                                maxlength="15"
                                required
                                class="mt-1 block w-full rounded border-gray-300"
                            >

                            @error('codigoPostal')
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
                            Guardar escuela
                        </button>

                        <a
                            href="{{ route('escuelas.index') }}"
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