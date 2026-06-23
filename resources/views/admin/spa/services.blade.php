<div class="flex flex-col gap-4">
    {{-- ===== Stat cards ===== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="relative flex flex-col gap-1.5 rounded-2xl border-2 bg-white px-6 py-5" style="border-color: {{ $stat['accent'] }}">
                <span class="absolute right-5 top-5 flex size-7 items-center justify-center rounded-lg" style="background: {{ $stat['accent'] }}1a; color: {{ $stat['accent'] }}">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="M12 2C8 6 8 10 12 14c4-4 4-8 0-12zM5 14c2 2 5 2 7 0M12 14c2 2 5 2 7 0M12 14v8" stroke-linecap="round"/></svg>
                </span>
                <p class="text-[11px] font-medium uppercase tracking-[0.5px] text-[#6b7280]">{{ $stat['label'] }}</p>
                <p class="text-[clamp(22px,2vw,28px)] font-semibold leading-tight text-[#1e1e1e]">{{ $stat['value'] }}</p>
                <p class="text-[11px] font-medium" style="color: {{ $stat['accent'] }}">{{ $stat['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ===== Toolbar ===== --}}
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
        <div class="relative w-full lg:max-w-[240px]">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-[#9ca3af]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search services…"
                   class="h-11 w-full rounded-full border border-[#e5e7eb] bg-white pl-10 pr-4 text-[14px] text-[#1e1e1e] placeholder:text-[#9ca3af] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20">
        </div>

        <div class="no-scrollbar flex flex-nowrap items-center gap-1.5 overflow-x-auto">
            @php $tabs = ['' => ['All', $counts['all']], 'active' => ['Active', $counts['active']], 'inactive' => ['Hidden', $counts['inactive']]]; @endphp
            @foreach ($tabs as $key => [$label, $count])
                <button type="button" wire:click="$set('statusFilter', @js($key))"
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

        <div class="flex flex-wrap items-center gap-2.5 lg:ml-auto">
            <div class="flex h-11 items-center gap-1 rounded-full border border-[#e5e7eb] bg-white pl-2.5 pr-1.5">
                <svg class="size-4 shrink-0 text-[#9ca3af]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h12M3 12h9M3 17h6M17 7v10M17 17l3-3M17 17l-3-3"/></svg>
                <select wire:model.live="sort" class="h-full bg-transparent pr-1 text-[13px] font-medium text-[#1e1e1e] focus:outline-none">
                    <option value="sort_order">Default</option>
                    <option value="name">Name</option>
                    <option value="price_desc">Price ↓</option>
                    <option value="price_asc">Price ↑</option>
                </select>
            </div>
            <button type="button" wire:click="openCreate"
                    class="flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#f38c00] px-5 text-[14px] font-bold text-white transition hover:bg-[#dd7f00]">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Add Service
            </button>
        </div>
    </div>

    {{-- ===== Service list ===== --}}
    @if ($services->isEmpty())
        <div class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-[#d6d9d2] bg-white py-16 text-center">
            <span class="flex size-12 items-center justify-center rounded-full bg-[#fff7ed] text-[#f38c00]">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="M12 2C8 6 8 10 12 14c4-4 4-8 0-12zM5 14c2 2 5 2 7 0M12 14c2 2 5 2 7 0M12 14v8" stroke-linecap="round"/></svg>
            </span>
            <p class="text-[15px] font-semibold text-[#1e1e1e]">No services found</p>
            <p class="text-[13px] text-[#6b7280]">Add a spa service — it will appear in the website Book Session popup.</p>
            <button type="button" wire:click="openCreate" class="mt-1 rounded-xl bg-[#f38c00] px-4 py-2 text-[13px] font-bold text-white hover:bg-[#dd7f00]">Add Service</button>
        </div>
    @else
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            @foreach ($services as $s)
                @php $cc = $s->is_active ? '#16a34a' : '#d97706'; @endphp
                <div class="flex gap-4 overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white" wire:key="spa-{{ $s->id }}">
                    <span class="w-1.5 shrink-0" style="background: {{ $cc }}"></span>
                    <span class="my-3 flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-[#f9fafb]">
                        @if ($s->imageUrl())
                            <img src="{{ $s->imageUrl() }}" alt="{{ $s->name }}" class="h-full w-full object-cover">
                        @else
                            <svg class="size-7 text-[#cbd5e1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
                        @endif
                    </span>
                    <div class="flex flex-1 flex-col gap-1.5 py-3 pr-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-[16px] font-bold text-[#1e1e1e]">{{ $s->name }}</p>
                            <span class="flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold" style="background: {{ $cc }}1a; color: {{ $cc }}">
                                <span class="size-1.5 rounded-full" style="background: {{ $cc }}"></span>{{ $s->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </div>
                        @if ($s->description)
                            <p class="line-clamp-2 text-[13px] text-[#6b7280]">{{ $s->description }}</p>
                        @endif
                        <div class="mt-auto flex items-center justify-between gap-3 pt-1">
                            <div class="leading-tight">
                                <span class="text-[18px] font-bold text-[#f38c00]">{{ $s->priceLabel() }}</span>
                                <span class="text-[11px] text-[#9ca3af]"> / guest</span>
                            </div>
                            <div class="relative flex items-center gap-2" x-data="{ confirming: false }">
                                <button type="button" wire:click="openEdit({{ $s->id }})"
                                        class="flex items-center gap-1.5 rounded-lg border border-[#e5e7eb] bg-white px-3 py-1.5 text-[13px] font-medium text-[#374151] transition hover:bg-[#f9fafb]">
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                    Edit
                                </button>
                                <button type="button" @click="confirming = true"
                                        class="flex size-8 items-center justify-center rounded-lg border border-[#fecaca] bg-[#fef2f2] text-[#dc2626] transition hover:bg-[#fee2e2]">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                                <div x-show="confirming" x-cloak @click.outside="confirming = false" x-transition.opacity
                                     class="absolute right-0 top-full z-30 mt-2 w-56 rounded-xl border border-[#e5e7eb] bg-white p-3 shadow-lg">
                                    <p class="text-[13px] font-semibold text-[#1e1e1e]">Remove {{ $s->name }}?</p>
                                    <p class="mt-1 text-[12px] text-[#6b7280]">It will no longer appear on the website.</p>
                                    <div class="mt-3 flex justify-end gap-2">
                                        <button type="button" @click="confirming = false" class="rounded-lg px-3 py-1.5 text-[12px] font-medium text-[#6b7280] hover:bg-[#f9fafb]">Cancel</button>
                                        <button type="button" @click="confirming = false" wire:click="delete({{ $s->id }})" class="rounded-lg bg-[#dc2626] px-3 py-1.5 text-[12px] font-semibold text-white hover:bg-[#b91c1c]">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ===== Add / Edit modal ===== --}}
    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-cloak x-transition.opacity
         class="fixed inset-0 z-[80] flex items-start justify-center overflow-y-auto bg-black/50 px-4 py-8"
         @keydown.escape.window="show = false">
        <div class="absolute inset-0" @click="show = false"></div>
        <div class="relative z-10 w-full max-w-[560px] rounded-2xl bg-white p-6 shadow-xl sm:p-7" x-show="show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[1px] text-[#f38c00]">Spa &amp; Wellness</p>
                    <h2 class="text-[19px] font-bold text-[#1e1e1e]">{{ $editingId ? 'Edit — '.$fName : 'Add New Service' }}</h2>
                </div>
                <button type="button" @click="show = false" class="flex size-8 items-center justify-center rounded-lg text-[#6b7280] transition hover:bg-[#f3f4f6]">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="save" class="mt-5 flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-[0.5px] text-[#6b7280]">Service Name</label>
                    <input type="text" wire:model="fName" placeholder="e.g. Hot Stone Massage"
                           class="h-11 rounded-xl border border-[#e5e7eb] bg-white px-3.5 text-[14px] text-[#1e1e1e] placeholder:text-[#9ca3af] focus:border-[#f38c00] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/15">
                    @error('fName') <span class="text-[11px] text-[#dc2626]">{{ $message }}</span> @enderror
                </div>

                @php $previewImg = $this->imagePreviewUrl(); @endphp
                <div class="flex flex-col gap-1.5" wire:key="simg-{{ $editingId ?? 'new' }}">
                    <label class="text-[11px] font-semibold uppercase tracking-[0.5px] text-[#6b7280]">Service Image</label>
                    <div class="flex items-center gap-4">
                        <span class="flex h-20 w-28 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[#e5e7eb] bg-[#f9fafb]">
                            @if ($previewImg)
                                <img src="{{ $previewImg }}" alt="" class="h-full w-full object-cover">
                            @else
                                <svg class="size-7 text-[#cbd5e1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
                            @endif
                        </span>
                        <div class="flex flex-col items-start gap-1.5" x-data="cmsImageUpload('fImage')">
                            <label class="cursor-pointer rounded-xl border border-[#e5e7eb] bg-white px-4 py-2 text-[13px] font-semibold text-[#374151] transition hover:bg-[#f9fafb]">
                                <span x-show="!uploading">{{ $previewImg ? 'Change image' : 'Upload image' }}</span>
                                <span x-show="uploading" x-cloak>Uploading… <span x-text="progress + '%'"></span></span>
                                <input type="file" accept="image/*" class="hidden" @change="handle($event)">
                            </label>
                            <p class="text-[11px] text-[#9ca3af]">PNG or JPG, up to 5MB.</p>
                            @error('fImage') <span class="text-[11px] text-[#dc2626]">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-[0.5px] text-[#6b7280]">Price per Guest (₦)</label>
                    <div class="flex h-11 items-center rounded-xl border border-[#e5e7eb] bg-white px-3.5 focus-within:border-[#f38c00] focus-within:ring-2 focus-within:ring-[#f38c00]/15">
                        <span class="mr-2 text-[15px] font-semibold text-[#9ca3af]">₦</span>
                        <input type="number" min="0" wire:model="fPrice" placeholder="0" class="h-full w-full bg-transparent text-[14px] text-[#1e1e1e] focus:outline-none">
                    </div>
                    @error('fPrice') <span class="text-[11px] text-[#dc2626]">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-[0.5px] text-[#6b7280]">Description</label>
                    <textarea wire:model="fDescription" rows="3" placeholder="Short description shown on the service card"
                              class="rounded-xl border border-[#e5e7eb] bg-white p-3.5 text-[14px] text-[#1e1e1e] placeholder:text-[#9ca3af] focus:border-[#f38c00] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/15"></textarea>
                    @error('fDescription') <span class="text-[11px] text-[#dc2626]">{{ $message }}</span> @enderror
                </div>

                <label class="flex cursor-pointer items-center gap-2.5">
                    <input type="checkbox" wire:model="fActive" class="size-4 rounded border-[#d1d5db] text-[#f38c00] focus:ring-[#f38c00]/30">
                    <span class="text-[14px] font-medium text-[#374151]">Active (show on the website)</span>
                </label>

                <div class="mt-1 flex justify-end gap-3">
                    <button type="button" @click="show = false" class="rounded-xl border border-[#e5e7eb] bg-white px-5 py-2.5 text-[14px] font-medium text-[#374151] transition hover:bg-[#f9fafb]">Cancel</button>
                    <button type="submit" class="flex items-center gap-2 rounded-xl bg-[#f38c00] px-5 py-2.5 text-[14px] font-bold text-white transition hover:bg-[#dd7f00]">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                        {{ $editingId ? 'Save Changes' : 'Add Service' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
