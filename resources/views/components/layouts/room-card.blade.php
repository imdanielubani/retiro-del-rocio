@props([
    'image' => '',
    'name' => "Pandora's Suite",
    'price' => '₦350,000',
    'href' => null,
])

@php($href = $href ?? route('rooms.show'))

<a href="{{ $href }}" wire:navigate class="group relative block overflow-hidden rounded-2xl">
    <img src="{{ asset('images/'.$image) }}" alt="{{ $name }}"
         class="h-[320px] w-full object-cover transition duration-500 group-hover:scale-105 sm:h-[400px] lg:h-[500px]">
    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-transparent"></div>

    <div class="absolute inset-x-0 bottom-0 flex flex-col gap-4 p-6 lg:p-9">
        {{-- Title + price --}}
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-2xl font-semibold tracking-tight text-white lg:text-[40px]">{{ $name }}</p>
            <p class="flex items-baseline gap-1 text-white">
                <span class="text-2xl font-bold tracking-tight lg:text-[30px]">{{ $price }}</span>
                <span class="text-base font-semibold text-white/60 lg:text-[21px]">/ night</span>
            </p>
        </div>

        {{-- Amenities --}}
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[16px] font-medium tracking-tight text-white lg:text-[20px]">
            <span class="flex items-center gap-1.5">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 8V3h5M21 8V3h-5M3 16v5h5M21 16v5h-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                45 Sq ft
            </span>
            <span class="flex items-center gap-1.5">
                <img src="{{ asset('images/fluent_bed-24-regular.png') }}" alt="" class="size-5 shrink-0 object-contain [filter:brightness(0)_invert(1)]">
                Twin Bed
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M16 4a3 3 0 0 1 0 6M21 20c0-2.5-1.5-4.6-3.6-5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                2 Guest
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 12V6a2 2 0 0 1 2-2 2 2 0 0 1 2 2M3 12h18v2a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5v-2zM6 19l-1 2M18 19l1 2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                1 Bathroom
            </span>
        </div>
    </div>
</a>
