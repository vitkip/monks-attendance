<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[#D4A017]/70 text-[10px] tracking-[0.35em] uppercase font-medium mb-1">ທະບຽນ</p>
            <h1 class="text-xl sm:text-[22px] font-semibold text-[#1A0A02] tracking-wide leading-tight">ພຣະສົງ ແລະ ສາມະເນນ</h1>
            <p class="text-[#9B7A5A] text-sm mt-1">ຈັດການຂໍ້ມູນສະມາຊິກຂອງວັດທັງໝົດ</p>
        </div>
        <button wire:click="openCreate"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5
                       bg-[#1A0A02] text-[#E8C97A] rounded-lg text-sm font-medium
                       border border-[#D4A017]/30 hover:bg-[#2A1205] hover:border-[#D4A017]/60
                       transition-all duration-150 flex-shrink-0">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            ເພີ່ມສະມາຊິກ
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-[#FFFDF7] border border-[#D4A017]/20 rounded-xl p-4 mb-5 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#9B7A5A] pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text"
                   placeholder="ຄົ້ນຫາຊື່, ນາມສະກຸນ, ວັດ..."
                   class="w-full border border-[#D4A017]/25 rounded-lg pl-10 pr-4 py-2.5 text-sm
                          bg-white/60 text-[#1A0A02] placeholder-[#9B7A5A]
                          focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017]/50 transition">
        </div>
        <select wire:model.live="filterType"
                class="border border-[#D4A017]/25 rounded-lg px-4 py-2.5 text-sm
                       bg-white/60 text-[#1A0A02] sm:w-auto
                       focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 transition">
            <option value="">ທຸກປະເພດ</option>
            <option value="monk">ພຣະສົງ</option>
            <option value="novice">ສາມະເນນ</option>
        </select>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MOBILE: Card list (visible below md)                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="md:hidden space-y-3">
        @forelse ($monks as $monk)
            <div class="rounded-xl border border-[#D4A017]/20 overflow-hidden"
                 style="box-shadow: 0 1px 8px rgba(26,10,2,0.06)">

                {{-- Identity row --}}
                <div class="flex items-center gap-3 px-4 py-3.5" style="background: #FFFDF7">
                    <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}"
                         class="w-11 h-11 rounded-full object-cover flex-shrink-0"
                         style="border: 2px solid rgba(212,160,23,0.45)">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-[#1A0A02] text-sm leading-tight">{{ $monk->full_name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium flex-shrink-0
                                {{ $monk->type === 'monk'
                                    ? 'bg-[#D4670D]/12 text-[#7A3200]'
                                    : 'bg-[#2D5A3D]/10 text-[#1A4A2A]' }}">
                                {{ $monk->type_label }}
                            </span>
                        </div>
                        <p class="text-[#9B7A5A] text-xs mt-0.5 truncate">
                            {{ $monk->temple ?? '—' }}&nbsp;·&nbsp;{{ $monk->pansa }} ພັນສາ
                        </p>
                    </div>
                </div>

                {{-- Stats + actions strip --}}
                <div class="flex items-stretch border-t border-[#D4A017]/12" style="background: #FAF6EE">

                    {{-- Absences --}}
                    <div class="flex-1 flex flex-col items-center justify-center py-2.5
                                border-r border-[#D4A017]/12">
                        <span class="text-[9px] text-[#9B7A5A] uppercase tracking-wider mb-0.5">ຂາດ</span>
                        @if ($monk->absences_count > 0)
                            <span class="text-sm font-bold text-[#8B1A1A]">{{ $monk->absences_count }}</span>
                        @else
                            <span class="text-sm text-[#C4A882]">—</span>
                        @endif
                    </div>

                    {{-- Fine --}}
                    <div class="flex-1 flex flex-col items-center justify-center py-2.5
                                border-r border-[#D4A017]/12 px-2">
                        <span class="text-[9px] text-[#9B7A5A] uppercase tracking-wider mb-0.5">ຄ່າປັບ</span>
                        <span class="text-xs font-medium {{ ($monk->absences_sum_fine_amount ?? 0) > 0 ? 'text-[#8B1A1A]' : 'text-[#C4A882]' }}">
                            {{ ($monk->absences_sum_fine_amount ?? 0) > 0
                                ? number_format($monk->absences_sum_fine_amount)
                                : '—' }}
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-0.5 px-3">
                        <button wire:click="openEdit({{ $monk->id }})"
                                class="p-2.5 rounded-lg text-[#4A6FA5] hover:bg-[#4A6FA5]/10 transition-all"
                                aria-label="ແກ້ໄຂ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="confirmDelete({{ $monk->id }})"
                                class="p-2.5 rounded-lg text-[#8B1A1A]/55 hover:text-[#8B1A1A] hover:bg-[#8B1A1A]/8 transition-all"
                                aria-label="ລຶບ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-[#D4A017]/20 py-16 text-center"
                 style="background: #FAF6EE">
                <div class="text-4xl mb-3 select-none opacity-20 text-[#1A0A02]">☸</div>
                <p class="text-[#9B7A5A] text-sm">ຍັງບໍ່ມີຂໍ້ມູນພຣະສົງ/ສາມະເນນ</p>
                <p class="text-[#C4A882] text-xs mt-1">ກົດ "ເພີ່ມສະມາຊິກ" ເພື່ອເລີ່ມຕົ້ນ</p>
            </div>
        @endforelse

        @if ($monks->hasPages())
            <div class="pt-1">{{ $monks->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- DESKTOP: Full table (visible md+)                             --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="hidden md:block rounded-xl overflow-hidden border border-[#D4A017]/20"
         style="box-shadow: 0 1px 16px rgba(26,10,2,0.07)">
        <table class="w-full text-sm">
            <thead style="background: #1A0A02;">
                <tr>
                    <th class="px-4 py-3.5 text-left text-[#7A5A30] font-medium text-[10px] tracking-widest uppercase">#</th>
                    <th class="px-4 py-3.5 text-left text-[#E8C97A] font-medium text-[10px] tracking-widest uppercase">ຮູບ</th>
                    <th class="px-4 py-3.5 text-left text-[#E8C97A] font-medium text-[10px] tracking-widest uppercase">ຊື່-ນາມສະກຸນ</th>
                    <th class="px-4 py-3.5 text-left text-[#E8C97A] font-medium text-[10px] tracking-widest uppercase">ປະເພດ</th>
                    <th class="px-4 py-3.5 text-left text-[#E8C97A] font-medium text-[10px] tracking-widest uppercase hidden lg:table-cell">ພັນສາ</th>
                    <th class="px-4 py-3.5 text-left text-[#E8C97A] font-medium text-[10px] tracking-widest uppercase hidden lg:table-cell">ວັດ</th>
                    <th class="px-4 py-3.5 text-center text-[#E8C97A] font-medium text-[10px] tracking-widest uppercase">ຂາດ</th>
                    <th class="px-4 py-3.5 text-right text-[#E8C97A] font-medium text-[10px] tracking-widest uppercase">ຄ່າປັບ</th>
                    <th class="px-4 py-3.5 text-center text-[#7A5A30] font-medium text-[10px] tracking-widest uppercase">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($monks as $monk)
                    <tr class="border-t border-[#D4A017]/10 transition-colors duration-100 hover:bg-[#D4A017]/5"
                        style="background: {{ $loop->even ? '#FFFDF7' : '#FAF6EE' }}">
                        <td class="px-4 py-3.5 text-[#C4A882] text-xs">{{ $monk->id }}</td>
                        <td class="px-4 py-3.5">
                            <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}"
                                 class="w-9 h-9 rounded-full object-cover"
                                 style="border: 2px solid rgba(212,160,23,0.45)">
                        </td>
                        <td class="px-4 py-3.5 font-medium text-[#1A0A02]">{{ $monk->full_name }}</td>
                        <td class="px-4 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $monk->type === 'monk'
                                    ? 'bg-[#D4670D]/12 text-[#7A3200]'
                                    : 'bg-[#2D5A3D]/10 text-[#1A4A2A]' }}">
                                {{ $monk->type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[#6B4A2A] hidden lg:table-cell">{{ $monk->pansa }} ພັນສາ</td>
                        <td class="px-4 py-3.5 text-[#6B4A2A] hidden lg:table-cell">{{ $monk->temple ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if ($monk->absences_count > 0)
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                             text-xs font-bold bg-[#8B1A1A]/10 text-[#8B1A1A]">
                                    {{ $monk->absences_count }}
                                </span>
                            @else
                                <span class="text-[#C4A882] text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right font-medium
                            {{ ($monk->absences_sum_fine_amount ?? 0) > 0 ? 'text-[#8B1A1A]' : 'text-[#C4A882]' }}">
                            {{ ($monk->absences_sum_fine_amount ?? 0) > 0
                                ? number_format($monk->absences_sum_fine_amount) . ' ກີບ'
                                : '—' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex justify-center gap-1">
                                <button wire:click="openEdit({{ $monk->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                               text-[#4A6FA5] hover:text-[#2A4F85] hover:bg-[#4A6FA5]/10 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    ແກ້ໄຂ
                                </button>
                                <button wire:click="confirmDelete({{ $monk->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                               text-[#8B1A1A]/60 hover:text-[#8B1A1A] hover:bg-[#8B1A1A]/8 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    ລຶບ
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr style="background: #FAF6EE">
                        <td colspan="9" class="px-4 py-16 text-center">
                            <div class="text-4xl mb-3 select-none opacity-20 text-[#1A0A02]">☸</div>
                            <p class="text-[#9B7A5A] text-sm">ຍັງບໍ່ມີຂໍ້ມູນພຣະສົງ/ສາມະເນນ</p>
                            <p class="text-[#C4A882] text-xs mt-1">ກົດ "ເພີ່ມສະມາຊິກ" ເພື່ອເລີ່ມຕົ້ນ</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-[#D4A017]/15 bg-[#FAF6EE]">
            {{ $monks->links() }}
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
    <div class="fixed inset-0 flex items-end sm:items-center justify-center z-50 p-0 sm:p-4"
         style="background: rgba(26,10,2,0.65); backdrop-filter: blur(4px)">
        <div class="w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-2xl sm:rounded-2xl overflow-hidden"
             style="box-shadow: 0 -8px 40px rgba(26,10,2,0.3), 0 24px 64px rgba(26,10,2,0.45)">

            {{-- Drag handle (mobile only) --}}
            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0" style="background: #1A0A02">
                <div class="w-10 h-1 rounded-full" style="background: rgba(212,160,23,0.3)"></div>
            </div>

            {{-- Modal header --}}
            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between"
                 style="background: #1A0A02; border-bottom: 1px solid rgba(212,160,23,0.25)">
                <h2 class="text-sm font-semibold text-[#E8C97A] tracking-wide">
                    {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນສະມາຊິກ' : 'ເພີ່ມສະມາຊິກໃໝ່' }}
                </h2>
                <button wire:click="$set('showModal', false)"
                        class="w-7 h-7 flex items-center justify-center rounded-full text-[#7A5A30]
                               hover:text-[#E8C97A] hover:bg-white/10 transition-all text-sm leading-none">
                    ✕
                </button>
            </div>

            {{-- Modal body (scrollable) --}}
            <form wire:submit="save"
                  class="flex-1 overflow-y-auto px-6 py-5 space-y-4 bg-[#FAF6EE]"
                  style="-webkit-overflow-scrolling: touch">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-medium text-[#6B4A2A] mb-1.5 uppercase tracking-widest">
                            ຊື່ <span class="text-[#8B1A1A]">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="ຊື່"
                               class="w-full border border-[#D4A017]/25 rounded-lg px-3 py-2.5 text-sm
                                      bg-white/70 text-[#1A0A02] placeholder-[#9B7A5A]
                                      focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017]/50 transition
                                      @error('name') border-[#8B1A1A]/50 @enderror">
                        @error('name') <p class="text-[#8B1A1A] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-[#6B4A2A] mb-1.5 uppercase tracking-widest">
                            ນາມສະກຸນ <span class="text-[#8B1A1A]">*</span>
                        </label>
                        <input wire:model="surname" type="text" placeholder="ນາມສະກຸນ"
                               class="w-full border border-[#D4A017]/25 rounded-lg px-3 py-2.5 text-sm
                                      bg-white/70 text-[#1A0A02] placeholder-[#9B7A5A]
                                      focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-[#D4A017]/50 transition
                                      @error('surname') border-[#8B1A1A]/50 @enderror">
                        @error('surname') <p class="text-[#8B1A1A] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-medium text-[#6B4A2A] mb-1.5 uppercase tracking-widest">ປະເພດ</label>
                        <select wire:model="type"
                                class="w-full border border-[#D4A017]/25 rounded-lg px-3 py-2.5 text-sm
                                       bg-white/70 text-[#1A0A02]
                                       focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 transition">
                            <option value="monk">ພຣະສົງ</option>
                            <option value="novice">ສາມະເນນ</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-[#6B4A2A] mb-1.5 uppercase tracking-widest">ພັນສາ</label>
                        <input wire:model="pansa" type="number" min="0" placeholder="0"
                               class="w-full border border-[#D4A017]/25 rounded-lg px-3 py-2.5 text-sm
                                      bg-white/70 text-[#1A0A02]
                                      focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-[#6B4A2A] mb-1.5 uppercase tracking-widest">ວັດ</label>
                    <input wire:model="temple" type="text" placeholder="ຊື່ວັດ"
                           class="w-full border border-[#D4A017]/25 rounded-lg px-3 py-2.5 text-sm
                                  bg-white/70 text-[#1A0A02] placeholder-[#9B7A5A]
                                  focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 transition">
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-[#6B4A2A] mb-1.5 uppercase tracking-widest">ຮູບພາບ</label>
                    <input wire:model="photo" type="file" accept="image/*"
                           class="w-full border border-[#D4A017]/25 rounded-lg px-3 py-2.5 text-sm
                                  bg-white/70 text-[#6B4A2A]
                                  file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0
                                  file:text-xs file:font-medium file:bg-[#1A0A02] file:text-[#E8C97A] file:cursor-pointer">
                    @error('photo') <p class="text-[#8B1A1A] text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sticky footer actions --}}
                <div class="flex justify-end gap-3 pt-1 pb-safe">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 border border-[#D4A017]/30 rounded-lg text-sm text-[#6B4A2A]
                                   hover:bg-[#D4A017]/8 hover:border-[#D4A017]/50 transition-all">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-[#1A0A02] text-[#E8C97A] rounded-lg text-sm font-medium
                                   border border-[#D4A017]/30 hover:bg-[#2A1205] hover:border-[#D4A017]/60 transition-all">
                        ບັນທຶກ
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirm Modal --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 flex items-end sm:items-center justify-center z-50 p-0 sm:p-4"
         style="background: rgba(26,10,2,0.65); backdrop-filter: blur(4px)">
        <div class="w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl overflow-hidden"
             style="box-shadow: 0 -8px 40px rgba(26,10,2,0.3), 0 24px 64px rgba(26,10,2,0.45)">

            {{-- Drag handle (mobile only) --}}
            <div class="sm:hidden flex justify-center pt-3 pb-1" style="background: #1A0A02">
                <div class="w-10 h-1 rounded-full" style="background: rgba(139,26,26,0.4)"></div>
            </div>

            {{-- Modal header --}}
            <div class="px-6 py-4 flex items-center justify-between"
                 style="background: #1A0A02; border-bottom: 1px solid rgba(139,26,26,0.35)">
                <h3 class="text-sm font-semibold text-[#E8A0A0] tracking-wide">ຢືນຢັນການລຶບ</h3>
                <button wire:click="$set('showDeleteModal', false)"
                        class="w-7 h-7 flex items-center justify-center rounded-full text-[#7A5A30]
                               hover:text-[#E8A0A0] hover:bg-white/10 transition-all text-sm leading-none">✕</button>
            </div>

            {{-- Modal body --}}
            <div class="px-6 py-6 text-center bg-[#FAF6EE] pb-safe">
                <div class="w-12 h-12 rounded-full bg-[#8B1A1A]/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-[#8B1A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <p class="text-[#1A0A02] text-sm font-medium mb-1">ລຶບຂໍ້ມູນສະມາຊິກ</p>
                <p class="text-[#9B7A5A] text-xs mb-6">ຂໍ້ມູນການຂາດລາທີ່ກ່ຽວຂ້ອງທັງໝົດ<br>ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 sm:flex-none px-5 py-2.5 border border-[#D4A017]/30 rounded-lg
                                   text-sm text-[#6B4A2A] hover:bg-[#D4A017]/8 transition-all">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-[#8B1A1A] text-white rounded-lg
                                   text-sm font-medium hover:bg-[#6B1010] transition-all">
                        ລຶບຂໍ້ມູນ
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
