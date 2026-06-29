{{-- Cinema checkout + success popup — lives inside the cinemaBooking() Alpine scope. --}}
<div x-show="showCheckout" x-cloak class="fixed inset-0 z-[80] flex items-start justify-center overflow-y-auto bg-black/80 p-4 sm:p-6"
     x-transition.opacity @keydown.escape.window="closeCheckout()">
    <div class="relative my-6 w-full max-w-[1080px] overflow-hidden rounded-[20px] bg-[#15151a] shadow-2xl"
         @click.outside="closeCheckout()"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-6 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100">

        <button type="button" @click="closeCheckout()" class="absolute right-5 top-5 z-20 flex size-11 items-center justify-center rounded-full border border-white/40 text-white transition hover:bg-white/10">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        {{-- ============ STEP: CHECKOUT ============ --}}
        <div x-show="step === 'checkout'" x-cloak class="p-6 sm:p-9 lg:p-11">
            <h2 class="text-2xl font-semibold tracking-tight text-white lg:text-h2" x-text="movie.title"></h2>

            <div class="mt-7 grid grid-cols-1 gap-8 lg:grid-cols-[1fr_1.1fr]">
                {{-- Order summary --}}
                <div class="rounded-[16px] border border-white/10 bg-[#1b1b18] p-6">
                    <p class="text-title font-semibold text-white">{{ cms('cinemamovie.order_label') }}</p>
                    <div class="mt-4 flex flex-col gap-2.5 text-body text-white/80">
                        <p class="flex justify-between gap-3"><span class="text-white/50">Date</span><span x-text="prettyDate(selectedDate)"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/50">Time</span><span x-text="selectedTime || '—'"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/50">Seats</span><span x-text="seats.length ? seats.join(', ') : '—'"></span></p>
                        <p class="flex justify-between gap-3" x-show="adult > 0"><span class="text-white/50">Adult tickets</span><span x-text="'×' + adult"></span></p>
                        <p class="flex justify-between gap-3" x-show="child > 0"><span class="text-white/50">Child tickets</span><span x-text="'×' + child"></span></p>
                        <template x-for="s in chosenSnacks" :key="s.name">
                            <p class="flex justify-between gap-3"><span class="text-white/50" x-text="s.name"></span><span x-text="'×' + s.qty"></span></p>
                        </template>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-white/10 pt-4">
                        <span class="text-body text-white/60">Total</span>
                        <span class="text-h3 font-semibold text-white" x-text="money(grandTotal)"></span>
                    </div>
                </div>

                {{-- Customer + payment --}}
                <div class="flex flex-col gap-5">
                    <div class="flex flex-col gap-4">
                        <p class="text-title font-semibold text-white">Customer Details</p>
                        <label class="flex flex-col gap-1.5 rounded-xl bg-[#26262b] px-4 py-3">
                            <span class="text-label font-medium text-[#a5a5a5]">Full Name</span>
                            <input type="text" x-model="name" placeholder="Micheal Philip" class="bg-transparent text-body-lg text-white placeholder:text-white/40 focus:outline-none">
                        </label>
                        <label class="flex flex-col gap-1.5 rounded-xl bg-[#26262b] px-4 py-3">
                            <span class="text-label font-medium text-[#a5a5a5]">Email Address</span>
                            <input type="email" x-model="email" placeholder="mich.philip@gmail.com" class="bg-transparent text-body-lg text-white placeholder:text-white/40 focus:outline-none">
                        </label>
                        <label class="flex flex-col gap-1.5 rounded-xl bg-[#26262b] px-4 py-3">
                            <span class="text-label font-medium text-[#a5a5a5]">Phone Number</span>
                            <div class="flex items-center gap-2">
                                <span class="shrink-0 text-body-lg text-white">🇳🇬 +234</span>
                                <input type="tel" x-model="phone" inputmode="numeric" placeholder="7012623680" class="w-full bg-transparent text-body-lg text-white placeholder:text-white/40 focus:outline-none">
                            </div>
                        </label>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-[#1b1b18] p-4">
                        <p class="text-body font-semibold text-white">Cancellation Policy</p>
                        <p class="mt-1.5 text-label leading-relaxed text-white/55">{{ cms('cinemacheckout.cancellation_policy') }}</p>
                    </div>

                    <div>
                        <p class="text-body font-semibold text-white">Payment Options</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" @click="channel = 'card'" :class="channel === 'card' ? 'bg-[#ba6d04] text-white' : 'bg-[#696969] text-[#c9c9c9]'" class="flex h-[46px] items-center gap-2 rounded-[11px] px-5 text-body font-semibold transition">Card</button>
                            <button type="button" @click="channel = 'bank'" :class="channel === 'bank' ? 'bg-[#ba6d04] text-white' : 'bg-[#696969] text-[#c9c9c9]'" class="flex h-[46px] items-center gap-2 rounded-[11px] px-5 text-body font-medium transition">Bank</button>
                            <button type="button" @click="channel = 'transfer'" :class="channel === 'transfer' ? 'bg-[#ba6d04] text-white' : 'bg-[#696969] text-[#c9c9c9]'" class="flex h-[46px] items-center gap-2 rounded-[11px] px-5 text-body font-medium transition">Transfer</button>
                        </div>
                        <p class="mt-3 flex items-center gap-2 text-label text-white/55">
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke-linecap="round"/></svg>
                            Card details are entered securely in the Paystack window. We never store your card.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" @click="pay()" class="flex h-[58px] flex-1 items-center justify-center rounded-[10px] bg-[#f38c00] text-body-lg font-semibold text-white transition hover:bg-[#dd7f00]">{{ cms('cinemacheckout.pay_label') }} <span class="ml-1.5" x-text="money(grandTotal)"></span></button>
                        <button type="button" @click="closeCheckout()" class="h-[58px] rounded-[10px] border border-white/30 px-6 text-body font-medium text-white transition hover:bg-white/10">Back</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ STEP: SUCCESS ============ --}}
        <div x-show="step === 'success'" x-cloak class="px-6 pb-10 pt-12 sm:px-10 sm:pb-12 lg:px-14 lg:pb-14 lg:pt-16">
            <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-[1fr_1fr] lg:gap-14">
                {{-- Ticket card --}}
                <div class="overflow-hidden rounded-[16px] bg-[#1b1b18]">
                    <img :src="success ? success.poster : ''" alt="" class="h-[260px] w-full object-cover">
                    <div class="flex flex-col gap-4 p-6">
                        <p class="text-title font-semibold text-white" x-text="success ? success.movie_title : ''"></p>
                        <div class="flex flex-wrap gap-x-8 gap-y-3 text-body text-white/80">
                            <div class="flex flex-col"><span class="text-label text-white/45">Date</span><span x-text="success ? success.date : ''"></span></div>
                            <div class="flex flex-col"><span class="text-label text-white/45">Time</span><span x-text="success ? success.time : ''"></span></div>
                            <div class="flex flex-col"><span class="text-label text-white/45">Seats</span><span x-text="success ? success.seats : ''"></span></div>
                        </div>
                        <div class="mt-1 flex flex-col gap-2 border-t border-white/10 pt-4">
                            <div class="h-12 w-full rounded bg-[repeating-linear-gradient(90deg,#fff_0,#fff_2px,transparent_2px,transparent_5px)]"></div>
                            <p class="text-label text-white/55">Ticket ID: <span class="font-semibold text-white" x-text="success ? success.code : ''"></span></p>
                        </div>
                    </div>
                </div>

                {{-- Confirmation --}}
                <div class="flex flex-col gap-6">
                    <img loading="lazy" src="{{ asset('images/checkcircle.png') }}" alt="Success" class="size-[80px] object-contain lg:size-[96px]">
                    <div class="flex flex-col gap-1.5">
                        <h2 class="text-h3 font-bold tracking-tight text-[#f38c00] lg:text-h2">{{ cms('cinemacheckout.success_title') }}</h2>
                        <p class="text-body text-white/80">{{ cms('cinemacheckout.success_text') }}</p>
                    </div>

                    <div class="flex flex-col gap-2 text-body text-white/80">
                        <p class="font-medium text-white">Item Details</p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Movie</span><span class="text-right" x-text="success ? success.movie_title : ''"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Date</span><span x-text="success ? success.date : ''"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Time</span><span x-text="success ? success.time : ''"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Seats</span><span x-text="success ? success.seats : ''"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Ticket type</span><span x-text="success ? success.ticket_type : ''"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Food &amp; Drinks</span><span class="text-right" x-text="success ? success.snacks : ''"></span></p>
                        <p class="flex justify-between gap-3 border-t border-white/10 pt-2"><span class="text-white/55">Total order</span><span class="font-semibold text-white" x-text="success ? success.total : ''"></span></p>
                    </div>

                    <div class="flex flex-col gap-2 border-t border-white/10 pt-4 text-body text-white/80">
                        <p class="font-medium text-white">Customer Details</p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Name</span><span x-text="success ? success.customer_name : ''"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Contact number</span><span x-text="success ? (success.customer_phone || '—') : ''"></span></p>
                        <p class="flex justify-between gap-3"><span class="text-white/55">Email Address</span><span class="break-all" x-text="success ? success.customer_email : ''"></span></p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="button" onclick="window.print()" class="flex h-[58px] w-full items-center justify-center gap-2.5 rounded-[10px] bg-[#f38c00] text-body-lg font-semibold text-white transition hover:bg-[#dd7f00]">
                            Download Receipt
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
                        </button>
                        <a href="{{ route('home') }}" wire:navigate class="flex h-[58px] w-full items-center justify-center rounded-[10px] border border-white/60 text-body-lg font-medium text-white transition hover:bg-white/10">Back to Homepage</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden POST form submitted after a successful Paystack charge --}}
        <form x-ref="form" method="POST" action="{{ route('cinema.book') }}" class="hidden">
            @csrf
            <input type="hidden" name="reference" :value="payReference">
            <input type="hidden" name="movie" value="{{ $movie->slug }}">
            <input type="hidden" name="date" :value="selectedDate">
            <input type="hidden" name="time" :value="selectedTime">
            <input type="hidden" name="seats" :value="JSON.stringify(seats)">
            <input type="hidden" name="adult" :value="adult">
            <input type="hidden" name="child" :value="child">
            <input type="hidden" name="snacks" :value="JSON.stringify(chosenSnacks)">
            <input type="hidden" name="name" :value="name">
            <input type="hidden" name="email" :value="email">
            <input type="hidden" name="phone" :value="phone">
            <input type="hidden" name="channel" :value="channel">
        </form>
    </div>
</div>
