@props([
    'title' => 'Dashboard',
    'subtitle' => null,
])

@php
    $user = auth()->user();
    $initial = strtoupper(mb_substr($user->name ?? 'A', 0, 1));

    // Single (non-expandable) primary item.
    $dashboard = ['label' => 'Dashboard', 'icon' => 'Dashboard.png', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')];

    // Operations section. Items with `children` are expandable; others are direct links.
    // NOTE: submenu links use '#' placeholders until their module routes exist.
    $operations = [
        ['key' => 'apartments', 'label' => 'Apartments', 'icon' => 'Exclude.png', 'children' => [
            ['label' => 'Bookings', 'href' => '#'],
            ['label' => 'Rooms', 'href' => '#'],
        ]],
        ['key' => 'restaurants', 'label' => 'Restaurants', 'icon' => 'restaurants.png', 'children' => [
            ['label' => 'Menus', 'href' => '#'],
            ['label' => 'Reservations', 'href' => '#'],
        ]],
        ['key' => 'car-rentals', 'label' => 'Car Rentals', 'icon' => 'car.png', 'children' => [
            ['label' => 'Vehicles', 'href' => '#'],
            ['label' => 'Bookings', 'href' => '#'],
        ]],
        ['key' => 'spa', 'label' => 'Spa & Wellness', 'icon' => 'spa&wellness.png', 'children' => [
            ['label' => 'Services', 'href' => '#'],
            ['label' => 'Appointments', 'href' => '#'],
        ]],
        ['label' => 'Website CMS', 'icon' => 'websitecms.png', 'href' => '#'],
        ['label' => 'Gym', 'icon' => 'gym.png', 'href' => '#'],
        ['key' => 'cinema', 'label' => 'Cinema', 'icon' => 'cinema.png', 'children' => [
            ['label' => 'Movies', 'href' => '#'],
            ['label' => 'Showtimes', 'href' => '#'],
        ]],
        ['label' => 'Payment', 'icon' => 'payment.png', 'href' => '#'],
        ['label' => 'Role & Permissions', 'icon' => 'spa&wellness.png', 'href' => '#'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Hotel Logo 1.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#222a1f] text-[#1e1e1e] antialiased">
    <div
        x-data="{
            collapsed: JSON.parse(localStorage.getItem('sidebarCollapsed') ?? 'false'),
            open: null,
            mobileOpen: false,
            isDesktop: window.matchMedia('(min-width: 1024px)').matches,
            get mini() { return this.collapsed && this.isDesktop },
            toggleMenu(key) { this.open = this.open === key ? null : key },
            init() {
                this.$watch('collapsed', v => localStorage.setItem('sidebarCollapsed', JSON.stringify(v)));
                const mq = window.matchMedia('(min-width: 1024px)');
                mq.addEventListener('change', e => { this.isDesktop = e.matches; if (e.matches) this.mobileOpen = false; });
            },
        }"
        class="flex h-screen overflow-hidden"
    >
        {{-- Mobile backdrop --}}
        <div x-show="mobileOpen" x-cloak x-transition.opacity @click="mobileOpen = false"
             class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>
        {{-- ===== Sidebar ===== --}}
        <aside
            :class="{
                'max-lg:translate-x-0!': mobileOpen,
                'lg:w-[88px]': mini,
                'lg:w-[280px]': !mini,
            }"
            class="fixed inset-y-0 left-0 z-50 flex w-[280px] shrink-0 flex-col overflow-hidden bg-[#222a1f] px-4 py-6 transition-transform duration-300 ease-in-out max-lg:-translate-x-full lg:static lg:z-auto lg:translate-x-0 lg:bg-transparent lg:transition-[width]"
        >
            {{-- Logo (static) --}}
            <div class="flex shrink-0 items-center justify-center py-1">
                <img x-show="!mini" x-cloak src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-20 w-auto object-contain">
                <img x-show="mini" x-cloak src="{{ asset('images/Hotel Logo 1.png') }}" alt="" class="h-9 w-auto object-contain">
            </div>

            <div x-show="mini" x-cloak class="my-4 h-px w-full shrink-0 bg-white/10"></div>

            {{-- User card (static) --}}
            <div class="mt-4 flex shrink-0 items-center gap-3 rounded-xl bg-white/5 p-3" :class="mini ? 'justify-center bg-transparent p-0' : ''">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[#f38c00] text-[15px] font-bold text-white">{{ $initial }}</div>
                <div x-show="!mini" x-cloak class="min-w-0">
                    <p class="truncate text-[14px] font-bold text-white">{{ $user->name }}</p>
                    <p class="truncate text-[12px] text-[#9aa491]">{{ $user->email }}</p>
                </div>
            </div>

            {{-- Nav (scrollable) --}}
            <nav class="no-scrollbar mt-6 flex min-h-0 flex-1 flex-col gap-1 overflow-y-auto">
                {{-- Dashboard --}}
                <a href="{{ $dashboard['href'] }}" wire:navigate @click="mobileOpen = false"
                   @class([
                       'group flex items-center gap-3 rounded-xl px-3 py-3 text-[14px] font-medium transition',
                       'bg-[#3a4631] text-white' => $dashboard['active'],
                       'text-[#c7cfc0] hover:bg-white/5 hover:text-white' => ! $dashboard['active'],
                   ])
                   :class="mini ? 'justify-center' : ''"
                   x-bind:title="mini ? '{{ $dashboard['label'] }}' : ''">
                    <img src="{{ asset('images/'.$dashboard['icon']) }}" alt="" class="size-5 shrink-0">
                    <span x-show="!mini" x-cloak>{{ $dashboard['label'] }}</span>
                </a>

                {{-- Operations label --}}
                <p x-show="!mini" x-cloak class="mt-5 mb-1 px-3 text-[12px] text-[#7d8a72]">Operations</p>

                @foreach ($operations as $item)
                    @if (! empty($item['children']))
                        {{-- Expandable item --}}
                        <button type="button" @click="toggleMenu('{{ $item['key'] }}')"
                                class="group flex items-center gap-3 rounded-xl px-3 py-3 text-[14px] font-medium text-[#c7cfc0] transition hover:bg-white/5 hover:text-white"
                                :class="mini ? 'justify-center' : ''"
                                x-bind:title="mini ? '{{ $item['label'] }}' : ''">
                            <img src="{{ asset('images/'.$item['icon']) }}" alt="" class="size-5 shrink-0">
                            <span x-show="!mini" x-cloak class="flex-1 text-left">{{ $item['label'] }}</span>
                            <svg x-show="!mini" x-cloak class="size-4 shrink-0 transition-transform duration-200"
                                 :class="open === '{{ $item['key'] }}' ? '-rotate-180' : ''"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        {{-- Submenu --}}
                        <div x-show="!mini && open === '{{ $item['key'] }}'" x-collapse x-cloak
                             class="ml-5 flex flex-col rounded-xl bg-white/5 p-1.5">
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['href'] }}" @click="mobileOpen = false"
                                   class="rounded-lg px-4 py-2 text-[14px] text-[#c7cfc0] transition hover:bg-white/5 hover:text-white">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        {{-- Direct link --}}
                        <a href="{{ $item['href'] }}" @click="mobileOpen = false"
                           class="group flex items-center gap-3 rounded-xl px-3 py-3 text-[14px] font-medium text-[#c7cfc0] transition hover:bg-white/5 hover:text-white"
                           :class="mini ? 'justify-center' : ''"
                           x-bind:title="mini ? '{{ $item['label'] }}' : ''">
                            <img src="{{ asset('images/'.$item['icon']) }}" alt="" class="size-5 shrink-0">
                            <span x-show="!mini" x-cloak>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            {{-- Footer (static) --}}
            <div class="mt-6 flex shrink-0 flex-col gap-2">
                <a href="#" @click="mobileOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-3 text-[14px] font-medium text-[#c7cfc0] transition hover:bg-white/5 hover:text-white"
                   :class="mini ? 'justify-center' : ''"
                   x-bind:title="mini ? 'Settings' : ''">
                    <img src="{{ asset('images/settings.png') }}" alt="" class="size-5 shrink-0">
                    <span x-show="!mini" x-cloak>Settings</span>
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl bg-[#f38c00] px-3 py-3 text-[14px] font-bold text-white transition hover:bg-[#dd7f00]"
                            :class="mini ? 'justify-center' : ''"
                            x-bind:title="mini ? 'Logout' : ''">
                        <img src="{{ asset('images/logout.png') }}" alt="" class="size-5 shrink-0">
                        <span x-show="!mini" x-cloak>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== Main panel ===== --}}
        <main class="m-3 flex flex-1 flex-col overflow-hidden rounded-[20px] bg-[#f3f3ee] sm:rounded-[28px] lg:ml-0">
            {{-- Topbar --}}
            <header class="m-3 flex items-center justify-between gap-3 rounded-2xl bg-white px-4 py-3 shadow-sm sm:m-4 sm:gap-4 sm:px-5">
                <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                    {{-- Mobile: open drawer --}}
                    <button type="button" @click="mobileOpen = true"
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl border border-[#e5e7eb] transition hover:bg-[#f9fafb] lg:hidden"
                            aria-label="Open menu">
                        <svg class="size-5 text-[#1e1e1e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18M3 12h18M3 18h18" />
                        </svg>
                    </button>
                    {{-- Desktop: collapse toggle --}}
                    <button type="button" @click="collapsed = !collapsed"
                            class="hidden size-10 shrink-0 items-center justify-center rounded-xl border border-[#e5e7eb] transition hover:bg-[#f9fafb] lg:flex"
                            aria-label="Toggle sidebar">
                        <img x-show="!collapsed" x-cloak src="{{ asset('images/arrowback.png') }}" alt="" class="size-10">
                        <img x-show="collapsed" x-cloak src="{{ asset('images/arrowfront.png') }}" alt="" class="size-10">
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-[17px] font-bold leading-tight text-[#1e1e1e] sm:text-[20px]">{{ $title }}</h1>
                        @if ($subtitle)
                            <p class="truncate text-[12px] text-[#6b7280] sm:text-[13px]">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-4">
                    {{-- Search --}}
                    <div class="relative hidden md:block">
                        <img src="{{ asset('images/search-line.png') }}" alt="" class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 opacity-50">
                        <input type="text" placeholder="Search pages..."
                               class="h-11 w-[200px] rounded-full bg-[#f3f4f6] pl-11 pr-4 text-[14px] text-[#1e1e1e] placeholder:text-[#9ca3af] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20 lg:w-[360px]">
                    </div>

                    {{-- Bell --}}
                    <button type="button" class="relative flex size-10 items-center justify-center rounded-full transition hover:bg-[#f3f4f6]" aria-label="Notifications">
                        <img src="{{ asset('images/bell-03.png') }}" alt="" class="size-5">
                    </button>

                    {{-- User --}}
                    <div class="hidden items-center gap-3 sm:flex">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[#f38c00] text-[15px] font-bold text-white">{{ $initial }}</div>
                        <div class="hidden leading-tight md:block">
                            <p class="text-[14px] font-bold text-[#1e1e1e]">{{ $user->name }}</p>
                            <p class="text-[12px] text-[#9ca3af]">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto px-3 pb-3 sm:px-4 sm:pb-4">
                {{ $slot }}
            </div>
        </main>
    </div>

    <x-toast />

    @livewireScripts
</body>
</html>
