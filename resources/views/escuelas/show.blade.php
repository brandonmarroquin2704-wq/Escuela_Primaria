<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Información de la escuela
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-lg bg-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <x-escuela-info
                :escuela="$escuela"
                :puedeEditar="$puedeEditar"
                :puedeRegistrar="$puedeRegistrar"
            />

            <a
                href="{{ route('dashboard') }}"
                class="inline-block rounded-lg bg-gray-500 px-5 py-3 text-lg font-semibold text-white hover:bg-gray-600"
            >
                Volver al panel
            </a>
        </div>
    </div>
</x-app-layout>