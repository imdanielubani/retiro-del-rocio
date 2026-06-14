<x-layouts.web title="Pandora's Suite — Rooms & Apartment — Retiro Del Rocio"
    description="Pandora's Suite at Retiro Del Rocio — a luxury apartment with twin beds, ensuite bathroom, fitness lounge, pool, wifi, complimentary breakfast, and more. ₦350,000 / night.">

    @php
        $gallery = ['image 7bg.jpg', 'images 2.jpg', 'images 8.jpg', 'images 10.jpg', 'images 9.jpg', 'images 12.jpg'];
        $galleryUrls = array_map(fn ($img) => str_replace(' ', '%20', asset('images/'.$img)), $gallery);

        $amenities = [
            ['label' => 'Fitness Lounge', 'icon' => 'fitness'],
            ['label' => '2 Beds', 'icon' => 'bed'],
            ['label' => 'Wifi', 'icon' => 'wifi'],
            ['label' => 'Pool', 'icon' => 'pool'],
            ['label' => 'Restaurant', 'icon' => 'restaurant'],
            ['label' => 'Parking', 'icon' => 'parking'],
            ['label' => 'Complimentary Breakfast', 'icon' => 'breakfast'],
        ];

        $offers = [
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 3.jpg'],
            ['name' => "Pandora's Suite", 'price' => '₦350,000', 'image' => 'image 9.jpg'],
        ];
    @endphp

    {{-- =========================== GALLERY HERO =========================== --}}
    <section x-data="{ urls: @js($galleryUrls), i: 0, prev() { this.i = (this.i - 1 + this.urls.length) % this.urls.length }, next() { this.i = (this.i + 1) % this.urls.length } }">
        {{-- Main image --}}
        <div class="relative w-full overflow-hidden">
            <img :src="urls[i]" alt="Pandora's Suite"
                 class="h-[380px] w-full object-cover sm:h-[560px] lg:h-[720px]">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/75 via-transparent to-black/20"></div>

            {{-- Breadcrumb (top-left, over hero) --}}
            <x-layouts.container class="absolute inset-x-0 top-6 lg:top-10">
                <nav class="flex items-center gap-2 text-white">
                    <a href="{{ route('rooms') }}" wire:navigate aria-label="Back"
                       class="flex icon-xl shrink-0 items-center justify-center rounded-full transition hover:bg-white/10 lg:icon-xl">
                        <svg class="icon-lg lg:icon-xl" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <p class="text-base font-semibold tracking-tight sm:text-xl lg:text-title">
                        <a href="{{ route('home') }}" wire:navigate class="hover:text-[#f38c00]">Home</a>
                        <span class="text-white/80"> / </span>
                        <a href="{{ route('rooms') }}" wire:navigate class="hover:text-[#f38c00]">Room &amp; Apartment</a>
                        <span class="text-white/80"> / </span>
                        <span class="text-[#f38c00]">Pandora's_Suite</span>
                    </p>
                </nav>
            </x-layouts.container>

            {{-- Prev / Next --}}
            <button type="button" @click="prev" aria-label="Previous image"
                    class="absolute left-4 top-1/2 flex icon-xl -translate-y-1/2 items-center justify-center rounded-full border border-white/70 text-white transition hover:bg-white/15 lg:left-8 lg:size-[68px]">
                <svg class="icon-lg lg:icon-xl" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button type="button" @click="next" aria-label="Next image"
                    class="absolute right-4 top-1/2 flex icon-xl -translate-y-1/2 items-center justify-center rounded-full border border-white/70 text-white transition hover:bg-white/15 lg:right-8 lg:size-[68px]">
                <svg class="icon-lg lg:icon-xl" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            {{-- Dots --}}
            <div class="absolute inset-x-0 bottom-5 flex items-center justify-center gap-2">
                <template x-for="(u, idx) in urls" :key="idx">
                    <button type="button" @click="i = idx" aria-label="Go to image"
                            class="h-1.5 rounded-full transition-all"
                            :class="i === idx ? 'w-7 bg-[#f38c00]' : 'w-4 bg-white/50'"></button>
                </template>
            </div>
        </div>

        {{-- Thumbnail strip --}}
        <div class="no-scrollbar flex gap-[2px] overflow-x-auto bg-[#4e3a31]">
            <template x-for="(u, idx) in urls.slice(1)" :key="idx">
                <button type="button" @click="i = idx + 1" class="shrink-0">
                    <img :src="u" alt="" class="h-[140px] w-[220px] object-cover transition lg:h-[235px] lg:w-[352px]"
                         :class="i === idx + 1 ? 'opacity-100 ring-2 ring-[#f38c00]' : 'opacity-90 hover:opacity-100'">
                </button>
            </template>
        </div>
    </section>

    {{-- =========================== BOOKING / SUMMARY =========================== --}}
    {{-- "backdrop" block (Figma node 85:990): dark gradient behind the title + booking area --}}
    <section x-data="{
                airportModal: false,
                vehicleModal: false,
                guests: 2,
                checkIn: '2026-05-23',
                checkOut: '2026-05-25',
                location: 'Yakubu Gowon Airport (IATA)',
                passengers: 2,
                arrivalDate: '2026-05-23',
                pickupTime: '11:30',
                flightNumber: 'LOS3782923',
                pickup: null,
                selectVehicle(v) { this.pickup = v; this.vehicleModal = false; },
                fmtDate(d) { if (!d) return ''; return new Date(d + 'T00:00').toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }); },
                fmtTime(t) { if (!t) return ''; let [h, m] = t.split(':'); h = parseInt(h); const ap = h >= 12 ? 'PM' : 'AM'; h = h % 12 || 12; return h + ':' + m + ' ' + ap; },
             }"
             class="w-full bg-gradient-to-t from-[#222a1f] to-[#1e1e1e] py-12 lg:py-16">
        <x-layouts.container class="flex flex-col gap-[26px]">
            {{-- Title + price --}}
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl lg:text-h1">Pandora's Suite</h1>
                <p class="flex items-baseline gap-1 text-white">
                    <span class="text-2xl font-semibold tracking-tight lg:text-h2">₦350,000</span>
                    <span class="text-base text-white/60 lg:text-body-lg">/ night</span>
                </p>
            </div>

            {{-- Booking row (functional) --}}
            <form method="POST" action="{{ route('checkout.start') }}" class="flex flex-col gap-[3px] sm:flex-row sm:flex-wrap">
                @csrf
                <input type="hidden" name="room" value="Pandora's Suite">
                <input type="hidden" name="price" value="₦350,000">
                {{-- Airport pick-up (only submitted when a vehicle is selected) --}}
                <input type="hidden" name="pickup_vehicle" :value="pickup ? pickup.name : ''">
                <input type="hidden" name="pickup_price" :value="pickup ? pickup.price : ''">
                <input type="hidden" name="location" :value="pickup ? location : ''">
                <input type="hidden" name="passengers" :value="pickup ? passengers : ''">
                <input type="hidden" name="arrival_date" :value="pickup ? arrivalDate : ''">
                <input type="hidden" name="pickup_time" :value="pickup ? pickupTime : ''">
                <input type="hidden" name="flight_number" :value="pickup ? flightNumber : ''">

                <div class="flex min-w-[240px] flex-1 flex-col justify-center rounded-[6px] border-[0.5px] border-black/20 bg-[#f6f6f6]/[0.87] px-[23px] py-[14px]">
                    <label class="text-body-sm font-medium tracking-tight text-black">Number of Guest</label>
                    <div class="flex items-center justify-between">
                        <select name="guests" x-model.number="guests"
                                class="w-full appearance-none bg-transparent text-body-lg font-bold text-black focus:outline-none">
                            <template x-for="n in 10" :key="n"><option :value="n" x-text="n"></option></template>
                        </select>
                        <img src="{{ asset('images/keyboard_arrow_down.png') }}" alt="" class="pointer-events-none icon-md shrink-0 object-contain">
                    </div>
                </div>
                <div class="flex min-w-[200px] flex-1 flex-col justify-center rounded-[6px] border-[0.5px] border-black/20 bg-[#f6f6f6]/[0.87] px-[25px] py-[11px]">
                    <label class="text-body-sm font-medium tracking-tight text-black">Check-in Date</label>
                    <div class="flex items-center gap-[5px]">
                        <img src="{{ asset('images/date.png') }}" alt="" class="icon-lg shrink-0 object-contain">
                        <input type="date" name="check_in" x-model="checkIn"
                               @click="$event.target.showPicker && $event.target.showPicker()"
                               class="w-full bg-transparent text-body-lg font-bold text-black focus:outline-none [&::-webkit-calendar-picker-indicator]:hidden">
                    </div>
                </div>
                <div class="flex min-w-[200px] flex-1 flex-col justify-center rounded-[6px] border-[0.5px] border-black/20 bg-[#f6f6f6]/[0.87] px-[25px] py-[11px]">
                    <label class="text-body-sm font-medium tracking-tight text-black">Check-out Date</label>
                    <div class="flex items-center gap-[7px]">
                        <img src="{{ asset('images/date.png') }}" alt="" class="icon-lg shrink-0 object-contain">
                        <input type="date" name="check_out" x-model="checkOut"
                               @click="$event.target.showPicker && $event.target.showPicker()"
                               class="w-full bg-transparent text-body-lg font-bold text-black focus:outline-none [&::-webkit-calendar-picker-indicator]:hidden">
                    </div>
                </div>
                <button type="submit"
                        class="flex min-h-[78px] min-w-[200px] items-center justify-center rounded-[6px] bg-[#ba6d04] px-6 text-body-lg font-semibold tracking-tight text-white transition hover:bg-[#a35f03] sm:min-w-[279px]">
                    Make reservation
                </button>
            </form>

            {{-- Airport pickup trigger (shown until a vehicle is selected) --}}
            <button type="button" x-show="!pickup" @click="airportModal = true" class="flex w-fit cursor-pointer items-center gap-1.5 text-white transition hover:text-[#f38c00]">
                <svg class="icon-lg shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="3"/></svg>
                <span class="text-body-lg font-bold tracking-tight">Airport Pick-up</span>
            </button>

            {{-- Selected airport pick-up summary (Figma node 85:1299) --}}
            <div x-show="pickup" x-cloak
                 class="flex flex-col gap-3 rounded-[6px] bg-[#dadbda] px-[23px] py-3.5 text-[#232323] lg:flex-row lg:flex-wrap lg:items-center lg:gap-x-8">
                <button type="button" @click="pickup = null" class="flex shrink-0 cursor-pointer items-center gap-1.5" title="Remove airport pick-up">
                    <svg class="icon-lg shrink-0 text-[#191919]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="4"/><path d="m8 12 3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-body-lg font-bold tracking-tight lg:text-body-lg">Airport Pick-up</span>
                </button>
                <span class="flex items-center gap-2 text-body font-medium lg:text-body-lg">
                    Car Type:
                    <img :src="pickup ? pickup.img : ''" alt="" class="h-[21px] w-[32px] shrink-0 object-contain">
                    <span x-text="pickup ? pickup.name : ''"></span>
                </span>
                <span class="text-body font-medium lg:text-body-lg">Arrival Date: <span x-text="fmtDate(arrivalDate)"></span></span>
                <span class="text-body font-medium lg:text-body-lg">Arrival Time: <span x-text="fmtTime(pickupTime)"></span></span>
                <span class="text-body-lg font-semibold text-[#191919] lg:ml-auto lg:text-title" x-text="pickup ? pickup.price : ''"></span>
            </div>

            {{-- Discount terms --}}
            <div class="flex flex-col gap-1.5">
                <p class="text-title font-semibold tracking-tight text-white">Discount Terms</p>
                <p class="flex flex-wrap items-center gap-x-2 text-body-lg">
                    <span class="font-medium tracking-tight text-white">Book for 3 days and get a discount of 50% on the overall checkout.</span>
                    <a href="#" class="tracking-tight text-[#5d9efa] hover:underline">Terms and Conditions apply</a>
                </p>
            </div>
        </x-layouts.container>

        {{-- ===================== AIRPORT PICK-UP POPUP ===================== --}}
        <div x-show="airportModal" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @keydown.escape.window="airportModal = false"
             class="fixed inset-0 z-[100] flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8 lg:py-12">
            {{-- backdrop click closes --}}
            <div class="absolute inset-0" @click="airportModal = false"></div>

            {{-- Panel --}}
            <div class="relative z-10 w-full max-w-[1440px] overflow-hidden rounded-2xl p-6 sm:p-8 lg:p-10"
                 style="background-image: linear-gradient(180deg, #110f06 22%, #222a1f 74%, #1e1e1e 90%);"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                {{-- Header --}}
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-2xl font-bold tracking-tight text-white sm:text-4xl lg:text-h1">Airport Pick-Up Service</h2>
                    <button type="button" @click="airportModal = false" aria-label="Close"
                            class="flex icon-xl shrink-0 items-center justify-center rounded-full text-white transition hover:bg-white/10 lg:icon-xl">
                        <svg class="icon-lg lg:icon-xl" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Search / booking bar (compact, centered fields like the design) --}}
                <div class="mt-6 rounded-[19px] bg-[#d9d9d9] px-5 py-7 lg:px-[55px]">
                    <div class="flex flex-wrap items-stretch justify-center gap-[9px]">
                        {{-- Location --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[17px] sm:w-[330px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">Location</p>
                            <div class="flex items-center gap-[7px]">
                                <svg class="icon-md shrink-0 text-[#202020]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
                                <input type="text" x-model="location" placeholder="Pick-up location"
                                       class="w-full truncate bg-transparent text-body-sm font-bold text-[#202020] placeholder:font-medium placeholder:text-[#7a7a7a] focus:outline-none">
                            </div>
                        </div>
                        {{-- Passengers --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[18px] sm:w-[160px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">No. of Passengers</p>
                            <div class="flex items-center gap-[5px]">
                                <svg class="icon-md shrink-0 text-[#202020]" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16a3 3 0 0 1 3-3h2a3 3 0 0 1 3 3v4H5v-4zM9 4a3 3 0 1 1 0 6 3 3 0 0 1 0-6zM16 11h2a2 2 0 0 1 2 2v7h-4v-9z"/></svg>
                                <select x-model.number="passengers"
                                        class="w-full appearance-none bg-transparent text-body-sm font-bold tracking-tight text-[#202020] focus:outline-none">
                                    <template x-for="n in 10" :key="n"><option :value="n" x-text="n"></option></template>
                                </select>
                            </div>
                        </div>
                        {{-- Arrival Date --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[19px] sm:w-[189px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">Arrival Date</p>
                            <div class="flex items-center gap-[5px]">
                                <img src="{{ asset('images/date.png') }}" alt="" class="icon-lg shrink-0 object-contain">
                                <input type="date" x-model="arrivalDate"
                                       @click="$event.target.showPicker && $event.target.showPicker()"
                                       class="w-full bg-transparent text-body-sm font-semibold tracking-tight text-[#202020] focus:outline-none [&::-webkit-calendar-picker-indicator]:hidden">
                            </div>
                        </div>
                        {{-- Pick-up Time --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[19px] sm:w-[172px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">Pick-up Time</p>
                            <div class="flex items-center gap-[7px]">
                                <svg class="icon-md shrink-0 text-[#202020]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input type="time" x-model="pickupTime"
                                       @click="$event.target.showPicker && $event.target.showPicker()"
                                       class="w-full bg-transparent text-body-sm font-semibold tracking-tight text-[#202020] focus:outline-none [&::-webkit-calendar-picker-indicator]:hidden">
                            </div>
                        </div>
                        {{-- Flight Number --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[19px] sm:w-[172px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">Flight Number</p>
                            <input type="text" x-model="flightNumber" placeholder="Flight number"
                                   class="w-full bg-transparent text-body-sm font-semibold tracking-tight text-[#383838] placeholder:font-medium placeholder:text-[#7a7a7a] focus:outline-none">
                        </div>
                        {{-- Book --}}
                        <button type="button" @click="airportModal = false; vehicleModal = true"
                                class="flex h-[73px] w-full shrink-0 items-center justify-center gap-[2px] rounded-[14px] bg-[#ba6d04] text-body-lg font-semibold tracking-tight text-white transition hover:bg-[#a35f03] sm:w-[125px]">
                            Book
                            <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 10 4 15l5 5"/><path d="M20 4v7a4 4 0 0 1-4 4H4"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Content: car image (left) + heading/text + chauffeur image (right) --}}
                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <img src="{{ asset('images/airportpickup image popup1.jpg') }}" alt="Premium chauffeur car"
                         class="h-[300px] w-full rounded-xl object-cover lg:h-full lg:min-h-[620px]">

                    <div class="flex flex-col gap-[29px]">
                        <h3 class="text-4xl font-medium leading-tight tracking-tight text-white lg:text-display lg:leading-[60px]">
                            Premium Chauffeur Experience
                        </h3>
                        <p class="text-lg leading-relaxed tracking-tight text-white/90 lg:text-body-lg">
                            Combine luxury accommodation with premium transportation services and enjoy special packages designed to enhance your stay from the moment you arrive.
                        </p>
                        <img src="{{ asset('images/airportpickup image popup2.jpg') }}" alt="Chauffeur assisting guest"
                             class="h-[280px] w-full rounded-xl object-cover lg:h-auto lg:flex-1">
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== VEHICLE SELECTION (after "Book") ===================== --}}
        @php
            $vehicles = [
                ['name' => 'Standard Ride', 'image' => 'Standard Ride.png', 'price' => '₦37,500', 'seats' => '4', 'suitcases' => '3'],
                ['name' => 'Mini Bus', 'image' => 'Mini Bus.png', 'price' => '₦77,500', 'seats' => '4', 'suitcases' => '3'],
                ['name' => 'Bus', 'image' => 'Bus.png', 'price' => '₦150,500', 'seats' => '14', 'suitcases' => '10'],
                ['name' => 'Executive SUV', 'image' => 'Executive SUV.png', 'price' => '₦211,500', 'seats' => '4', 'suitcases' => '3'],
                ['name' => 'Luxury Ride', 'image' => 'Luxury Ride.png', 'price' => '₦307,500', 'seats' => '2', 'suitcases' => '3'],
            ];
        @endphp
        <div x-show="vehicleModal" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @keydown.escape.window="vehicleModal = false"
             class="fixed inset-0 z-[100] flex items-start justify-center overflow-y-auto bg-black/70 px-4 py-8 lg:py-12">
            {{-- backdrop click closes --}}
            <div class="absolute inset-0" @click="vehicleModal = false"></div>

            {{-- Panel --}}
            <div class="relative z-10 w-full max-w-[1440px] overflow-hidden rounded-2xl p-6 sm:p-8 lg:p-10"
                 style="background-image: linear-gradient(180deg, #110f06 22%, #222a1f 74%, #1e1e1e 90%);"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                {{-- Header --}}
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-2xl font-bold tracking-tight text-white sm:text-4xl lg:text-h1">Airport Pick-Up Service</h2>
                    <button type="button" @click="vehicleModal = false" aria-label="Close"
                            class="flex icon-xl shrink-0 items-center justify-center rounded-full text-white transition hover:bg-white/10 lg:icon-xl">
                        <svg class="icon-lg lg:icon-xl" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Search / booking bar --}}
                <div class="mt-6 rounded-[19px] bg-[#d9d9d9] px-5 py-7 lg:px-[55px]">
                    <div class="flex flex-wrap items-stretch justify-center gap-[9px]">
                        {{-- Location --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[17px] sm:w-[330px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">Location</p>
                            <div class="flex items-center gap-[7px]">
                                <svg class="icon-md shrink-0 text-[#202020]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
                                <input type="text" x-model="location" placeholder="Pick-up location"
                                       class="w-full truncate bg-transparent text-body-sm font-bold text-[#202020] placeholder:font-medium placeholder:text-[#7a7a7a] focus:outline-none">
                            </div>
                        </div>
                        {{-- Passengers --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[18px] sm:w-[160px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">No. of Passengers</p>
                            <div class="flex items-center gap-[5px]">
                                <svg class="icon-md shrink-0 text-[#202020]" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16a3 3 0 0 1 3-3h2a3 3 0 0 1 3 3v4H5v-4zM9 4a3 3 0 1 1 0 6 3 3 0 0 1 0-6zM16 11h2a2 2 0 0 1 2 2v7h-4v-9z"/></svg>
                                <select x-model.number="passengers"
                                        class="w-full appearance-none bg-transparent text-body-sm font-bold tracking-tight text-[#202020] focus:outline-none">
                                    <template x-for="n in 10" :key="n"><option :value="n" x-text="n"></option></template>
                                </select>
                            </div>
                        </div>
                        {{-- Arrival Date --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[19px] sm:w-[189px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">Arrival Date</p>
                            <div class="flex items-center gap-[5px]">
                                <img src="{{ asset('images/date.png') }}" alt="" class="icon-lg shrink-0 object-contain">
                                <input type="date" x-model="arrivalDate"
                                       @click="$event.target.showPicker && $event.target.showPicker()"
                                       class="w-full bg-transparent text-body-sm font-semibold tracking-tight text-[#202020] focus:outline-none [&::-webkit-calendar-picker-indicator]:hidden">
                            </div>
                        </div>
                        {{-- Pick-up Time --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[19px] sm:w-[172px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">Pick-up Time</p>
                            <div class="flex items-center gap-[7px]">
                                <svg class="icon-md shrink-0 text-[#202020]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input type="time" x-model="pickupTime"
                                       @click="$event.target.showPicker && $event.target.showPicker()"
                                       class="w-full bg-transparent text-body-sm font-semibold tracking-tight text-[#202020] focus:outline-none [&::-webkit-calendar-picker-indicator]:hidden">
                            </div>
                        </div>
                        {{-- Flight Number --}}
                        <div class="flex h-[73px] w-full shrink-0 flex-col justify-center rounded-[14px] border-[0.5px] border-black/20 bg-[#f6f6f6]/75 px-[19px] sm:w-[172px]">
                            <p class="text-label font-medium tracking-tight text-[#3c3c3c]">Flight Number</p>
                            <input type="text" x-model="flightNumber" placeholder="Flight number"
                                   class="w-full bg-transparent text-body-sm font-semibold tracking-tight text-[#383838] placeholder:font-medium placeholder:text-[#7a7a7a] focus:outline-none">
                        </div>
                        {{-- Book --}}
                        <button type="button"
                                class="flex h-[73px] w-full shrink-0 items-center justify-center gap-[2px] rounded-[14px] bg-[#ba6d04] text-body-lg font-semibold tracking-tight text-white transition hover:bg-[#a35f03] sm:w-[125px]">
                            Book
                            <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 10 4 15l5 5"/><path d="M20 4v7a4 4 0 0 1-4 4H4"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Vehicle option cards --}}
                <div class="mt-6 flex flex-col gap-4">
                    @foreach ($vehicles as $v)
                        <div class="flex flex-col items-center gap-6 rounded-[10px] bg-[#d2d2d2] p-6 sm:flex-row sm:gap-10 lg:px-[60px]">
                            {{-- Vehicle image --}}
                            <img src="{{ str_replace(' ', '%20', asset('images/'.$v['image'])) }}" alt="{{ $v['name'] }}"
                                 class="h-[110px] w-[230px] shrink-0 object-contain sm:h-[130px] sm:w-[260px]">

                            {{-- Details --}}
                            <div class="flex flex-1 flex-col gap-3">
                                <p class="text-h3 font-bold tracking-tight text-black sm:text-h2 lg:text-h1">{{ $v['name'] }}</p>
                                <div class="flex flex-wrap items-center gap-x-10 gap-y-3 text-body-lg font-medium tracking-tight text-black sm:text-title lg:text-h3">
                                    <span class="flex items-end gap-2.5">
                                        <svg class="icon-lg shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m5.6 5.6 12.8 12.8" stroke-linecap="round"/></svg>
                                        Free cancellation
                                    </span>
                                    <span class="flex items-end gap-2.5">
                                        <svg class="icon-lg shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M4 18v-5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5h-2v-2H6v2H4zm3-9V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2a3 3 0 0 0-3 3H10a3 3 0 0 0-3-3z"/></svg>
                                        <span><span class="font-bold">{{ $v['seats'] }} S</span>eats</span>
                                    </span>
                                    <span class="flex items-end gap-2.5">
                                        <svg class="icon-lg shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="6" y="7" width="12" height="14" rx="2"/><path d="M9 7V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v3M10 11v6M14 11v6" stroke-linecap="round"/></svg>
                                        <span><span class="font-bold">{{ $v['suitcases'] }} </span>Suitcases</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Price + select --}}
                            <div class="flex w-full flex-col items-center gap-4 sm:w-[246px] sm:items-start">
                                <p class="text-h2 font-semibold tracking-tight text-[#191919] sm:text-h1">{{ $v['price'] }}</p>
                                <button type="button"
                                        @click="selectVehicle({ name: '{{ $v['name'] }}', price: '{{ $v['price'] }}', img: '{{ str_replace(' ', '%20', asset('images/'.$v['image'])) }}' })"
                                        class="flex h-[73px] w-full items-center justify-center rounded-[10px] bg-[#ba6d04] text-body-lg font-semibold tracking-tight text-white transition hover:bg-[#a35f03]">
                                    Select
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- =========================== DESCRIPTION =========================== --}}
    <section class="w-full pb-10">
        <x-layouts.container class="flex flex-col gap-[17px]">
            <h2 class="text-h3 font-semibold tracking-tight text-white">Description</h2>
            <p class="text-lg leading-relaxed tracking-tight text-white/90 lg:text-body-lg">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sed lacus luctus dolor volutpat aliquet eget ut dolor. Duis diam nisl, maximus ac egestas eget, porta eget ipsum. Fusce tristique erat elit, sit amet pretium nisi consectetur sed. Integer sagittis fermentum semper. Duis metus erat,
            </p>
        </x-layouts.container>
    </section>

    {{-- =========================== AMENITIES =========================== --}}
    <section class="w-full pb-10">
        <x-layouts.container class="flex flex-col gap-6">
            <h2 class="text-h3 font-semibold tracking-tight text-white">Apartment Amenities</h2>
            <div class="flex flex-wrap gap-[6px]">
                @foreach ($amenities as $a)
                    <span class="flex h-[54px] items-center gap-[5px] rounded-[6px] bg-[#dadbda] px-4 text-label font-medium tracking-tight text-black">
                        <span class="text-black">
                            @switch($a['icon'])
                                @case('fitness')
                                    <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor"><path d="M20.57 14.86 22 13.43 20.57 12 17 15.57 8.43 7 12 3.43 10.57 2 9.14 3.43 7.71 2 5.57 4.14 4.14 2.71 2.71 4.14l1.43 1.43L2 7.71l1.43 1.43L2 10.57 3.43 12 7 8.43 15.57 17 12 20.57 13.43 22l1.43-1.43L16.29 22l2.14-2.14 1.43 1.43 1.43-1.43-1.43-1.43L22 16.29z"/></svg>
                                    @break
                                @case('bed')
                                    <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor"><path d="M21 10.78V8a2 2 0 0 0-2-2h-5v5h-4V6H5a2 2 0 0 0-2 2v2.78A2 2 0 0 0 2 12.5V19h2v-2h16v2h2v-6.5a2 2 0 0 0-1-1.72z"/></svg>
                                    @break
                                @case('wifi')
                                    <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor"><path d="M12 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0-5c-1.66 0-3.16.67-4.24 1.76l1.41 1.41a4 4 0 0 1 5.66 0l1.41-1.41A5.98 5.98 0 0 0 12 13zm0-5c-3.04 0-5.79 1.23-7.78 3.22l1.41 1.41a8.97 8.97 0 0 1 12.73 0l1.41-1.41A10.97 10.97 0 0 0 12 8zm0-5C7.74 3 3.89 4.73 1.1 7.51L2.5 8.93a13.96 13.96 0 0 1 19 0l1.41-1.42A17.94 17.94 0 0 0 12 3z"/></svg>
                                    @break
                                @case('pool')
                                    <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M2 18c1.5 0 1.5 1 3 1s1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1M7 15V5a2 2 0 0 1 4 0M13 13V5a2 2 0 0 1 4 0"/></svg>
                                    @break
                                @case('restaurant')
                                    <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 3v7a2 2 0 0 0 2 2v9M6 3v6M8 3v6M18 3c-1.5 0-2.5 2-2.5 5s1 4 2.5 4v9"/></svg>
                                    @break
                                @case('parking')
                                    <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5zm4 4h4a3 3 0 0 1 0 6h-2v4H9V7zm2 2v2h2a1 1 0 0 0 0-2h-2z"/></svg>
                                    @break
                                @default
                                    <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"/></svg>
                            @endswitch
                        </span>
                        {{ $a['label'] }}
                    </span>
                @endforeach
            </div>
        </x-layouts.container>
    </section>

    {{-- =========================== ADDITIONAL =========================== --}}
    <section class="w-full pb-10">
        <x-layouts.container class="flex flex-col gap-[15px]">
            <h2 class="text-h3 font-semibold tracking-tight text-white">Additional</h2>
            <div class="flex flex-wrap items-center gap-x-[15px] gap-y-3 text-body-lg font-semibold tracking-tight text-white">
                <span class="flex items-center gap-1.5">
                    <svg class="icon-md shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                    Self-check-in
                </span>
                <span class="hidden h-5 w-px bg-white/30 sm:block"></span>
                <span class="flex items-center gap-1.5">
                    <svg class="icon-md shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M2 19h20v2H2zM4 17h16l-1-6h-3l-2-7-2 .5 1.5 6.5H8l-1.5-3H5l1 3H4z"/></svg>
                    Airport pick-up
                </span>
                <span class="hidden h-5 w-px bg-white/30 sm:block"></span>
                <span class="flex items-center gap-1.5">
                    <svg class="icon-md shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3a6 6 0 0 0-6 6c0 .35.03.69.08 1.02A3.5 3.5 0 0 0 7 17h10a3.5 3.5 0 0 0 .92-6.98c.05-.33.08-.67.08-1.02a6 6 0 0 0-6-6zM7 19h10v2H7z"/></svg>
                    Private chef
                </span>
                <span class="hidden h-5 w-px bg-white/30 sm:block"></span>
                <span class="flex items-center gap-1.5">
                    <svg class="icon-md shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V10l9-7 9 7v11M9 21v-6h6v6"/></svg>
                    24/7 House-keeping
                </span>
            </div>
        </x-layouts.container>
    </section>

    {{-- ======================= CANCELLATION POLICY ======================= --}}
    <section class="w-full pb-12 lg:pb-16">
        <x-layouts.container>
            <div class="rounded-[10px] bg-[rgba(113,113,113,0.27)] px-6 py-7 lg:px-10 lg:py-9">
                <p class="text-title font-semibold tracking-tight text-white lg:text-h3">Cancellation Policy</p>
                <p class="mt-5 text-lg leading-snug tracking-tight text-[#dadbda] lg:text-body-lg">
                    Set in a beautifully restored colonial building, sofitel Legend Santa offers luxurious blend of modern amenities blend of modern amenities blend of modern amenities blend of modern amenities
                </p>
                <a href="#" class="mt-1 inline-block text-body-lg text-[#368aff] hover:underline">Read more</a>
            </div>
        </x-layouts.container>
    </section>

    {{-- ===================== EXPLORE OUR EXCLUSIVE OFFERS ===================== --}}
    <section class="w-full py-12 lg:py-16">
        <x-layouts.container class="flex flex-col gap-[50px] lg:gap-[68px]">
            <h2 class="text-center text-2xl tracking-tight text-white sm:text-3xl lg:text-h2">Explore our exclusive offers</h2>
            <div class="grid grid-cols-1 gap-[15px] md:grid-cols-2">
                @foreach ($offers as $room)
                    <x-layouts.room-card :image="$room['image']" :name="$room['name']" :price="$room['price']" />
                @endforeach
            </div>
        </x-layouts.container>
    </section>
</x-layouts.web>
