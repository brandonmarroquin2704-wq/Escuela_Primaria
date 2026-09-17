<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar rol
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('roles.store') }}">
                    @csrf

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">
                            Nombre del rol
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            maxlength="25"
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

                    <div class="mt-4">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="3"
                            maxlength="45"
                            class="mt-1 block w-full rounded border-gray-300"
                        >{{ old('descripcion') }}</textarea>

                        @error('descripcion')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            style="background-color: #2563eb; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; border: none;"
                        >
                            Guardar rol
                        </button>

                        <a
                            href="{{ route('roles.index') }}"
                            style="background-color: #6b7280; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; text-decoration: none;"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>