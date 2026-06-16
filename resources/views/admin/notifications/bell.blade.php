<div class="relative" wire:poll.30s x-data="{ notifOpen: false }" @keydown.escape.window="notifOpen = false">
    <button type="button" @click="notifOpen = !notifOpen"
            class="relative flex size-9 items-center justify-center rounded-full transition hover:bg-[#f3f4f6]" aria-label="Notifications">
        <svg class="size-6 text-[#1e1e1e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
        </svg>
        @if ($notifCount > 0)
            <span class="absolute right-[7px] top-[7px] size-[7px] rounded-[3.5px] border-[0.8px] border-white bg-[#f38c00]"></span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="notifOpen" x-cloak x-transition.origin.top.right @click.outside="notifOpen = false"
         class="absolute right-0 z-50 mt-2 w-[340px] max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-[#f1f1ee] px-4 py-3">
            <p class="text-[14px] font-bold text-[#1e1e1e]">Notifications</p>
            @if ($notifCount > 0)
                <span class="rounded-full bg-[#fff3e0] px-2 py-0.5 text-[11px] font-semibold text-[#b45309]">{{ $notifCount }} new</span>
            @endif
        </div>

        <div class="max-h-[60vh] overflow-y-auto">
            @if ($newMessages->isEmpty() && $recentBookings->isEmpty())
                <div class="flex flex-col items-center gap-2 px-4 py-10 text-center">
                    <div class="flex size-11 items-center justify-center rounded-full bg-[#f3f3ee]">
                        <svg class="size-5 text-[#9ca3af]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    </div>
                    <p class="text-[13px] font-semibold text-[#1e1e1e]">You're all caught up</p>
                    <p class="text-[12px] text-[#9ca3af]">New messages and bookings will show here.</p>
                </div>
            @endif

            {{-- New contact messages --}}
            @if ($newMessages->isNotEmpty())
                <p class="bg-[#fafaf7] px-4 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-[#9ca3af]">Messages</p>
                @foreach ($newMessages as $m)
                    <a href="{{ route('admin.messages.index') }}" wire:navigate @click="notifOpen = false"
                       class="flex items-start gap-3 px-4 py-3 transition hover:bg-[#f9fafb]">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#f3f3ee] text-[12px] font-bold text-[#6b7280]">{{ $m->initials }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] font-semibold text-[#1e1e1e]">{{ $m->full_name }}</p>
                            <p class="truncate text-[12px] text-[#6b7280]">{{ $m->message ?: 'New enquiry' }}</p>
                            <p class="mt-0.5 text-[11px] text-[#9ca3af]">{{ $m->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="mt-1 size-2 shrink-0 rounded-full bg-[#f38c00]"></span>
                    </a>
                @endforeach
            @endif

            {{-- Recent bookings --}}
            @if ($recentBookings->isNotEmpty())
                <p class="bg-[#fafaf7] px-4 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-[#9ca3af]">Recent bookings</p>
                @foreach ($recentBookings as $b)
                    <a href="{{ route('admin.bookings.index') }}" wire:navigate @click="notifOpen = false"
                       class="flex items-start gap-3 px-4 py-3 transition hover:bg-[#f9fafb]">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#e7f6ec] text-[#16a34a]">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] font-semibold text-[#1e1e1e]">{{ $b->customer_name ?: 'New booking' }}</p>
                            <p class="truncate text-[12px] text-[#6b7280]">{{ $b->room_name }} · {{ $b->amountLabel() }}</p>
                            <p class="mt-0.5 text-[11px] text-[#9ca3af]">{{ $b->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>

        <a href="{{ route('admin.messages.index') }}" wire:navigate @click="notifOpen = false"
           class="block border-t border-[#f1f1ee] px-4 py-3 text-center text-[13px] font-semibold text-[#ba6d04] transition hover:bg-[#f9fafb]">
            View all messages
        </a>
    </div>
</div>
