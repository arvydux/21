<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen {{ Route::is('orders') ? 'bg-[#020617] m-0 p-0 overflow-hidden' : 'bg-white dark:bg-zinc-800' }}">

        {{ $slot }}

        @fluxScripts
    </body>
</html>
