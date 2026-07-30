<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລະບົບ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ຈັດການຜູ້ໃຊ້ລະບົບ</h1>
            <p class="text-gray-400 text-sm mt-1">ເພີ່ມ, ແກ້ໄຂ ແລະ ລຶບຜູ້ໃຊ້</p>
        </div>
        <button wire:click="openCreate"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5
                       bg-brand-green text-white rounded-2xl text-sm font-semibold
                       hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 flex-shrink-0">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            ເພີ່ມຜູ້ໃຊ້
        </button>
    </div>

    {{-- Search --}}
    <div class="bg-white card-shadow rounded-2xl p-4 mb-5">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text"
                   placeholder="ຄົ້ນຫາຊື່ ຫຼື ອີເມລ..."
                   class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm
                          text-slate-800 placeholder:text-gray-400
                          focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($users as $user)
            <div class="bg-white rounded-2xl card-shadow overflow-hidden">
                <div class="flex items-center gap-3 px-4 py-3.5">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $user->isAdmin() ? 'bg-brand-light-green' : 'bg-gray-100' }}">
                        <svg class="w-5 h-5 {{ $user->isAdmin() ? 'text-brand-green' : 'text-gray-500' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-slate-800 text-sm">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-brand-light-green text-brand-green">ຂ້ອຍ</span>
                            @endif
                        </div>
                        <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold flex-shrink-0
                        {{ $user->isAdmin()
                            ? 'bg-brand-light-green text-brand-green'
                            : 'bg-gray-100 text-gray-500' }}">
                        {{ $user->isAdmin() ? 'Admin' : 'Staff' }}
                    </span>
                </div>
                @php
                    $cannotDelete = $user->id === auth()->id()
                        || ($user->isAdmin() && $adminCount <= 1);
                @endphp
                <div class="flex border-t border-gray-100">
                    <button wire:click="openEdit({{ $user->id }})"
                            class="flex-1 py-2.5 text-xs text-slate-600 hover:bg-gray-50 transition-colors border-r border-gray-100 font-medium">
                        ແກ້ໄຂ
                    </button>
                    <button wire:click="confirmDelete({{ $user->id }})"
                            @if($cannotDelete) disabled @endif
                            class="flex-1 py-2.5 text-xs font-medium transition-colors
                                   {{ $cannotDelete
                                       ? 'text-gray-300 cursor-not-allowed'
                                       : 'text-red-500 hover:bg-red-50' }}">
                        ລຶບ
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-gray-400 text-sm">ບໍ່ພົບຂໍ້ມູນ</div>
        @endforelse
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">#</th>
                    <th class="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຊື່</th>
                    <th class="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ອີເມລ</th>
                    <th class="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ສິດ</th>
                    <th class="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ວັນທີສ້າງ</th>
                    <th class="px-4 py-3.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-300 text-xs">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                            {{ $user->isAdmin() ? 'bg-brand-light-green' : 'bg-gray-100' }}">
                                    <svg class="w-4 h-4 {{ $user->isAdmin() ? 'text-brand-green' : 'text-gray-500' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-slate-800">{{ $user->name }}</span>
                                @if($user->id === auth()->id())
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-brand-light-green text-brand-green">ຂ້ອຍ</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">{{ $user->email }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold
                                {{ $user->isAdmin()
                                    ? 'bg-brand-light-green text-brand-green'
                                    : 'bg-gray-100 text-gray-500' }}">
                                {{ $user->isAdmin() ? 'Admin' : 'Staff' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3.5">
                            @php
                                $cannotDelete = $user->id === auth()->id()
                                    || ($user->isAdmin() && $adminCount <= 1);
                            @endphp
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEdit({{ $user->id }})"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600
                                               border border-gray-200 hover:bg-gray-50
                                               transition-all duration-150">
                                    ແກ້ໄຂ
                                </button>
                                <button wire:click="confirmDelete({{ $user->id }})"
                                        @if($cannotDelete) disabled @endif
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-150
                                               {{ $cannotDelete
                                                   ? 'text-gray-300 border border-gray-100 cursor-not-allowed'
                                                   : 'text-red-500 border border-red-200 hover:bg-red-50' }}">
                                    ລຶບ
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-gray-400">ບໍ່ພົບຂໍ້ມູນ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile pagination --}}
    @if($users->hasPages())
        <div class="md:hidden mt-4">{{ $users->links() }}</div>
    @endif

    {{-- ══ Create / Edit Modal ══ --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         x-data x-on:keydown.escape.window="$wire.set('showModal', false)"
         wire:click.self="$set('showModal', false)">
        <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden max-h-[95dvh] sm:max-h-[90vh] flex flex-col">

            <div class="px-6 py-4 flex items-center justify-between shrink-0 border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່' }}
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">ຜູ້ໃຊ້ງານ</h2>
                </div>
                <button wire:click="$set('showModal', false)"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-slate-700 hover:bg-gray-100 transition-colors touch-manipulation">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            {{-- Drag handle (mobile only) --}}
            <div class="sm:hidden flex justify-center pt-3 shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4 overflow-y-auto">

                {{-- Name --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ຊື່ - ນາມສະກຸນ</label>
                    <input wire:model="name" type="text" placeholder="ຊື່ ນາມສະກຸນ"
                           class="w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                  placeholder:text-gray-400 outline-none transition-shadow
                                  {{ $errors->has('name') ? 'border-red-300 focus:ring-2 focus:ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-brand-green/30 focus:border-transparent' }}">
                    @error('name') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ອີເມລ</label>
                    <input wire:model="email" type="email" placeholder="example@email.com"
                           class="w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                  placeholder:text-gray-400 outline-none transition-shadow
                                  {{ $errors->has('email') ? 'border-red-300 focus:ring-2 focus:ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-brand-green/30 focus:border-transparent' }}">
                    @error('email') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ສິດການໃຊ້ງານ</label>
                    <select wire:model="role"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                   outline-none transition-shadow
                                   {{ $errors->has('role') ? 'border-red-300 focus:ring-2 focus:ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-brand-green/30 focus:border-transparent' }}">
                        <option value="staff">Staff — ບັນທຶກຂາດໄດ້ຢ່າງດຽວ</option>
                        <option value="admin">Admin — ເຮັດໄດ້ທຸກຢ່າງ</option>
                    </select>
                    @error('role') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">
                        ລະຫັດຜ່ານ
                        @if($editId) <span class="font-normal text-gray-400 normal-case tracking-normal">(ປ່ອຍວ່າງ = ບໍ່ປ່ຽນ)</span> @endif
                    </label>
                    <input wire:model="password" type="password" placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                  placeholder:text-gray-400 outline-none transition-shadow
                                  {{ $errors->has('password') ? 'border-red-300 focus:ring-2 focus:ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-brand-green/30 focus:border-transparent' }}">
                    @error('password') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ຢືນຢັນລະຫັດຜ່ານ</label>
                    <input wire:model="password_confirmation" type="password" placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                  placeholder:text-gray-400 outline-none transition-shadow border-gray-200
                                  focus:ring-2 focus:ring-brand-green/30 focus:border-transparent">
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-2.5 rounded-2xl border border-gray-200 text-sm text-slate-600
                                   hover:bg-gray-50 transition-colors font-medium">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-2xl bg-brand-green text-white text-sm font-semibold
                                   hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20">
                        <span wire:loading.remove wire:target="save">{{ $editId ? 'ບັນທຶກ' : 'ເພີ່ມ' }}</span>
                        <span wire:loading wire:target="save">ກຳລັງບັນທຶກ...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif

    {{-- ══ Delete Confirm Modal ══ --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         x-data x-on:keydown.escape.window="$wire.set('showDeleteModal', false)"
         wire:click.self="$set('showDeleteModal', false)">
        <div class="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
            {{-- Drag handle (mobile only) --}}
            <div class="sm:hidden flex justify-center pt-3">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>
            <div class="px-6 py-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">ຢືນຢັນການລຶບ</h3>
                <p class="text-gray-400 text-sm mb-6">ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຜູ້ໃຊ້ນີ້? ການດຳເນີນການນີ້ບໍ່ສາມາດຍ້ອນກັບໄດ້</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 py-2.5 rounded-2xl border border-gray-200 text-sm text-slate-600
                                   hover:bg-gray-50 transition-colors font-medium">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 py-2.5 rounded-2xl bg-red-500 text-white text-sm font-semibold
                                   hover:bg-red-600 transition-colors">
                        <span wire:loading.remove wire:target="delete">ລຶບ</span>
                        <span wire:loading wire:target="delete">ກຳລັງລຶບ...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
