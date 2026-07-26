<x-layouts.public :title="'ບົດສູດມົນ'">

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-brand-green-dark">
        <span class="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none" aria-hidden="true">☸</span>

        <div class="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                <span aria-hidden="true">☸</span> ຄຳສອນ
            </span>

            <h1 class="text-white text-3xl sm:text-4xl font-bold leading-tight">ບົດສູດມົນ</h1>
            <p class="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                ລວມບົດສູດມົນສຳລັບການປະຕິບັດທຳ
            </p>

            <div class="flex flex-wrap items-center gap-3 mt-7">
                <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                    <span class="text-lg font-bold text-white tabular-nums leading-none">{{ $chants->total() }}</span>
                    <span class="text-xs text-white/60">ບົດສູດມົນ</span>
                </div>
                @if ($categories->isNotEmpty())
                    <div class="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                        <span class="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{{ $categories->count() }}</span>
                        <span class="text-xs text-white/60">ໝວດໝູ່</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        @if ($categories->isNotEmpty())
            <nav aria-label="ໝວດໝູ່ບົດສູດມົນ"
                 class="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                    <a href="{{ route('chants.public.index') }}"
                       class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                              {{ ! $categorySlug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                        ທັງໝົດ
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('chants.public.index', ['category' => $category->slug]) }}"
                           class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                                  {{ $categorySlug === $category->slug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                            {{ str_repeat('— ', $category->depth) }}{{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </nav>
        @endif

        @if ($chants->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-24">
                <span class="text-5xl text-brand-green/30 mb-4">☸</span>
                <p class="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີບົດສູດມົນ</p>
                <p class="text-slate-400 text-sm">ກະລຸນາກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ($chants as $item)
                    @php
                        $itemMinutes = max(1, (int) ceil(\Illuminate\Support\Str::wordCount(strip_tags($item->content)) / 150));
                    @endphp
                    <a href="{{ route('chants.public.show', $item->slug) }}"
                       class="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-9 h-9 rounded-xl bg-brand-light-green flex items-center justify-center flex-shrink-0">
                                <span class="text-brand-green">☸</span>
                            </span>
                            @if ($item->category)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600">
                                    {{ $item->category->name }}
                                </span>
                            @endif
                        </div>
                        <h3 class="font-bold text-slate-800 text-base leading-snug group-hover:text-brand-green transition-colors duration-300">
                            {{ $item->title }}
                        </h3>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed flex-1 line-clamp-3">
                            {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 120) }}
                        </p>
                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-black/5">
                            <span class="inline-flex items-center gap-1 text-[11px] text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                                    <circle cx="10" cy="10" r="7"/>
                                    <path stroke-linecap="round" d="M10 6v4l3 2"/>
                                </svg>
                                {{ $itemMinutes }} ນາທີ
                            </span>
                            <span class="text-[11px] font-semibold text-brand-green group-hover:translate-x-0.5 transition-transform duration-300">ອ່ານ →</span>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($chants->hasPages())
                <div class="mt-10">{{ $chants->links() }}</div>
            @endif
        @endif

    </div>

</x-layouts.public>
