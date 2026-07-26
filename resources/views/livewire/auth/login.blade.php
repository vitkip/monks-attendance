<div>

    <h2 class="text-2xl font-bold text-slate-800 mb-2">ເຂົ້າສູ່ລະບົບ</h2>
    <p class="text-slate-400 text-sm mb-8">ກະລຸນາໃສ່ອີເມລ ແລະ ລະຫັດຜ່ານ ເພື່ອເຂົ້າສູ່ລະບົບ</p>

    <form wire:submit="login" novalidate class="space-y-5">

        {{-- Email --}}
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ອີເມລ</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125H3.375A1.125 1.125 0 012.25 17.25V6.75zm0 0L12 13.5l9.75-6.75" />
                    </svg>
                </span>
                <input wire:model="email"
                       type="email"
                       autocomplete="email"
                       placeholder="admin@example.com"
                       class="w-full pl-11 pr-4 py-3 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                              placeholder-gray-400 outline-none transition-all
                              {{ $errors->has('email') ? 'border-red-300 ring-1 ring-red-200' : 'border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20' }}">
            </div>
            @error('email')
                <p class="mt-1.5 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ລະຫັດຜ່ານ</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h10.5a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5h-10.5a1.5 1.5 0 01-1.5-1.5v-7.5a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </span>
                <input wire:model="password"
                       type="password"
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full pl-11 pr-4 py-3 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                              placeholder-gray-400 outline-none transition-all
                              {{ $errors->has('password') ? 'border-red-300 ring-1 ring-red-200' : 'border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20' }}">
            </div>
            @error('password')
                <p class="mt-1.5 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember --}}
        <div class="flex items-center gap-2">
            <input wire:model="remember" type="checkbox" id="remember"
                   class="w-4 h-4 rounded border-gray-300 accent-brand-green cursor-pointer">
            <label for="remember" class="text-slate-600 text-sm cursor-pointer select-none">ຈື່ການເຂົ້າລະບົບ</label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="w-full py-3.5 rounded-xl bg-brand-green hover:bg-opacity-90
                       text-white font-semibold text-sm
                       transition shadow-lg shadow-brand-green/20
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-green focus-visible:ring-offset-2
                       disabled:opacity-70 disabled:cursor-not-allowed
                       flex items-center justify-center gap-2">
            <span wire:loading.remove wire:target="login">ເຂົ້າລະບົບ</span>
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                ກຳລັງກວດສອບ...
            </span>
        </button>

    </form>

    <p class="text-center text-gray-300 text-xs mt-8">&copy; {{ date('Y') }} ວັດປ່າໜອງບົວທອງໃຕ້</p>

</div>
