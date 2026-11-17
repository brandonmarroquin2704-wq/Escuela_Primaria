<x-app-layout>
    <x-slot name="header">
        <h2 class="truncate text-lg font-bold text-[#1E3A5F]">Foro de avisos</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-4 px-4 py-6 sm:px-6">
        @if (session('success'))
            <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('docente.foro.create') }}"
           class="flex min-h-12 w-full items-center justify-center rounded-lg bg-[#1E3A5F] px-5 py-3 text-base font-bold text-white transition hover:bg-[#2563A6] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563A6] sm:w-auto">
            Nuevo comunicado
        </a>

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
                <p class="mt-1 text-xs text-[#667085]">Publicado el {{ $c->created_at->format('d/m/Y H:i') }}</p>

                @if ($c->fecha_entrega)
                    <p class="mt-2 text-sm font-semibold text-[#263548]">Fecha de entrega: {{ $c->fecha_entrega->format('d/m/Y') }}</p>
                @endif

                <p class="mt-3 break-words text-base text-[#263548]">{!! nl2br(e($c->contenido)) !!}</p>

                <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('docente.foro.edit', $c) }}"
                       class="flex min-h-12 items-center justify-center rounded-lg border border-[#2563A6] px-4 py-2 font-bold text-[#2563A6] hover:bg-[#EAF3FC] sm:min-w-32">
                        Editar
                    </a>
                    <form method="POST" action="{{ route('docente.foro.destroy', $c) }}"
                          onsubmit="return confirm('¿Eliminar este comunicado?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="flex min-h-12 w-full items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 font-bold text-red-700 hover:bg-red-100 sm:min-w-32">
                            Eliminar
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <p class="rounded-lg border border-dashed border-[#DCE6F2] bg-white px-4 py-8 text-center text-[#667085]">
                Aún no has publicado comunicados.
            </p>
        @endforelse

        {{ $comunicados->links() }}
    </div>
</x-app-layout>