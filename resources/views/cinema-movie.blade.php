<x-layouts.web :title="$movie->title.' — Retiro Del Rocio'" :description="\Illuminate\Support\Str::limit($movie->synopsis, 150)">

    @php
        $cinemaSuccess = session('cinema_success');
        session()->forget('cinema_success');
        $paystackKey = config('services.paystack.public_key');

        $movieJson = [
            'title' => $movie->title, 'slug' => $movie->slug,
            'adult_price' => $movie->adult_price, 'child_price' => $movie->child_price,
            'showtimes' => $movie->showtimeList(),
        ];
        $snacksJson = $snacks->map(fn ($s) => [
            'id' => $s->id, 'name' => $s->name, 'price' => $s->price, 'image' => $s->imageUrl(),
        ])->values();
    @endphp

    <div class="bg-[#0e0e10]"
         x-data="cinemaBooking({
            movie: @js($movieJson),
            snacks: @js($snacksJson),
            paystackKey: @js($paystackKey),
            successData: @js($cinemaSuccess),
            seatsUrl: @js(url('cinema/'.$movie->slug.'/seats')),
            holdUrl: @js(route('cinema.hold')),
            releaseUrl: @js(route('cinema.release')),
            csrf: @js(csrf_token()),
         })">

        {{-- ============================ MOVIE HERO ============================ --}}
        <section class="relative w-full overflow-hidden">
            <x-img src="{{ $movie->backdropUrl() }}" alt="{{ $movie->title }}" sizes="100vw" loading="eager" fetchpriority="high"
                   class="absolute inset-0 h-full w-full scale-110 object-cover blur-sm" />
            <div class="absolute inset-0 bg-black/75"></div>
            <x-layouts.container class="relative flex flex-col gap-8 py-12 lg:flex-row lg:items-center lg:gap-12 lg:py-16">
                <x-img src="{{ $movie->posterUrl() }}" alt="{{ $movie->title }}" sizes="(min-width:1024px) 320px, 60vw"
                       class="h-[360px] w-[240px] shrink-0 rounded-[16px] object-cover shadow-2xl lg:h-[440px] lg:w-[300px]" />
                <div class="flex flex-col gap-5 text-white">
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-h1">{{ $movie->title }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-label text-white/80">
                        @if ($movie->rating)<span class="rounded border border-white/30 px-2 py-0.5 text-[11px] font-semibold">{{ $movie->rating }}</span>@endif
                        <span>{{ $movie->genre }}</span>
                        @if ($movie->duration)<span class="flex items-center gap-1"><svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg>{{ $movie->duration }}</span>@endif
                        <span class="rounded bg-[#f38c00] px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-white">{{ $movie->classificationLabel() }}</span>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <p class="text-title font-semibold">{{ cms('cinemamovie.summary_label') }}</p>
                        <p class="max-w-[640px] text-body leading-relaxed text-white/75">{{ $movie->synopsis }}</p>
                    </div>
                    @if ($movie->trailer_url)
                        <div>
                            <a href="{{ $movie->trailer_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 rounded-[10px] bg-[#e50914] px-7 py-3 text-body-lg font-semibold text-white transition hover:bg-[#c20710]">
                                <svg class="size-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                {{ cms('cinemamovie.trailer_label') }}
                            </a>
                        </div>
                    @endif
                </div>
            </x-layouts.container>
        </section>

        {{-- Offer ticker --}}
        <div class="w-full bg-[#1b1b18] py-3">
            <x-layouts.container class="flex flex-wrap items-center justify-center gap-2 text-center">
                <span class="text-body font-semibold text-white">{{ cms('cinema.offer_text') }}</span>
                <span class="text-label text-[#f38c00]">{{ cms('cinema.offer_terms') }}</span>
            </x-layouts.container>
        </div>

        {{-- ============================ BOOKING ============================ --}}
        <section class="w-full py-12 lg:py-16">
            <x-layouts.container class="flex flex-col gap-10">

                {{-- Date --}}
                <div class="flex flex-col gap-4">
                    <p class="text-title font-semibold text-white">{{ cms('cinemamovie.date_label') }}</p>
                    <div class="no-scrollbar flex gap-3 overflow-x-auto pb-1">
                        <template x-for="d in dates" :key="d.iso">
                            <button type="button" @click="selectedDate = d.iso"
                                    class="flex w-[78px] shrink-0 flex-col items-center gap-0.5 rounded-[12px] border py-3 transition"
                                    :class="selectedDate === d.iso ? 'border-[#f38c00] bg-[#f38c00] text-white' : 'border-white/15 bg-[#1b1b18] text-white/70 hover:border-white/40'">
                                <span class="text-label" x-text="d.dow"></span>
                                <span class="text-xl font-bold" x-text="d.day"></span>
                                <span class="text-[11px]" x-text="d.mon"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Time --}}
                <div class="flex flex-col gap-4">
                    <p class="text-title font-semibold text-white">{{ cms('cinemamovie.time_label') }}</p>
                    <div class="flex flex-wrap gap-3">
                        <template x-for="t in movie.showtimes" :key="t">
                            <button type="button" @click="selectedTime = t"
                                    class="rounded-[10px] border px-5 py-2.5 text-body font-medium transition"
                                    :class="selectedTime === t ? 'border-[#f38c00] bg-[#f38c00] text-white' : 'border-white/15 bg-[#1b1b18] text-white/70 hover:border-white/40'"
                                    x-text="t"></button>
                        </template>
                    </div>
                </div>

                {{-- Tickets & seats --}}
                <div class="flex flex-col gap-5">
                    <p class="text-title font-semibold text-white">{{ cms('cinemamovie.seats_label') }}</p>
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[320px_1fr]">
                        {{-- Ticket counters --}}
                        <div class="flex flex-col gap-4">
                            @foreach (['adult' => ['Adult', $movie->adultPriceLabel()], 'child' => ['Child', $movie->childPriceLabel()]] as $k => $info)
                                <div class="flex items-center justify-between rounded-[12px] border border-white/12 bg-[#1b1b18] px-5 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-body font-semibold text-white">{{ $info[0] }}</span>
                                        <span class="text-label text-[#f38c00]">{{ $info[1] }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="{{ $k }} = Math.max(0, {{ $k }} - 1)" class="flex size-8 items-center justify-center rounded-full border border-white/25 text-white transition hover:bg-white/10">−</button>
                                        <span class="w-6 text-center text-body-lg font-semibold text-white" x-text="{{ $k }}"></span>
                                        <button type="button" @click="{{ $k }}++" class="flex size-8 items-center justify-center rounded-full bg-[#f38c00] text-white transition hover:bg-[#dd7f00]">+</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Seat map --}}
                        <div class="flex flex-col items-center gap-5 rounded-[16px] border border-white/10 bg-[#15151a] p-6">
                            <div class="h-1.5 w-[70%] rounded-full bg-gradient-to-r from-transparent via-[#f38c00] to-transparent"></div>
                            <p class="text-label uppercase tracking-[2px] text-white/40">Screen</p>
                            <div class="flex flex-col gap-2">
                                <template x-for="row in rows" :key="row">
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 text-label text-white/40" x-text="row"></span>
                                        <template x-for="c in cols" :key="row + c">
                                            <button type="button" @click="toggleSeat(row + '' + c)"
                                                    :disabled="isTaken(row + '' + c)"
                                                    class="size-6 rounded-[5px] text-[9px] font-medium transition lg:size-7"
                                                    :class="isTaken(row + '' + c) ? 'cursor-not-allowed bg-white/5 text-white/20 line-through' : (isSeat(row + '' + c) ? 'bg-[#f38c00] text-white' : 'bg-white/12 text-white/50 hover:bg-white/25')"
                                                    x-text="c"></button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                            <div class="flex flex-wrap items-center gap-5 text-label text-white/60">
                                <span class="flex items-center gap-2"><span class="size-3.5 rounded-[4px] bg-white/12"></span> Available</span>
                                <span class="flex items-center gap-2"><span class="size-3.5 rounded-[4px] bg-[#f38c00]"></span> Selected</span>
                                <span class="flex items-center gap-2"><span class="size-3.5 rounded-[4px] bg-white/5"></span> Taken</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Snacks --}}
                @if ($snacks->isNotEmpty())
                    <div class="flex flex-col gap-5">
                        <p class="text-title font-semibold text-white">{{ cms('cinemamovie.snacks_label') }}</p>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                            <template x-for="s in snacks" :key="s.id">
                                <div class="flex flex-col items-center gap-3 rounded-[14px] border border-white/12 bg-[#1b1b18] p-4 text-center">
                                    <img :src="s.image" :alt="s.name" class="h-16 w-16 object-contain" loading="lazy">
                                    <p class="text-body font-semibold text-white" x-text="s.name"></p>
                                    <p class="text-label text-[#f38c00]" x-text="money(s.price)"></p>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="snackQty[s.id] = Math.max(0, (snackQty[s.id]||0) - 1)" class="flex size-7 items-center justify-center rounded-full border border-white/25 text-white transition hover:bg-white/10">−</button>
                                        <span class="w-5 text-center text-body font-semibold text-white" x-text="snackQty[s.id] || 0"></span>
                                        <button type="button" @click="snackQty[s.id] = (snackQty[s.id]||0) + 1" class="flex size-7 items-center justify-center rounded-full bg-[#f38c00] text-white transition hover:bg-[#dd7f00]">+</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                @endif

                {{-- Order details --}}
                <div class="flex flex-col gap-4 rounded-[16px] border border-white/10 bg-[#15151a] p-6 lg:p-8">
                    <p class="text-h3 font-semibold tracking-tight text-white">{{ cms('cinemamovie.order_label') }}</p>
                    <div class="flex flex-col gap-2.5 text-body text-white/80">
                        <p class="font-medium text-[#f38c00]">Tickets</p>
                        <div class="flex justify-between" x-show="adult > 0"><span x-text="'Adult Tickets (×' + adult + ')'"></span><span class="font-medium text-white" x-text="money(adult * movie.adult_price)"></span></div>
                        <div class="flex justify-between" x-show="child > 0"><span x-text="'Child Tickets (×' + child + ')'"></span><span class="font-medium text-white" x-text="money(child * movie.child_price)"></span></div>
                        <div class="flex justify-between text-white/45" x-show="adult === 0 && child === 0"><span>No tickets selected</span><span>—</span></div>
                    </div>
                    <template x-if="chosenSnacks.length">
                        <div class="flex flex-col gap-2.5 border-t border-white/10 pt-4 text-body text-white/80">
                            <p class="font-medium text-[#f38c00]">Food &amp; Drinks</p>
                            <template x-for="s in chosenSnacks" :key="s.name">
                                <div class="flex justify-between"><span x-text="s.name + ' (×' + s.qty + ')'"></span><span class="font-medium text-white" x-text="money(s.qty * s.price)"></span></div>
                            </template>
                        </div>
                    </template>
                    <div class="flex items-center justify-between border-t border-white/15 pt-4">
                        <span class="text-body-lg font-medium text-[#f38c00]">TOTAL</span>
                        <span class="text-h3 font-semibold tracking-tight text-white" x-text="money(grandTotal)"></span>
                    </div>
                    <button type="button" @click="openCheckout()" :disabled="holding" class="mt-2 w-full rounded-[10px] bg-[#f38c00] py-4 text-body-lg font-semibold text-white transition hover:bg-[#dd7f00] disabled:opacity-60 sm:w-[260px]"><span x-show="!holding">{{ cms('cinemamovie.book_label') }}</span><span x-show="holding" x-cloak>Reserving seats…</span></button>
                </div>

                <a href="{{ route('cinema') }}" wire:navigate class="text-body font-medium text-white/60 transition hover:text-white">&larr; Back to all movies</a>
            </x-layouts.container>
        </section>

        @include('partials.cinema-popup')

    </div>

    @if (! empty($paystackKey))
        @push('scripts')
            <script src="https://js.paystack.co/v1/inline.js"></script>
        @endpush
    @endif
</x-layouts.web>
