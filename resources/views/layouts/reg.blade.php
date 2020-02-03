<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
   

    <!-- Fonts -->
 
    <!-- Styles -->
    <link href="{{ asset('css/login_style.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
    
        <main class="py-4 reg" style="margin-top: 100px">
            @yield('content')
        </main>
    </div>
</body>
</html>
