@props(['escuela', 'puedeEditar' => false, 'puedeRegistrar' => false])

<section class="mt-8 bg-white rounded-2xl shadow-sm p-6">
    @if ($escuela)
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs tracking-widest text-gray-500 uppercase">Datos de la escuela</p>
                <h3 class="text-xl font-semibold text-gray-900 mt-1">{{ $escuela->nombre }}</h3>
            </div>
            <span class="px-3 py-1 text-sm rounded-full {{ $escuela->estado ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                {{ $escuela->estado ? 'Activa' : 'Inactiva' }}
            </span>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            @foreach ([
                'Clave CCT' => $escuela->claveCentroTrabajo,
                'Municipio' => $escuela->municipio,
                'Código postal' => $escuela->codigoPostal,
                'Dirección' => $escuela->direccion,
                'Teléfono' => $escuela->telefono,
                'Correo' => $escuela->correo,
            ] as $etiqueta => $valor)
                <div>
                    <dt class="text-xs uppercase text-gray-500">{{ $etiqueta }}</dt>
                    <dd class="mt-1 text-gray-900">{{ $valor }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($puedeEditar)
            <div class="mt-6">
                <a href="{{ route('escuelas.edit', $escuela) }}"
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Editar datos de la escuela
                </a>
            </div>
        @endif
    @else
        <p class="text-gray-600">Aún no se han configurado los datos de la escuela.</p>
        @if ($puedeRegistrar)
            <a href="{{ route('escuelas.create') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">
                Configurar escuela
            </a>
        @endif
    @endif
</section>