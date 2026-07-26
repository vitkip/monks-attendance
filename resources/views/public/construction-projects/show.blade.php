<x-layouts.public :title="$project->name">

    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        <a href="{{ route('construction-projects.public.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-brand-green transition-colors mb-6">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            ໂຄງການກໍ່ສ້າງທັງໝົດ
        </a>

        @if ($project->image_url)
            <img src="{{ $project->image_url }}" alt="{{ $project->name }}"
                 class="w-full aspect-[16/9] object-cover rounded-3xl mb-8 shadow-lg shadow-black/5">
        @endif

        <div class="flex items-center gap-2 mb-2">
            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold
                {{ $project->status === 'completed' ? 'bg-brand-light-green text-brand-green' : ($project->status === 'paused' ? 'bg-gray-100 text-gray-500' : 'bg-amber-50 text-amber-600') }}">
                {{ \App\Models\ConstructionProject::statuses()[$project->status] ?? $project->status }}
            </span>
            @if ($project->start_date)
                <span class="text-slate-400 text-xs">ເລີ່ມ {{ $project->start_date->translatedFormat('d F Y') }}</span>
            @endif
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 leading-snug mb-3">{{ $project->name }}</h1>
        @if ($project->description)
            <p class="text-slate-500 text-sm leading-relaxed max-w-2xl mb-8">{{ $project->description }}</p>
        @else
            <div class="mb-8"></div>
        @endif

        {{-- Fundraising board --}}
        <div class="bg-white rounded-2xl card-shadow border border-black/5 p-5 sm:p-6 mb-10">
            @if ($project->progress_percent !== null)
                <div class="flex items-baseline justify-between mb-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລະດົມທຶນໄດ້</span>
                    <span class="text-xl font-extrabold text-brand-green tabular-nums">{{ $project->progress_percent }}%</span>
                </div>
                <div class="relative h-3.5 rounded-full bg-brand-light-green mb-2">
                    <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#ffb366] to-brand-green transition-all duration-700 ease-out"
                         style="width: {{ $project->progress_percent }}%"></div>
                    <div class="absolute inset-y-0 left-1/4 w-px bg-white/60"></div>
                    <div class="absolute inset-y-0 left-1/2 w-px bg-white/60"></div>
                    <div class="absolute inset-y-0 left-3/4 w-px bg-white/60"></div>
                    <div class="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-white border-2 border-brand-green shadow-sm"
                         style="left: {{ $project->progress_percent }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mb-5">{{ number_format($project->total_income) }} / {{ number_format($project->target_amount) }} ກີບ · ເປົ້າໝາຍລະດົມທຶນ</p>
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ໄດ້ຮັບ</p>
                        <p class="text-sm font-bold text-brand-green tabular-nums">{{ number_format($project->total_income) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຈ່າຍແລ້ວ</p>
                        <p class="text-sm font-bold text-rose-800 tabular-nums">{{ number_format($project->total_expense) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຍອດເຫຼືອ</p>
                        <p class="text-sm font-bold tabular-nums {{ $project->balance >= 0 ? 'text-slate-800' : 'text-rose-800' }}">{{ number_format($project->balance) }}</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ໄດ້ຮັບ</p>
                        <p class="text-base font-bold text-brand-green tabular-nums">{{ number_format($project->total_income) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຈ່າຍແລ້ວ</p>
                        <p class="text-base font-bold text-rose-800 tabular-nums">{{ number_format($project->total_expense) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຍອດເຫຼືອ</p>
                        <p class="text-base font-bold tabular-nums {{ $project->balance >= 0 ? 'text-slate-800' : 'text-rose-800' }}">{{ number_format($project->balance) }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Ledger --}}
        <div class="flex items-center gap-3 mb-5">
            <p class="text-sm font-bold text-slate-800 shrink-0">ບັນຊີລາຍຮັບ-ລາຍຈ່າຍ</p>
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full shrink-0">
                {{ $transactions->total() }} ລາຍການ
            </span>
        </div>

        @if ($transactions->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-16 bg-white rounded-2xl card-shadow border border-black/5">
                <p class="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີລາຍການ</p>
                <p class="text-slate-400 text-sm">ໂຄງການນີ້ຍັງບໍ່ທັນມີການບັນທຶກລາຍຮັບ-ລາຍຈ່າຍ</p>
            </div>
        @else
            <div class="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden">
                @foreach ($transactions as $tx)
                    <div class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="shrink-0 w-10 text-center">
                            <p class="text-[9px] font-bold uppercase tracking-wide text-gray-400 leading-none mb-0.5">{{ $tx->transaction_date->translatedFormat('M') }}</p>
                            <p class="text-base font-extrabold text-slate-800 leading-none tabular-nums">{{ $tx->transaction_date->format('d') }}</p>
                        </div>

                        @if ($tx->image_url)
                            <a href="{{ $tx->image_url }}" target="_blank" rel="noopener"
                               class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                                <img src="{{ $tx->image_url }}" alt="ຮູບບິນ" class="w-full h-full object-cover">
                            </a>
                        @else
                            <div class="w-12 h-12 rounded-xl bg-[#f8fafa] border border-gray-100 shrink-0 flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 text-sm truncate">{{ $tx->description ?: ($tx->type === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ') }}</p>
                        </div>

                        <p class="shrink-0 font-extrabold tabular-nums text-sm sm:text-base {{ $tx->type === 'income' ? 'text-brand-green' : 'text-rose-800' }}">
                            {{ $tx->type === 'income' ? '+' : '−' }}{{ number_format($tx->amount) }}
                        </p>
                    </div>
                @endforeach
            </div>

            @if ($transactions->hasPages())
                <div class="mt-6">{{ $transactions->links() }}</div>
            @endif
        @endif
    </div>

</x-layouts.public>
