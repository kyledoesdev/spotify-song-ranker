<!--
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

(c){{ now()->format('Y') }} kyledoesdev. All rights reserved.

(c){{ now()->format('Y') }} songrank.dev All rights reserved.

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Show custom set title or default title() helper -->
        <title>{{ $title ?? title() }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- SEO stuff -->
        @include('layouts.partials.meta')

        <!-- Fonts -->
        <script src="https://kit.fontawesome.com/07b7751319.js" crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fredoka:400,500,600" rel="stylesheet">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex flex-col min-h-screen gradient-background">
        <main class="flex-1" id="app">
            <div class="container mx-auto p-4">
                @include('layouts.partials.messages')
                
                {{-- todo this sucks --}}
                @if (! in_array(Route::currentRouteName(), ['welcome', 'about', 'terms']))
                    <livewire:navigation />
                @endif

                {{ $slot }}
            </div>
        </main>

        <footer class="bg-dark text-light">
            @include('layouts.partials.footer')
            
            {{-- for now --}}
            @auth
                <x-support-bubble />
            @endauth
        </footer>
    </body>
</html>
