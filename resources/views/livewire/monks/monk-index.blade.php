<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ທະບຽນ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ພຣະສົງ ແລະ ສາມະເນນ</h1>
            <p class="text-gray-400 text-sm mt-1">ຈັດການຂໍ້ມູນສະມາຊິກຂອງວັດທັງໝົດ</p>
        </div>
        <div class="w-full sm:w-auto flex items-center gap-2.5">
            <a href="{{ route('monks.public.index') }}" target="_blank" rel="noopener"
               class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5
                      bg-white border border-gray-200 text-slate-600 rounded-2xl text-sm font-semibold
                      hover:bg-gray-50 transition flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                ເປີດໜ້າ Frontend
            </a>
            <button wire:click="openCreate"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5
                           bg-brand-green text-white rounded-2xl text-sm font-semibold
                           shadow-lg shadow-brand-green/20 hover:bg-opacity-90
                           transition flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                ເພີ່ມສະມາຊິກ
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white card-shadow rounded-2xl p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text"
                   placeholder="ຄົ້ນຫາຊື່, ນາມສະກຸນ, ວັດ..."
                   class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm
                          text-slate-800 placeholder:text-gray-400
                          focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
        </div>
        <select wire:model.live="filterType"
                class="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                       text-slate-800 sm:w-auto
                       focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
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
            <div class="bg-white rounded-2xl card-shadow overflow-hidden">

                {{-- Identity row --}}
                <div class="flex items-center gap-3 px-4 py-3.5">
                    <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}"
                         class="w-11 h-11 rounded-full object-cover flex-shrink-0 ring-2 ring-brand-green/30">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-800 text-sm leading-tight">{{ $monk->full_name }}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0
                                {{ $monk->type === 'monk'
                                    ? 'bg-orange-50 text-orange-600'
                                    : 'bg-teal-50 text-teal-600' }}">
                                {{ $monk->type_label }}
                            </span>
                        </div>
                        <p class="text-gray-400 text-xs mt-0.5 truncate">
                            {{ $monk->temple ?? '—' }}&nbsp;·&nbsp;{{ $monk->pansa }} ພັນສາ&nbsp;·&nbsp;ອາຍຸ {{ $monk->age ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Stats + actions strip --}}
                <div class="flex items-stretch border-t border-gray-100">

                    {{-- Absences --}}
                    <div class="flex-1 flex flex-col items-center justify-center py-2.5
                                border-r border-gray-100">
                        <span class="text-[9px] text-gray-400 uppercase tracking-wider mb-0.5">ຂາດ</span>
                        @if ($monk->absences_count > 0)
                            <span class="text-sm font-bold text-red-500">{{ $monk->absences_count }}</span>
                        @else
                            <span class="text-sm text-gray-300">—</span>
                        @endif
                    </div>

                    {{-- Fine --}}
                    <div class="flex-1 flex flex-col items-center justify-center py-2.5
                                border-r border-gray-100 px-2">
                        <span class="text-[9px] text-gray-400 uppercase tracking-wider mb-0.5">ຄ່າປັບ</span>
                        <span class="text-xs font-medium {{ ($monk->absences_sum_fine_amount ?? 0) > 0 ? 'text-red-500' : 'text-gray-300' }}">
                            {{ ($monk->absences_sum_fine_amount ?? 0) > 0
                                ? number_format($monk->absences_sum_fine_amount)
                                : '—' }}
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-0.5 px-3">
                        <button wire:click="openEdit({{ $monk->id }})"
                                class="p-2.5 rounded-lg text-slate-500 hover:bg-gray-100 transition-all"
                                aria-label="ແກ້ໄຂ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="confirmDelete({{ $monk->id }})"
                                class="p-2.5 rounded-lg text-red-500 hover:bg-red-50 transition-all"
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
            <div class="bg-white rounded-3xl card-shadow py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl text-brand-green select-none">☸</span>
                </div>
                <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີຂໍ້ມູນພຣະສົງ/ສາມະເນນ</p>
                <p class="text-gray-400 text-sm">ກົດ "ເພີ່ມສະມາຊິກ" ເພື່ອເລີ່ມຕົ້ນ</p>
            </div>
        @endforelse

        @if ($monks->hasPages())
            <div class="pt-1">{{ $monks->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- DESKTOP: Full table (visible md+)                             --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">#</th>
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຮູບ</th>
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຊື່-ນາມສະກຸນ</th>
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ປະເພດ</th>
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ພັນສາ</th>
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ອາຍຸ</th>
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ວັດ</th>
                    <th class="px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຂາດ</th>
                    <th class="px-4 py-3.5 text-right text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຄ່າປັບ</th>
                    <th class="px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($monks as $monk)
                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-xs text-gray-300 tabular-nums">{{ $monk->id }}</td>
                        <td class="px-4 py-3.5">
                            <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}"
                                 class="w-9 h-9 rounded-full object-cover ring-2 ring-brand-green/30">
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-800">{{ $monk->full_name }}</td>
                        <td class="px-4 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $monk->type === 'monk'
                                    ? 'bg-orange-50 text-orange-600'
                                    : 'bg-teal-50 text-teal-600' }}">
                                {{ $monk->type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 hidden lg:table-cell">{{ $monk->pansa }} ພັນສາ</td>
                        <td class="px-4 py-3.5 text-slate-600 hidden lg:table-cell">{{ $monk->age ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-slate-600 hidden lg:table-cell">{{ $monk->temple ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if ($monk->absences_count > 0)
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                             text-xs font-bold bg-red-50 text-red-500">
                                    {{ $monk->absences_count }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right font-medium
                            {{ ($monk->absences_sum_fine_amount ?? 0) > 0 ? 'text-red-500' : 'text-gray-300' }}">
                            {{ ($monk->absences_sum_fine_amount ?? 0) > 0
                                ? number_format($monk->absences_sum_fine_amount) . ' ກີບ'
                                : '—' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex justify-center gap-1">
                                <button wire:click="openEdit({{ $monk->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                               text-slate-500 hover:bg-gray-100 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    ແກ້ໄຂ
                                </button>
                                <button wire:click="confirmDelete({{ $monk->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                               text-red-500 hover:bg-red-50 transition-all">
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
                    <tr>
                        <td colspan="10" class="px-4 py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl text-brand-green select-none">☸</span>
                            </div>
                            <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີຂໍ້ມູນພຣະສົງ/ສາມະເນນ</p>
                            <p class="text-gray-400 text-sm">ກົດ "ເພີ່ມສະມາຊິກ" ເພື່ອເລີ່ມຕົ້ນ</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            {{ $monks->links() }}
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-5xl flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            {{-- Drag handle (mobile only) --}}
            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            {{-- Modal header --}}
            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່' }}
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນສະມາຊິກ' : 'ເພີ່ມສະມາຊິກໃໝ່' }}
                    </h2>
                </div>
                <button wire:click="$set('showModal', false)"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                               hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            {{-- Modal body (scrollable) --}}
            <form wire:submit="save"
                  class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                            ຊື່ <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="ຊື່"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                      text-slate-800 placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('name') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                            ນາມສະກຸນ <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="surname" type="text" placeholder="ນາມສະກຸນ"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                      text-slate-800 placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('surname') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('surname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ປະເພດ</label>
                        <select wire:model.live="type"
                                class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                       text-slate-800
                                       focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                            <option value="monk">ພຣະສົງ</option>
                            <option value="novice">ສາມະເນນ</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                            ວັນເດືອນປີເກີດ
                        </label>
                        <input wire:model.live="birth_date" type="date"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                      text-slate-800
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('birth_date') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('birth_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                            ວັນເດືອນປີບວດ
                        </label>
                        <input wire:model.live="ordination_date" type="date"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                      text-slate-800
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('ordination_date') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('ordination_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1 bg-brand-light-green rounded-xl px-3 py-2.5">
                            <p class="text-[9px] font-medium text-brand-green/70 uppercase tracking-widest mb-0.5">ອາຍຸ</p>
                            <p class="text-sm font-bold text-brand-green">
                                {{ $birth_date ? \Carbon\Carbon::parse($birth_date)->diffInYears(now()) . ' ປີ' : '—' }}
                            </p>
                        </div>
                        <div class="flex-1 bg-gray-100 rounded-xl px-3 py-2.5">
                            <p class="text-[9px] font-medium text-gray-400 uppercase tracking-widest mb-0.5">ພັນສາ (ອັດຕະໂນມັດ)</p>
                            <p class="text-sm font-bold text-slate-700">
                                {{ \App\Models\Monk::calculatePansa($ordination_date ?: null) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ວັດ</label>
                    <input wire:model="temple" type="text" placeholder="ຊື່ວັດ"
                           class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                  text-slate-800 placeholder:text-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຮູບພາບ</label>
                    <input wire:model="photo" type="file" accept="image/*"
                           class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                  text-slate-600
                                  file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold file:bg-brand-green file:text-white file:cursor-pointer">
                    @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sticky footer actions --}}
                <div class="flex justify-end gap-3 pt-1 pb-safe">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600
                                   hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold
                                   shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition">
                        ບັນທຶກ
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirm Modal --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            {{-- Drag handle (mobile only) --}}
            <div class="sm:hidden flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            {{-- Modal header --}}
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <h3 class="text-lg font-bold text-slate-800">ຢືນຢັນການລຶບ</h3>
                <button wire:click="$set('showDeleteModal', false)"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                               hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="px-6 py-6 text-center pb-safe">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <p class="text-slate-800 text-sm font-bold mb-1">ລຶບຂໍ້ມູນສະມາຊິກ</p>
                <p class="text-gray-400 text-xs mb-6">ຂໍ້ມູນການຂາດລາທີ່ກ່ຽວຂ້ອງທັງໝົດ<br>ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl
                                   text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl
                                   text-sm font-semibold hover:bg-red-600 transition-colors">
                        ລຶບຂໍ້ມູນ
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
