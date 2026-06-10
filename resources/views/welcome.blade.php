<x-layouts.web title="Retiro Del Rocio — Luxury Hotel & Retreat in Jos"
    description="Experience stillness at Retiro Del Rocio, a luxury retreat in Jos, Plateau State. Book rooms and apartments, wellness and spa experiences, fine dining, and curated escapes across Jos City.">
    @php
        $rooms = [
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 3.jpg'],
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 6.jpg'],
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 7.jpg'],
        ];

        $values = [
            ['title' => 'WELLNESS', 'text' => 'Nurturing mind, body and soul.'],
            ['title' => 'PURENESS', 'text' => "Inspired by nature's purest elements."],
            ['title' => 'TRANQUILITY', 'text' => 'A sanctuary for deep relaxation.'],
            ['title' => 'LUXURY', 'text' => 'Timeless experiences crafted with care.'],
            ['title' => 'HARMONY', 'text' => 'Balance, flow and inner peace.'],
        ];

        $offersHeading = 'Explore our exclusive offers';
    @endphp

    {{-- ============================ HERO + SEARCH ============================ --}}
    <section class="w-full">
        <x-layouts.container>
            <div class="relative">
                {{-- Hero image --}}
                <div class="overflow-hidden rounded-[19px]">
                    <img src="{{ asset('images/image 1.jpg') }}" alt="Retiro Del Rocio"
                         class="h-[380px] w-full object-cover sm:h-[520px] lg:h-[660px]">
                </div>

                {{-- Search panel (#d9d9d9) overlapping the hero bottom --}}
                <div class="relative z-10 mx-auto -mt-[100px] w-full rounded-[19px] bg-[#d9d9d9] px-4 py-5 shadow-2xl sm:-mt-[120px] sm:px-6 lg:-mt-[130px] lg:px-[26px] lg:py-[20px]">
                    {{-- Category tabs --}}
                    <div class="flex flex-wrap items-center gap-x-12 gap-y-3 px-1">
                        <div class="flex flex-col items-start gap-2">
                            <span class="flex items-center gap-2 text-[18px] font-semibold tracking-tight text-[#ba6d04] sm:text-[22px]">
                                <img src="{{ asset('images/fluent_bed-24-regular.png') }}" alt="" class="size-[26px] object-contain [filter:brightness(0)_saturate(100%)_invert(48%)_sepia(72%)_saturate(1100%)_hue-rotate(2deg)]">
                                Rooms &amp; Apartment
                            </span>
                            <span class="h-[3px] w-[207px] max-w-full rounded-full bg-[#ba6d04]"></span>
                        </div>
                        <span class="flex items-center gap-2 text-[18px] font-semibold tracking-tight text-[#6c6c6c] sm:text-[22px]">
                            <img src="{{ asset('images/Airport.png') }}" alt="" class="size-[28px] object-contain [filter:brightness(0)_opacity(45%)]">
                            Airport Pickup
                        </span>
                        <span class="flex items-center gap-2 text-[18px] font-semibold tracking-tight text-[#6c6c6c] sm:text-[22px]">
                            <img src="{{ asset('images/travel.png') }}" alt="" class="size-[30px] object-contain [filter:brightness(0)_opacity(45%)]">
                            Experience
                        </span>
                    </div>
                    <hr class="my-4 border-black/10">

                    {{-- Search fields (translucent cards on the gray panel) --}}
                    <div class="flex flex-wrap items-stretch gap-[9px]">
                        <div class="flex min-w-[240px] flex-1 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[26px] py-[13px]">
                            <p class="text-[15px] font-medium tracking-tight text-[#3c3c3c] sm:text-[16px]">Room Type</p>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[18px] font-bold text-black sm:text-[20px]">Deluxe (1 Bedroom)</p>
                                <img src="{{ asset('images/keyboard_arrow_down.png') }}" alt="" class="size-[24px] shrink-0 object-contain">
                            </div>
                        </div>
                        <div class="flex min-w-[150px] flex-1 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[23px] py-[14px]">
                            <p class="text-[15px] font-medium tracking-tight text-[#3c3c3c] sm:text-[16px]">Number of Guest</p>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[18px] font-bold text-black sm:text-[20px]">2</p>
                                <img src="{{ asset('images/keyboard_arrow_down.png') }}" alt="" class="size-[24px] shrink-0 object-contain">
                            </div>
                        </div>
                        <div class="flex min-w-[170px] flex-1 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[25px] py-[11px]">
                            <p class="text-[15px] font-medium tracking-tight text-[#3c3c3c] sm:text-[16px]">Check-in Date</p>
                            <div class="flex items-center gap-[6px]">
                                <img src="{{ asset('images/date.png') }}" alt="" class="size-[28px] shrink-0 object-contain">
                                <p class="text-[18px] font-bold text-black sm:text-[20px]">23/05/2026</p>
                            </div>
                        </div>
                        <div class="flex min-w-[170px] flex-1 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[25px] py-[11px]">
                            <p class="text-[15px] font-medium tracking-tight text-[#3c3c3c] sm:text-[16px]">Check-out Date</p>
                            <div class="flex items-center gap-[7px]">
                                <img src="{{ asset('images/date.png') }}" alt="" class="size-[28px] shrink-0 object-contain">
                                <p class="text-[18px] font-bold text-black sm:text-[20px]">25/05/2026</p>
                            </div>
                        </div>
                        <div class="flex min-w-[170px] flex-1 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[25px] py-[11px]">
                            <p class="text-[15px] font-medium tracking-tight text-[#3c3c3c] sm:text-[16px]">Amenities &amp; Services</p>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[18px] font-semibold tracking-tight text-[#8c8c8c] sm:text-[20px]">Select</p>
                                <img src="{{ asset('images/keyboard_arrow_down.png') }}" alt="" class="size-[24px] shrink-0 object-contain">
                            </div>
                        </div>
                        <button type="button"
                                class="flex min-h-[73px] min-w-[180px] flex-1 items-center justify-center gap-[10px] rounded-[14px] bg-[#ba6d04] text-[19px] font-semibold tracking-tight text-white transition hover:bg-[#a35f03]">
                            Search
                            <img src="{{ asset('images/search-line.png') }}" alt="" class="size-[24px] object-contain [filter:brightness(0)_invert(1)]">
                        </button>
                    </div>
                </div>
            </div>
        </x-layouts.container>
    </section>

    {{-- ====================== WHERE STILLNESS FINDS YOU ====================== --}}
    <section class="relative w-full overflow-hidden py-20 lg:pb-[120px]">
        <x-layouts.container>
            {{-- Heading with the del.png wordmark sitting behind + below it (like the design) --}}
            <div class="relative">
                {{-- Watermark: flush left, anchored just under the heading's first line, extending down --}}
                <img src="{{ asset('images/del.png') }}" alt="" aria-hidden="true"
                     class="pointer-events-none absolute left-0 top-[40px] z-0 w-full max-w-none select-none pl-2 opacity-[10] sm:top-[58px] lg:top-[78px]">

                <div class="relative z-10 grid grid-cols-1 items-start gap-6 px-4 sm:px-8 lg:grid-cols-2 lg:gap-16 lg:px-[60px]">
                    <h2 class="text-4xl font-medium leading-tight tracking-tight text-white sm:text-6xl lg:text-[72px] lg:leading-[74px]">
                        Where stillness finds you
                    </h2>
                    <p class="text-lg leading-relaxed tracking-tight text-white/90 lg:pt-3 lg:text-[22px]">
                        Retiro Del Rocio blends modern hospitality with intentional living. From intelligent room experiences and personalized comfort to curated wellness spaces and attentive service, every part of your journey is designed to feel effortless.
                    </p>
                </div>
            </div>

            {{-- Mobile fallback: the strip's labels stacked, since the wide strip is unreadable on small screens --}}
            <div class="relative z-10 mt-10 grid grid-cols-2 gap-x-6 gap-y-8 px-4 sm:grid-cols-3 sm:px-8 lg:hidden">
                @foreach ($values as $value)
                    <div class="flex flex-col gap-1">
                        <p class="text-[13px] font-semibold tracking-[0.15em] text-[#caa468]">{{ $value['title'] }}</p>
                        <p class="text-[13px] leading-snug text-white/60">{{ $value['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-layouts.container>

    </section>

    
        {{-- Values strip (roll.jpg) — full-bleed, full viewport width --}}
        <img src="{{ asset('images/roll.jpg') }}"
             alt="Wellness · Pureness · Tranquility · Luxury · Harmony"
             class="mt-10 hidden w-full lg:mt-14 lg:block">

    {{-- ===== TEAL BACKDROP (Rectangle 611.jpg) behind offers #1 + destination ===== --}}
    <div class="relative bg-no-repeat"
         style="background-image: url('{{ str_replace(' ', '%20', asset('images/Rectangle 611.jpg')) }}'); background-size: 100% 100%;">

    {{-- ===================== EXCLUSIVE OFFERS (carousel) ====================== --}}
    <x-layouts.offers :rooms="$rooms" :heading="$offersHeading" />

    {{-- ===================== MORE THAN A DESTINATION ======================= --}}
    <section class="w-full py-12 lg:py-16">
        <x-layouts.container>
            <div class="relative overflow-hidden rounded-2xl">
                <img src="{{ asset('images/image 5.png') }}" alt="More than a destination"
                     class="h-[440px] w-full object-cover sm:h-[600px] lg:h-[780px]">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>

                {{-- Heading (left) + paragraph (right), aligned to the bottom --}}
                <div class="absolute inset-x-0 bottom-0 flex flex-col gap-6 p-6 sm:p-10 lg:flex-row lg:items-end lg:gap-12 lg:p-16">
                    <h2 class="text-4xl font-medium leading-none tracking-tight text-white sm:text-6xl lg:w-[640px] lg:shrink-0 lg:text-[92px] lg:leading-[90px]">
                        More than a<br>destination
                    </h2>
                    <p class="text-base leading-relaxed tracking-tight text-white/90 lg:flex-1 lg:pb-3 lg:text-[22px]">
                        Surrounded by calming architecture and refined hospitality, every experience is thoughtfully crafted to help you slow down and reconnect. From personalized room experiences and wellness-centered spaces to seamless service and quiet luxury, every detail is designed to make your stay feel effortless.
                    </p>
                </div>
            </div>
        </x-layouts.container>
    </section>
    </div>
    {{-- /teal backdrop (Rectangle 611) --}}

    {{-- ========================= BECOME A MEMBER =========================== --}}
    <section class="w-full py-12 lg:py-16">
        <x-layouts.container>
            <div class="grid grid-cols-1 overflow-hidden rounded-[6px] lg:grid-cols-[57%_43%]">
                <img src="{{ asset('images/image 47.jpg') }}" alt=""
                     class="h-[260px] w-full object-cover lg:h-full lg:min-h-[508px]">
                <div class="flex flex-col justify-center gap-[27px] bg-[#e8e6e1] px-8 py-12 lg:px-[51px] lg:py-[80px]">
                    <div class="flex flex-col gap-[12px] text-[#343a40]">
                        <h2 class="text-3xl font-bold leading-tight tracking-tight sm:text-4xl lg:text-[52px] lg:leading-[52px]">
                            Become a member of Retiro Del Rocio
                        </h2>
                        <p class="text-base leading-snug lg:text-[20px]">
                            Get exclusive discounts on services, experiences, and curated destinations across Jos and beyond. Enjoy member-only perks designed to help you explore more for less.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                                class="flex items-center justify-center rounded-[6px] bg-[#ba6d04] px-6 py-4 text-[20px] font-semibold text-white transition hover:bg-[#a35f03]">
                            Subscribe
                        </button>
                        <button type="button"
                                class="flex items-center justify-center gap-3 rounded-[6px] px-6 py-4 text-[20px] font-semibold text-[#343a40] transition hover:text-[#ba6d04]">
                            Contact Us
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><path d="m10 8 4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </x-layouts.container>
    </section>

    {{-- ================== EXCLUSIVE OFFERS (carousel) #2 ===================== --}}
    <x-layouts.offers :rooms="$rooms" :heading="$offersHeading" />

    {{-- ===================== BEYOND THE STAY / JOS ======================== --}}
    <section class="w-full py-16 lg:py-24">
        <x-layouts.container class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{-- top-left landscape --}}
            <img src="{{ asset('images/IMG_2625 1.jpg') }}" alt="Jos City" class="h-[240px] w-full rounded-xl object-cover lg:h-[300px]">
            {{-- top-right text --}}
            <div class="flex flex-col justify-center gap-[24px]">
                <h2 class="text-4xl font-medium leading-tight tracking-tight text-white sm:text-5xl lg:text-[60px] lg:leading-[60px]">
                    Beyond the stay<br>Explore Jos City
                </h2>
                <p class="text-base leading-relaxed tracking-tight text-white/90 lg:text-[20px]">
                    Experience the beauty and calm that make Jos truly unforgettable. From breathtaking rock landscapes and cool weather to peaceful nature trails and rich local culture, every moment invites discovery. Whether you seek adventure, relaxation, or quiet reflection, Jos offers a refreshing escape where nature, serenity, and memorable experiences come together beautifully.
                </p>
            </div>
            {{-- bottom-left ruins --}}
            <img src="{{ asset('images/IMG_2620 2.jpg') }}" alt="Jos City" class="h-[240px] w-full rounded-xl object-cover lg:h-[320px]">
            {{-- bottom-right forest --}}
            <img src="{{ asset('images/IMG_2627 2.jpg') }}" alt="Jos City" class="h-[240px] w-full rounded-xl object-cover lg:h-[320px]">
        </x-layouts.container>
    </section>

    {{-- ============ WELLNESS LIFESTYLE + TRAIN. RECOVER. RECHARGE. ========= --}}
    <section class="w-full py-12 lg:py-16">
        <x-layouts.container class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Left: wellness lifestyle tall image card --}}
            <div class="relative min-h-[480px] overflow-hidden rounded-2xl lg:min-h-[760px]">
                <img src="{{ asset('images/image 14.jpg') }}" alt="Wellness lifestyle"
                     class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 flex flex-col gap-[29px] p-8 lg:p-[60px]">
                    <h2 class="max-w-[420px] text-3xl font-medium leading-tight tracking-tight text-white sm:text-4xl lg:text-[60px] lg:leading-[58px]">
                        Explore our wellness lifestyle
                    </h2>
                    <button type="button"
                            class="flex h-[64px] w-[220px] items-center justify-center rounded-[10px] bg-[#ba6d04] text-[20px] font-semibold tracking-tight text-white transition hover:bg-[#a35f03]">
                        Explore
                    </button>
                </div>
            </div>

            {{-- Right: train/recover text + gym image --}}
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-[24px]">
                    <h2 class="text-3xl font-medium leading-tight tracking-tight text-white sm:text-5xl lg:text-[60px] lg:leading-[60px]">
                        Train. Recover. Recharge.
                    </h2>
                    <p class="text-base leading-relaxed tracking-tight text-white/90 lg:text-[20px]">
                        Stay active and restore your balance in a space designed for movement, wellness, and recovery. Whether you’re maintaining your routine, starting your day with energy, or unwinding after a long one, our fitness and wellness experience is designed to help you feel refreshed, focused, and recharged throughout your stay.
                    </p>
                </div>
                <img src="{{ asset('images/image 13.jpg') }}" alt="Wellness"
                     class="h-[300px] w-full flex-1 rounded-2xl object-cover lg:h-auto lg:min-h-[480px]">
            </div>
        </x-layouts.container>
    </section>
</x-layouts.web>
