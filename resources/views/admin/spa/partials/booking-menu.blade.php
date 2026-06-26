{{-- Row action menu. Uses fixed positioning (anchored via getBoundingClientRect)
     so it is never clipped by the table's overflow. $b = the SpaBooking row. --}}
<div x-data="{ open: false, pos: { top: '0px', left: '0px' } }" class="relative inline-block text-left">
    <button type="button" x-ref="trigger"
            @click="open = !open; if (open) { const r = $refs.trigger.getBoundingClientRect(); pos = { top: (r.bottom + 6) + 'px', left: (r.right - 200) + 'px' }; }"
            class="flex size-8 items-center justify-center rounded-lg text-[#6b7280] transition hover:bg-[#f1f1ee] hover:text-[#1e1e1e]">
        <svg class="size-[18px]" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
    </button>

        <div x-show="open" x-cloak @click.outside="open = false" x-transition.opacity.duration.100ms
             class="fixed z-[100] overflow-hidden rounded-xl border border-[#e5e7eb] bg-white py-1.5 shadow-xl"
             style="width: 200px;" :style="`top:${pos.top}; left:${pos.left}`">
            {{-- View details --}}
            <button type="button" @click="open = false" wire:click="viewDetails({{ $b->id }})"
                    class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-[13px] text-[#374151] transition hover:bg-[#f9fafb]">
                <svg class="size-[15px] text-[#6b7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                View Details
            </button>

            @if ($b->status !== 'confirmed' && $b->status !== 'cancelled')
                <button type="button" @click="open = false" wire:click="confirmSession({{ $b->id }})"
                        class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-[13px] text-[#16a34a] transition hover:bg-[#f0fdf4]">
                    <svg class="size-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    Confirm Session
                </button>
            @endif

            @if ($b->payment_status !== 'paid' && $b->status !== 'cancelled')
                <button type="button" @click="open = false" wire:click="recordPayment({{ $b->id }})"
                        class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-[13px] text-[#374151] transition hover:bg-[#f9fafb]">
                    <svg class="size-[15px] text-[#6b7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20" stroke-linecap="round"/></svg>
                    Record Payment
                </button>
            @endif

            @if ($b->status !== 'cancelled')
                <div class="my-1 h-px bg-[#f1f1ee]"></div>
                <button type="button" @click="open = false" wire:click="cancelSession({{ $b->id }})"
                        class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-[13px] text-[#dc2626] transition hover:bg-[#fef2f2]">
                    <svg class="size-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    Cancel Session
                </button>
            @endif
        </div>
</div>
