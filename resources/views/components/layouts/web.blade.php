<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Retiro Del Rocio' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Hotel Logo 1.png') }}">

    {{-- Website fonts: Inter (body, closest free match to SF Pro), Poppins, Italianno (script) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Poppins:wght@500;600;700&family=Italianno&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col bg-[#242d22] font-inter text-white antialiased">
    <x-layouts.navbar />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-layouts.footer />

    @livewireScripts
</body>
</html>
