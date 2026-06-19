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
            ['label' => 'Bookings', 'href' => route('admin.bookings.index'), 'active' => request()->routeIs('admin.bookings.*')],
            ['label' => 'Rooms', 'href' => route('admin.rooms.index'), 'active' => request()->routeIs('admin.rooms.*')],
        ]],
        ['key' => 'restaurants', 'label' => 'Restaurants', 'icon' => 'restaurants.png', 'children' => [
            ['label' => 'Menus', 'href' => '#'],
            ['label' => 'Reservations', 'href' => '#'],
        ]],
        ['key' => 'car-rentals', 'label' => 'Airport Pickups', 'svg' => 'airport', 'children' => [
            ['label' => 'Vehicles', 'href' => route('admin.vehicles.index'), 'active' => request()->routeIs('admin.vehicles.index')],
            ['label' => 'Bookings', 'href' => route('admin.vehicles.bookings'), 'active' => request()->routeIs('admin.vehicles.bookings')],
        ]],
        ['key' => 'spa', 'label' => 'Spa & Wellness', 'icon' => 'spa&wellness.png', 'children' => [
            ['label' => 'Services', 'href' => '#'],
            ['label' => 'Appointments', 'href' => '#'],
        ]],
        ['key' => 'website-cms', 'label' => 'Website CMS', 'icon' => 'websitecms.png', 'children' => [
            ['label' => 'Content', 'href' => route('admin.cms.edit'), 'active' => request()->routeIs('admin.cms.*')],
            ['label' => 'Messages', 'href' => route('admin.messages.index'), 'active' => request()->routeIs('admin.messages.*')],
        ]],
        ['label' => 'Gym', 'icon' => 'gym.png', 'href' => '#'],
        ['key' => 'cinema', 'label' => 'Cinema', 'icon' => 'cinema.png', 'children' => [
            ['label' => 'Movies', 'href' => '#'],
            ['label' => 'Showtimes', 'href' => '#'],
        ]],
        ['label' => 'Payment', 'icon' => 'payment.png', 'href' => route('admin.payment.index'), 'active' => request()->routeIs('admin.payment.*')],
        ['label' => 'Role & Permissions', 'icon' => 'spa&wellness.png', 'href' => '#'],
    ];

    // Which expandable group should start open (the one containing the active route).
    $activeMenu = collect($operations)
        ->first(fn ($item) => ! empty($item['children']) && collect($item['children'])->contains(fn ($c) => $c['active'] ?? false))['key'] ?? null;
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
            open: @js($activeMenu),
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

            {{-- Demarcation line above the navigation --}}
            <div class="my-2 h-px w-full shrink-0 bg-white/20"></div>

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
                                @class([
                                    'group flex items-center gap-3 rounded-xl px-3 py-3 text-[14px] font-medium transition',
                                    'bg-[#3a4631] text-white' => $activeMenu === $item['key'],
                                    'text-[#c7cfc0] hover:bg-white/5 hover:text-white' => $activeMenu !== $item['key'],
                                ])
                                :class="mini ? 'justify-center' : ''"
                                x-bind:title="mini ? '{{ $item['label'] }}' : ''">
                            @if (($item['svg'] ?? null) === 'airport')
                                {{-- Registered website "Airport pick-up" icon --}}
                                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M2 19h20v2H2zM4 17h16l-1-6h-3l-2-7-2 .5 1.5 6.5H8l-1.5-3H5l1 3H4z"/></svg>
                            @else
                                <img src="{{ asset('images/'.$item['icon']) }}" alt="" class="size-5 shrink-0">
                            @endif
                            <span x-show="!mini" x-cloak class="flex-1 text-left">{{ $item['label'] }}</span>
                            <svg x-show="!mini" x-cloak class="size-4 shrink-0 transition-transform duration-200"
                                 :class="open === '{{ $item['key'] }}' ? '-rotate-180' : ''"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        {{-- Submenu --}}
                        <div x-show="!mini && open === '{{ $item['key'] }}'" x-collapse x-cloak
                             class="flex flex-col rounded-xl bg-white/5 p-1.5">
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['href'] }}" @if(($child['href'] ?? '#') !== '#') wire:navigate @endif @click="mobileOpen = false"
                                   @class([
                                       'rounded-lg px-4 py-2 text-[14px] transition',
                                       'bg-white/10 font-semibold text-white' => $child['active'] ?? false,
                                       'text-[#c7cfc0] hover:bg-white/5 hover:text-white' => ! ($child['active'] ?? false),
                                   ])>
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        {{-- Direct link --}}
                        <a href="{{ $item['href'] }}" @if(($item['href'] ?? '#') !== '#') wire:navigate @endif @click="mobileOpen = false"
                           @class([
                               'group flex items-center gap-3 rounded-xl px-3 py-3 text-[14px] font-medium transition',
                               'bg-[#3a4631] text-white' => $item['active'] ?? false,
                               'text-[#c7cfc0] hover:bg-white/5 hover:text-white' => ! ($item['active'] ?? false),
                           ])
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
            <header class="m-3 flex items-center justify-between gap-3 rounded-[15px] border border-[#e5e5e5]/95 bg-white px-3.5 py-2.5 sm:m-4 sm:gap-4">
                <div class="flex min-w-0 items-center gap-3 sm:gap-[15px]">
                    {{-- Mobile: open drawer --}}
                    <button type="button" @click="mobileOpen = true"
                            class="flex size-8 shrink-0 items-center justify-center rounded-md border border-[#ededed] bg-white text-[#1e1e1e] transition hover:bg-[#f9fafb] lg:hidden"
                            aria-label="Open menu">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18M3 12h18M3 18h18" />
                        </svg>
                    </button>
                    {{-- Desktop: collapse toggle --}}
                    <button type="button" @click="collapsed = !collapsed"
                            class="hidden size-8 shrink-0 items-center justify-center rounded-md border border-[#ededed] bg-white text-[#1e1e1e] transition hover:bg-[#f9fafb] lg:flex"
                            aria-label="Toggle sidebar">
                        <svg class="size-4 transition-transform duration-200" :class="collapsed ? 'rotate-180' : ''"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m17 7-5 5 5 5M11 7l-5 5 5 5" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-[17px] font-bold leading-tight text-[#1e1e1e] sm:text-[20px]">{{ $title }}</h1>
                        @if ($subtitle)
                            <p class="truncate text-[12px] text-[#6b7280]">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-[13px]">
                    {{-- Search --}}
                    <div class="relative hidden md:block">
                        <svg class="pointer-events-none absolute left-[17px] top-1/2 size-[18px] -translate-y-1/2 text-[#9f9f9f]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
                        <input type="text" placeholder="Search pages..."
                               class="h-[37px] w-[240px] rounded-full border border-[#e5e5e5]/90 bg-[#f5f5f0] pl-[44px] pr-4 text-[14px] text-[#1e1e1e] placeholder:text-[#9f9f9f] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20 lg:w-[312px]">
                    </div>

                    {{-- Notifications (real-time, polled) --}}
                    <livewire:admin.notifications.bell />

                    {{-- User --}}
                    <div class="hidden items-center gap-[10px] sm:flex">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-[18px] bg-[#f38c00] text-[18px] font-bold leading-none text-white">{{ $initial }}</div>
                        <div class="hidden leading-tight md:block">
                            <p class="text-[15px] font-bold text-[#1e1e1e]">{{ $user->name }}</p>
                            <p class="text-[12px] text-[#99a694]">{{ $user->email }}</p>
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
