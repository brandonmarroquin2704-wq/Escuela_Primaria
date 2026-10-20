@php
    $descripcion = [
        'Administrador' => 'Administra la información académica, los grupos, alumnos, docentes y asignaturas desde un solo lugar.',
        'Director' => 'Consulta la información institucional y académica autorizada desde el menú lateral.',
        'Docente' => 'Consulta tus grupos asignados, atiende solicitudes de acceso al salón virtual y gestiona la información académica autorizada.',
        'Tutor' => 'Consulta el estado de tus solicitudes y la información escolar autorizada de tus hijos.',
    ][$rol] ?? '';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Panel de {{ $rol }}
        </h2>
    </x-slot>

    <div class="min-h-[calc(100vh-5rem)] bg-stone-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">

            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <section
                class="overflow-hidden rounded-3xl border border-stone-300 bg-crema px-6 py-12 text-black shadow-lg sm:px-10 sm:py-16 lg:px-16"
            >
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-neutral-600 sm:text-base">
                    Sistema de control escolar
                </p>

                <div class="mt-5 rounded-2xl border border-stone-300 bg-white/70 p-4 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-neutral-600">
                            Sesión iniciada
                        </p>

                        <p class="mt-1 text-xl font-bold text-black sm:text-2xl">
                            Bienvenido: {{ $usuarioEscolar->nombre }}
                            {{ $usuarioEscolar->apellidoPaterno }}
                            {{ $usuarioEscolar->apellidoMaterno }}
                        </p>
                    </div>

                    <p class="mt-3 inline-flex w-fit rounded-full bg-crema-oscuro px-4 py-2 text-base font-bold text-black sm:mt-0">
                        ({{ $rol }})
                    </p>
                </div>

                <h1 class="mt-8 text-4xl font-extrabold leading-tight text-black sm:text-5xl lg:text-7xl">
                    Bienvenido a<br>
                    Primaria 21 De Octubre
                </h1>

                <p class="mt-6 max-w-2xl text-base leading-relaxed text-neutral-700 sm:text-lg lg:text-xl">
                    {{ $descripcion }}
                </p>
            </section>
        </div>
    </div>
</x-app-layout>