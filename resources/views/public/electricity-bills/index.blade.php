<x-layouts.public :title="'ລາຍຈ່າຍຄ່າໄຟຟ້າ'" :description="'ລາຍງານຄ່າໄຟຟ້າຂອງວັດປ່າໜອງບົວທອງໃຕ້ ແບບໂປ່ງໃສ'">

    @php
        $maxMonthly = $monthly->max() ?: 0;
        $hasMonthlyData = $maxMonthly > 0;
    @endphp

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-brand-green-dark">
        <span
            class="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none"
            aria-hidden="true">⚡</span>

        <div class="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                <span aria-hidden="true">⚡</span> ຄວາມໂປ່ງໃສທາງການເງິນ
            </span>

            <h1 class="text-white text-3xl sm:text-4xl font-bold leading-tight">ເຊີນຮ່ວມບໍລິຈາກຄ່າໄຟຟ້າ ຕາມເລກລະຫັດບິນ
            </h1>
            <p class="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                ບັນທຶກໃບບິນຄ່າໄຟຟ້າຂອງວັດໃນແຕ່ລະເດືອນ ເປີດເຜີຍໃຫ້ຍາດໂຍມ ແລະ ຜູ້ມີສັດທາຮ່ວມສົມທົບໄດ້
            </p>

            <div class="flex flex-wrap items-center gap-3 mt-7">
                <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                    <span
                        class="text-lg font-bold text-white tabular-nums leading-none">{{ number_format($totalYear) }}</span>
                    <span class="text-xs text-white/60">ກີບ · ຍອດລວມປີ {{ $year }}</span>
                </div>
                <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                    <span
                        class="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{{ $countYear }}</span>
                    <span class="text-xs text-white/60">ໃບ · ຈຳນວນບິນ</span>
                </div>
            </div>
            <p class="text-white/30 text-xs mt-4">ຍອດລວມທັງໝົດທຸກປີ: {{ number_format($totalAllTime) }} ກີບ</p>

            {{-- Monthly summary bars — the year's spending shape at a glance --}}
            @if ($hasMonthlyData)
                <div class="mt-9 bg-white/[0.04] rounded-2xl px-5 pt-8 pb-3">
                    <div class="flex items-end gap-1.5 sm:gap-2.5 h-24">
                        @foreach ($monthly as $index => $value)
                            @php
                                $monthNum = $index + 1;
                                $isPeak = $hasMonthlyData && $value > 0 && $value === $maxMonthly;
                                $heightPct = $maxMonthly > 0 ? max(($value / $maxMonthly) * 100, $value > 0 ? 6 : 2) : 2;
                            @endphp
                            <div class="relative flex-1 h-full flex flex-col items-center justify-end group">
                                @if ($isPeak)
                                    <span class="absolute -top-5 text-[10px] font-bold text-brand-bright-green whitespace-nowrap">
                                        {{ number_format($value) }}
                                    </span>
                                @endif
                                <div title="{{ sprintf('%02d', $monthNum) }}/{{ $year }} — {{ number_format($value) }} ກີບ"
                                    class="w-full rounded-t-[3px] transition-all duration-300
                                                                    {{ $isPeak ? 'bg-brand-bright-green' : 'bg-white/15 group-hover:bg-white/25' }}"
                                    style="height: {{ $heightPct }}%"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-1.5 sm:gap-2.5 mt-2">
                        @foreach ($monthly as $index => $value)
                            <span
                                class="flex-1 text-center text-[9px] text-white/35 tabular-nums">{{ sprintf('%02d', $index + 1) }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        {{-- Year filter --}}
        @if ($availableYears->count() > 1)
            <nav aria-label="ເລືອກປີ"
                class="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                    @foreach ($availableYears as $y)
                        <a href="{{ route('electricity-bills.public.index', ['year' => $y]) }}"
                            class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                                                          {{ (int) $year === (int) $y ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                            ປີ {{ $y }}
                        </a>
                    @endforeach
                </div>
            </nav>
        @endif

        @if ($billsByMonth->isEmpty())
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center text-center py-24">
                <span class="text-5xl text-brand-green/30 mb-4">⚡</span>
                <p class="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂໍ້ມູນໃນປີ {{ $year }}</p>
                <p class="text-slate-400 text-sm">ກະລຸນາເລືອກປີອື່ນ ຫຼື ກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
            </div>
        @else

            @php
                $laoMonths = [
                    1 => 'ມັງກອນ', 2 => 'ກຸມພາ', 3 => 'ມີນາ', 4 => 'ເມສາ',
                    5 => 'ພຶດສະພາ', 6 => 'ມິຖຸນາ', 7 => 'ກໍລະກົດ', 8 => 'ສິງຫາ',
                    9 => 'ກັນຍາ', 10 => 'ຕຸລາ', 11 => 'ພະຈິກ', 12 => 'ທັນວາ',
                ];
            @endphp

            {{-- Ledger, grouped by month --}}
            <div class="space-y-8">
                @foreach ($billsByMonth as $monthBills)
                    @php
                        $monthNum = (int) $monthBills->first()->bill_month->format('n');
                        $monthTotal = $monthBills->sum('amount');
                    @endphp
                    <div>
                        {{-- Month header --}}
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex items-center gap-2.5 shrink-0">
                                <div class="w-8 h-8 rounded-lg bg-brand-light-green flex items-center justify-center">
                                    <span class="text-brand-green text-sm">⚡</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 leading-none">ເດືອນ{{ $laoMonths[$monthNum] }} {{ $year }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 tabular-nums">{{ sprintf('%02d', $monthNum) }}/{{ $year }} · {{ $monthBills->count() }} ໃບ</p>
                                </div>
                            </div>
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span
                                class="text-xs font-bold text-brand-green tabular-nums px-2.5 py-1 bg-brand-light-green rounded-full whitespace-nowrap">
                                {{ number_format($monthTotal) }} ກີບ
                            </span>
                        </div>

                        <div class="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden">
                            @foreach ($monthBills as $bill)
                                <div
                                    class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                    <span
                                        class="hidden sm:flex w-7 h-7 shrink-0 rounded-full bg-gray-50 text-gray-400 text-[11px] font-bold items-center justify-center tabular-nums">
                                        {{ sprintf('%02d', $loop->iteration) }}
                                    </span>

                                    <a href="{{ $bill->image_url }}" target="_blank" rel="noopener"
                                        class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                                        <img src="{{ $bill->image_url }}"
                                            alt="ໃບບິນ {{ $bill->customer_name }} ເດືອນ {{ $bill->bill_month->format('m/Y') }}"
                                            class="w-full h-full object-cover">
                                    </a>

                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-800 text-sm truncate">{{ $bill->customer_name }}</p>
                                        <p class="text-gray-400 text-xs mt-0.5">
                                            {{ $bill->province }} &nbsp;·&nbsp; ບັນຊີຜູ້ໃຊ້ໄຟ
                                            <button type="button"
                                                class="copy-account relative inline-flex items-center gap-1 align-middle font-semibold text-slate-600 hover:text-brand-green transition-colors"
                                                data-account="{{ $bill->account_number }}" title="ກົດເພື່ອຄັດລອກເລກບັນຊີ"
                                                aria-label="ຄັດລອກເລກບັນຊີ {{ $bill->account_number }}">
                                                <span class="copy-label tabular-nums">{{ $bill->account_number }}</span>
                                                <svg class="copy-icon w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8 7V5a2 2 0 012-2h9a2 2 0 012 2v9a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-2M8 7h8v8H8V7z" />
                                                </svg>
                                                <span
                                                    class="copy-toast pointer-events-none absolute -top-7 left-1/2 -translate-x-1/2 whitespace-nowrap px-2 py-1 rounded-md bg-slate-800 text-white text-[10px] font-semibold opacity-0 transition-opacity duration-200">
                                                    ຄັດລອກແລ້ວ!
                                                </span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="text-right shrink-0">
                                        <p class="font-bold text-slate-800 text-sm tabular-nums">{{ number_format($bill->amount) }} ກີບ</p>
                                        <p class="text-gray-400 text-xs mt-0.5 tabular-nums">{{ $bill->bill_month->format('m/Y') }}</p>
                                    </div>

                                    <a href="{{ $bill->image_url }}" target="_blank" rel="noopener"
                                        class="hidden md:inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-brand-green hover:text-brand-bright-green transition-colors ml-1"
                                        aria-label="ເບິ່ງໃບບິນ {{ $bill->customer_name }}">
                                        ເບິ່ງໃບບິນ
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 4l6 6-6 6" />
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Year total — the ledger's bottom line --}}
            <div class="flex items-center justify-between gap-4 mt-8 px-5 py-4 bg-brand-green-dark rounded-2xl">
                <span class="text-sm font-bold text-white/80">ລວມຄ່າໄຟຟ້າທັງໝົດປີ {{ $year }}</span>
                <span class="text-base font-bold text-brand-bright-green tabular-nums">{{ number_format($totalYear) }}
                    ກີບ</span>
            </div>

        @endif

    </div>

    <script>
        document.addEventListener('click', function (event) {
            const button = event.target.closest('.copy-account');
            if (!button) return;

            const account = button.dataset.account;
            const toast = button.querySelector('.copy-toast');
            const iconPath = button.querySelector('.copy-icon path');
            const defaultPath = iconPath.getAttribute('d');
            const checkPath = 'M5 13l4 4L19 7';

            const copyText = (text) => {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    return navigator.clipboard.writeText(text);
                }
                const scratch = document.createElement('textarea');
                scratch.value = text;
                scratch.style.position = 'fixed';
                scratch.style.opacity = '0';
                document.body.appendChild(scratch);
                scratch.select();
                document.execCommand('copy');
                document.body.removeChild(scratch);
                return Promise.resolve();
            };

            copyText(account).then(() => {
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
                iconPath.setAttribute('d', checkPath);

                clearTimeout(button._copyTimeout);
                button._copyTimeout = setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                    iconPath.setAttribute('d', defaultPath);
                }, 1500);
            });
        });
    </script>

</x-layouts.public>