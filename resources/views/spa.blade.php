<x-layouts.web title="Spa & Wellness — Retiro Del Rocio"
    description="Rejuvenate mind, body and soul at Retiro Del Rocio. Explore our spa services — skincare, massage, sauna baths and more — and discover a healthier way to relax in Jos.">

    @php
        $services = [
            ['title' => cms('spa.service_1_title'), 'image' => cms_image('spa.service_1_image')],
            ['title' => cms('spa.service_2_title'), 'image' => cms_image('spa.service_2_image')],
            ['title' => cms('spa.service_3_title'), 'image' => cms_image('spa.service_3_image')],
            ['title' => cms('spa.service_4_title'), 'image' => cms_image('spa.service_4_image')],
        ];
        $features = cms_array('spa.features');
        $heroCtaUrl = cms('spa.hero_cta_url') ?: '#';

        // Decorative icons for the "Why us" features (match the Figma, by position).
        $featureIcons = [
            asset('images/products.png'),
            asset('images/temaki_spa.png'),
            asset('images/treatments.png'),
            asset('images/ic_twotone-spa.png'),
        ];
    @endphp

    {{-- ============================ HERO ============================ --}}
    <section class="relative w-full">
        <img loading="eager" fetchpriority="high" src="{{ cms_image('spa.hero_image') }}" alt="Spa & Wellness"
             class="h-[460px] w-full object-cover sm:h-[600px] lg:h-[720px]">
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/45 to-black/10"></div>
        <x-layouts.container class="absolute inset-0 flex items-center">
            <div class="flex max-w-[640px] flex-col gap-6">
                <h1 class="text-4xl font-semibold leading-tight tracking-tight text-white sm:text-5xl lg:text-display lg:leading-[1.05]">
                    {{ cms('spa.hero_title') }}
                </h1>
                <p class="max-w-[520px] text-lg leading-relaxed tracking-tight text-white/90 lg:text-body-lg">
                    {{ cms('spa.hero_text') }}
                </p>
                <a href="{{ $heroCtaUrl }}" @if (str_starts_with($heroCtaUrl, '/')) wire:navigate @endif
                   class="flex w-fit items-center gap-2.5 rounded-[10px] bg-[#ba6d04] px-8 py-4 text-body-lg font-semibold tracking-tight text-white transition hover:bg-[#a35f03]">
                    {{ cms('spa.hero_cta_label') }}
                    <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        </x-layouts.container>
    </section>

    {{-- ===================== EXPLORE OUR SPA SERVICES ===================== --}}
    <section class="w-full py-16 lg:py-24">
        <x-layouts.container class="flex flex-col gap-10 lg:gap-14">
            <div class="mx-auto flex max-w-[920px] flex-col gap-4 text-center">
                <h2 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl lg:text-h1">{{ cms('spa.services_title') }}</h2>
                <p class="text-base leading-relaxed tracking-tight text-white/70 lg:text-body-lg">{{ cms('spa.services_text') }}</p>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($services as $service)
                    <div class="flex flex-col gap-4">
                        <div class="overflow-hidden rounded-2xl">
                            @if ($service['image'])
                                <img loading="lazy" src="{{ $service['image'] }}" alt="{{ $service['title'] }}"
                                     class="h-[300px] w-full object-cover transition duration-300 hover:scale-105 lg:h-[419px]">
                            @else
                                <div class="flex h-[300px] w-full items-center justify-center bg-[#373d35] lg:h-[419px]">
                                    <svg class="size-10 text-white/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
                                </div>
                            @endif
                        </div>
                        <p class="text-center text-title font-semibold tracking-tight text-white lg:text-h3">{{ $service['title'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-layouts.container>
    </section>

    {{-- ================= DISCOVER A HEALTHIER WAY TO RELAX ================= --}}
    <section class="relative w-full">
        <img loading="lazy" src="{{ cms_image('spa.discover_image') }}" alt=""
             class="h-[460px] w-full object-cover lg:h-[560px]">
        <div class="absolute inset-0 bg-black/65"></div>
        <x-layouts.container class="absolute inset-0 flex items-center">
            <div class="grid grid-cols-1 items-center gap-6 lg:grid-cols-2 lg:gap-16">
                <p class="order-2 text-lg leading-relaxed tracking-tight text-white/90 lg:order-1 lg:text-body-lg">
                    {{ cms('spa.discover_text') }}
                </p>
                <h2 class="order-1 text-3xl font-semibold leading-tight tracking-tight text-white sm:text-4xl lg:order-2 lg:text-h1">
                    {{ cms('spa.discover_title') }}
                </h2>
            </div>
        </x-layouts.container>
    </section>

    {{-- ========================= WHY RETIRO DEL ROCIO ========================= --}}
    <section class="relative w-full overflow-hidden py-16 text-black lg:py-20"
             style="background-image: linear-gradient(95deg, #feefe4 2%, #fafaee 27%, #fafaee 68%, #f6d7c3 99%);">
        {{-- Faint spa background image overlay --}}
        <img loading="lazy" src="{{ asset('images/spa/why-bg.jpg') }}" alt=""
             class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.09]">

        <x-layouts.container class="relative flex flex-col items-center gap-10 lg:gap-12">
            <h2 class="text-center text-[clamp(28px,4vw,41px)] font-light tracking-tight text-black">{{ cms('spa.why_title') }}</h2>

            <div class="grid w-full grid-cols-1 gap-x-7 gap-y-12 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($features as $i => $feature)
                    <div class="flex flex-col items-center gap-2 text-center">
                        <span class="flex h-[88px] items-end justify-center">
                            <img loading="lazy" src="{{ $featureIcons[$i % count($featureIcons)] }}" alt="" class="max-h-[88px] w-auto object-contain">
                        </span>
                        <h3 class="text-2xl font-semibold tracking-tight text-black lg:text-[30px]">{{ $feature['title'] ?? '' }}</h3>
                        <p class="max-w-[340px] text-base leading-snug tracking-tight text-black lg:text-[19px]">{{ $feature['text'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </x-layouts.container>
    </section>
</x-layouts.web>
