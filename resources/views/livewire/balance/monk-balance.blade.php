<div>
    {{-- Page header --}}
    <div class="mb-5">
        <p class="text-[10px] font-medium text-[#D4A017]/70 uppercase tracking-[0.35em] mb-1">ການເງິນ</p>
        <h1 class="text-2xl font-bold text-[#1A0A02]">ຍອດຄ້າງຊຳລະ</h1>
        <p class="text-[#9B7A5A] text-sm mt-1">ລາຍຊື່ພຣະສົງ ແລະ ສາມະເນນ ທີ່ຍັງຄ້າງຄ່າປັບ</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">

        {{-- Total unpaid amount — spans full width on mobile, 1/3 on sm+ --}}
        <div class="col-span-2 sm:col-span-1 bg-[#FFFDF7] rounded-xl border border-[#8B1A1A]/20 p-4"
             style="border-left: 3px solid rgba(139,26,26,0.50);">
            <p class="text-[10px] font-medium text-[#8B1A1A]/60 uppercase tracking-[0.2em] mb-1">ຍອດລວມຄ້າງ</p>
            <p class="text-xs text-[#6B4A2A] mb-2">ຄ່າປັບທີ່ຍັງບໍ່ໄດ້ຊຳລະ</p>
            <div class="flex items-baseline gap-1.5 flex-wrap">
                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full border border-[#8B1A1A]/40 text-[#8B1A1A] text-[8px] font-bold shrink-0 leading-none select-none">₭</span>
                <span class="text-2xl sm:text-3xl font-bold text-[#8B1A1A] tabular-nums leading-none">{{ number_format($totalUnpaid) }}</span>
                <span class="text-xs text-[#9B7A5A]">ກີບ</span>
            </div>
        </div>

        {{-- Monks with debt --}}
        <div class="bg-[#FFFDF7] rounded-xl border border-[#D4A017]/20 p-4"
             style="border-left: 3px solid rgba(212,160,23,0.45);">
            <p class="text-[10px] font-medium text-[#D4A017]/60 uppercase tracking-[0.2em] mb-1">ຈຳນວນ</p>
            <p class="text-xs text-[#6B4A2A] mb-2">ພຣະ/ສາມະເນນ</p>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold text-[#1A0A02] tabular-nums leading-none">{{ $monksWithDebt }}</span>
                <span class="text-xs text-[#9B7A5A] ml-1">ອົງ</span>
            </div>
        </div>

        {{-- Unpaid absences count --}}
        <div class="bg-[#FFFDF7] rounded-xl border border-[#D4A017]/20 p-4"
             style="border-left: 3px solid rgba(212,160,23,0.45);">
            <p class="text-[10px] font-medium text-[#D4A017]/60 uppercase tracking-[0.2em] mb-1">ຄ້າງ</p>
            <p class="text-xs text-[#6B4A2A] mb-2">ຈຳນວນຄັ້ງທີ່ຂາດ</p>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold text-[#1A0A02] tabular-nums leading-none">{{ $unpaidCount }}</span>
                <span class="text-xs text-[#9B7A5A] ml-1">ຄັ້ງ</span>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-[#FFFDF7] border border-[#D4A017]/15 rounded-xl p-4 mb-5">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
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

            {{-- Type filter --}}
            <select wire:model.live="filterType"
                    class="bg-white border border-[#D4A017]/20 rounded-lg px-3 py-2.5 text-sm text-[#1A0A02] focus:outline-none focus:ring-2 focus:ring-[#D4A017]/40 focus:border-transparent transition-shadow sm:w-40">
                <option value="">ທຸກປະເພດ</option>
                <option value="monk">ພຣະສົງ</option>
                <option value="novice">ສາມະເນນ</option>
            </select>

            {{-- Only show debt toggle --}}
            <label class="flex items-center gap-2.5 cursor-pointer py-1 sm:py-0">
                <input wire:model.live="onlyDebt" type="checkbox" value="1" class="sr-only peer">
                <div class="relative w-9 h-5 rounded-full transition-colors duration-200
                            bg-[#D4A017]/25 peer-checked:bg-[#1A0A02]
                            after:content-[''] after:absolute after:top-0.5 after:left-0.5
                            after:bg-white after:rounded-full after:w-4 after:h-4
                            after:transition-transform after:duration-200
                            peer-checked:after:translate-x-4 shrink-0"></div>
                <span class="text-sm text-[#6B4A2A] whitespace-nowrap">ສະເພາະທີ່ຄ້າງ</span>
            </label>
        </div>
    </div>

    {{-- Monk balance list --}}
    <div class="space-y-3">
        @forelse ($monks as $monk)
            <div class="bg-[#FFFDF7] rounded-xl border border-[#D4A017]/15 overflow-hidden"
                 style="box-shadow: 0 1px 6px rgba(26,10,2,0.05);">

                {{-- Identity + amount row --}}
                <div class="px-4 pt-4 pb-4 flex items-start gap-4">

                    {{-- Avatar --}}
                    <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}"
                         class="w-12 h-12 rounded-full object-cover shrink-0 mt-0.5"
                         style="border: 2px solid rgba(212,160,23,0.45)">

                    {{-- Body --}}
                    <div class="flex-1 min-w-0">

                        {{-- Name + type badge --}}
                        <div class="flex items-start gap-2 flex-wrap">
                            <span class="font-semibold text-[#1A0A02] text-base leading-tight">{{ $monk->full_name }}</span>
                            <span class="shrink-0 mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-medium
                                {{ $monk->type === 'monk' ? 'bg-[#D4670D]/12 text-[#7A3200]' : 'bg-[#2D5A3D]/10 text-[#1A4A2A]' }}">
                                {{ $monk->type_label }}
                            </span>
                        </div>
                        <p class="text-xs text-[#9B7A5A] mt-0.5">
                            @if($monk->temple){{ $monk->temple }} · @endif{{ $monk->pansa }} ພັນສາ
                        </p>

                        {{-- THE LEDGER AMOUNT — the dominant visual element --}}
                        <div class="mt-3 pt-3 flex items-end justify-between gap-3"
                             style="border-top: 1px solid rgba(212,160,23,0.12);">

                            <div class="min-w-0">
                                <p class="text-[10px] font-medium text-[#8B1A1A]/55 uppercase tracking-[0.2em] mb-0.5 select-none">ຍອດຄ້າງຊຳລະ</p>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-[11px] font-semibold text-[#8B1A1A]/70 select-none">₭</span>
                                    <span class="text-[2rem] sm:text-[2.5rem] font-bold text-[#8B1A1A] tabular-nums leading-none">
                                        {{ $monk->unpaid_fine ? number_format($monk->unpaid_fine) : '0' }}
                                    </span>
                                    <span class="text-xs text-[#9B7A5A]">ກີບ</span>
                                </div>
                                <p class="text-[11px] text-[#9B7A5A] mt-1">
                                    ຄ້າງ <span class="font-medium text-[#6B4A2A]">{{ $monk->unpaid_count }}</span> ຄັ້ງ
                                    @if($monk->total_count > $monk->unpaid_count)
                                        &nbsp;·&nbsp; ຊຳລະແລ້ວ {{ $monk->total_count - $monk->unpaid_count }} ຄັ້ງ
                                    @endif
                                </p>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex flex-col items-end gap-2 shrink-0">
                                @if($monk->unpaid_count > 0)
                                    <button wire:click="markAllPaid({{ $monk->id }})"
                                            wire:confirm="ຢືນຢັນໝາຍຈ່າຍທັງໝົດ {{ $monk->full_name }}?"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-[#1A0A02] text-[#E8C97A] border border-[#D4A017]/30 rounded-lg text-xs font-medium hover:bg-[#2D1206] transition-colors touch-manipulation whitespace-nowrap">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 10.5l5 5 7-7"/>
                                        </svg>
                                        ຈ່າຍໝົດ
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#2D7A3A]/10 text-[#1A4A2A]">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 10.5l5 5 7-7"/>
                                        </svg>
                                        ຊຳລະແລ້ວ
                                    </span>
                                @endif

                                <button wire:click="toggleExpand({{ $monk->id }})"
                                        class="flex items-center gap-1 text-xs text-[#9B7A5A] hover:text-[#6B4A2A] transition-colors touch-manipulation">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75"
                                         class="w-3.5 h-3.5 transition-transform duration-200 {{ in_array($monk->id, $expanded) ? 'rotate-180' : '' }}">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 7.5l5 5 5-5"/>
                                    </svg>
                                    {{ in_array($monk->id, $expanded) ? 'ຫຍໍ້' : 'ລາຍລະອຽດ' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Expandable: individual unpaid absences --}}
                @if(in_array($monk->id, $expanded))
                    <div style="border-top: 1px solid rgba(212,160,23,0.15); background: #FAF6EE;">
                        @if(isset($expandedAbsences[$monk->id]) && $expandedAbsences[$monk->id]->isNotEmpty())
                            @foreach($expandedAbsences[$monk->id] as $absence)
                                <div class="flex items-center gap-3 px-4 py-3 touch-manipulation"
                                     style="{{ !$loop->last ? 'border-bottom: 1px solid rgba(212,160,23,0.08)' : '' }}">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-medium text-[#1A0A02] tabular-nums">{{ $absence->absent_date->format('d/m/Y') }}</span>
                                            <span class="text-[11px] text-[#9B7A5A] px-1.5 py-0.5 rounded bg-[#D4A017]/8">{{ $absence->fineRate->name }}</span>
                                        </div>
                                        @if($absence->reason)
                                            <p class="text-xs text-[#9B7A5A] mt-0.5 truncate">{{ $absence->reason }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <div class="text-right">
                                            <span class="font-bold text-[#8B1A1A] tabular-nums text-sm">{{ number_format($absence->fine_amount) }}</span>
                                            <span class="text-[11px] text-[#9B7A5A] ml-0.5">ກີບ</span>
                                        </div>
                                        <button wire:click="markPaid({{ $absence->id }})"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-[#7A3200] border border-[#D4A017]/20 hover:bg-[#D4A017]/10 transition-colors touch-manipulation whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3 shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10.5l5 5 7-7"/>
                                            </svg>
                                            ໝາຍຈ່າຍ
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-4 py-4 text-center">
                                <p class="text-sm text-[#2D7A3A]">ຊຳລະທັງໝົດແລ້ວ</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                @if($onlyDebt)
                    <div class="w-16 h-16 rounded-full bg-[#2D7A3A]/10 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#2D7A3A" stroke-width="1.5" class="w-8 h-8 opacity-70">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-[#1A0A02] mb-1">ບໍ່ມີຍອດຄ້າງ</p>
                    <p class="text-[#9B7A5A] text-sm">ພຣະສົງທຸກອົງຊຳລະຄ່າປັບຄົບຖ້ວນແລ້ວ</p>
                @else
                    <div class="w-16 h-16 rounded-full bg-[#D4A017]/10 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="1.5" class="w-8 h-8 opacity-60">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-[#1A0A02] mb-1">ບໍ່ພົບຂໍ້ມູນ</p>
                    <p class="text-[#9B7A5A] text-sm">ລອງປ່ຽນເງື່ອນໄຂການຄົ້ນຫາ</p>
                @endif
            </div>
        @endforelse
    </div>
</div>
