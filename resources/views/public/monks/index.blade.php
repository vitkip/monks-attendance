<x-layouts.public :title="'ພຣະສົງ ແລະ ສາມະເນນ'" :description="'ຂໍ້ມູນພຣະສົງ ແລະ ສາມະເນນ ພາຍໃນວັດປ່າໜອງບົວທອງໃຕ້'">

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-brand-green-dark">
        <span class="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none" aria-hidden="true">☸</span>

        <div class="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                <span aria-hidden="true">☸</span> ທະບຽນວັດ
            </span>

            <h1 class="text-white text-3xl sm:text-4xl font-bold leading-tight">ພຣະສົງ ແລະ ສາມະເນນ</h1>
            <p class="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                ລາຍຊື່ພຣະສົງ ແລະ ສາມະເນນທັງໝົດພາຍໃນວັດ
            </p>

            <div class="flex flex-wrap items-center gap-3 mt-7">
                <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                    <span class="text-lg font-bold text-white tabular-nums leading-none">{{ $totalMonks }}</span>
                    <span class="text-xs text-white/60">ອົງ · ພຣະສົງ</span>
                </div>
                <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                    <span class="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{{ $totalNovices }}</span>
                    <span class="text-xs text-white/60">ອົງ · ສາມະເນນ</span>
                </div>
                <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                    <span class="text-lg font-bold text-purple-300 tabular-nums leading-none">{{ $totalNuns }}</span>
                    <span class="text-xs text-white/60">ຄົນ · ແມ່ຂາວ</span>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        {{-- Filter tabs --}}
        <nav aria-label="ປະເພດສະມາຊິກ"
             class="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5">
            <div class="relative">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                    <a href="{{ route('monks.public.index') }}"
                       class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                              {{ ! $type ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                        ທັງໝົດ
                    </a>
                    <a href="{{ route('monks.public.index', ['type' => 'monk']) }}"
                       class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                              {{ $type === 'monk' ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                        ພຣະສົງ
                    </a>
                    <a href="{{ route('monks.public.index', ['type' => 'novice']) }}"
                       class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                              {{ $type === 'novice' ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                        ສາມະເນນ
                    </a>
                    <a href="{{ route('monks.public.index', ['type' => 'nun']) }}"
                       class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                              {{ $type === 'nun' ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                        ແມ່ຂາວ
                    </a>
                </div>
                <div class="sm:hidden pointer-events-none absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-[#faf8f2] to-transparent" aria-hidden="true"></div>
            </div>
        </nav>

        @if ($monkGroup->isEmpty() && $noviceGroup->isEmpty() && $nunGroup->isEmpty())
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($monkGroup as $monk)
                            <div class="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden p-4 flex items-start gap-3 transition-shadow duration-300 hover:shadow-lg">
                                <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}" loading="lazy"
                                     class="w-12 h-12 rounded-full object-cover shrink-0 border border-gray-200">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 text-sm leading-snug">{{ $monk->full_name }}</p>
                                    @if ($monk->temple)
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $monk->temple }}</p>
                                    @endif
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
                <div class="mb-10">
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($noviceGroup as $monk)
                            <div class="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden p-4 flex items-start gap-3 transition-shadow duration-300 hover:shadow-lg">
                                <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}" loading="lazy"
                                     class="w-12 h-12 rounded-full object-cover shrink-0 border border-gray-200">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 text-sm leading-snug">{{ $monk->full_name }}</p>
                                    @if ($monk->temple)
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $monk->temple }}</p>
                                    @endif
                                    <span class="inline-flex items-center mt-2 px-2.5 py-1 rounded-full text-[11px] font-medium bg-orange-50 text-orange-600">
                                        ພັນສາ {{ $monk->pansa }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ─── NUNS SECTION ──────────────────── --}}
            @if ($nunGroup->isNotEmpty())
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex items-center gap-2.5 shrink-0">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                                <span class="text-purple-500 text-sm">☸</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 leading-none">ແມ່ຂາວ</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Nuns</p>
                            </div>
                        </div>
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full">
                            {{ $nunGroup->count() }} ຄົນ
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($nunGroup as $monk)
                            <div class="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden p-4 flex items-start gap-3 transition-shadow duration-300 hover:shadow-lg">
                                <img src="{{ $monk->photo_url }}" alt="{{ $monk->full_name }}" loading="lazy"
                                     class="w-12 h-12 rounded-full object-cover shrink-0 border border-gray-200">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 text-sm leading-snug">{{ $monk->full_name }}</p>
                                    @if ($monk->temple)
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $monk->temple }}</p>
                                    @endif
                                    <span class="inline-flex items-center mt-2 px-2.5 py-1 rounded-full text-[11px] font-medium bg-purple-50 text-purple-600">
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
