{{-- Status-aware ⋮ actions menu for a transport booking ($b) --}}
<div class="relative inline-block text-left" x-data="{ open: false }">
    <button type="button" @click="open = !open" @click.outside="open = false"
            class="flex size-8 items-center justify-center rounded-lg text-[#6b7280] transition hover:bg-[#f3f4f6]">
        <svg class="size-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
    </button>

    <div x-show="open" x-cloak x-transition
         class="absolute right-0 z-30 mt-1 w-56 overflow-hidden rounded-xl border border-[#e5e7eb] bg-white shadow-lg">
        <div class="border-b border-[#f1f1ee] px-4 py-2.5 text-right">
            <p class="text-[11px] font-bold text-[#f38c00]">{{ $b->transportCode() }}</p>
            <p class="text-[12px] font-medium text-[#1e1e1e]">{{ $b->customer_name ?: 'Guest' }}</p>
        </div>
        <button type="button" @click="open = false" wire:click="view({{ $b->id }})"
                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-[13px] text-[#374151] transition hover:bg-[#f9fafb]">
            <svg class="size-4 text-[#6b7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
            View Details
        </button>
        @if ($b->status === 'pending')
            <button type="button" @click="open = false" wire:click="confirm({{ $b->id }})"
                    class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-[13px] font-medium text-[#16a34a] transition hover:bg-[#f0fdf4]">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Confirm Booking
            </button>
            <button type="button" @click="open = false" wire:click="reject({{ $b->id }})"
                    class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-[13px] font-medium text-[#dc2626] transition hover:bg-[#fef2f2]">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="m15 9-6 6M9 9l6 6" stroke-linecap="round"/></svg>
                Reject Booking
            </button>
        @endif
    </div>
</div>
