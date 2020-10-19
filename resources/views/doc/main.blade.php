<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Documentation</title>

    <!-- Scripts -->
    <script src="{{ url('public/js/app.js') }}" defer></script>
    <script src="{{ url('public/js/bootstrap.min.js') }}" defer></script>
    <script src="{{ url('public/js/bootstrap.bundle.min.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ url('public/css/app.css') }}" rel="stylesheet">
    <link href="{{ url('public/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('public/css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <link href="{{ url('public/css/bootstrap-reboot.min.css') }}" rel="stylesheet">

</head>
<body>
<div id="app">


    <main class="h-screen flex">
        @yield('content')
    </main>
</div>
</body>
</html>
