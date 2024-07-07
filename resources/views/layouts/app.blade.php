<!--
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

(c){{ now()->format('Y') }} Kyle Online. All rights reserved.

(c){{ now()->format('Y') }} Kyle's Song Ranker. All rights reserved.

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

        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body class="gradient-background">
        <div id="app" style="padding-bottom: 10vh">
            <!-- $small var set at blade view sets larage or small container -->
            <div class="{{ isset($small) && $small === true ? 'container' : 'container-fluid' }}">
                <main class="m-4">
                    @include('layouts.partials.messages')
                    @yield('content')
                </main>
            </div>
        </div>
        <footer class="bg-dark text-light">
            @include('layouts.partials.footer')
            @stack('scripts')
        </footer>
    </body>
</html>
