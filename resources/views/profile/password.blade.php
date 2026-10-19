<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Actualizar contraseña
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">

                @if (session('status') === 'password-updated')
                    <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
                        Contraseña actualizada correctamente.
                    </div>
                @endif

                <p class="mb-4 text-sm text-gray-600">
                    Usa una contraseña de al menos 8 caracteres que no uses en otros sitios.
                </p>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">
                            Contraseña actual
                        </label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            autocomplete="current-password"
                            required
                            class="mt-1 block w-full rounded border-gray-300"
                        >
                        @error('current_password', 'updatePassword')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Nueva contraseña
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            required
                            class="mt-1 block w-full rounded border-gray-300"
                        >
                        @error('password', 'updatePassword')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirmar nueva contraseña
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            required
                            class="mt-1 block w-full rounded border-gray-300"
                        >
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            style="background-color: #2563eb; color: #ffffff; padding: 10px 16px; border-radius: 6px; font-weight: 600; border: none;"
                        >
                            Guardar contraseña
                        </button>

                        <a
                            href="{{ route('profile.edit') }}"
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