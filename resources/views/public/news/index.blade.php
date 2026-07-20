<x-layouts.public :title="'ຂ່າວສານ ແລະ ປະກາດ'">

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-brand-green-dark">
        <div class="absolute inset-0 bg-brand-tracker opacity-80"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-brand-green-dark/20 via-brand-green-dark/60 to-brand-green-dark"></div>
        <div class="relative max-w-5xl mx-auto px-5 sm:px-8 pt-14 pb-14 sm:pt-20 sm:pb-20">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                <span aria-hidden="true">☸</span> ຂ່າວສານຊຸມຊົນ
            </span>
            <h1 class="text-white text-3xl sm:text-4xl font-bold leading-tight">ຂ່າວສານ ແລະ ປະກາດ</h1>
            <p class="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                ຕິດຕາມຂ່າວສານ, ກິດຈະກຳ ແລະ ປະກາດຫຼ້າສຸດຈາກທາງວັດ ເພື່ອບໍ່ພາດທຸກຄວາມເຄື່ອນໄຫວ
            </p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        @if ($categories->isNotEmpty())
            <nav aria-label="ໝວດໝູ່ຂ່າວ"
                 class="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                    <a href="{{ route('news.public.index') }}"
                       class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                              {{ ! $categorySlug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                        ທັງໝົດ
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('news.public.index', ['category' => $category->slug]) }}"
                           class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                                  {{ $categorySlug === $category->slug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </nav>
        @endif

        @if (! $featured && $news->isEmpty())
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center text-center py-24">
                <span class="text-5xl text-brand-green/30 mb-4">☸</span>
                <p class="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂ່າວປະກາດ</p>
                <p class="text-slate-400 text-sm">ກະລຸນາກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
            </div>
        @else

            {{-- Featured article --}}
            @if ($featured)
                @php
                    $featuredMinutes = max(1, (int) ceil(\Illuminate\Support\Str::wordCount(strip_tags($featured->content)) / 200));
                    $featuredDate = $featured->published_at ?? $featured->created_at;
                @endphp
                <a href="{{ route('news.public.show', $featured->slug) }}"
                   class="group block relative rounded-3xl overflow-hidden bg-brand-green-dark mb-14 shadow-xl shadow-black/5 transition-all duration-300 hover:shadow-2xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-bright-green">
                    <div class="aspect-[16/9] sm:aspect-[21/9]">
                        @if ($featured->image_url)
                            <img src="{{ $featured->image_url }}" alt="{{ $featured->title }}"
                                 class="w-full h-full object-cover opacity-70 group-hover:opacity-60 group-hover:scale-[1.02] transition-all duration-500">
                        @else
                            <div class="w-full h-full bg-brand-tracker opacity-90"></div>
                        @endif
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-green-dark via-brand-green-dark/55 to-brand-green-dark/10"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6 sm:p-10">
                        <div class="flex flex-wrap items-center gap-1.5 mb-3">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-brand-bright-green/20 text-brand-bright-green">
                                ຂ່າວລ່າສຸດ
                            </span>
                            @if ($featured->category)
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/15 text-white">
                                    {{ $featured->category->name }}
                                </span>
                            @endif
                        </div>
                        <h2 class="text-white text-xl sm:text-3xl font-bold leading-snug max-w-2xl group-hover:text-brand-bright-green transition-colors duration-300 line-clamp-2">
                            {{ $featured->title }}
                        </h2>
                        <p class="text-white/70 text-sm mt-3 max-w-xl leading-relaxed hidden sm:block line-clamp-2">
                            {{ $featured->excerpt_or_summary }}
                        </p>
                        <p class="flex items-center flex-wrap gap-x-1.5 text-white/50 text-xs mt-4">
                            <time datetime="{{ $featuredDate->toDateString() }}">{{ $featuredDate->format('d/m/Y') }}</time>
                            <span aria-hidden="true">·</span>
                            <span>{{ $featured->author?->name ?? 'ບໍ່ລະບຸ' }}</span>
                            <span aria-hidden="true">·</span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                                    <circle cx="10" cy="10" r="7"/>
                                    <path stroke-linecap="round" d="M10 6v4l3 2"/>
                                </svg>
                                {{ $featuredMinutes }} ນາທີອ່ານ
                            </span>
                        </p>
                    </div>
                </a>
            @endif

            @if ($news->isNotEmpty())
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-5">ຂ່າວອື່ນໆ</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($news as $item)
                        @php
                            $itemMinutes = max(1, (int) ceil(\Illuminate\Support\Str::wordCount(strip_tags($item->content)) / 200));
                            $itemDate = $item->published_at ?? $item->created_at;
                        @endphp
                        <a href="{{ route('news.public.show', $item->slug) }}"
                           class="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                            <div class="aspect-[16/9] bg-brand-light-green overflow-hidden">
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy"
                                         class="w-full h-full object-cover opacity-0 group-hover:scale-105 transition-[opacity,transform] duration-500"
                                         onload="this.classList.remove('opacity-0')">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-2xl text-brand-green/40">☸</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <time datetime="{{ $itemDate->toDateString() }}" class="text-[10px] font-medium text-gray-400 uppercase tracking-widest">
                                        {{ $itemDate->format('d/m/Y') }}
                                    </time>
                                    @if ($item->category)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600">
                                            {{ $item->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-slate-800 text-base leading-snug group-hover:text-brand-green transition-colors duration-300 line-clamp-2">
                                    {{ $item->title }}
                                </h3>
                                <p class="text-sm text-slate-500 mt-2 leading-relaxed flex-1 line-clamp-3">{{ $item->excerpt_or_summary }}</p>
                                <div class="flex items-center justify-between mt-3">
                                    <p class="text-xs text-gray-400">{{ $item->author?->name ?? 'ບໍ່ລະບຸ' }}</p>
                                    <p class="text-xs text-gray-400">{{ $itemMinutes }} ນາທີ</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($news->hasPages())
                    <div class="mt-10">{{ $news->links() }}</div>
                @endif
            @endif

        @endif

    </div>

</x-layouts.public>
