<x-layouts.public :title="'ພຣະສົງ ແລະ ສາມະເນນ'">

    <div class="max-w-5xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        {{-- Page header --}}
        <div class="mb-8">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລາຍຊື່</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ພຣະສົງ ແລະ ສາມະເນນ</h1>
            <p class="text-gray-400 text-sm mt-1">ລາຍຊື່ພຣະສົງ ແລະ ສາມະເນນທັງໝົດພາຍໃນວັດ</p>
        </div>

        {{-- Stats cards --}}
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-brand-green text-white rounded-3xl card-shadow p-5">
                <p class="text-[10px] font-medium text-white/70 uppercase tracking-widest mb-1">ພຣະສົງ</p>
                <p class="text-xs text-white/70 mb-2">ຈຳນວນພຣະສົງທັງໝົດ</p>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl sm:text-2xl font-bold tabular-nums leading-none">{{ $totalMonks }}</span>
                    <span class="text-xs text-white/70">ອົງ</span>
                </div>
            </div>
            <div class="bg-white rounded-3xl card-shadow border border-gray-50 p-5">
                <p class="text-[10px] font-medium text-orange-400 uppercase tracking-widest mb-1">ສາມະເນນ</p>
                <p class="text-xs text-gray-400 mb-2">ຈຳນວນສາມະເນນທັງໝົດ</p>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl sm:text-2xl font-bold text-slate-800 tabular-nums leading-none">{{ $totalNovices }}</span>
                    <span class="text-xs text-gray-400">ອົງ</span>
                </div>
            </div>
        </div>

        {{-- Filter tabs --}}
        <div class="flex flex-wrap gap-2 mb-10">
            <a href="{{ route('monks.public.index') }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors
                      {{ ! $type ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                ທັງໝົດ
            </a>
            <a href="{{ route('monks.public.index', ['type' => 'monk']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors
                      {{ $type === 'monk' ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                ພຣະສົງ
            </a>
            <a href="{{ route('monks.public.index', ['type' => 'novice']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors
                      {{ $type === 'novice' ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                ສາມະເນນ
            </a>
        </div>

        @if ($monkGroup->isEmpty() && $noviceGroup->isEmpty())
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center text-center py-24">
                <span class="text-5xl text-brand-green/30 mb-4">☸</span>
                <p class="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂໍ້ມູນ</p>
                <p class="text-slate-400 text-sm">ກະລຸນາກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
            </div>
        @else

            {{-- ─── MONKS SECTION ──────────────────── --}}
            @if ($monkGroup->isNotEmpty())
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex items-center gap-2.5 shrink-0">
                            <div class="w-8 h-8 rounded-lg bg-brand-light-green flex items-center justify-center">
                                <span class="text-brand-green text-sm">☸</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 leading-none">ພຣະສົງ</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Monks</p>
                            </div>
                        </div>
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full">
                            {{ $monkGroup->count() }} ອົງ
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($monkGroup as $monk)
                            <div class="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden p-4 flex items-start gap-3 hover:shadow-md transition-shadow">
                                <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}"
                                     class="w-12 h-12 rounded-full object-cover shrink-0 border border-gray-200">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 text-sm leading-snug">{{ $monk->full_name }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $monk->temple ?: 'ບໍ່ລະບຸວັດ' }}</p>
                                    <span class="inline-flex items-center mt-2 px-2.5 py-1 rounded-full text-[11px] font-medium bg-brand-light-green text-brand-green">
                                        ພັນສາ {{ $monk->pansa }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ─── NOVICES SECTION ──────────────────── --}}
            @if ($noviceGroup->isNotEmpty())
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex items-center gap-2.5 shrink-0">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                                <span class="text-orange-500 text-sm">☸</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 leading-none">ສາມະເນນ</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Novices</p>
                            </div>
                        </div>
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full">
                            {{ $noviceGroup->count() }} ອົງ
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($noviceGroup as $monk)
                            <div class="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden p-4 flex items-start gap-3 hover:shadow-md transition-shadow">
                                <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}"
                                     class="w-12 h-12 rounded-full object-cover shrink-0 border border-gray-200">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 text-sm leading-snug">{{ $monk->full_name }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $monk->temple ?: 'ບໍ່ລະບຸວັດ' }}</p>
                                    <span class="inline-flex items-center mt-2 px-2.5 py-1 rounded-full text-[11px] font-medium bg-orange-50 text-orange-600">
                                        ພັນສາ {{ $monk->pansa }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        @endif

    </div>

</x-layouts.public>
