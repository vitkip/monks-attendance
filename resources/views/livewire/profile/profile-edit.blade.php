<div class="max-w-xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ບັນຊີ</p>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ຂໍ້ມູນສ່ວນຕົວ</h1>
        <p class="text-gray-400 text-sm mt-1">ແກ້ໄຂຊື່, ອີເມລ ແລະ ລະຫັດຜ່ານຂອງທ່ານ</p>
    </div>

    {{-- Profile Card --}}
    <div class="bg-white rounded-3xl card-shadow overflow-hidden">

        {{-- Avatar strip --}}
        <div class="flex items-center gap-4 px-6 py-5 border-b border-gray-100 bg-[#f8fafa]">
            <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0
                        {{ auth()->user()->isAdmin() ? 'bg-brand-light-green' : 'bg-gray-100' }}">
                <svg class="w-7 h-7 {{ auth()->user()->isAdmin() ? 'text-brand-green' : 'text-slate-500' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-slate-800 font-semibold text-base">{{ auth()->user()->name }}</p>
                <span class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                    {{ auth()->user()->isAdmin()
                        ? 'bg-brand-light-green text-brand-green'
                        : 'bg-gray-100 text-slate-500' }}">
                    {{ auth()->user()->isAdmin() ? 'Admin' : 'Staff' }}
                </span>
            </div>
        </div>

        {{-- Info form --}}
        <form wire:submit="saveInfo" class="px-6 py-5 space-y-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ຂໍ້ມູນທົ່ວໄປ</p>

            <div>
                <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ຊື່ - ນາມສະກຸນ</label>
                <input wire:model="name" type="text" placeholder="ຊື່ ນາມສະກຸນ"
                       class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none focus:ring-2 focus:border-transparent transition-shadow
                              {{ $errors->has('name') ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30' }}">
                @error('name') <p class="mt-1.5 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ອີເມລ</label>
                <input wire:model="email" type="email" placeholder="example@email.com"
                       class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none focus:ring-2 focus:border-transparent transition-shadow
                              {{ $errors->has('email') ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30' }}">
                @error('email') <p class="mt-1.5 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                        class="px-6 py-2.5 rounded-2xl bg-brand-green text-white text-sm font-semibold
                               hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20">
                    <span wire:loading.remove wire:target="saveInfo">ບັນທຶກຂໍ້ມູນ</span>
                    <span wire:loading wire:target="saveInfo">ກຳລັງບັນທຶກ...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Password Card --}}
    <div class="bg-white rounded-3xl card-shadow overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 bg-[#f8fafa]">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ປ່ຽນລະຫັດຜ່ານ</p>
        </div>

        <form wire:submit="savePassword" class="px-6 py-5 space-y-4">

            <div>
                <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ລະຫັດຜ່ານປັດຈຸບັນ</label>
                <input wire:model="current_password" type="password" placeholder="••••••••"
                       class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none focus:ring-2 focus:border-transparent transition-shadow
                              {{ $errors->has('current_password') ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30' }}">
                @error('current_password') <p class="mt-1.5 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ລະຫັດຜ່ານໃໝ່</label>
                <input wire:model="new_password" type="password" placeholder="••••••••"
                       class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none focus:ring-2 focus:border-transparent transition-shadow
                              {{ $errors->has('new_password') ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30' }}">
                @error('new_password') <p class="mt-1.5 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ຢືນຢັນລະຫັດຜ່ານໃໝ່</label>
                <input wire:model="new_password_confirmation" type="password" placeholder="••••••••"
                       class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none
                              focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                        class="px-6 py-2.5 rounded-2xl bg-brand-green text-white text-sm font-semibold
                               hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20">
                    <span wire:loading.remove wire:target="savePassword">ປ່ຽນລະຫັດຜ່ານ</span>
                    <span wire:loading wire:target="savePassword">ກຳລັງບັນທຶກ...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Logout --}}
    <div class="bg-white rounded-3xl card-shadow overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 bg-[#f8fafa]">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ອອກຈາກລະບົບ</p>
        </div>

        <div class="px-6 py-5">
            <p class="text-gray-400 text-sm mb-4">ອອກຈາກລະບົບໃນທຸກອຸປະກອນຂອງທ່ານ</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-semibold
                               text-red-500 border border-red-200 hover:bg-red-50
                               hover:border-red-300 transition-all duration-150">
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
