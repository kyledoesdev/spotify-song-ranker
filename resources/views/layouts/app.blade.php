<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', "Kyle's Song Ranker") }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <script src="https://kit.fontawesome.com/07b7751319.js" crossorigin="anonymous"></script>
        <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        <style>
            body, html {
                margin: 0;
                padding: 0;
                overflow: hidden;
                height: 100%;
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
            }
        </style>
    </head>
    <body class="gradient-background">
        <div id="app">
            <div class="container-fluid">
                <main class="m-4">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
