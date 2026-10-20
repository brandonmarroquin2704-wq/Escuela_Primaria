<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Escuela Primaria 21 De Octubre</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-stone-100 px-4 py-8">
            {{-- Nombre de la escuela --}}
            <a href="/" class="text-center">
                <h1 class="text-3xl font-extrabold leading-tight text-stone-900">
                    Escuela Primaria<br>21 De Octubre
                </h1>
            </a>

            {{-- Tarjeta con el formulario --}}
            <div class="mt-6 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:max-w-md sm:rounded-lg">
                {{ $slot }}
            </div>

            {{-- Logos oficiales --}}
            <x-logos-oficiales class="mt-8" />
        </div>
    </body>
</html>