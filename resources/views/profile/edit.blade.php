@php
    $esTutor = $perfil?->rol?->nombre === 'Tutor';
    $nombreCompleto = $perfil
        ? trim(preg_replace('/\s+/', ' ', $perfil->nombre . ' ' . $perfil->apellidoPaterno . ' ' . $perfil->apellidoMaterno))
        : null;
    $iniciales = $perfil
        ? mb_strtoupper(mb_substr($perfil->nombre, 0, 1) . mb_substr($perfil->apellidoPaterno, 0, 1))
        : '—';

    $rol = $perfil?->rol?->nombre;
    $tipoResponsable = $perfil?->datosTutor?->tipoResponsable;
    $subtitulo = collect([$rol, $tipoResponsable])->filter()->implode(' · ');

    $correo = $user->email;
    $telefono = $perfil?->datosTutor?->telefono;
    $telefonoDigitos = $telefono ? preg_replace('/\D+/', '', $telefono) : null;
    $telefonoVisible = $telefono;
    if ($telefonoDigitos && strlen($telefonoDigitos) === 10) {
        $telefonoVisible = substr($telefonoDigitos, 0, 3) . ' ' . substr($telefonoDigitos, 3, 3) . ' ' . substr($telefonoDigitos, 6);
    }

    $registro = $perfil?->fRegistro?->format('d/m/Y');
    $cuentaActiva = $perfil ? (bool) $perfil->estado : null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Mi cuenta
        </h2>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">

        {{-- Encabezado --}}
        <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-4">
                <span class="mt-1 hidden h-12 w-12 shrink-0 place-items-center rounded-full bg-blue-50 text-blue-700 sm:grid" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 17a7 7 0 0 1 14 0v.5H3V17z" />
                    </svg>
                </span>
                <div>
                    <nav aria-label="Ruta de navegación" class="text-sm text-slate-600">
                        <a href="{{ route('dashboard') }}" class="font-medium text-blue-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">Inicio</a>
                        <span aria-hidden="true">/</span>
                        <span class="font-semibold text-slate-800">Mi cuenta</span>
                    </nav>
                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Mi cuenta</h1>
                    <p class="mt-1 text-base text-slate-700">Administra tus datos personales y la seguridad de tu acceso.</p>
                </div>
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-reduce:transition-none"
            >
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.7 15.7a1 1 0 0 1-1.4 0l-5-5a1 1 0 0 1 0-1.4l5-5a1 1 0 0 1 1.4 1.4L6.4 9H16a1 1 0 1 1 0 2H6.4l3.3 3.3a1 1 0 0 1 0 1.4z" clip-rule="evenodd" />
                </svg>
                Volver al panel
            </a>
        </header>

        @if (session('status') || session('success'))
            <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 font-semibold text-green-800">
                {{ session('status') ?? session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">

            {{-- Perfil --}}
            <section aria-labelledby="titulo-perfil" class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm lg:col-span-2">
                <div class="flex flex-col gap-4 border-b border-blue-100 bg-blue-50 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex items-center gap-4">
                        <span class="grid h-16 w-16 shrink-0 place-items-center rounded-full bg-blue-700 text-xl font-bold text-white" role="img" aria-label="Iniciales de {{ $nombreCompleto ?? 'la cuenta' }}">{{ $iniciales }}</span>
                        <div class="min-w-0">
                            <h2 id="titulo-perfil" class="break-words text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $nombreCompleto ?? 'Nombre no disponible' }}</h2>
                            @if ($subtitulo !== '')
                                <p class="text-sm font-medium text-slate-700">{{ $subtitulo }}</p>
                            @endif
                        </div>
                    </div>

                    @if ($cuentaActiva === true)
                        <span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 ring-1 ring-green-200">
                            <span class="h-2 w-2 rounded-full bg-green-600" aria-hidden="true"></span>
                            Cuenta activa
                        </span>
                    @elseif ($cuentaActiva === false)
                        <span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-slate-200 px-3 py-1 text-sm font-semibold text-slate-800 ring-1 ring-slate-300">
                            <span class="h-2 w-2 rounded-full bg-slate-500" aria-hidden="true"></span>
                            Cuenta inactiva
                        </span>
                    @endif
                </div>

                <div class="divide-y divide-blue-100">
                    {{-- Información personal --}}
                    <div class="p-6 sm:p-8">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-blue-900">
                            {{ $esTutor ? 'Datos del padre o tutor' : 'Datos personales' }}
                        </h3>
                        <dl class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-semibold text-slate-600">Nombre completo</dt>
                                <dd class="mt-1 break-words text-lg font-semibold text-slate-900">{{ $nombreCompleto ?: 'No disponible' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-slate-600">Rol</dt>
                                <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $rol ?: 'No disponible' }}</dd>
                            </div>
                            @if ($perfil?->datosTutor)
                                <div>
                                    <dt class="text-sm font-semibold text-slate-600">Tipo de responsable</dt>
                                    <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $tipoResponsable ?: 'No disponible' }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Contacto --}}
                    <div class="p-6 sm:p-8">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-blue-900">Información de contacto</h3>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 p-4" x-data="{ copiado: false }">
                                <p class="text-sm font-semibold text-slate-600">Correo electrónico</p>
                                @if ($correo)
                                    <a href="mailto:{{ $correo }}" class="mt-1 inline-block min-h-[44px] break-all text-lg font-bold text-blue-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">{{ $correo }}</a>
                                    <div class="flex items-center gap-3">
                                        <button
                                            type="button"
                                            @click="navigator.clipboard.writeText('{{ $correo }}').then(() => { copiado = true; setTimeout(() => copiado = false, 2500) })"
                                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
                                        >Copiar correo</button>
                                        <span x-show="copiado" x-cloak role="status" class="text-sm font-semibold text-green-800">Correo copiado</span>
                                    </div>
                                @else
                                    <p class="mt-1 text-lg font-semibold text-slate-900">No disponible</p>
                                @endif
                            </div>

                            @if ($perfil?->datosTutor)
                                <div class="rounded-xl border border-slate-200 p-4" x-data="{ copiado: false }">
                                    <p class="text-sm font-semibold text-slate-600">Teléfono</p>
                                    @if ($telefonoDigitos)
                                        <a href="tel:{{ $telefonoDigitos }}" aria-label="Llamar al {{ $telefonoVisible }}" class="mt-1 inline-block min-h-[44px] break-all text-lg font-bold text-blue-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">{{ $telefonoVisible }}</a>
                                        <div class="flex items-center gap-3">
                                            <button
                                                type="button"
                                                @click="navigator.clipboard.writeText('{{ $telefonoDigitos }}').then(() => { copiado = true; setTimeout(() => copiado = false, 2500) })"
                                                class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
                                            >Copiar teléfono</button>
                                            <span x-show="copiado" x-cloak role="status" class="text-sm font-semibold text-green-800">Teléfono copiado</span>
                                        </div>
                                    @else
                                        <p class="mt-1 text-lg font-semibold text-slate-900">No disponible</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Cuenta --}}
                    <div class="p-6 sm:p-8">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-blue-900">Información de la cuenta</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-semibold text-slate-600">Fecha de registro</dt>
                                <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $registro ?: 'No disponible' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="border-t border-blue-100 bg-slate-50 px-6 py-4 sm:px-8">
                    <p class="text-sm text-slate-700">
                        <span class="font-semibold">¿Hay algún dato incorrecto?</span>
                        Solicita la corrección al administrador.
                    </p>
                </div>
            </section>

            {{-- Seguridad --}}
            <aside class="space-y-6" aria-label="Seguridad de la cuenta">
                <section aria-labelledby="titulo-seguridad" class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-blue-50 text-blue-700" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0 1 10 0v2a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2zm8-2v2H7V7a3 3 0 0 1 6 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <div>
                            <h2 id="titulo-seguridad" class="text-lg font-bold text-slate-900">Seguridad de la cuenta</h2>
                            <p class="mt-1 text-sm text-slate-700">Protege tu acceso actualizando tu contraseña cuando lo necesites.</p>
                        </div>
                    </div>

                    <a
                        href="{{ route('profile.password') }}"
                        class="mt-5 inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-blue-700 px-5 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-reduce:transition-none"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0 1 10 0v2a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2zm8-2v2H7V7a3 3 0 0 1 6 0z" clip-rule="evenodd" />
                        </svg>
                        Actualizar contraseña
                    </a>
                </section>

                <section class="rounded-2xl border border-blue-100 bg-blue-50 p-6">
                    <h2 class="text-base font-bold text-blue-950">Consejo de seguridad</h2>
                    <p class="mt-2 text-sm text-blue-950">
                        No compartas tu contraseña y evita usar la misma en otros servicios.
                    </p>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>