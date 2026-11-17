<x-app-layout>
    <x-slot name="header">
        <h2 class="truncate text-lg font-bold text-[#1E3A5F]">Avisos de los docentes</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-4 px-4 py-6 sm:px-6">
        @forelse ($comunicados as $c)
            <article class="rounded-lg border border-[#DCE6F2] bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="rounded-full bg-[#EAF3FC] px-3 py-1 font-bold text-[#1E3A5F]">{{ \App\Models\Comunicado::TIPOS[$c->tipo] ?? $c->tipo }}</span>
                    <span class="text-[#667085]">
                        {{ $c->asignacion?->grupo?->grado }}° {{ $c->asignacion?->grupo?->letraGrupo }}
                        · {{ $c->asignacion?->asignatura?->nombre }}
                    </span>
                </div>

                <h3 class="mt-2 break-words text-lg font-bold text-[#1E3A5F]">{{ $c->titulo }}</h3>
                <p class="mt-1 text-xs text-[#667085]">
                    {{ $c->docente?->nombre }} {{ $c->docente?->apellidoPaterno }} · {{ $c->created_at->format('d/m/Y H:i') }}
                </p>

                @if ($c->fecha_entrega)
                    <p class="mt-2 text-sm font-semibold text-[#263548]">Fecha de entrega: {{ $c->fecha_entrega->format('d/m/Y') }}</p>
                @endif

                <p class="mt-3 break-words text-base text-[#263548]">{!! nl2br(e($c->contenido)) !!}</p>

                <p class="mt-4 rounded-lg bg-[#F5F8FC] px-4 py-3 text-sm text-[#667085]">
                    {{ $c->mensajeAutomatico() }}
                </p>
            </article>
        @empty
            <p class="rounded-lg border border-dashed border-[#DCE6F2] bg-white px-4 py-8 text-center text-[#667085]">
                No hay avisos por el momento.
            </p>
        @endforelse

        {{ $comunicados->links() }}
    </div>
</x-app-layout>