<div class="w-full max-w-sm mx-auto px-4">

    {{-- Logo / Brand --}}
    <div class="text-center mb-8">
        @php $logoPath = \App\Models\Setting::get('logo'); @endphp
        @if($logoPath)
            <div class="w-16 h-16 mx-auto rounded-2xl overflow-hidden bg-white/10 backdrop-blur-md flex items-center justify-center mb-4">
                <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-full h-full object-contain p-1">
            </div>
        @else
            <div class="w-16 h-16 mx-auto rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-4">
                <span class="text-3xl select-none">☸</span>
            </div>
        @endif
        <h1 class="text-white font-bold text-xl">ລະບົບກວດສອບການຂາດລາ</h1>
        <p class="text-white/60 text-xs tracking-[0.2em] mt-1 uppercase">Monk Attendance</p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-[2rem] card-shadow shadow-2xl overflow-hidden">

        <div class="px-8 py-8">

            <h2 class="text-slate-800 font-bold text-lg mb-6 text-center">ເຂົ້າລະບົບ</h2>

            <form wire:submit="login" novalidate>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-1.5">ອີເມລ</label>
                    <input wire:model="email"
                           type="email"
                           autocomplete="email"
                           placeholder="admin@example.com"
                           class="w-full px-4 py-3 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                  placeholder-gray-400 outline-none transition-all
                                  {{ $errors->has('email') ? 'border-red-300 ring-1 ring-red-200' : 'border-gray-200 focus:border-brand-green focus:ring-1 focus:ring-brand-green/30' }}">
                    @error('email')
                        <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-1.5">ລະຫັດຜ່ານ</label>
                    <input wire:model="password"
                           type="password"
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                  placeholder-gray-400 outline-none transition-all
                                  {{ $errors->has('password') ? 'border-red-300 ring-1 ring-red-200' : 'border-gray-200 focus:border-brand-green focus:ring-1 focus:ring-brand-green/30' }}">
                    @error('password')
                        <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="flex items-center gap-2 mb-6">
                    <input wire:model="remember" type="checkbox" id="remember"
                           class="w-4 h-4 rounded border-gray-300 accent-[#216143] cursor-pointer">
                    <label for="remember" class="text-slate-600 text-sm cursor-pointer select-none">ຈື່ການເຂົ້າລະບົບ</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-brand-green hover:bg-opacity-90
                               text-white font-semibold text-sm
                               transition shadow-lg shadow-brand-green/20">
                    <span wire:loading.remove wire:target="login">ເຂົ້າລະບົບ</span>
                    <span wire:loading wire:target="login">ກຳລັງກວດສອບ...</span>
                </button>

            </form>
        </div>
    </div>

    <p class="text-center text-white/40 text-[11px] mt-6 tracking-[0.3em] select-none">✦ ✦ ✦</p>

</div>
