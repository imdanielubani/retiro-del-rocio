<x-layouts.web title="Spa Reservation — Retiro Del Rocio">
    <section class="w-full bg-gradient-to-b from-[#222a1f] to-[#1e1e1e] py-10 lg:py-14"
             x-data="{
                firstName: '',
                lastName: '',
                email: '',
                phone: '',
                channel: 'card',
                pay() {
                    if (!this.firstName || !this.lastName || !this.email) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please enter your name and email to continue.' } }));
                        return;
                    }
                    @if (empty($paystackKey))
                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payments are not configured yet. Please add your Paystack keys.' } }));
                        return;
                    @else
                        if (typeof PaystackPop === 'undefined') {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payment library failed to load. Check your connection and try again.' } }));
                            return;
                        }
                        const channelMap = { card: 'card', bank: 'bank', transfer: 'bank_transfer' };
                        const handler = PaystackPop.setup({
                            key: @js($paystackKey),
                            email: this.email,
                            amount: {{ (int) $booking['total_kobo'] }},
                            currency: 'NGN',
                            channels: [channelMap[this.channel] || 'card'],
                            metadata: {
                                name: (this.firstName + ' ' + this.lastName).trim(),
                                phone: this.phone ? '+234' + this.phone : '',
                                custom_fields: [
                                    { display_name: 'Services', variable_name: 'services', value: @js(collect($booking['services'])->pluck('name')->implode(', ')) },
                                    { display_name: 'Date', variable_name: 'date', value: @js($booking['date_label']) },
                                ],
                            },
                            callback: function (response) {
                                window.location.href = '{{ route('spa.checkout.callback') }}?reference=' + response.reference;
                            },
                            onClose: function () {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Payment window closed before completing your reservation.' } }));
                            },
                        });
                        handler.openIframe();
                    @endif
                },
             }">
        <x-layouts.container class="flex flex-col gap-8">
            {{-- Breadcrumb --}}
            <a href="{{ route('spa') }}" wire:navigate class="flex w-fit items-center gap-2 text-body font-semibold tracking-tight text-white transition hover:text-[#f38c00] sm:text-title">
                <svg class="icon-lg shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                <span>Home / Spa &amp; Wellness / <span class="text-[#f38c00]">Reservation</span></span>
            </a>

            <h1 class="max-w-[687px] text-3xl font-semibold leading-tight tracking-tight text-white sm:text-4xl lg:text-h1 lg:leading-[52px]">
                Complete your spa reservation securely in under 2 minutes.
            </h1>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_578px]">
                {{-- ============ LEFT: image + order details ============ --}}
                <div class="flex flex-col gap-1.5">
                    <div class="relative overflow-hidden rounded-[10px]">
                        <img loading="lazy" src="{{ str_replace(' ', '%20', asset('images/spabg.jpg')) }}" alt="Spa & Wellness"
                             class="h-[300px] w-full object-cover sm:h-[420px] lg:h-[520px]">
                        <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-transparent to-black/40"></div>
                    </div>

                    <div class="flex flex-col gap-7 rounded-[10px] bg-[#373d35]/[0.34] p-6 sm:p-8 lg:p-10">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-3xl font-semibold tracking-tight text-white lg:text-h2">Order Details</h2>
                            <a href="{{ route('spa') }}" wire:navigate class="flex shrink-0 items-center gap-2 text-body font-semibold tracking-tight text-white transition hover:text-[#f38c00] lg:text-body-lg">
                                Edit selection
                                <svg class="icon-md shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </a>
                        </div>

                        {{-- Services --}}
                        <div class="flex flex-col gap-3">
                            <p class="text-body font-medium text-[#f38c00] lg:text-body-lg">Service</p>
                            @foreach ($booking['services'] as $s)
                                <div class="flex items-center justify-between gap-4 text-body-sm font-semibold text-[#f5f5f5] lg:text-body-lg">
                                    <span>{{ $s['name'] }} ({{ $booking['guests'] }} {{ \Illuminate\Support\Str::plural('Guest', $booking['guests']) }})</span>
                                    <span>{{ $s['subtotal_label'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Reservation --}}
                        <div class="flex flex-col gap-2.5 border-t border-white/10 pt-5">
                            <p class="text-body font-medium text-[#f38c00] lg:text-body-lg">Reservation</p>
                            <div class="flex items-center justify-between gap-4 text-body-sm text-[#f5f5f5] lg:text-body-lg">
                                <span>Number of Guest</span><span class="font-semibold">{{ $booking['guests'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 text-body-sm text-[#f5f5f5] lg:text-body-lg">
                                <span>Date</span><span class="font-semibold">{{ $booking['date_label'] }}</span>
                            </div>
                            @if ($booking['time'])
                                <div class="flex items-center justify-between gap-4 text-body-sm text-[#f5f5f5] lg:text-body-lg">
                                    <span>Time</span><span class="font-semibold">{{ $booking['time'] }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Totals --}}
                        <div class="flex flex-col border-t-2 border-white/15 pt-2 text-white">
                            <div class="flex items-center justify-between gap-4 py-3 text-body-lg">
                                <span class="font-medium tracking-tight">Subtotal</span>
                                <span class="font-semibold tracking-tight">{{ $booking['subtotal_label'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-t border-white/10 py-3 text-body-lg">
                                <span class="font-medium tracking-tight">Convenience Fee</span>
                                <span class="font-semibold tracking-tight">{{ $booking['fees_label'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-t border-white/10 py-3 text-body-lg">
                                <span class="font-medium tracking-tight">Taxes (VAT 7.5%)</span>
                                <span class="font-semibold tracking-tight">{{ $booking['taxes_label'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-t border-white/10 py-4">
                                <span class="text-title font-semibold tracking-tight lg:text-h3">Total</span>
                                <span class="text-h3 font-semibold tracking-tight lg:text-h2">{{ $booking['total_label'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============ RIGHT: customer + payment ============ --}}
                <div class="flex flex-col gap-[21px]">
                    <div class="rounded-[10px] bg-[#373d35] p-6 sm:p-8">
                        <h2 class="text-h3 font-semibold tracking-tight text-white">Customer Details</h2>
                        <div class="mt-6 flex flex-col gap-5">
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <label class="flex flex-col gap-1.5 border-b border-white/30 pb-2">
                                    <span class="text-label font-medium tracking-tight text-[#a5a5a5]">First Name</span>
                                    <input type="text" x-model="firstName" placeholder="Micheal" class="bg-transparent text-body-lg tracking-tight text-white placeholder:text-white/40 focus:outline-none">
                                </label>
                                <label class="flex flex-col gap-1.5 border-b border-white/30 pb-2">
                                    <span class="text-label font-medium tracking-tight text-[#a5a5a5]">Last Name</span>
                                    <input type="text" x-model="lastName" placeholder="Philips" class="bg-transparent text-body-lg tracking-tight text-white placeholder:text-white/40 focus:outline-none">
                                </label>
                            </div>
                            <label class="flex flex-col gap-1.5 border-b border-white/30 pb-2">
                                <span class="text-label font-medium tracking-tight text-[#a5a5a5]">Email Address</span>
                                <input type="email" x-model="email" placeholder="micheal.philips@gmail.com" class="bg-transparent text-body-lg tracking-tight text-white placeholder:text-white/40 focus:outline-none">
                            </label>
                            <label class="flex flex-col gap-1.5 border-b border-white/30 pb-2">
                                <span class="text-label font-medium tracking-tight text-[#a5a5a5]">Phone Number</span>
                                <div class="flex items-center gap-2">
                                    <span class="shrink-0 text-body-lg text-white">🇳🇬 +234</span>
                                    <input type="tel" x-model="phone" placeholder="8143432903" inputmode="numeric" class="w-full bg-transparent text-body-lg tracking-tight text-white placeholder:text-white/40 focus:outline-none">
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="rounded-[10px] bg-[rgba(113,113,113,0.27)] p-6 sm:p-8">
                        <h3 class="text-title font-semibold tracking-tight text-white lg:text-h3">Cancellation Policy</h3>
                        <p class="mt-5 text-body leading-snug tracking-tight text-[#dadbda] lg:text-body-lg">
                            Reschedule or cancel up to 24 hours before your appointment for a full refund. Within 24 hours, the convenience fee is non-refundable.
                        </p>
                    </div>

                    <div class="rounded-[10px] bg-[#373d35] p-6 sm:p-8">
                        <h2 class="text-h3 font-semibold tracking-tight text-white">Payment Options</h2>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <button type="button" @click="channel = 'card'" :class="channel === 'card' ? 'bg-[#ba6d04] text-white' : 'bg-[#696969] text-[#c9c9c9]'" class="flex h-[56px] items-center gap-2 rounded-[13px] px-[22px] text-body-lg font-semibold transition">
                                <svg class="icon-md shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20" stroke-linecap="round"/></svg>
                                Card
                            </button>
                            <button type="button" @click="channel = 'bank'" :class="channel === 'bank' ? 'bg-[#ba6d04] text-white' : 'bg-[#696969] text-[#c9c9c9]'" class="flex h-[56px] items-center gap-2 rounded-[13px] px-[18px] text-body-lg font-medium transition">
                                <svg class="icon-md shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 2 7v2h20V7L12 2zM4 11v7H2v2h20v-2h-2v-7h-2v7h-3v-7h-2v7H8v-7H6v7H4v-7z"/></svg>
                                Bank
                            </button>
                            <button type="button" @click="channel = 'transfer'" :class="channel === 'transfer' ? 'bg-[#ba6d04] text-white' : 'bg-[#696969] text-[#c9c9c9]'" class="flex h-[56px] items-center gap-2 rounded-[13px] px-[16px] text-body-lg font-medium transition">
                                <svg class="icon-md shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h13l-3-3M20 16H7l3 3"/></svg>
                                Transfer
                            </button>
                        </div>
                        <p class="mt-6 flex items-center gap-2 text-label text-white/60">
                            <svg class="icon-xs shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke-linecap="round"/></svg>
                            Card details are entered securely in the Paystack window.
                        </p>
                        <div class="mt-7 flex flex-wrap items-center gap-7">
                            <button type="button" @click="pay()" class="flex h-[75px] min-w-[240px] flex-1 items-center justify-center rounded-[6px] bg-[#ba6d04] text-body-lg font-semibold tracking-tight text-white transition hover:bg-[#a35f03] sm:flex-none sm:w-[279px]">
                                Make reservation
                            </button>
                            <div class="flex flex-col text-white">
                                <span class="text-body-lg font-semibold tracking-tight">Total</span>
                                <span class="text-h3 font-semibold tracking-tight lg:text-h2">{{ $booking['total_label'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-layouts.container>
    </section>

    @if (! empty($paystackKey))
        @push('scripts')
            <script src="https://js.paystack.co/v1/inline.js"></script>
        @endpush
    @endif
</x-layouts.web>
