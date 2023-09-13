<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <script src="https://kit.fontawesome.com/07b7751319.js" crossorigin="anonymous"></script>
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(90deg, rgba(207,120,224,1) 25%, rgba(100,249,179,1) 100%);
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="container-fluid">
            <main class="m-4">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
