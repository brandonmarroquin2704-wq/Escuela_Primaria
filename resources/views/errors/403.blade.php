<x-guest-layout>
    <h2 class="text-xl font-bold text-stone-800">Acceso denegado</h2>

    <p class="mt-3 text-stone-600">
        {{ $exception->getMessage() ?: 'No tienes permiso para ver esta página.' }}
    </p>

    <div class="mt-6">
        <a href="{{ route('dashboard') }}" class="inline-block rounded border-2 border-black bg-white px-4 py-2 font-semibold text-black transition hover:bg-stone-100">
            Volver al inicio
        </a>
    </div>
</x-guest-layout>