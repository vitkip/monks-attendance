<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[#D4A017]/70 text-[10px] tracking-[0.35em] uppercase font-medium mb-1">ລະບົບ</p>
            <h1 class="text-xl sm:text-[22px] font-semibold text-[#1A0A02] tracking-wide leading-tight">ຈັດການຜູ້ໃຊ້ລະບົບ</h1>
            <p class="text-[#9B7A5A] text-sm mt-1">ເພີ່ມ, ແກ້ໄຂ ແລະ ລຶບຜູ້ໃຊ້</p>
        </div>
        <button wire:click="openCreate"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5
                       bg-[#1A0A02] text-[#E8C97A] rounded-lg text-sm font-medium
                       border border-[#D4A017]/30 hover:bg-[#2A1205] hover:border-[#D4A017]/60
                       transition-all duration-150 flex-shrink-0">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            ເພີ່ມຜູ້ໃຊ້
        </button>
    </div>

    {{-- Search --}}
    <div class="bg-[#FFFDF7] border border-[#D4A017]/20 rounded-xl p-4 mb-5">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#9B7A5A] pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text"
                   placeholder="ຄົ້ນຫາຊື່ ຫຼື ອີເມລ..."
                   class="w-full border border-[#D4A017]/25 rounded-lg pl-10 pr-4 py-2.5 text-sm
                          bg-white/60 text-[#1A0A02] placeholder-[#9B7A5A]
                          focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017]/50 transition">
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($users as $user)
            <div class="rounded-xl border border-[#D4A017]/20 overflow-hidden"
                 style="box-shadow: 0 1px 8px rgba(26,10,2,0.06)">
                <div class="flex items-center gap-3 px-4 py-3.5" style="background: #FFFDF7">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $user->isAdmin() ? 'bg-[#D4A017]/20' : 'bg-[#2D5A3D]/10' }}">
                        <svg class="w-5 h-5 {{ $user->isAdmin() ? 'text-[#7A4A00]' : 'text-[#1A4A2A]' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-[#1A0A02] text-sm">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-[#D4A017]/15 text-[#7A4A00]">ຂ້ອຍ</span>
                            @endif
                        </div>
                        <p class="text-[#9B7A5A] text-xs mt-0.5 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold flex-shrink-0
                        {{ $user->isAdmin()
                            ? 'bg-[#D4A017]/15 text-[#7A4A00]'
                            : 'bg-[#2D5A3D]/10 text-[#1A4A2A]' }}">
                        {{ $user->isAdmin() ? 'Admin' : 'Staff' }}
                    </span>
                </div>
                @php
                    $cannotDelete = $user->id === auth()->id()
                        || ($user->isAdmin() && $adminCount <= 1);
                @endphp
                <div class="flex border-t border-[#D4A017]/12" style="background: #FAF6EE">
                    <button wire:click="openEdit({{ $user->id }})"
                            class="flex-1 py-2.5 text-xs text-[#3A5A4A] hover:bg-[#2D5A3D]/8 transition-colors border-r border-[#D4A017]/12 font-medium">
                        ແກ້ໄຂ
                    </button>
                    <button wire:click="confirmDelete({{ $user->id }})"
                            @if($cannotDelete) disabled @endif
                            class="flex-1 py-2.5 text-xs font-medium transition-colors
                                   {{ $cannotDelete
                                       ? 'text-[#B8A080] cursor-not-allowed'
                                       : 'text-[#B83030] hover:bg-[#B83030]/8' }}">
                        ລຶບ
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-[#9B7A5A] text-sm">ບໍ່ພົບຂໍ້ມູນ</div>
        @endforelse
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-[#FFFDF7] border border-[#D4A017]/20 rounded-xl overflow-hidden"
         style="box-shadow: 0 1px 8px rgba(26,10,2,0.06)">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#D4A017]/20" style="background: #FAF0D8">
                    <th class="text-left px-5 py-3.5 text-[#3A1C05] font-semibold text-xs tracking-wide uppercase">#</th>
                    <th class="text-left px-5 py-3.5 text-[#3A1C05] font-semibold text-xs tracking-wide uppercase">ຊື່</th>
                    <th class="text-left px-5 py-3.5 text-[#3A1C05] font-semibold text-xs tracking-wide uppercase">ອີເມລ</th>
                    <th class="text-left px-5 py-3.5 text-[#3A1C05] font-semibold text-xs tracking-wide uppercase">ສິດ</th>
                    <th class="text-left px-5 py-3.5 text-[#3A1C05] font-semibold text-xs tracking-wide uppercase">ວັນທີສ້າງ</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#D4A017]/10">
                @forelse ($users as $user)
                    <tr class="hover:bg-[#D4A017]/5 transition-colors">
                        <td class="px-5 py-3.5 text-[#9B7A5A] text-xs">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                            {{ $user->isAdmin() ? 'bg-[#D4A017]/15' : 'bg-[#2D5A3D]/10' }}">
                                    <svg class="w-4 h-4 {{ $user->isAdmin() ? 'text-[#7A4A00]' : 'text-[#1A4A2A]' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-[#1A0A02]">{{ $user->name }}</span>
                                @if($user->id === auth()->id())
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-[#D4A017]/15 text-[#7A4A00]">ຂ້ອຍ</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[#5A3A1A]">{{ $user->email }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold
                                {{ $user->isAdmin()
                                    ? 'bg-[#D4A017]/15 text-[#7A4A00]'
                                    : 'bg-[#2D5A3D]/10 text-[#1A4A2A]' }}">
                                {{ $user->isAdmin() ? 'Admin' : 'Staff' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-[#9B7A5A] text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $cannotDelete = $user->id === auth()->id()
                                    || ($user->isAdmin() && $adminCount <= 1);
                            @endphp
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEdit({{ $user->id }})"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-[#1A0A02]
                                               border border-[#D4A017]/30 hover:border-[#D4A017]/60 hover:bg-[#D4A017]/8
                                               transition-all duration-150">
                                    ແກ້ໄຂ
                                </button>
                                <button wire:click="confirmDelete({{ $user->id }})"
                                        @if($cannotDelete) disabled @endif
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-150
                                               {{ $cannotDelete
                                                   ? 'text-[#B8A080] border border-[#D4A017]/10 cursor-not-allowed'
                                                   : 'text-[#B83030] border border-[#B83030]/30 hover:border-[#B83030]/60 hover:bg-[#B83030]/8' }}">
                                    ລຶບ
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-[#9B7A5A]">ບໍ່ພົບຂໍ້ມູນ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-[#D4A017]/15">
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
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.set('showModal', false)">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-[#FAF6EE] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
             style="box-shadow: 0 20px 60px rgba(26,10,2,0.4)">

            <div class="h-1 bg-gradient-to-r from-[#D4A017] via-[#E8C97A] to-[#D4A017]"></div>

            <div class="px-6 py-5 border-b border-[#D4A017]/15">
                <h3 class="text-[#1A0A02] font-semibold text-base">
                    {{ $editId ? 'ແກ້ໄຂຜູ້ໃຊ້' : 'ເພີ່ມຜູ້ໃຊ້ໃໝ່' }}
                </h3>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4">

                {{-- Name --}}
                <div>
                    <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ຊື່ - ນາມສະກຸນ</label>
                    <input wire:model="name" type="text" placeholder="ຊື່ ນາມສະກຸນ"
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                                  placeholder-[#B8A080] outline-none transition-all
                                  {{ $errors->has('name') ? 'border-[#B83030]' : 'border-[#D4A017]/35 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                    @error('name') <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ອີເມລ</label>
                    <input wire:model="email" type="email" placeholder="example@email.com"
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                                  placeholder-[#B8A080] outline-none transition-all
                                  {{ $errors->has('email') ? 'border-[#B83030]' : 'border-[#D4A017]/35 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                    @error('email') <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ສິດການໃຊ້ງານ</label>
                    <select wire:model="role"
                            class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                                   outline-none transition-all
                                   {{ $errors->has('role') ? 'border-[#B83030]' : 'border-[#D4A017]/35 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                        <option value="staff">Staff — ບັນທຶກຂາດໄດ້ຢ່າງດຽວ</option>
                        <option value="admin">Admin — ເຮັດໄດ້ທຸກຢ່າງ</option>
                    </select>
                    @error('role') <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">
                        ລະຫັດຜ່ານ
                        @if($editId) <span class="font-normal text-[#9B7A5A] normal-case tracking-normal">(ປ່ອຍວ່າງ = ບໍ່ປ່ຽນ)</span> @endif
                    </label>
                    <input wire:model="password" type="password" placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                                  placeholder-[#B8A080] outline-none transition-all
                                  {{ $errors->has('password') ? 'border-[#B83030]' : 'border-[#D4A017]/35 focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30' }}">
                    @error('password') <p class="mt-1 text-[#B83030] text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-[#3A1C05] text-xs font-semibold uppercase tracking-wide mb-1.5">ຢືນຢັນລະຫັດຜ່ານ</label>
                    <input wire:model="password_confirmation" type="password" placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-[#1A0A02]
                                  placeholder-[#B8A080] outline-none transition-all border-[#D4A017]/35
                                  focus:border-[#D4A017] focus:ring-1 focus:ring-[#D4A017]/30">
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-2.5 rounded-lg border border-[#D4A017]/30 text-sm text-[#5A3A1A]
                                   hover:bg-[#D4A017]/8 transition-colors font-medium">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-lg bg-[#1A0A02] text-[#E8C97A] text-sm font-semibold
                                   hover:bg-[#2A1205] transition-colors border border-[#D4A017]/30">
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
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.set('showDeleteModal', false)">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
        <div class="relative bg-[#FAF6EE] rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
             style="box-shadow: 0 20px 60px rgba(26,10,2,0.4)">
            <div class="h-1 bg-[#B83030]"></div>
            <div class="px-6 py-6 text-center">
                <div class="w-12 h-12 rounded-full bg-[#B83030]/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-[#B83030]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h3 class="text-[#1A0A02] font-semibold text-base mb-2">ຢືນຢັນການລຶບ</h3>
                <p class="text-[#9B7A5A] text-sm mb-6">ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຜູ້ໃຊ້ນີ້? ການດຳເນີນການນີ້ບໍ່ສາມາດຍ້ອນກັບໄດ້</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 py-2.5 rounded-lg border border-[#D4A017]/30 text-sm text-[#5A3A1A]
                                   hover:bg-[#D4A017]/8 transition-colors font-medium">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 py-2.5 rounded-lg bg-[#B83030] text-white text-sm font-semibold
                                   hover:bg-[#9A2020] transition-colors">
                        <span wire:loading.remove wire:target="delete">ລຶບ</span>
                        <span wire:loading wire:target="delete">ກຳລັງລຶບ...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
