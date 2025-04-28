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
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

{{--    title--}}
    @hasSection('title')
        <title> {{ config('app.name') }} - @yield('title')</title>
        @else
    <title> {{ config('app.name') }}</title>
        @endif
</head>
<body class="bg-secondary m-3">
@include('layouts.navbar')
@include('layouts.navlink')
<div class="container-fluid">
    <div class="mt-4">
    @yield('content')
    </div>
</div>
</body>
</html>
