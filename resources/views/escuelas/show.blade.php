@php
    $dato = fn ($valor) => filled($valor) ? $valor : null;

    $telefono = $escuela?->telefono;
    $telefonoDigitos = $telefono ? preg_replace('/\D+/', '', $telefono) : null;
    $telefonoVisible = $telefono;
    if ($telefonoDigitos && strlen($telefonoDigitos) === 10) {
        $telefonoVisible = substr($telefonoDigitos, 0, 3) . ' ' . substr($telefonoDigitos, 3, 3) . ' ' . substr($telefonoDigitos, 6);
    }

    $correo = $escuela?->correo;

    $actualizacion = null;
    if ($escuela && filled($escuela->fechaActualizacion)) {
        try {
            $actualizacion = \Illuminate\Support\Carbon::parse($escuela->fechaActualizacion)->format('d/m/Y');
        } catch (\Throwable $e) {
            $actualizacion = null;
        }
    }

    $puedeIrAEditar = $escuela && $puedeEditar && Route::has('escuelas.edit');
    $puedeIrACrear = ! $escuela && $puedeRegistrar && Route::has('escuelas.create');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-800">
            Información de la escuela
        </h2>
    </x-slot>

    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl space-y-6">

            @if (session('success'))
                <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 font-semibold text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-semibold text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Encabezado --}}
            <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <nav aria-label="Ruta de navegación" class="text-sm text-slate-600">
                        <a href="{{ route('dashboard') }}" class="font-medium text-blue-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">Inicio</a>
                        <span aria-hidden="true">/</span>
                        <span class="font-semibold text-slate-800">Información de la escuela</span>
                    </nav>

                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Información de la escuela</h1>
                    <p class="mt-1 text-base text-slate-700">
                        Consulta los datos generales y medios de contacto de la institución.
                    </p>
                </div>

                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-reduce:transition-none"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9.7 15.7a1 1 0 0 1-1.4 0l-5-5a1 1 0 0 1 0-1.4l5-5a1 1 0 0 1 1.4 1.4L6.4 9H16a1 1 0 1 1 0 2H6.4l3.3 3.3a1 1 0 0 1 0 1.4z" clip-rule="evenodd" />
                    </svg>
                    Volver a mi panel
                </a>
            </header>

            @if ($escuela)
                {{-- Perfil institucional --}}
                <section aria-labelledby="nombre-escuela" class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">
                    <div class="flex flex-col gap-4 border-b border-blue-100 bg-blue-50 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                        <div class="flex items-center gap-4">
                            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-blue-700 text-white" aria-hidden="true">
                                <svg class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2 1.5 6.5 10 11l6.5-3.4V13H18V6.5L10 2zM4.5 10.8v3.2c0 1.2 2.5 2.5 5.5 2.5s5.5-1.3 5.5-2.5v-3.2L10 13.7l-5.5-2.9z" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h2 id="nombre-escuela" class="break-words text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $escuela->nombre }}</h2>
                                <p class="text-sm font-medium text-slate-700">Institución educativa</p>
                            </div>
                        </div>

                        @if ($escuela->estado)
                            <span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 ring-1 ring-green-200">
                                <span class="h-2 w-2 rounded-full bg-green-600" aria-hidden="true"></span>
                                Activa
                            </span>
                        @else
                            <span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-slate-200 px-3 py-1 text-sm font-semibold text-slate-800 ring-1 ring-slate-300">
                                <span class="h-2 w-2 rounded-full bg-slate-500" aria-hidden="true"></span>
                                Inactiva
                            </span>
                        @endif
                    </div>

                    <div class="divide-y divide-blue-100">
                        {{-- Datos generales --}}
                        <div class="p-6 sm:p-8">
                            <h3 class="text-sm font-bold uppercase tracking-wide text-blue-900">Datos generales</h3>
                            <dl class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-semibold text-slate-600">Clave del centro de trabajo (CCT)</dt>
                                    <dd class="mt-1 break-words text-lg font-semibold text-slate-900">{{ $dato($escuela->claveCentroTrabajo) ?? 'No disponible' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-slate-600">Código postal</dt>
                                    <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $dato($escuela->codigoPostal) ?? 'No disponible' }}</dd>
                                </div>
                                @if ($actualizacion)
                                    <div>
                                        <dt class="text-sm font-semibold text-slate-600">Última actualización</dt>
                                        <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $actualizacion }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        {{-- Ubicación --}}
                        <div class="p-6 sm:p-8">
                            <h3 class="text-sm font-bold uppercase tracking-wide text-blue-900">Ubicación</h3>
                            <div class="mt-4 flex items-start gap-3">
                                <svg class="mt-1 h-5 w-5 shrink-0 text-blue-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18s6-5.3 6-10a6 6 0 1 0-12 0c0 4.7 6 10 6 10zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" clip-rule="evenodd" />
                                </svg>
                                <dl class="grid flex-1 grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-semibold text-slate-600">Dirección</dt>
                                        <dd class="mt-1 break-words text-lg font-semibold text-slate-900">{{ $dato($escuela->direccion) ?? 'No disponible' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-semibold text-slate-600">Municipio</dt>
                                        <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $dato($escuela->municipio) ?? 'No disponible' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        {{-- Contacto --}}
                        <div class="p-6 sm:p-8">
                            <h3 class="text-sm font-bold uppercase tracking-wide text-blue-900">Contacto</h3>

                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                                        <svg class="h-5 w-5 text-blue-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M2 3.5A1.5 1.5 0 0 1 3.5 2h2.2a1 1 0 0 1 1 .8l.6 2.7a1 1 0 0 1-.3.9L5.7 7.5a11 11 0 0 0 6.8 6.8l1.1-1.3a1 1 0 0 1 .9-.3l2.7.6a1 1 0 0 1 .8 1v2.2a1.5 1.5 0 0 1-1.5 1.5C8.6 18 2 11.4 2 3.5z" />
                                        </svg>
                                        Teléfono
                                    </div>

                                    @if ($telefonoDigitos)
                                        <a
                                            href="tel:{{ $telefonoDigitos }}"
                                            aria-label="Llamar a la escuela al {{ $telefonoVisible }}"
                                            class="mt-2 inline-block min-h-[44px] break-all text-lg font-bold text-blue-800 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                        >{{ $telefonoVisible }}</a>
                                        <p class="text-sm text-slate-600">Toca para llamar</p>
                                    @else
                                        <p class="mt-2 text-lg font-semibold text-slate-900">No disponible</p>
                                    @endif
                                </div>

                                <div class="rounded-xl border border-slate-200 p-4" x-data="{ copiado: false }">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                                        <svg class="h-5 w-5 text-blue-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M2.5 5A1.5 1.5 0 0 1 4 3.5h12A1.5 1.5 0 0 1 17.5 5v.4l-7.5 4.6L2.5 5.400V5z" />
                                            <path d="M2.5 7.7V15A1.5 1.5 0 0 0 4 16.500h12a1.5 1.5 0 0 0 1.500-1.500V7.700l-7.100 4.300a.8.8 0 0 1-.8 0L2.500 7.700z" />
                                        </svg>
                                        Correo electrónico
                                    </div>

                                    @if ($correo)
                                        <a
                                            href="mailto:{{ $correo }}"
                                            class="mt-2 inline-block min-h-[44px] break-all text-lg font-bold text-blue-800 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                        >{{ $correo }}</a>

                                        <div class="flex items-center gap-3">
                                            <button
                                                type="button"
                                                @click="navigator.clipboard.writeText('{{ $correo }}').then(() => { copiado = true; setTimeout(() => copiado = false, 2500) })"
                                                class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 motion-reduce:transition-none"
                                            >
                                                Copiar correo
                                            </button>
                                            <span x-show="copiado" x-cloak role="status" class="text-sm font-semibold text-green-800">Correo copiado</span>
                                        </div>
                                    @else
                                        <p class="mt-2 text-lg font-semibold text-slate-900">No disponible</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($puedeIrAEditar)
                        <div class="border-t border-blue-100 bg-slate-50 px-6 py-4 sm:px-8">
                            <a
                                href="{{ route('escuelas.edit', $escuela) }}"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 sm:w-auto motion-reduce:transition-none"
                            >
                                Editar datos de la escuela
                            </a>
                        </div>
                    @endif
                </section>
            @else
                <section class="rounded-2xl border border-blue-100 bg-white p-8 text-center shadow-sm">
                    <p class="text-lg font-semibold text-slate-900">La información de la escuela aún no está disponible.</p>
                    <p class="mt-1 text-slate-700">Comunícate con la administración para solicitar los datos.</p>

                    @if ($puedeIrACrear)
                        <a
                            href="{{ route('escuelas.create') }}"
                            class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-reduce:transition-none"
                        >
                            Configurar escuela
                        </a>
                    @endif
                </section>
            @endif
        </div>
    </div>
</x-app-layout>