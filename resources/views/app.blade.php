<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js'])
    @inertiaHead
    @routes
</head>
<body class="antialiased">
    @inertia
    <div id="modals"></div>
</body>
</html>
