<div>
    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຕາຕະລາງ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ໜ້າທີ່ຮັບຜິດຊອບ</h1>
            <p class="text-gray-400 text-sm mt-1">ຕາຕະລາງໜ້າທີ່ — ພຣະທີ່ມີໜ້າທີ່ຈະບໍ່ສາມາດໝາຍຂາດໄດ້</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            {{-- Report button --}}
            <a href="{{ route('duty-schedules.report', $filterType ? ['type' => $filterType] : []) }}"
               target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 bg-white text-slate-600 border border-gray-200 rounded-2xl text-sm font-medium hover:bg-gray-50 transition-colors touch-manipulation">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 7V4a1 1 0 011-1h8a1 1 0 011 1v3M5 13H3a1 1 0 01-1-1V9a1 1 0 011-1h14a1 1 0 011 1v3a1 1 0 01-1 1h-2M6 13v4h8v-4H6z"/>
                </svg>
                <span class="hidden sm:inline">ລາຍງານ PDF</span>
                <span class="sm:hidden">PDF</span>
            </a>

            {{-- Add duty button --}}
            <button wire:click="openCreate"
                    class="flex items-center gap-2 px-4 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 4v12M4 10h12"/>
                </svg>
                <span class="hidden sm:inline">ເພີ່ມໜ້າທີ່</span>
                <span class="sm:hidden">ເພີ່ມ</span>
            </button>
        </div>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-brand-green text-white rounded-3xl card-shadow p-5">
            <p class="text-[10px] font-medium text-white/70 uppercase tracking-widest mb-1">ໝຸນວຽນ</p>
            <p class="text-xs text-white/70 mb-2">ໜ້າທີ່ປະຈຳອາທິດ</p>
            <div class="flex items-baseline gap-1.5">
                <span class="text-xl sm:text-2xl font-bold tabular-nums leading-none">{{ $totalWeekly }}</span>
                <span class="text-xs text-white/70">ລາຍການ</span>
            </div>
        </div>
        <div class="bg-white rounded-3xl card-shadow border border-gray-50 p-5">
            <p class="text-[10px] font-medium text-orange-400 uppercase tracking-widest mb-1">ສະເພາະ</p>
            <p class="text-xs text-gray-400 mb-2">ໜ້າທີ່ວັນສະເພາະ</p>
            <div class="flex items-baseline gap-1.5">
                <span class="text-xl sm:text-2xl font-bold text-slate-800 tabular-nums leading-none">{{ $totalOnce }}</span>
                <span class="text-xs text-gray-400">ລາຍການ</span>
            </div>
        </div>
    </div>

    {{-- Notice banner --}}
    <div class="flex items-start gap-3 bg-brand-light-green rounded-2xl px-4 py-3 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="#216143" stroke-width="1.75" class="w-4 h-4 mt-0.5 shrink-0">
            <circle cx="10" cy="10" r="8"/>
            <path stroke-linecap="round" d="M10 6v4.5M10 13.5v.5"/>
        </svg>
        <p class="text-xs text-slate-600 leading-relaxed">
            ພຣະທີ່ຖືກມອບໝາຍໜ້າທີ່ (ທັງສະເພາະວັນ ແລະ ໜ້າທີ່ໝຸນວຽນ) <strong class="text-slate-800">ຈະບໍ່ສາມາດໝາຍຂາດໃນວັນນັ້ນ</strong> ໄດ້
        </p>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white card-shadow rounded-2xl p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative sm:flex-1 sm:min-w-52">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 text-gray-400">
                        <circle cx="8.5" cy="8.5" r="5.5"/>
                        <path stroke-linecap="round" d="M14 14l3 3"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="ຄົ້ນຫາຊື່ພຣະ ຫຼື ໜ້າທີ່..."
                       class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
            </div>
            <select wire:model.live="filterType"
                    class="bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                <option value="">ທຸກປະເພດ</option>
                <option value="weekly">ໝຸນວຽນ (ປະຈຳອາທິດ)</option>
                <option value="once">ສະເພາະວັນ</option>
            </select>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- GROUPED CARD VIEW                                   --}}
    {{-- ═══════════════════════════════════════════════════ --}}

    @php $hasAny = $weeklyGroups->isNotEmpty() || $onceGroups->isNotEmpty(); @endphp

    @if (!$hasAny)
        {{-- Empty state --}}
        <div class="bg-white rounded-3xl card-shadow flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#216143" stroke-width="1.5" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <p class="font-bold text-slate-800 mb-1">ຍັງບໍ່ມີໜ້າທີ່</p>
            <p class="text-gray-400 text-sm">ກົດ "ເພີ່ມໜ້າທີ່" ເພື່ອມອບໝາຍໜ້າທີ່</p>
        </div>
    @else

        {{-- ─── WEEKLY SECTION ────────────────────────────────── --}}
        @if ($weeklyGroups->isNotEmpty())
            <div class="mb-8">
                {{-- Section header --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center gap-2.5 shrink-0">
                        <div class="w-8 h-8 rounded-lg bg-brand-light-green flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="#216143" stroke-width="1.75" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 4v3M10 13v3M4 10h3M13 10h3M5.636 5.636l2.121 2.121M12.243 12.243l2.121 2.121M12.243 7.757l2.121-2.121M5.636 14.364l2.121-2.121"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 leading-none">ໝຸນວຽນ</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">ໜ້າທີ່ປະຈຳອາທິດ</p>
                        </div>
                    </div>
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full">
                        {{ $weeklyGroups->flatten()->count() }} ລາຍ
                    </span>
                </div>

                {{-- Day groups --}}
                @php
                    $dayColors = [
                        1 => ['pill_bg'=>'#3D2E00','text'=>'#F5D060','dot'=>'#F5D060','line'=>'rgba(245,208,96,0.35)','border'=>'rgba(245,208,96,0.55)','photo'=>'rgba(245,208,96,0.50)','badge_bg'=>'rgba(245,208,96,0.13)','badge_text'=>'#5A3D00'],
                        2 => ['pill_bg'=>'#3D0A1A','text'=>'#F0A0B0','dot'=>'#F0A0B0','line'=>'rgba(240,160,176,0.35)','border'=>'rgba(240,160,176,0.55)','photo'=>'rgba(240,160,176,0.50)','badge_bg'=>'rgba(240,160,176,0.13)','badge_text'=>'#6B0A22'],
                        3 => ['pill_bg'=>'#1A3A02','text'=>'#A8D87A','dot'=>'#A8D87A','line'=>'rgba(168,216,122,0.35)','border'=>'rgba(74,122,26,0.55)','photo'=>'rgba(74,122,26,0.50)','badge_bg'=>'rgba(74,122,26,0.13)','badge_text'=>'#1A4A02'],
                        4 => ['pill_bg'=>'#3D1A00','text'=>'#F5A060','dot'=>'#F5A060','line'=>'rgba(245,160,96,0.35)','border'=>'rgba(245,160,96,0.55)','photo'=>'rgba(245,160,96,0.50)','badge_bg'=>'rgba(245,160,96,0.13)','badge_text'=>'#6B2E00'],
                        5 => ['pill_bg'=>'#0A1A3D','text'=>'#90BCEE','dot'=>'#90BCEE','line'=>'rgba(144,188,238,0.35)','border'=>'rgba(144,188,238,0.55)','photo'=>'rgba(144,188,238,0.50)','badge_bg'=>'rgba(144,188,238,0.13)','badge_text'=>'#0A2A6B'],
                        6 => ['pill_bg'=>'#2A0A3D','text'=>'#C090E0','dot'=>'#C090E0','line'=>'rgba(192,144,224,0.35)','border'=>'rgba(192,144,224,0.55)','photo'=>'rgba(192,144,224,0.50)','badge_bg'=>'rgba(192,144,224,0.13)','badge_text'=>'#4A0A6B'],
                        7 => ['pill_bg'=>'#3D0A0A','text'=>'#EE9090','dot'=>'#EE9090','line'=>'rgba(238,144,144,0.35)','border'=>'rgba(238,144,144,0.55)','photo'=>'rgba(238,144,144,0.50)','badge_bg'=>'rgba(238,144,144,0.13)','badge_text'=>'#6B0A0A'],
                    ];
                @endphp
                <div class="space-y-6">
                    @foreach ($weeklyGroups as $dayNum => $dayDuties)
                        @php $dc = $dayColors[$dayNum] ?? $dayColors[3]; @endphp
                        <div>
                            {{-- Day pill header --}}
                            <div class="flex items-center gap-2.5 mb-3">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full shrink-0">
                                    <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                    <span class="text-xs font-bold text-gray-500">{{ $dayNames[$dayNum] }}</span>
                                </div>
                                <div class="flex-1 h-px bg-gray-200"></div>
                                <span class="text-[11px] font-medium text-gray-400">{{ $dayDuties->count() }} ລາຍ</span>
                            </div>

                            {{-- Cards grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 pl-2">
                                @foreach ($dayDuties as $duty)
                                    <div class="bg-white rounded-2xl card-shadow overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="p-4">
                                            <div class="flex items-start gap-3">
                                                <img src="{{ $duty->monk->photo_url }}" alt="{{ $duty->monk->full_name }}"
                                                     class="w-9 h-9 rounded-full object-cover shrink-0 border border-gray-200">
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-slate-800 text-sm leading-snug">{{ $duty->monk->full_name }}</p>
                                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $duty->monk->type_label }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-start gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-brand-light-green text-brand-green">
                                                    {{ $duty->duty_name }}
                                                </span>
                                                @if ($duty->description)
                                                    <p class="text-[11px] text-gray-400 leading-tight w-full mt-1">{{ $duty->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex border-t border-gray-100" style="min-height: 40px;">
                                            <button wire:click="openEdit({{ $duty->id }})"
                                                    class="flex-1 flex items-center justify-center gap-1.5 text-xs text-slate-500 hover:bg-gray-50 transition-colors touch-manipulation py-2.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                                                </svg>
                                                ແກ້ໄຂ
                                            </button>
                                            <div class="w-px self-stretch bg-gray-100"></div>
                                            <button wire:click="delete({{ $duty->id }})"
                                                    wire:confirm="ຢືນຢັນການລຶບໜ້າທີ່ນີ້?"
                                                    class="flex-1 flex items-center justify-center gap-1.5 text-xs text-red-500 hover:bg-red-50 transition-colors touch-manipulation py-2.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                                                </svg>
                                                ລຶບ
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Divider between sections (when both visible) --}}
        @if ($weeklyGroups->isNotEmpty() && $onceGroups->isNotEmpty())
            <div class="flex items-center gap-3 mb-7">
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>
        @endif

        {{-- ─── ONCE / SPECIFIC DATE SECTION ──────────────────── --}}
        @if ($onceGroups->isNotEmpty())
            <div>
                {{-- Section header --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center gap-2.5 shrink-0">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="#f97316" stroke-width="1.75" class="w-4 h-4">
                                <rect x="3" y="4" width="14" height="13" rx="2"/>
                                <path stroke-linecap="round" d="M3 9h14M7 4V2M13 4V2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 leading-none">ສະເພາະວັນ</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">ໜ້າທີ່ວັນສະເພາະ</p>
                        </div>
                    </div>
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full">
                        {{ $onceGroups->flatten()->count() }} ລາຍ
                    </span>
                </div>

                {{-- Date groups --}}
                <div class="space-y-6">
                    @foreach ($onceGroups as $dateKey => $dateDuties)
                        @php
                            $dateCarbon = \Carbon\Carbon::parse($dateKey);
                            $isToday    = $dateCarbon->isToday();
                            $isTomorrow = $dateCarbon->isTomorrow();
                            $isFuture   = $dateCarbon->isFuture();
                            $isPast     = !$isToday && !$isFuture;
                        @endphp
                        <div>
                            {{-- Date pill header --}}
                            <div class="flex items-center gap-2.5 mb-3">
                                @if ($isToday)
                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-red-500 rounded-full shrink-0">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                        <span class="text-xs font-bold text-white">{{ $dateCarbon->format('d/m/Y') }}</span>
                                        <span class="text-[10px] font-bold text-white/70 ml-0.5">ວັນນີ້</span>
                                    </div>
                                @elseif ($isTomorrow)
                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-brand-green-dark rounded-full shrink-0">
                                        <div class="w-1.5 h-1.5 rounded-full bg-brand-bright-green"></div>
                                        <span class="text-xs font-bold text-white">{{ $dateCarbon->format('d/m/Y') }}</span>
                                        <span class="text-[10px] font-medium text-white/70 ml-0.5">ມື້ອື່ນ</span>
                                    </div>
                                @elseif ($isFuture)
                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-brand-green-dark rounded-full shrink-0">
                                        <div class="w-1.5 h-1.5 rounded-full bg-brand-bright-green/60"></div>
                                        <span class="text-xs font-bold text-white">{{ $dateCarbon->format('d/m/Y') }}</span>
                                        <span class="text-[10px] font-medium text-white/60 ml-0.5">ກຳລັງຈະມາ</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full shrink-0">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                        <span class="text-xs font-bold text-gray-500">{{ $dateCarbon->format('d/m/Y') }}</span>
                                        <span class="text-[10px] font-medium text-gray-400 ml-0.5">ຜ່ານໄປ</span>
                                    </div>
                                @endif
                                <div class="flex-1 h-px bg-gray-200"></div>
                                <span class="text-[11px] text-gray-400 font-medium">{{ $dateDuties->count() }} ລາຍ</span>
                            </div>

                            {{-- Cards grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 pl-2 {{ $isPast ? 'opacity-60' : '' }}">
                                @foreach ($dateDuties as $duty)
                                    <div class="bg-white rounded-2xl card-shadow overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="p-4">
                                            <div class="flex items-start gap-3">
                                                <img src="{{ $duty->monk->photo_url }}" alt="{{ $duty->monk->full_name }}"
                                                     class="w-9 h-9 rounded-full object-cover shrink-0 border border-gray-200">
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-slate-800 text-sm leading-snug">{{ $duty->monk->full_name }}</p>
                                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $duty->monk->type_label }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-start gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-orange-50 text-orange-600">
                                                    {{ $duty->duty_name }}
                                                </span>
                                                @if ($duty->description)
                                                    <p class="text-[11px] text-gray-400 leading-tight w-full mt-1">{{ $duty->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex border-t border-gray-100" style="min-height: 40px;">
                                            <button wire:click="openEdit({{ $duty->id }})"
                                                    class="flex-1 flex items-center justify-center gap-1.5 text-xs text-slate-500 hover:bg-gray-50 transition-colors touch-manipulation py-2.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                                                </svg>
                                                ແກ້ໄຂ
                                            </button>
                                            <div class="w-px self-stretch bg-gray-100"></div>
                                            <button wire:click="delete({{ $duty->id }})"
                                                    wire:confirm="ຢືນຢັນການລຶບໜ້າທີ່ນີ້?"
                                                    class="flex-1 flex items-center justify-center gap-1.5 text-xs text-red-500 hover:bg-red-50 transition-colors touch-manipulation py-2.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                                                </svg>
                                                ລຶບ
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- Modal                                       --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         wire:click.self="$set('showModal', false)">

        <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden max-h-[95dvh] sm:max-h-[90vh] flex flex-col">

            {{-- Modal header --}}
            <div class="px-6 py-4 flex items-center justify-between shrink-0 border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ມອບໝາຍໜ້າທີ່' }}
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">ໜ້າທີ່ຮັບຜິດຊອບ</h2>
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

            {{-- Form --}}
            <form wire:submit="save" class="px-6 py-5 space-y-5 overflow-y-auto">

                {{-- Monk --}}
                <div>
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                        ພຣະສົງ / ສາມະເນນ <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="monk_id"
                            class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                   @error('monk_id') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        <option value="">— ເລືອກພຣະ —</option>
                        @foreach ($monks as $monk)
                            <option value="{{ $monk->id }}">{{ $monk->full_name }} ({{ $monk->type_label }})</option>
                        @endforeach
                    </select>
                    @error('monk_id')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Duty name --}}
                <div>
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                        ຊື່ໜ້າທີ່ <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="duty_name" type="text" placeholder="ເຊັ່ນ: ຮັບໃຊ້ອາຫານ, ທຳຄວາມສະອາດ, ອ່ານພຣະ..."
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('duty_name') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('duty_name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                {{-- Schedule type toggle --}}
                <div>
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                        ຮູບແບບໜ້າທີ່
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" wire:click="$set('schedule_type', 'once')"
                                class="flex items-center gap-2.5 px-4 py-3 rounded-xl border text-sm font-medium transition-all
                                       {{ $schedule_type === 'once'
                                           ? 'bg-orange-50 text-orange-600 border-orange-200'
                                           : 'bg-white text-slate-600 border-gray-200 hover:border-gray-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 shrink-0">
                                <rect x="3" y="4" width="14" height="13" rx="2"/>
                                <path stroke-linecap="round" d="M3 9h14M7 4V2M13 4V2"/>
                            </svg>
                            ສະເພາະວັນ
                        </button>
                        <button type="button" wire:click="$set('schedule_type', 'weekly')"
                                class="flex items-center gap-2.5 px-4 py-3 rounded-xl border text-sm font-medium transition-all
                                       {{ $schedule_type === 'weekly'
                                           ? 'bg-brand-light-green text-brand-green border-brand-green/30'
                                           : 'bg-white text-slate-600 border-gray-200 hover:border-gray-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 4v3M10 13v3M4 10h3M13 10h3M5.636 5.636l2.121 2.121M12.243 12.243l2.121 2.121M12.243 7.757l2.121-2.121M5.636 14.364l2.121-2.121"/>
                            </svg>
                            ໝຸນວຽນ
                        </button>
                    </div>
                </div>

                {{-- Conditional: date picker OR day-of-week --}}
                @if ($schedule_type === 'once')
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                            ວັນທີ <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="duty_date" type="date"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('duty_date') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('duty_date')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                @else
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                            ວັນໃນອາທິດ <span class="text-red-500">*</span>
                        </label>
                        @php
                            $pickerColors = [
                                1 => ['bg'=>'#3D2E00','text'=>'#F5D060','border'=>'rgba(245,208,96,0.55)'],
                                2 => ['bg'=>'#3D0A1A','text'=>'#F0A0B0','border'=>'rgba(240,160,176,0.55)'],
                                3 => ['bg'=>'#1A3A02','text'=>'#A8D87A','border'=>'rgba(74,122,26,0.55)'],
                                4 => ['bg'=>'#3D1A00','text'=>'#F5A060','border'=>'rgba(245,160,96,0.55)'],
                                5 => ['bg'=>'#0A1A3D','text'=>'#90BCEE','border'=>'rgba(144,188,238,0.55)'],
                                6 => ['bg'=>'#2A0A3D','text'=>'#C090E0','border'=>'rgba(192,144,224,0.55)'],
                                7 => ['bg'=>'#3D0A0A','text'=>'#EE9090','border'=>'rgba(238,144,144,0.55)'],
                            ];
                        @endphp
                        <div class="grid grid-cols-7 gap-1.5">
                            @foreach ([1=>'ຈ', 2=>'ອ', 3=>'ພ', 4=>'ພຫ', 5=>'ສ', 6=>'ເສ', 7=>'ທ'] as $num => $short)
                                @php $pc = $pickerColors[$num]; $isSelected = (int)$day_of_week === $num; @endphp
                                <button type="button"
                                        wire:click="$set('day_of_week', {{ $num }})"
                                        class="flex flex-col items-center justify-center py-2.5 rounded-xl border text-xs font-semibold transition-all
                                               {{ $isSelected
                                                   ? 'bg-brand-green text-white border-brand-green shadow-sm'
                                                   : 'bg-[#f8fafa] text-slate-600 border-gray-200 hover:border-gray-300' }}">
                                    <span class="text-[11px] font-bold">{{ $short }}</span>
                                    <span class="text-[9px] mt-0.5 opacity-70">{{ $num }}</span>
                                </button>
                            @endforeach
                        </div>
                        @if($day_of_week)
                            @php $selPc = $pickerColors[(int)$day_of_week] ?? $pickerColors[3]; @endphp
                            <p class="text-xs mt-2 font-medium text-brand-green">
                                ✓ ເລືອກ: {{ $dayNames[(int)$day_of_week] }} (ທຸກໆອາທິດ)
                            </p>
                        @endif
                        @error('day_of_week')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                @endif

                {{-- Description --}}
                <div>
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ລາຍລະອຽດ</label>
                    <textarea wire:model="description" rows="2" placeholder="ລາຍລະອຽດໜ້າທີ່..."
                              class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent resize-none transition-shadow"></textarea>
                </div>

                {{-- Submit row --}}
                <div class="flex justify-end gap-3 pb-1 pt-1">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors touch-manipulation">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="flex items-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation">
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
