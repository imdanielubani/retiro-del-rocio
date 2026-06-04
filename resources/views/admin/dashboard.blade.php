<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Retiro Del Rocio</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Hotel Logo 1.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#f9fafb] text-[#1e1e1e]">
    <div class="min-h-screen">
        <header class="flex items-center justify-between border-b border-[#e5e7eb] bg-[#222a1f] px-8 py-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/Hotel Logo 1.png') }}" alt="Logo" class="h-9 w-auto">
                <span class="text-lg font-bold text-white">Retiro Del Rocio <span class="text-[#f38c00]">Admin</span></span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                        class="rounded-lg bg-[#f38c00] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#dd7f00]">
                    Sign out
                </button>
            </form>
        </header>

        <main class="mx-auto max-w-5xl px-8 py-12">
            <h1 class="text-2xl font-bold">Welcome back, {{ auth()->user()->name }} 👋</h1>
            <p class="mt-2 text-sm text-[#6b7280]">
                You are signed in as
                <span class="font-medium text-[#1e1e1e]">{{ auth()->user()->getRoleNames()->implode(', ') ?: 'admin' }}</span>.
                This is a placeholder dashboard — the authentication flow is complete.
            </p>

            <div class="mt-8 rounded-2xl border border-[#e5e7eb] bg-white p-6">
                <p class="text-sm text-[#6b7280]">Next up: build out the dashboard modules (bookings, rooms, users, CMS, etc.).</p>
            </div>
        </main>
    </div>
</body>
</html>
