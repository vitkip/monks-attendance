<div class="max-w-xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div>
        <p class="text-[#D4A017]/70 text-[10px] tracking-[0.35em] uppercase font-medium mb-1">ບັນຊີ</p>
        <h1 class="text-xl sm:text-[22px] font-semibold text-[#1A0A02] tracking-wide leading-tight">ຂໍ້ມູນສ່ວນຕົວ</h1>
        <p class="text-[#9B7A5A] text-sm mt-1">ແກ້ໄຂຊື່, ອີເມລ ແລະ ລະຫັດຜ່ານຂອງທ່ານ</p>
    </div>

    {{-- Profile Card --}}
    <div class="bg-[#FFFDF7] border border-[#D4A017]/20 rounded-2xl overflow-hidden"
         style="box-shadow: 0 1px 8px rgba(26,10,2,0.06)">

        {{-- Avatar strip --}}
        <div class="flex items-center gap-4 px-6 py-5 border-b border-[#D4A017]/15" style="background: #FAF0D8">
            <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0
                        {{ auth()->user()->isAdmin() ? 'bg-[#D4A017]/25' : 'bg-[#2D5A3D]/12' }}">
                <svg class="w-7 h-7 {{ auth()->user()->isAdmin() ? 'text-[#7A4A00]' : 'text-[#1A4A2A]' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-[#1A0A02] font-semibold text-base">{{ auth()->user()->name }}</p>
                <span class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                    {{ auth()->user()->isAdmin()
                        ? 'bg-[#D4A017]/20 text-[#7A4A00]'
                        : 'bg-[#2D5A3D]/12 text-[#1A4A2A]' }}">
                    {{ auth()->user()->isAdmin() ? 'Admin' : 'Staff' }}
                </span>
            </div>
        </div>

        {{-- Info form --}}
        <form wire:submit="saveInfo" class="px-6 py-5 space-y-4">
            <p class="text-[#3A1C05] text-xs font-semibold uppercase tracking-[0.2em]">ຂໍ້ມູນທົ່ວໄປ</p>

            <div>
                <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ຊື່ - ນາມສະກຸນ</label>
                <input wire:model="name" type="text" placeholder="ຊື່ ນາມສະກຸນ"
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                              placeholder-[#B8A080] outline-none transition-all
                              {{ $errors->has('name') ? 'border-[#B83030]' : 'border-[#D4A017]/35 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                @error('name') <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ອີເມລ</label>
                <input wire:model="email" type="email" placeholder="example@email.com"
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                              placeholder-[#B8A080] outline-none transition-all
                              {{ $errors->has('email') ? 'border-[#B83030]' : 'border-[#D4A017]/35 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                @error('email') <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-[#1A0A02] text-[#E8C97A] text-sm font-semibold
                               hover:bg-[#2A1205] transition-colors border border-[#D4A017]/30">
                    <span wire:loading.remove wire:target="saveInfo">ບັນທຶກຂໍ້ມູນ</span>
                    <span wire:loading wire:target="saveInfo">ກຳລັງບັນທຶກ...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Password Card --}}
    <div class="bg-[#FFFDF7] border border-[#D4A017]/20 rounded-2xl overflow-hidden"
         style="box-shadow: 0 1px 8px rgba(26,10,2,0.06)">

        <div class="px-6 py-4 border-b border-[#D4A017]/15" style="background: #FAF0D8">
            <p class="text-[#3A1C05] text-xs font-semibold uppercase tracking-[0.2em]">ປ່ຽນລະຫັດຜ່ານ</p>
        </div>

        <form wire:submit="savePassword" class="px-6 py-5 space-y-4">

            <div>
                <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ລະຫັດຜ່ານປັດຈຸບັນ</label>
                <input wire:model="current_password" type="password" placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                              placeholder-[#B8A080] outline-none transition-all
                              {{ $errors->has('current_password') ? 'border-[#B83030]' : 'border-[#D4A017]/35 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                @error('current_password') <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ລະຫັດຜ່ານໃໝ່</label>
                <input wire:model="new_password" type="password" placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                              placeholder-[#B8A080] outline-none transition-all
                              {{ $errors->has('new_password') ? 'border-[#B83030]' : 'border-[#D4A017]/35 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                @error('new_password') <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ຢືນຢັນລະຫັດຜ່ານໃໝ່</label>
                <input wire:model="new_password_confirmation" type="password" placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                              placeholder-[#B8A080] outline-none transition-all border-[#D4A017]/35
                              focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30">
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-[#1A0A02] text-[#E8C97A] text-sm font-semibold
                               hover:bg-[#2A1205] transition-colors border border-[#D4A017]/30">
                    <span wire:loading.remove wire:target="savePassword">ປ່ຽນລະຫັດຜ່ານ</span>
                    <span wire:loading wire:target="savePassword">ກຳລັງບັນທຶກ...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Logout --}}
    <div class="bg-[#FFFDF7] border border-[#D4A017]/20 rounded-2xl overflow-hidden"
         style="box-shadow: 0 1px 8px rgba(26,10,2,0.06)">

        <div class="px-6 py-4 border-b border-[#D4A017]/15" style="background: #FAF0D8">
            <p class="text-[#3A1C05] text-xs font-semibold uppercase tracking-[0.2em]">ອອກຈາກລະບົບ</p>
        </div>

        <div class="px-6 py-5">
            <p class="text-[#9B7A5A] text-sm mb-4">ອອກຈາກລະບົບໃນທຸກອຸປະກອນຂອງທ່ານ</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold
                               text-[#B83030] border border-[#B83030]/30 hover:bg-[#B83030]/8
                               hover:border-[#B83030]/60 transition-all duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    ອອກຈາກລະບົບ
                </button>
            </form>
        </div>
    </div>

</div>
