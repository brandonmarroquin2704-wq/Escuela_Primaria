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

    <div class="min-h-[calc(100vh-5rem)] bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
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
                class="overflow-hidden rounded-3xl border border-blue-100 bg-gradient-to-br from-blue-100 via-blue-50 to-white text-black shadow-lg shadow-blue-900/5"
            >
                <div class="h-2 bg-blue-600"></div>

                <div class="px-6 py-10 sm:px-10 sm:py-12 lg:px-16">
                    <p class="text-sm font-bold uppercase tracking-[0.25em] text-slate-500 sm:text-base">
                        Sistema de control escolar
                    </p>

                    <div class="mt-5 rounded-2xl border border-blue-100 bg-white/80 p-4 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">
                                Sesión iniciada
                            </p>

                            <p class="mt-1 text-xl font-bold text-black sm:text-2xl">
                                Bienvenido: {{ $usuarioEscolar->nombre }}
                                {{ $usuarioEscolar->apellidoPaterno }}
                                {{ $usuarioEscolar->apellidoMaterno }}
                            </p>
                        </div>

                        <p class="mt-3 inline-flex w-fit rounded-full bg-blue-100 px-4 py-2 text-base font-bold text-blue-900 ring-1 ring-blue-300 sm:mt-0">
                            {{ $rol }}
                        </p>
                    </div>

                    <p class="mt-8 text-xl font-semibold text-slate-600 sm:text-2xl">Bienvenido a</p>
                    <h1 class="text-4xl font-extrabold leading-tight text-black sm:text-5xl">
                        Escuela primaria 21 De Octubre
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-relaxed text-slate-700 sm:text-lg">
                        {{ $descripcion }}
                    </p>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>