<div class="rounded-2xl border border-[#e5e7eb] bg-white p-10 shadow-sm">
    <h1 class="text-[28px] font-bold leading-tight text-[#1e1e1e]">Reset password</h1>
    <p class="mt-2 text-sm text-[#6b7280]">Choose a new password for your admin account.</p>
    <span class="mt-4 block h-[3px] w-14 rounded-full bg-[#f38c00]"></span>

    @error('email')
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            {{ $message }}
        </div>
    @enderror

    <form wire:submit="resetPassword" class="mt-6 space-y-5">
        {{-- Email --}}
        <div>
            <label for="email" class="mb-2 block text-xs text-[#6b7280]">Email address</label>
            <input
                type="email"
                id="email"
                wire:model="email"
                autocomplete="username"
                class="h-12 w-full rounded-lg border border-[#e5e7eb] bg-[#f9fafb] px-4 text-sm text-[#1e1e1e] placeholder:text-[#b2bac2] focus:border-[#f38c00] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20"
            >
        </div>

        {{-- Password --}}
        <div x-data="{ show: false }">
            <label for="password" class="mb-2 block text-xs text-[#6b7280]">New password</label>
            <div class="relative flex h-12 items-center rounded-lg border border-[#e5e7eb] bg-[#f9fafb] px-4 focus-within:border-[#f38c00] focus-within:bg-white focus-within:ring-2 focus-within:ring-[#f38c00]/20">
                <input
                    :type="show ? 'text' : 'password'"
                    id="password"
                    wire:model="password"
                    autocomplete="new-password"
                    placeholder="••••••••••••"
                    class="h-full w-full bg-transparent pr-12 text-sm text-[#1e1e1e] placeholder:text-[#6b7280] focus:outline-none"
                >
                <button type="button" @click="show = !show"
                        class="absolute right-4 text-xs font-medium text-[#f38c00] hover:underline"
                        x-text="show ? 'Hide' : 'Show'">Show</button>
            </div>
            @error('password')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm --}}
        <div>
            <label for="password_confirmation" class="mb-2 block text-xs text-[#6b7280]">Confirm password</label>
            <input
                type="password"
                id="password_confirmation"
                wire:model="password_confirmation"
                autocomplete="new-password"
                placeholder="••••••••••••"
                class="h-12 w-full rounded-lg border border-[#e5e7eb] bg-[#f9fafb] px-4 text-sm text-[#1e1e1e] placeholder:text-[#6b7280] focus:border-[#f38c00] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#f38c00]/20"
            >
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="flex h-[52px] w-full items-center justify-center rounded-[10px] bg-[#f38c00] text-base font-bold text-white transition hover:bg-[#dd7f00] focus:outline-none focus:ring-2 focus:ring-[#f38c00]/40 disabled:opacity-70"
        >
            <span wire:loading.remove wire:target="resetPassword">Reset password</span>
            <span wire:loading wire:target="resetPassword">Resetting…</span>
        </button>
    </form>
</div>
