<div class="w-full max-w-sm mx-auto px-4">

    {{-- Logo / Brand --}}
    <div class="text-center mb-8">
        @php $logoPath = \App\Models\Setting::get('logo'); @endphp
        @if($logoPath)
            <div class="w-16 h-16 mx-auto rounded-xl overflow-hidden border border-[#D4A017]/30 bg-white/5 flex items-center justify-center mb-3">
                <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-full h-full object-contain p-1">
            </div>
        @else
            <span class="text-5xl select-none">☸️</span>
        @endif
        <h1 class="mt-3 text-[#E8C97A] font-semibold text-xl tracking-wide">ລະບົບກວດສອບການຂາດລາ</h1>
        <p class="text-[#7A5A30] text-xs tracking-[0.2em] mt-1 uppercase">Monk Attendance</p>
    </div>

    {{-- Card --}}
    <div class="bg-[#FAF6EE] rounded-2xl shadow-2xl shadow-black/50 overflow-hidden">

        <div class="h-1 bg-gradient-to-r from-[#D4A017] via-[#E8C97A] to-[#D4A017]"></div>

        <div class="px-8 py-8">

            <h2 class="text-[#1A0A02] font-semibold text-base mb-6 text-center">ເຂົ້າລະບົບ</h2>

            <form wire:submit="login" novalidate>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-[#3A1C05] text-sm font-medium mb-1.5">ອີເມລ</label>
                    <input wire:model="email"
                           type="email"
                           autocomplete="email"
                           placeholder="admin@example.com"
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                                  placeholder-[#B8A080] outline-none transition-all
                                  {{ $errors->has('email') ? 'border-[#B83030] ring-1 ring-[#B83030]/30' : 'border-[#D4A017]/40 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                    @error('email')
                        <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label class="block text-[#3A1C05] text-sm font-medium mb-1.5">ລະຫັດຜ່ານ</label>
                    <input wire:model="password"
                           type="password"
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                                  placeholder-[#B8A080] outline-none transition-all
                                  {{ $errors->has('password') ? 'border-[#B83030] ring-1 ring-[#B83030]/30' : 'border-[#D4A017]/40 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                    @error('password')
                        <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="flex items-center gap-2 mb-6">
                    <input wire:model="remember" type="checkbox" id="remember"
                           class="w-4 h-4 rounded border-[#D4A017]/40 accent-[#D4A017] cursor-pointer">
                    <label for="remember" class="text-[#3A1C05] text-sm cursor-pointer select-none">ຈື່ການເຂົ້າລະບົບ</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-2.5 rounded-lg bg-[#D4A017] hover:bg-[#C49010] active:bg-[#B4800D]
                               text-[#1A0A02] font-semibold text-sm tracking-wide
                               transition-colors duration-150 shadow-md shadow-[#D4A017]/30">
                    <span wire:loading.remove wire:target="login">ເຂົ້າລະບົບ</span>
                    <span wire:loading wire:target="login">ກຳລັງກວດສອບ...</span>
                </button>

            </form>
        </div>
    </div>

    <p class="text-center text-[#2A1205] text-[11px] mt-6 tracking-[0.2em] select-none">✦ ✦ ✦</p>

</div>
