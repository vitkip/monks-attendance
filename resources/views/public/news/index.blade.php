<x-layouts.public :title="'ຂ່າວສານ ແລະ ປະກາດ'">

    {{-- Hero --}}
    @if ($heroSlides->isNotEmpty())
        <section id="hero-slider" class="relative overflow-hidden bg-brand-green-dark min-h-[420px] sm:min-h-[500px]">

            @foreach ($heroSlides as $index => $slide)
                <div class="hero-slide absolute inset-0 transition-opacity duration-700 ease-in-out
                            {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}"
                     aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                    <img src="{{ $slide->image_url }}" alt="" aria-hidden="true"
                         class="absolute inset-0 w-full h-full object-cover opacity-60">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-green-dark via-brand-green-dark/75 to-brand-green-dark/30"></div>

                    <div class="relative h-full max-w-6xl mx-auto px-5 sm:px-8 pt-24 pb-10 sm:pb-14 w-full flex flex-col justify-end">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4 w-fit">
                            <span aria-hidden="true">☸</span> ຂ່າວສານຊຸມຊົນ
                        </span>

                        @if ($slide->link_url)
                            <a href="{{ $slide->link_url }}" target="_blank" rel="noopener"
                               class="group block focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-bright-green rounded">
                                <h1 class="text-white text-2xl sm:text-4xl font-bold leading-tight max-w-2xl group-hover:text-brand-bright-green transition-colors duration-300 line-clamp-2">
                                    {{ $slide->title }}
                                </h1>
                            </a>
                        @else
                            <h1 class="text-white text-2xl sm:text-4xl font-bold leading-tight max-w-2xl line-clamp-2">
                                {{ $slide->title }}
                            </h1>
                        @endif

                        @if ($slide->subtitle)
                            <p class="text-white/70 text-sm sm:text-base mt-3 max-w-xl leading-relaxed hidden sm:block line-clamp-2">
                                {{ $slide->subtitle }}
                            </p>
                        @endif

                        @if ($slide->link_url)
                            <a href="{{ $slide->link_url }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 mt-6 w-fit bg-brand-bright-green text-brand-green-dark px-6 py-3 rounded-xl text-sm font-bold transition-all hover:shadow-lg hover:shadow-black/20 active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                                {{ $slide->button_text ?: 'ອ່ານຕໍ່' }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 4l6 6-6 6"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($heroSlides->count() > 1)
                <button type="button" class="hero-prev absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center backdrop-blur-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-white"
                        aria-label="ສະໄລ້ກ່ອນໜ້າ">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4l-6 6 6 6"/>
                    </svg>
                </button>
                <button type="button" class="hero-next absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center backdrop-blur-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-white"
                        aria-label="ສະໄລ້ຕໍ່ໄປ">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 4l6 6-6 6"/>
                    </svg>
                </button>

                <div class="hero-dots absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
                    @foreach ($heroSlides as $index => $slide)
                        <button type="button" class="hero-dot h-1.5 rounded-full transition-all duration-300
                                    {{ $index === 0 ? 'w-6 bg-white' : 'w-1.5 bg-white/40 hover:bg-white/60' }}"
                                aria-label="ໄປສະໄລ້ທີ {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($heroSlides->count() > 1)
            <script>
                (function () {
                    const root = document.getElementById('hero-slider');
                    if (!root) return;

                    const slides = root.querySelectorAll('.hero-slide');
                    const dots = root.querySelectorAll('.hero-dot');
                    const total = slides.length;
                    let current = 0;
                    let timer = null;

                    function show(index) {
                        current = (index + total) % total;

                        slides.forEach((slide, i) => {
                            const active = i === current;
                            slide.classList.toggle('opacity-100', active);
                            slide.classList.toggle('z-10', active);
                            slide.classList.toggle('opacity-0', !active);
                            slide.classList.toggle('z-0', !active);
                            slide.classList.toggle('pointer-events-none', !active);
                            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
                        });

                        dots.forEach((dot, i) => {
                            const active = i === current;
                            dot.classList.toggle('w-6', active);
                            dot.classList.toggle('bg-white', active);
                            dot.classList.toggle('w-1.5', !active);
                            dot.classList.toggle('bg-white/40', !active);
                        });
                    }

                    function next() { show(current + 1); }
                    function prev() { show(current - 1); }

                    function startAutoplay() {
                        stopAutoplay();
                        timer = setInterval(next, 6000);
                    }

                    function stopAutoplay() {
                        if (timer) clearInterval(timer);
                        timer = null;
                    }

                    root.querySelector('.hero-next')?.addEventListener('click', () => { next(); startAutoplay(); });
                    root.querySelector('.hero-prev')?.addEventListener('click', () => { prev(); startAutoplay(); });
                    dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); startAutoplay(); }));

                    root.addEventListener('mouseenter', stopAutoplay);
                    root.addEventListener('mouseleave', startAutoplay);

                    startAutoplay();
                })();
            </script>
        @endif
    @else
        <section class="relative overflow-hidden bg-brand-green-dark min-h-[420px] sm:min-h-[500px] flex items-end">
            <div class="absolute inset-0">
                @if ($featured && $featured->image_url)
                    <img src="{{ $featured->image_url }}" alt="" aria-hidden="true"
                         class="w-full h-full object-cover opacity-60">
                @else
                    <div class="w-full h-full bg-brand-tracker opacity-80"></div>
                @endif
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-brand-green-dark via-brand-green-dark/75 to-brand-green-dark/30"></div>

            <div class="relative max-w-6xl mx-auto px-5 sm:px-8 pt-24 pb-10 sm:pb-14 w-full">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                    <span aria-hidden="true">☸</span> ຂ່າວສານຊຸມຊົນ
                </span>

                @if ($featured)
                    @php
                        $featuredMinutes = max(1, (int) ceil(\Illuminate\Support\Str::wordCount(strip_tags($featured->content)) / 200));
                        $featuredDate = $featured->published_at ?? $featured->created_at;
                    @endphp

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

                    <a href="{{ route('news.public.show', $featured->slug) }}"
                       class="group block focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-bright-green rounded">
                        <h1 class="text-white text-2xl sm:text-4xl font-bold leading-tight max-w-2xl group-hover:text-brand-bright-green transition-colors duration-300 line-clamp-2">
                            {{ $featured->title }}
                        </h1>
                    </a>
                    <p class="text-white/70 text-sm sm:text-base mt-3 max-w-xl leading-relaxed hidden sm:block line-clamp-2">
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

                    <a href="{{ route('news.public.show', $featured->slug) }}"
                       class="inline-flex items-center gap-2 mt-6 bg-brand-bright-green text-brand-green-dark px-6 py-3 rounded-xl text-sm font-bold transition-all hover:shadow-lg hover:shadow-black/20 active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                        ອ່ານຕໍ່
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 4l6 6-6 6"/>
                        </svg>
                    </a>
                @else
                    <h1 class="text-white text-3xl sm:text-4xl font-bold leading-tight">ຂ່າວສານ ແລະ ປະກາດ</h1>
                    <p class="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                        ຕິດຕາມຂ່າວສານ, ກິດຈະກຳ ແລະ ປະກາດຫຼ້າສຸດຈາກທາງວັດ ເພື່ອບໍ່ພາດທຸກຄວາມເຄື່ອນໄຫວ
                    </p>
                @endif
            </div>
        </section>
    @endif

    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

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
        @elseif ($news->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-24">
                <span class="text-5xl text-brand-green/30 mb-4">☸</span>
                <p class="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂ່າວໃນໝວດໝູ່ນີ້</p>
                <p class="text-slate-400 text-sm">ລອງເລືອກໝວດໝູ່ອື່ນເບິ່ງ</p>
            </div>
        @else

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- News grid (2/3 width) --}}
                <div class="lg:col-span-2">
                    <div class="flex items-end justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">
                                {{ $categorySlug ? optional($categories->firstWhere('slug', $categorySlug))->name : 'ຂ່າວອື່ນໆ' }}
                            </h2>
                            <p class="text-slate-400 text-xs mt-0.5">ຕິດຕາມຄວາມເຄື່ອນໄຫວພາຍໃນວັດ</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                                    @if ($item->category)
                                        <span class="text-[10px] font-bold text-brand-green uppercase tracking-widest">
                                            {{ $item->category->name }}
                                        </span>
                                    @endif
                                    <h3 class="font-bold text-slate-800 text-base leading-snug mt-2 mb-2 group-hover:text-brand-green transition-colors duration-300 line-clamp-2">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-sm text-slate-500 leading-relaxed flex-1 line-clamp-2">{{ $item->excerpt_or_summary }}</p>
                                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-black/5">
                                        <span class="inline-flex items-center gap-1 text-[11px] text-gray-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                                                <rect x="3" y="4" width="14" height="13" rx="2"/>
                                                <path stroke-linecap="round" d="M3 8h14M7 2v4M13 2v4"/>
                                            </svg>
                                            {{ $itemDate->format('d/m/Y') }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">{{ $itemMinutes }} ນາທີອ່ານ</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($news->hasPages())
                        <div class="mt-10">{{ $news->links() }}</div>
                    @endif
                </div>

                {{-- Sidebar (1/3 width) --}}
                <aside class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-black/5 card-shadow p-6 sm:p-7 lg:sticky lg:top-32">
                        <h2 class="text-base font-bold text-slate-800 mb-6 pb-4 border-b border-black/5">ຂ່າວຫຼ້າສຸດ</h2>

                        @if ($latestNews->isEmpty())
                            <p class="text-sm text-slate-400">ຍັງບໍ່ມີຂ່າວ</p>
                        @else
                            <div class="space-y-5">
                                @foreach ($latestNews as $item)
                                    @php $recentDate = $item->published_at ?? $item->created_at; @endphp
                                    <a href="{{ route('news.public.show', $item->slug) }}"
                                       class="flex gap-4 group focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green rounded-lg">
                                        <div class="shrink-0 w-12 h-12 rounded-lg flex flex-col items-center justify-center font-bold bg-brand-light-green text-brand-green">
                                            <span class="text-[10px] uppercase leading-none">{{ $recentDate->translatedFormat('M') }}</span>
                                            <span class="text-base leading-none mt-0.5">{{ $recentDate->format('d') }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-sm text-slate-800 leading-snug group-hover:text-brand-green transition-colors duration-300 line-clamp-2">
                                                {{ $item->title }}
                                            </h4>
                                            @if ($item->category)
                                                <p class="text-[11px] text-gray-400 mt-1">{{ $item->category->name }}</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </aside>

            </div>

        @endif

    </div>

</x-layouts.public>
