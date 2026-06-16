@php
    use App\Models\SiteContent;
@endphp

<div class="flex flex-col gap-5" x-data="{ tab: @entangle('tab').live }">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Section tabs --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach ($sections as $key => $section)
                <button type="button" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'bg-[#1e2318] text-white' : 'bg-white text-[#374151] border border-[#e5e7eb] hover:bg-[#f9fafb]'"
                        class="rounded-full px-4 py-2 text-[13px] font-semibold transition">
                    {{ $section['label'] }}
                </button>
            @endforeach
        </div>

        <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save,uploads"
                class="flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#f38c00] px-6 text-[14px] font-bold text-white transition hover:bg-[#dd7f00] disabled:opacity-60">
            <svg wire:loading wire:target="save" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
            <span wire:loading.remove wire:target="save">Save changes</span>
            <span wire:loading wire:target="save">Saving…</span>
        </button>
    </div>

    {{-- Sections --}}
    @foreach ($sections as $key => $section)
        <div x-show="tab === '{{ $key }}'" x-cloak class="flex flex-col gap-4">
            @foreach ($section['fields'] as $field)
                @php $name = $field['name']; @endphp

                {{-- ===== Image field ===== --}}
                @if ($field['type'] === 'image')
                    @php
                        $existing = $values[$name] ?? null;
                        $preview = $existing ? SiteContent::imageUrl($existing) : null;
                    @endphp
                    <div wire:key="f-{{ $name }}" class="rounded-2xl border border-[#e5e7eb] bg-white p-4">
                        <p class="mb-3 text-[14px] font-semibold text-[#1e1e1e]">{{ $field['label'] }}</p>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="h-[120px] w-full overflow-hidden rounded-xl bg-[#f3f3ee] sm:w-[200px] sm:shrink-0">
                                @if (! empty($uploads[$name]))
                                    <img src="{{ $uploads[$name]->temporaryUrl() }}" alt="" class="h-full w-full object-cover">
                                @elseif ($preview)
                                    <img src="{{ $preview }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col gap-2">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-[#d6d9d2] bg-[#fafaf7] px-4 py-3 text-[13px] font-medium text-[#374151] transition hover:bg-[#f3f3ee]">
                                    <svg class="size-4 text-[#9ca3af]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
                                    <span wire:loading.remove wire:target="uploads.{{ $name }}">Upload new image · PNG/JPG up to 5MB</span>
                                    <span wire:loading wire:target="uploads.{{ $name }}">Uploading…</span>
                                    <input type="file" wire:model="uploads.{{ $name }}" accept="image/*" class="hidden">
                                </label>
                                @error('uploads.'.$name) <span class="text-[12px] text-[#dc2626]">{{ $message }}</span> @enderror
                                @if ($existing || ! empty($uploads[$name]))
                                    <button type="button" wire:click="removeImage('{{ $name }}')"
                                            class="self-start text-[12px] font-medium text-[#dc2626] hover:underline">Remove image</button>
                                @endif
                            </div>
                        </div>
                    </div>

                {{-- ===== Repeater field (FAQs) ===== --}}
                @elseif ($field['type'] === 'repeater')
                    <div wire:key="f-{{ $name }}" class="rounded-2xl border border-[#e5e7eb] bg-white p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-[14px] font-semibold text-[#1e1e1e]">{{ $field['label'] }}</p>
                            <button type="button" wire:click="addFaq"
                                    class="flex items-center gap-1.5 rounded-lg border border-[#e5e7eb] px-3 py-1.5 text-[13px] font-medium text-[#374151] transition hover:bg-[#f9fafb]">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                Add
                            </button>
                        </div>
                        <div class="flex flex-col gap-3">
                            @forelse ($faqs as $i => $faq)
                                <div wire:key="faq-{{ $i }}" class="rounded-xl border border-[#eceae3] bg-[#fafaf7] p-3">
                                    <div class="flex items-start gap-2">
                                        <span class="mt-2.5 text-[12px] font-semibold text-[#9ca3af]">{{ $i + 1 }}</span>
                                        <div class="flex flex-1 flex-col gap-2">
                                            <input type="text" wire:model="faqs.{{ $i }}.q" placeholder="Question"
                                                   class="h-10 w-full rounded-lg border border-[#e5e7eb] bg-white px-3 text-[14px] font-semibold text-[#1e1e1e] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20">
                                            <textarea wire:model="faqs.{{ $i }}.a" rows="2" placeholder="Answer"
                                                      class="w-full resize-y rounded-lg border border-[#e5e7eb] bg-white p-3 text-[14px] text-[#374151] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20"></textarea>
                                        </div>
                                        <button type="button" wire:click="removeFaq({{ $i }})"
                                                class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-lg text-[#dc2626] transition hover:bg-[#fef2f2]" title="Remove">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-[13px] text-[#9ca3af]">No FAQs yet — click “Add” to create one.</p>
                            @endforelse
                        </div>
                    </div>

                {{-- ===== Textarea field ===== --}}
                @elseif ($field['type'] === 'textarea')
                    <div wire:key="f-{{ $name }}" class="rounded-2xl border border-[#e5e7eb] bg-white p-4">
                        <label class="mb-2 block text-[14px] font-semibold text-[#1e1e1e]">{{ $field['label'] }}</label>
                        <textarea wire:model="values.{{ $name }}" rows="4"
                                  class="w-full resize-y rounded-xl border border-[#e5e7eb] p-3 text-[14px] leading-relaxed text-[#374151] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20"></textarea>
                    </div>

                {{-- ===== Text field ===== --}}
                @else
                    <div wire:key="f-{{ $name }}" class="rounded-2xl border border-[#e5e7eb] bg-white p-4">
                        <label class="mb-2 block text-[14px] font-semibold text-[#1e1e1e]">{{ $field['label'] }}</label>
                        <input type="text" wire:model="values.{{ $name }}"
                               class="h-11 w-full rounded-xl border border-[#e5e7eb] px-3 text-[14px] text-[#1e1e1e] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20">
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
</div>
