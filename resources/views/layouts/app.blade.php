<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', "Kyle's Song Ranker") }}</title>

        <!-- Fonts -->
        <script src="https://kit.fontawesome.com/07b7751319.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        <style>
            body, html {
                margin: 0;
                padding: 0;
                height: 100%;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .gradient-background {
                background: linear-gradient(45deg, rgba(207,120,224,1) 25%, rgba(100,249,179,1));
                background-size: 400% 400%; /* Adjust for animation speed */
                animation: gradientAnimation 20s linear infinite;
            }

            /* Create a keyframe animation for the background */
            @keyframes gradientAnimation {
                0%, 100% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
            },
        </style>
    </head>
    <body class="gradient-background">
        <div id="app" style="padding-bottom: 10vh">
            <div class="container-fluid">
                <main class="m-4">
                    @yield('content')
                </main>
            </div>
        </div>
        <footer class="bg-dark text-white" style="margin-top: auto; min-height: 10vh; overflow: hidden;">
            @include('layouts.partials.footer')
        </footer>
    </body>
</html>
