<div>
    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4 mb-5">
        <div>
            <p class="text-[10px] font-medium text-[#D4A017]/70 uppercase tracking-[0.35em] mb-1">ບັນທຶກ</p>
            <h1 class="text-2xl font-bold text-[#1A0A02]">ກວດສອບການຂາດລາ</h1>
            <p class="text-[#9B7A5A] text-sm mt-1">ບັນທຶກ ແລະ ຄຳນວນຄ່າປັບໃໝ</p>
        </div>
        <button wire:click="openCreate"
                class="shrink-0 flex items-center gap-2 px-4 py-2.5 bg-[#1A0A02] text-[#E8C97A] border border-[#D4A017]/30 rounded-lg text-sm font-medium hover:bg-[#2D1206] transition-colors touch-manipulation">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 4v12M4 10h12"/>
            </svg>
            <span class="hidden sm:inline">ບັນທຶກການຂາດ</span>
            <span class="sm:hidden">ບັນທຶກ</span>
        </button>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 gap-4 mb-5">
        <div class="bg-[#FFFDF7] rounded-xl border border-[#D4A017]/20 p-4"
             style="border-left: 3px solid rgba(212,160,23,0.55);">
            <p class="text-[10px] font-medium text-[#D4A017]/60 uppercase tracking-[0.2em] mb-1">ທັງໝົດ</p>
            <p class="text-xs text-[#6B4A2A] mb-2">ຄ່າປັບທັງໝົດ</p>
            <div class="flex items-baseline gap-1.5 flex-wrap">
                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full border border-[#D4A017]/45 text-[#D4A017] text-[8px] font-bold shrink-0 leading-none select-none">₭</span>
                <span class="text-xl sm:text-2xl font-bold text-[#1A0A02] tabular-nums leading-none">{{ number_format($totalFine) }}</span>
                <span class="text-xs text-[#9B7A5A]">ກີບ</span>
            </div>
        </div>
        <div class="bg-[#FFFDF7] rounded-xl border border-[#8B1A1A]/20 p-4"
             style="border-left: 3px solid rgba(139,26,26,0.40);">
            <p class="text-[10px] font-medium text-[#8B1A1A]/55 uppercase tracking-[0.2em] mb-1">ລໍຖ້າ</p>
            <p class="text-xs text-[#6B4A2A] mb-2">ຍັງບໍ່ໄດ້ຈ່າຍ</p>
            <div class="flex items-baseline gap-1.5 flex-wrap">
                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full border border-[#8B1A1A]/35 text-[#8B1A1A] text-[8px] font-bold shrink-0 leading-none select-none">₭</span>
                <span class="text-xl sm:text-2xl font-bold text-[#8B1A1A] tabular-nums leading-none">{{ number_format($unpaidFine) }}</span>
                <span class="text-xs text-[#9B7A5A]">ກີບ</span>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-[#FFFDF7] border border-[#D4A017]/15 rounded-xl p-4 mb-5">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative sm:flex-1 sm:min-w-52">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 text-[#C4A882]">
                        <circle cx="8.5" cy="8.5" r="5.5"/>
                        <path stroke-linecap="round" d="M14 14l3 3"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="ຄົ້ນຫາຊື່ພຣະ..."
                       class="w-full bg-white border border-[#D4A017]/20 rounded-lg pl-9 pr-3 py-2.5 text-sm text-[#1A0A02] placeholder:text-[#C4A882] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-transparent transition-shadow">
            </div>
            <div class="grid grid-cols-2 gap-3 sm:contents">
                <input wire:model.live="filterMonth" type="month"
                       class="bg-white border border-[#D4A017]/20 rounded-lg px-3 py-2.5 text-sm text-[#1A0A02] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-transparent transition-shadow">
                <select wire:model.live="filterPaid"
                        class="bg-white border border-[#D4A017]/20 rounded-lg px-3 py-2.5 text-sm text-[#1A0A02] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-transparent transition-shadow">
                    <option value="">ທຸກສະຖານະ</option>
                    <option value="0">ຍັງບໍ່ຈ່າຍ</option>
                    <option value="1">ຈ່າຍແລ້ວ</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- GROUPED BY DATE                                     --}}
    {{-- ═══════════════════════════════════════════════════ --}}

    @if ($absenceGroups->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-full bg-[#D4A017]/10 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="1.5" class="w-7 h-7 opacity-50">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="font-semibold text-[#1A0A02] mb-1">ຍັງບໍ່ມີຂໍ້ມູນການຂາດ</p>
            <p class="text-[#9B7A5A] text-sm">ກົດ "ບັນທຶກ" ເພື່ອເພີ່ມຂໍ້ມູນ</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($absenceGroups as $dateKey => $dateAbsences)
                @php
                    $dateCarbon = \Carbon\Carbon::parse($dateKey);
                    $isToday    = $dateCarbon->isToday();
                    $isTomorrow = $dateCarbon->isTomorrow();
                    $isFuture   = $dateCarbon->isFuture();
                @endphp
                <div>
                    {{-- Date pill header --}}
                    <div class="flex items-center gap-2.5 mb-3">
                        @if ($isToday)
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#8B1A1A] rounded-lg shrink-0">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#FAF6EE]"></div>
                                <span class="text-xs font-bold text-[#FAF6EE]">{{ $dateCarbon->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-bold text-[#FAF6EE]/70 ml-0.5">ວັນນີ້</span>
                            </div>
                        @elseif ($isTomorrow)
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#2D1206] rounded-lg shrink-0">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#E8C97A]"></div>
                                <span class="text-xs font-bold text-[#E8C97A]">{{ $dateCarbon->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-medium text-[#E8C97A]/70 ml-0.5">ມື້ອື່ນ</span>
                            </div>
                        @elseif ($isFuture)
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#2D1206] rounded-lg shrink-0">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#E8C97A]/60"></div>
                                <span class="text-xs font-bold text-[#E8C97A]">{{ $dateCarbon->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-medium text-[#E8C97A]/60 ml-0.5">ກຳລັງຈະມາ</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#1A0A02]/10 rounded-lg shrink-0">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#9B7A5A]"></div>
                                <span class="text-xs font-bold text-[#9B7A5A]">{{ $dateCarbon->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        <div class="flex-1 h-px" style="background: linear-gradient(to right, {{ $isToday ? 'rgba(139,26,26,0.35)' : 'rgba(212,160,23,0.20)' }}, transparent)"></div>
                        <span class="text-[11px] text-[#9B7A5A] font-medium px-2 py-1 bg-[#D4A017]/8 rounded-full">
                            {{ $dateAbsences->count() }} ອົງ
                        </span>
                    </div>

                    {{-- ─── MOBILE: card list ─── --}}
                    <div class="md:hidden space-y-3">
                        @foreach ($dateAbsences as $absence)
                            <div class="bg-[#FFFDF7] rounded-xl border border-[#D4A017]/20 overflow-hidden"
                                 style="border-left: 3px solid rgba(212,160,23,0.30);">

                                {{-- Top row: name + fine type --}}
                                <div class="px-4 pt-4 pb-3 flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-[#1A0A02] text-base truncate">{{ $absence->monk->full_name }}</p>
                                        <p class="text-xs text-[#9B7A5A] mt-0.5">{{ $absence->monk->type_label }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-[11px] text-[#9B7A5A]">{{ $absence->fineRate->name }}</p>
                                    </div>
                                </div>

                                {{-- Middle row: reason + fine amount --}}
                                <div class="px-4 pb-3 pt-3 flex items-center justify-between gap-3"
                                     style="border-top: 1px solid rgba(212,160,23,0.10);">
                                    <p class="text-sm text-[#6B4A2A] flex-1 min-w-0 truncate">
                                        {{ $absence->reason ?: 'ບໍ່ລະບຸເຫດຜົນ' }}
                                    </p>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full border border-[#8B1A1A]/35 text-[#8B1A1A] text-[8px] font-bold leading-none select-none">₭</span>
                                        <span class="font-bold text-[#8B1A1A] tabular-nums text-sm">{{ number_format($absence->fine_amount) }}</span>
                                        <span class="text-xs text-[#9B7A5A]">ກີບ</span>
                                    </div>
                                </div>

                                {{-- Bottom row: status + edit + delete --}}
                                <div class="flex" style="border-top: 1px solid rgba(212,160,23,0.15); min-height: 48px;">
                                    @if ($absence->is_paid)
                                        <div class="flex-1 flex items-center justify-center gap-1.5 text-sm text-[#2D7A3A]">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10.5l5 5 7-7"/>
                                            </svg>
                                            ຈ່າຍແລ້ວ
                                        </div>
                                    @else
                                        <button wire:click="markPaid({{ $absence->id }})"
                                                class="flex-1 flex items-center justify-center gap-1.5 text-sm text-[#7A3200] hover:bg-[#D4A017]/10 transition-colors touch-manipulation">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 shrink-0">
                                                <circle cx="10" cy="10" r="7.5"/>
                                                <path stroke-linecap="round" d="M10 6.5V10l2.5 2"/>
                                            </svg>
                                            ໝາຍຈ່າຍ
                                        </button>
                                    @endif
                                    <div class="w-px self-stretch" style="background: rgba(212,160,23,0.15);"></div>
                                    <button wire:click="openEdit({{ $absence->id }})"
                                            class="w-12 flex items-center justify-center text-[#6B4A2A] hover:bg-[#D4A017]/8 transition-colors touch-manipulation">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                                        </svg>
                                    </button>
                                    <div class="w-px self-stretch" style="background: rgba(212,160,23,0.15);"></div>
                                    <button wire:click="delete({{ $absence->id }})"
                                            wire:confirm="ຢືນຢັນການລຶບຂໍ້ມູນນີ້?"
                                            class="w-12 flex items-center justify-center text-[#8B1A1A] hover:bg-[#8B1A1A]/6 transition-colors touch-manipulation">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ─── DESKTOP: table ─── --}}
                    <div class="hidden md:block rounded-xl overflow-hidden border border-[#D4A017]/15">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="background: #1A0A02;">
                                    <th class="px-4 py-3.5 text-left w-12" style="color: #7A5A30; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">#</th>
                                    <th class="px-4 py-3.5 text-left" style="color: #E8C97A; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">ຊື່ພຣະສົງ / ສາມະເນນ</th>
                                    <th class="px-4 py-3.5 text-left" style="color: #E8C97A; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">ເຫດຜົນ</th>
                                    <th class="px-4 py-3.5 text-left" style="color: #E8C97A; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">ປະເພດ</th>
                                    <th class="px-4 py-3.5 text-right" style="color: #E8C97A; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">ຄ່າປັບ</th>
                                    <th class="px-4 py-3.5 text-center" style="color: #E8C97A; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">ສະຖານະ</th>
                                    <th class="px-4 py-3.5 text-center w-20" style="color: #7A5A30; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">ຈັດການ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dateAbsences as $absence)
                                    <tr class="{{ $loop->even ? 'bg-[#FFFDF7]' : 'bg-[#FAF6EE]' }} hover:bg-[#D4A017]/5 transition-colors"
                                        style="border-bottom: 1px solid rgba(212,160,23,0.08);">
                                        <td class="px-4 py-3.5 text-xs text-[#C4A882] tabular-nums">{{ $absence->id }}</td>
                                        <td class="px-4 py-3.5">
                                            <p class="font-semibold text-[#1A0A02]">{{ $absence->monk->full_name }}</p>
                                            <p class="text-xs text-[#9B7A5A] mt-0.5">{{ $absence->monk->type_label }}</p>
                                        </td>
                                        <td class="px-4 py-3.5 text-[#9B7A5A]">{{ $absence->reason ?? '—' }}</td>
                                        <td class="px-4 py-3.5 text-[#9B7A5A]">{{ $absence->fineRate->name }}</td>
                                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                            <span class="font-bold text-[#8B1A1A] tabular-nums">{{ number_format($absence->fine_amount) }}</span>
                                            <span class="text-xs text-[#9B7A5A] ml-0.5">ກີບ</span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            @if ($absence->is_paid)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-[#2D7A3A]/10 text-[#1A4A2A]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3 shrink-0">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 10.5l5 5 7-7"/>
                                                    </svg>
                                                    ຈ່າຍແລ້ວ
                                                </span>
                                            @else
                                                <button wire:click="markPaid({{ $absence->id }})"
                                                        title="ກົດເພື່ອໝາຍວ່າຈ່າຍແລ້ວ"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-[#D4670D]/10 text-[#7A3200] hover:bg-[#D4670D]/20 transition-colors touch-manipulation">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3 h-3 shrink-0">
                                                        <circle cx="10" cy="10" r="7.5"/>
                                                        <path stroke-linecap="round" d="M10 6.5V10l2.5 2"/>
                                                    </svg>
                                                    ໝາຍຈ່າຍ
                                                </button>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center justify-center gap-1">
                                                <button wire:click="openEdit({{ $absence->id }})"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-[#6B4A2A] hover:bg-[#D4A017]/10 transition-colors touch-manipulation">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                                                    </svg>
                                                </button>
                                                <button wire:click="delete({{ $absence->id }})"
                                                        wire:confirm="ຢືນຢັນການລຶບຂໍ້ມູນນີ້?"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-[#8B1A1A] hover:bg-[#8B1A1A]/8 transition-colors touch-manipulation">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- Modal: bottom-sheet on mobile, centered sm+ --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
         style="background: rgba(26,10,2,0.65); backdrop-filter: blur(4px);"
         wire:click.self="$set('showModal', false)">

        <div class="bg-[#FAF6EE] w-full sm:max-w-lg rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden max-h-[95dvh] sm:max-h-[90vh] flex flex-col">

            {{-- Modal header --}}
            <div class="bg-[#1A0A02] px-6 py-4 flex items-center justify-between shrink-0"
                 style="border-bottom: 1px solid rgba(212,160,23,0.25);">
                <div>
                    <p class="text-[10px] font-medium text-[#D4A017]/60 uppercase tracking-[0.35em]">
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ບັນທຶກໃໝ່' }}
                    </p>
                    <h2 class="text-base font-semibold text-[#E8C97A] mt-0.5">ການຂາດລາ</h2>
                </div>
                <button wire:click="$set('showModal', false)"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-[#7A5A30] hover:text-[#E8C97A] hover:bg-white/8 transition-colors touch-manipulation">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            {{-- Drag handle (mobile only) --}}
            <div class="sm:hidden flex justify-center pt-3 shrink-0">
                <div class="w-10 h-1 rounded-full bg-[#D4A017]/20"></div>
            </div>

            {{-- Form --}}
            <form wire:submit="save" class="px-6 py-5 space-y-5 overflow-y-auto">

                {{-- Monk --}}
                <div>
                    @if ($editId)
                        <label class="block text-[10px] font-medium text-[#6B4A2A] uppercase tracking-widest mb-2">
                            ພຣະສົງ / ສາມະເນນ <span class="text-[#8B1A1A]">*</span>
                        </label>
                        <select wire:model="monk_id"
                                class="w-full bg-[#FFFDF7] border rounded-lg px-3 py-3 text-sm text-[#1A0A02] focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                       @error('monk_id') border-[#8B1A1A]/50 focus:ring-[#8B1A1A]/30 @else border-[#D4A017]/25 focus:ring-[#D4A017]/40 @enderror">
                            <option value="">— ເລືອກພຣະ —</option>
                            @foreach ($monks as $monk)
                                <option value="{{ $monk->id }}">{{ $monk->full_name }} ({{ $monk->type_label }})</option>
                            @endforeach
                        </select>
                        @error('monk_id')<p class="text-[#8B1A1A] text-xs mt-1.5">{{ $message }}</p>@enderror
                    @else
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-[10px] font-medium text-[#6B4A2A] uppercase tracking-widest">
                                ພຣະສົງ / ສາມະເນນ <span class="text-[#8B1A1A]">*</span>
                                <span class="normal-case font-normal text-[#9B7A5A]">(ເລືອກໄດ້ຫຼາຍອົງ)</span>
                            </label>
                            <div class="flex items-center gap-2 text-[11px]">
                                <button type="button" wire:click="$set('monk_ids', {{ json_encode($monks->pluck('id')->values()) }})"
                                        class="text-[#7A3200] hover:underline touch-manipulation">ເລືອກທັງໝົດ</button>
                                <span class="text-[#D4A017]/40">|</span>
                                <button type="button" wire:click="$set('monk_ids', [])"
                                        class="text-[#8B1A1A] hover:underline touch-manipulation">ລ້າງ</button>
                            </div>
                        </div>
                        <div class="max-h-64 overflow-y-auto bg-[#FFFDF7] border rounded-lg divide-y divide-[#D4A017]/10
                                    @error('monk_ids') border-[#8B1A1A]/50 @else border-[#D4A017]/25 @enderror">

                            {{-- Once (specific-date) duty groups --}}
                            @foreach ($onceMonkGroups as $dateKey => $duties)
                                <div class="px-3 py-1.5 bg-[#D4A017]/8 text-[10px] font-bold text-[#6B3A00] uppercase tracking-wide sticky top-0">
                                    {{ \Carbon\Carbon::parse($dateKey)->format('d/m/Y') }} · ສະເພາະວັນ
                                </div>
                                @foreach ($duties as $duty)
                                    <label class="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-[#D4A017]/5 transition-colors touch-manipulation">
                                        <input type="checkbox" wire:model="monk_ids" value="{{ $duty->monk->id }}"
                                               wire:key="monk-once-{{ $duty->id }}"
                                               class="w-4 h-4 rounded border-[#D4A017]/40 text-[#1A0A02] focus:ring-[#D4A017]/40 shrink-0">
                                        <span class="text-sm text-[#1A0A02]">{{ $duty->monk->full_name }}</span>
                                        <span class="text-[10px] text-[#9B7A5A] ml-auto shrink-0 truncate max-w-[35%]">{{ $duty->duty_name }}</span>
                                    </label>
                                @endforeach
                            @endforeach

                            {{-- Weekly (recurring) duty groups --}}
                            @foreach ($weeklyMonkGroups as $dayNum => $duties)
                                <div class="px-3 py-1.5 bg-[#1A3A02]/8 text-[10px] font-bold text-[#1A4A02] uppercase tracking-wide sticky top-0">
                                    {{ $dayNames[$dayNum] }} · ໝຸນວຽນ
                                </div>
                                @foreach ($duties as $duty)
                                    <label class="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-[#D4A017]/5 transition-colors touch-manipulation">
                                        <input type="checkbox" wire:model="monk_ids" value="{{ $duty->monk->id }}"
                                               wire:key="monk-weekly-{{ $duty->id }}"
                                               class="w-4 h-4 rounded border-[#D4A017]/40 text-[#1A0A02] focus:ring-[#D4A017]/40 shrink-0">
                                        <span class="text-sm text-[#1A0A02]">{{ $duty->monk->full_name }}</span>
                                        <span class="text-[10px] text-[#9B7A5A] ml-auto shrink-0 truncate max-w-[35%]">{{ $duty->duty_name }}</span>
                                    </label>
                                @endforeach
                            @endforeach

                            {{-- Monks with no duty assignment --}}
                            @if ($monksWithoutDuty->isNotEmpty())
                                <div class="px-3 py-1.5 bg-[#1A0A02]/5 text-[10px] font-bold text-[#6B4A2A] uppercase tracking-wide sticky top-0">
                                    ບໍ່ມີໜ້າທີ່ຮັບຜິດຊອບ
                                </div>
                                @foreach ($monksWithoutDuty as $monk)
                                    <label class="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-[#D4A017]/5 transition-colors touch-manipulation">
                                        <input type="checkbox" wire:model="monk_ids" value="{{ $monk->id }}"
                                               wire:key="monk-none-{{ $monk->id }}"
                                               class="w-4 h-4 rounded border-[#D4A017]/40 text-[#1A0A02] focus:ring-[#D4A017]/40 shrink-0">
                                        <span class="text-sm text-[#1A0A02]">{{ $monk->full_name }}</span>
                                        <span class="text-xs text-[#9B7A5A] ml-auto shrink-0">{{ $monk->type_label }}</span>
                                    </label>
                                @endforeach
                            @endif

                            @if ($onceMonkGroups->isEmpty() && $weeklyMonkGroups->isEmpty() && $monksWithoutDuty->isEmpty())
                                <p class="px-3 py-3 text-sm text-[#9B7A5A]">ບໍ່ມີພຣະສົງ/ສາມະເນນ</p>
                            @endif
                        </div>
                        @error('monk_ids')<p class="text-[#8B1A1A] text-xs mt-1.5">{{ $message }}</p>@enderror
                        @if (!empty($monk_ids))
                            <p class="text-xs text-[#9B7A5A] mt-1.5">ເລືອກແລ້ວ {{ count($monk_ids) }} ອົງ</p>
                        @endif
                    @endif
                </div>

                {{-- Date + Fine rate (stacked on mobile, 2-col on sm+) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-medium text-[#6B4A2A] uppercase tracking-widest mb-2">
                            ວັນທີຂາດ <span class="text-[#8B1A1A]">*</span>
                        </label>
                        <input wire:model="absent_date" type="date"
                               class="w-full bg-[#FFFDF7] border rounded-lg px-3 py-3 text-sm text-[#1A0A02] focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('absent_date') border-[#8B1A1A]/50 focus:ring-[#8B1A1A]/30 @else border-[#D4A017]/25 focus:ring-[#D4A017]/40 @enderror">
                        @error('absent_date')<p class="text-[#8B1A1A] text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-[#6B4A2A] uppercase tracking-widest mb-2">
                            ປະເພດຄ່າປັບ <span class="text-[#8B1A1A]">*</span>
                        </label>
                        <select wire:model.live="fine_rate_id"
                                class="w-full bg-[#FFFDF7] border rounded-lg px-3 py-3 text-sm text-[#1A0A02] focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                       @error('fine_rate_id') border-[#8B1A1A]/50 focus:ring-[#8B1A1A]/30 @else border-[#D4A017]/25 focus:ring-[#D4A017]/40 @enderror">
                            <option value="">— ເລືອກ —</option>
                            @foreach ($fineRates as $rate)
                                <option value="{{ $rate->id }}">{{ $rate->name }} ({{ number_format($rate->amount) }} ₭)</option>
                            @endforeach
                        </select>
                        @error('fine_rate_id')<p class="text-[#8B1A1A] text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Amount + Is paid (stacked on mobile, 2-col on sm+) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-medium text-[#6B4A2A] uppercase tracking-widest mb-2">
                            ຈຳນວນເງິນ <span class="text-[10px] normal-case font-normal text-[#9B7A5A]">(ກີບ)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full border border-[#D4A017]/40 text-[#D4A017] text-[8px] font-bold leading-none select-none">₭</span>
                            </div>
                            <input wire:model="fine_amount" type="number" min="0" step="1000"
                                   class="w-full bg-[#FFFDF7] border border-[#D4A017]/25 rounded-lg pl-9 pr-3 py-3 text-sm text-[#1A0A02] placeholder:text-[#C4A882] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-transparent tabular-nums transition-shadow">
                        </div>
                    </div>
                    <div class="flex sm:items-end sm:pb-0.5">
                        <label class="flex items-center gap-3 cursor-pointer w-full py-2 sm:py-0">
                            <input wire:model="is_paid" type="checkbox" value="1" class="sr-only peer">
                            <div class="relative w-10 h-6 rounded-full transition-colors duration-200
                                        bg-[#D4A017]/20 peer-checked:bg-[#2D7A3A]/75
                                        after:content-[''] after:absolute after:top-1 after:left-1
                                        after:bg-white after:rounded-full after:w-4 after:h-4
                                        after:transition-transform after:duration-200
                                        peer-checked:after:translate-x-4 shrink-0"></div>
                            <span class="text-sm text-[#6B4A2A]">ຈ່າຍແລ້ວ</span>
                        </label>
                    </div>
                </div>

                {{-- Reason --}}
                <div>
                    <label class="block text-[10px] font-medium text-[#6B4A2A] uppercase tracking-widest mb-2">ເຫດຜົນ</label>
                    <input wire:model="reason" type="text" placeholder="ເຫດຜົນທີ່ຂາດ..."
                           class="w-full bg-[#FFFDF7] border border-[#D4A017]/25 rounded-lg px-3 py-3 text-sm text-[#1A0A02] placeholder:text-[#C4A882] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-transparent transition-shadow">
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-[10px] font-medium text-[#6B4A2A] uppercase tracking-widest mb-2">ໝາຍເຫດ</label>
                    <textarea wire:model="note" rows="2" placeholder="ໝາຍເຫດ..."
                              class="w-full bg-[#FFFDF7] border border-[#D4A017]/25 rounded-lg px-3 py-3 text-sm text-[#1A0A02] placeholder:text-[#C4A882] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-transparent resize-none transition-shadow"></textarea>
                </div>

                {{-- Submit row --}}
                <div class="flex justify-end gap-3 pb-1 pt-1">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 border border-[#D4A017]/25 rounded-lg text-sm text-[#6B4A2A] hover:bg-[#D4A017]/8 transition-colors touch-manipulation">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="flex items-center gap-2 px-5 py-2.5 bg-[#1A0A02] text-[#E8C97A] border border-[#D4A017]/30 rounded-lg text-sm font-medium hover:bg-[#2D1206] transition-colors touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 3H7L3 7v10a1 1 0 001 1h13a1 1 0 001-1V4a1 1 0 00-1-1zm-3 14v-6H6v6"/>
                        </svg>
                        ບັນທຶກ
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
