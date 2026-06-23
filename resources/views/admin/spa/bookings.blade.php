<div class="flex flex-col gap-4">
    {{-- ===== Stat cards ===== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="relative flex flex-col gap-1.5 rounded-2xl border-2 bg-white px-6 py-5" style="border-color: {{ $stat['accent'] }}">
                <p class="text-[11px] font-medium uppercase tracking-[0.5px] text-[#6b7280]">{{ $stat['label'] }}</p>
                <p class="text-[clamp(20px,2vw,26px)] font-semibold leading-tight text-[#1e1e1e]">{{ $stat['value'] }}</p>
                <p class="text-[11px] font-medium" style="color: {{ $stat['accent'] }}">{{ $stat['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ===== Toolbar ===== --}}
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
        <div class="relative w-full lg:max-w-[280px]">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-[#9ca3af]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by reference, name, email…"
                   class="h-11 w-full rounded-full border border-[#e5e7eb] bg-white pl-10 pr-4 text-[14px] text-[#1e1e1e] placeholder:text-[#9ca3af] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20">
        </div>
        <div class="no-scrollbar flex flex-nowrap items-center gap-1.5 overflow-x-auto">
            @php $tabs = ['' => ['All', $counts['all']], 'paid' => ['Paid', $counts['paid']], 'cancelled' => ['Cancelled', $counts['cancelled']]]; @endphp
            @foreach ($tabs as $key => [$label, $count])
                <button type="button" wire:click="setStatus(@js($key))"
                        @class([
                            'flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full border px-3.5 py-2 text-[13px] font-medium transition',
                            'border-[#f38c00] bg-[#f38c00] text-white' => $statusFilter === $key,
                            'border-[#e5e7eb] bg-white text-[#6b7280] hover:bg-[#f9fafb]' => $statusFilter !== $key,
                        ])>
                    {{ $label }}
                    <span @class(['flex min-w-[18px] items-center justify-center rounded-full px-1 text-[11px] font-semibold', 'bg-white/25 text-white' => $statusFilter === $key, 'bg-[#f3f4f6] text-[#6b7280]' => $statusFilter !== $key])>{{ $count }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===== Table ===== --}}
    @if ($bookings->isEmpty())
        <div class="flex flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-[#d6d9d2] bg-white py-16 text-center">
            <p class="text-[15px] font-semibold text-[#1e1e1e]">No reservations yet</p>
            <p class="text-[13px] text-[#6b7280]">Spa bookings made on the website will appear here.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left">
                    <thead>
                        <tr class="border-b border-[#f1f1ee] text-[11px] font-semibold uppercase tracking-[0.5px] text-[#9ca3af]">
                            <th class="px-5 py-3.5">Reference</th>
                            <th class="px-5 py-3.5">Guest</th>
                            <th class="px-5 py-3.5">Services</th>
                            <th class="px-5 py-3.5">Date</th>
                            <th class="px-5 py-3.5">Total</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f1f1ee]">
                        @foreach ($bookings as $b)
                            @php $cc = $b->statusColor(); @endphp
                            <tr class="text-[13px] text-[#374151]" wire:key="spabk-{{ $b->id }}">
                                <td class="px-5 py-4 font-semibold text-[#1e1e1e]">{{ $b->reference }}</td>
                                <td class="px-5 py-4">
                                    <p class="font-medium text-[#1e1e1e]">{{ $b->customer_name ?: '—' }}</p>
                                    <p class="text-[12px] text-[#9ca3af]">{{ $b->customer_email ?: '' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="max-w-[220px] truncate">{{ $b->servicesLabel() }}</p>
                                    <p class="text-[12px] text-[#9ca3af]">{{ $b->guests }} {{ \Illuminate\Support\Str::plural('guest', $b->guests) }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p>{{ $b->date?->format('M j, Y') ?: '—' }}</p>
                                    <p class="text-[12px] text-[#9ca3af]">{{ $b->time }}</p>
                                </td>
                                <td class="px-5 py-4 font-semibold text-[#1e1e1e]">{{ $b->totalLabel() }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold" style="background: {{ $cc }}1a; color: {{ $cc }}">
                                        <span class="size-1.5 rounded-full" style="background: {{ $cc }}"></span>{{ $b->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($b->status !== 'cancelled')
                                        <div x-data="{ confirming: false }" class="relative inline-block">
                                            <button type="button" @click="confirming = true" class="rounded-lg border border-[#e5e7eb] px-3 py-1.5 text-[12px] font-medium text-[#6b7280] transition hover:bg-[#f9fafb]">Cancel</button>
                                            <div x-show="confirming" x-cloak @click.outside="confirming = false" x-transition.opacity
                                                 class="absolute right-0 top-full z-30 mt-2 w-52 rounded-xl border border-[#e5e7eb] bg-white p-3 text-left shadow-lg">
                                                <p class="text-[13px] font-semibold text-[#1e1e1e]">Cancel this reservation?</p>
                                                <div class="mt-3 flex justify-end gap-2">
                                                    <button type="button" @click="confirming = false" class="rounded-lg px-3 py-1.5 text-[12px] font-medium text-[#6b7280] hover:bg-[#f9fafb]">Keep</button>
                                                    <button type="button" @click="confirming = false" wire:click="markCancelled({{ $b->id }})" class="rounded-lg bg-[#dc2626] px-3 py-1.5 text-[12px] font-semibold text-white hover:bg-[#b91c1c]">Cancel it</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div>{{ $bookings->links() }}</div>
    @endif
</div>
