<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('title')
        <title>{{ config('app.name') }} - @yield('title')</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="m-3 font-sans {{ $theme === 'dark' ? 'dark-theme' : 'light-theme' }}">
@include('layouts.navbar')
@include('layouts.navlink')
<div class="container-fluid">
    <div class="mt-4 mb-4">
        @yield('content')
    </div>
</div>
{{--@include('layouts.footer')--}}

</body>
</html>
