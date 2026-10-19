@props(['escuela', 'puedeEditar' => false, 'puedeRegistrar' => false])

<section class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
    @if ($escuela)
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-gray-500">Datos de la escuela</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-900">{{ $escuela->nombre }}</h3>
            </div>

            <span class="rounded-full px-4 py-1 text-base font-semibold {{ $escuela->estado ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                {{ $escuela->estado ? 'Activa' : 'Inactiva' }}
            </span>
        </div>

        <dl class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach ([
                'Clave CCT' => $escuela->claveCentroTrabajo,
                'Municipio' => $escuela->municipio,
                'Código postal' => $escuela->codigoPostal,
                'Dirección' => $escuela->direccion,
                'Teléfono' => $escuela->telefono,
                'Correo' => $escuela->correo,
            ] as $etiqueta => $valor)
                <div>
                    <dt class="text-sm font-semibold uppercase text-gray-500">{{ $etiqueta }}</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $valor }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($puedeEditar)
            <div class="mt-8">
                <a
                    href="{{ route('escuelas.edit', $escuela) }}"
                    class="inline-block rounded-lg bg-blue-600 px-5 py-3 text-lg font-semibold text-white hover:bg-blue-700"
                >
                    Editar datos de la escuela
                </a>
            </div>
        @endif
    @else
        <p class="text-lg text-gray-600">Aún no se han configurado los datos de la escuela.</p>

        @if ($puedeRegistrar)
            <a
                href="{{ route('escuelas.create') }}"
                class="mt-4 inline-block rounded-lg bg-blue-600 px-5 py-3 text-lg font-semibold text-white hover:bg-blue-700"
            >
                Configurar escuela
            </a>
        @endif
    @endif
</section>