<x-layouts.public :title="'ໂຄງການກໍ່ສ້າງ'" :description="'ຕິດຕາມໂຄງການກໍ່ສ້າງ ແລະ ຄວາມຄືບໜ້າພາຍໃນວັດປ່າໜອງບົວທອງໃຕ້'">

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-brand-green-dark">
        <span
            class="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none"
            aria-hidden="true">🏗️</span>

        <div class="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                <span aria-hidden="true">🏗️</span> ຄວາມໂປ່ງໃສການກໍ່ສ້າງ
            </span>

            <h1 class="text-white text-3xl sm:text-4xl font-bold leading-tight">ຮ່ວມສ້າງບຸນ ກໍ່ສ້າງວັດ</h1>
            <p class="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                ຕິດຕາມຄວາມຄືບໜ້າ ທຶນບຸນ ແລະ ລາຍຈ່າຍຂອງແຕ່ລະໂຄງການກໍ່ສ້າງຂອງວັດ ເປີດເຜີຍໃຫ້ຍາດໂຍມ ແລະ ຜູ້ມີສັດທາຮ່ວມຕິດຕາມໄດ້
            </p>

            <div class="flex flex-wrap items-center gap-3 mt-7">
                <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                    <span class="text-lg font-bold text-white tabular-nums leading-none">{{ number_format($totalIncomeAll) }}</span>
                    <span class="text-xs text-white/60">ກີບ · ທຶນບຸນທີ່ໄດ້ຮັບທັງໝົດ</span>
                </div>
                <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                    <span class="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{{ $ongoingCount }}</span>
                    <span class="text-xs text-white/60">ໂຄງການ · ກຳລັງດຳເນີນການ</span>
                </div>
            </div>

            {{-- Featured project — the flagship fundraising board --}}
            @if ($featured)
                <div class="mt-9 bg-white/[0.04] rounded-2xl px-5 sm:px-6 py-6">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            @if ($featured->image_url)
                                <img src="{{ $featured->image_url }}" alt="{{ $featured->name }}"
                                     class="w-12 h-12 rounded-xl object-cover shrink-0 border border-white/10">
                            @endif
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">ໂຄງການທີ່ກຳລັງລະດົມທຶນ</p>
                                <p class="text-white font-bold truncate">{{ $featured->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('construction-projects.public.show', $featured) }}"
                           class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-brand-bright-green hover:text-white transition-colors">
                            ເບິ່ງລາຍລະອຽດ
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 4l6 6-6 6" />
                            </svg>
                        </a>
                    </div>

                    <div class="relative h-3.5 rounded-full bg-white/10 mb-2">
                        <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-amber-400 to-brand-bright-green"
                             style="width: {{ $featured->progress_percent }}%"></div>
                        <div class="absolute inset-y-0 left-1/4 w-px bg-black/10"></div>
                        <div class="absolute inset-y-0 left-1/2 w-px bg-black/10"></div>
                        <div class="absolute inset-y-0 left-3/4 w-px bg-black/10"></div>
                        <div class="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-white border-2 border-brand-bright-green shadow-sm"
                             style="left: {{ $featured->progress_percent }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-white/70 font-semibold tabular-nums">{{ number_format($featured->total_income) }} ກີບ</span>
                        <span class="text-brand-bright-green font-bold tabular-nums">{{ $featured->progress_percent }}%</span>
                        <span class="text-white/40 tabular-nums">ເປົ້າ {{ number_format($featured->target_amount) }} ກີບ</span>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        {{-- Status filter --}}
        <nav aria-label="ເລືອກສະຖານະ"
            class="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                <a href="{{ route('construction-projects.public.index') }}"
                    class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                          {{ $status === '' ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                    ທັງໝົດ
                </a>
                <a href="{{ route('construction-projects.public.index', ['status' => 'ongoing']) }}"
                    class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                          {{ $status === 'ongoing' ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                    ກຳລັງດຳເນີນການ
                </a>
                <a href="{{ route('construction-projects.public.index', ['status' => 'completed']) }}"
                    class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                          {{ $status === 'completed' ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                    ສຳເລັດແລ້ວ
                </a>
            </div>
        </nav>

        @if ($projects->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-24">
                <span class="text-5xl text-brand-green/30 mb-4">🏗️</span>
                <p class="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີໂຄງການໃນໝວດນີ້</p>
                <p class="text-slate-400 text-sm">ກະລຸນາເລືອກໝວດອື່ນ ຫຼື ກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($projects as $project)
                    @php
                        $incomeSum = (float) ($project->income_sum ?? 0);
                        $expenseSum = (float) ($project->expense_sum ?? 0);
                        $percent = ($project->target_amount && (float) $project->target_amount > 0)
                            ? min(100, round($incomeSum / (float) $project->target_amount * 100, 1))
                            : null;
                    @endphp
                    <a href="{{ route('construction-projects.public.show', $project) }}"
                       class="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col
                              transition-all duration-300 hover:-translate-y-1 hover:shadow-xl
                              focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                        @if ($project->image_url)
                            <div class="relative overflow-hidden">
                                <img src="{{ $project->image_url }}" alt="{{ $project->name }}"
                                     class="w-full aspect-[16/10] object-cover transition-transform duration-300 group-hover:scale-105">
                                <span class="absolute top-3 left-3 inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold backdrop-blur-sm
                                    {{ $project->status === 'completed' ? 'bg-brand-green/90 text-white' : ($project->status === 'paused' ? 'bg-white/90 text-gray-600' : 'bg-amber-500/90 text-white') }}">
                                    {{ \App\Models\ConstructionProject::statuses()[$project->status] ?? $project->status }}
                                </span>
                            </div>
                        @else
                            <div class="h-1.5 w-full {{ $project->status === 'completed' ? 'bg-brand-green' : ($project->status === 'paused' ? 'bg-gray-300' : 'bg-gradient-to-r from-amber-400 to-brand-green-mid') }}"></div>
                        @endif
                        <div class="px-5 pt-4 pb-5 flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @unless ($project->image_url)
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold
                                        {{ $project->status === 'completed' ? 'bg-brand-light-green text-brand-green' : ($project->status === 'paused' ? 'bg-gray-100 text-gray-500' : 'bg-amber-50 text-amber-600') }}">
                                        {{ \App\Models\ConstructionProject::statuses()[$project->status] ?? $project->status }}
                                    </span>
                                @endunless
                                @if ($project->start_date)
                                    <span class="text-gray-300 text-[11px] truncate">ເລີ່ມ {{ $project->start_date->translatedFormat('d/m/Y') }}</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-slate-800 leading-snug group-hover:text-brand-green transition-colors mb-3">{{ $project->name }}</h3>

                            @if ($percent !== null)
                                <div class="flex items-baseline justify-between mb-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລະດົມທຶນໄດ້</span>
                                    <span class="text-xs font-extrabold text-brand-green tabular-nums">{{ $percent }}%</span>
                                </div>
                                <div class="relative h-2 rounded-full bg-brand-light-green mb-2">
                                    <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#ffb366] to-brand-green"
                                         style="width: {{ $percent }}%"></div>
                                </div>
                                <p class="text-[11px] text-gray-400">{{ number_format($incomeSum) }} / {{ number_format($project->target_amount) }} ກີບ</p>
                            @else
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ໄດ້ຮັບ</p>
                                        <p class="text-xs font-bold text-brand-green truncate tabular-nums">{{ number_format($incomeSum) }} ກີບ</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ຈ່າຍແລ້ວ</p>
                                        <p class="text-xs font-bold text-rose-800 truncate tabular-nums">{{ number_format($expenseSum) }} ກີບ</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($projects->hasPages())
                <div class="mt-8">{{ $projects->links() }}</div>
            @endif
        @endif
    </div>

</x-layouts.public>
