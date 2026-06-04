@php($toast = session('toast'))

@if ($toast)
    @php($type = $toast['type'] ?? 'success')
    <div
        x-data="{ show: false }"
        x-init="$nextTick(() => { show = true }); setTimeout(() => show = false, 2500)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-3"
        x-cloak
        role="status"
        aria-live="polite"
        class="fixed right-5 top-5 z-50 flex w-[340px] max-w-[calc(100vw-2.5rem)] items-start gap-3 rounded-xl border border-[#e5e7eb] bg-white px-4 py-3 shadow-lg"
    >
        {{-- Icon --}}
        <span @class([
            'mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full',
            'bg-[#e7f6ec] text-[#16a34a]' => $type === 'success',
            'bg-red-100 text-red-600' => $type === 'error',
            'bg-[#fff3e0] text-[#f38c00]' => $type === 'info',
        ])>
            @if ($type === 'error')
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            @else
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            @endif
        </span>

        {{-- Message --}}
        <p class="flex-1 text-[13px] leading-snug text-[#1e1e1e]">{{ $toast['message'] ?? '' }}</p>

        {{-- Close --}}
        <button type="button" @click="show = false"
                class="mt-0.5 shrink-0 text-[#9ca3af] transition hover:text-[#1e1e1e]" aria-label="Dismiss">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif
