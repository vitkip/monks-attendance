<x-layouts.public :title="$article->title" :description="$article->excerpt_or_summary" :image="$article->image_url">

    {{-- Reading progress bar --}}
    <div id="reading-progress" class="fixed top-0 left-0 h-1 bg-brand-bright-green z-30 transition-[width] duration-150 ease-out" style="width:0%" aria-hidden="true"></div>

    <article class="max-w-3xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        <nav aria-label="ຍ້ອນກັບ">
            <a href="{{ route('news.public.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-brand-green transition-colors duration-300 mb-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4l-6 6 6 6"/>
                </svg>
                ຂ່າວທັງໝົດ
            </a>
        </nav>

        @if ($article->image_url)
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                 class="w-full aspect-[16/9] object-cover rounded-3xl mb-8 shadow-lg shadow-black/5">
        @endif

        @php
            $articleMinutes = max(1, (int) ceil(\Illuminate\Support\Str::wordCount(strip_tags($article->content)) / 200));
            $articleDate = $article->published_at ?? $article->created_at;
        @endphp

        <div class="flex items-center gap-2 mb-3 flex-wrap">
            @if ($article->category)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-light-green text-brand-green">
                    {{ $article->category->name }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                    <circle cx="10" cy="10" r="7"/>
                    <path stroke-linecap="round" d="M10 6v4l3 2"/>
                </svg>
                {{ $articleMinutes }} ນາທີອ່ານ
            </span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 leading-snug mb-6">{{ $article->title }}</h1>

        {{-- Meta: author + date --}}
        <div class="flex items-center gap-3 pb-6 mb-8 border-b border-black/5">
            <span class="w-10 h-10 rounded-full bg-brand-light-green flex items-center justify-center text-brand-green font-bold text-sm shrink-0" aria-hidden="true">
                {{ \Illuminate\Support\Str::of($article->author?->name ?? 'ບ')->substr(0, 1) }}
            </span>
            <div class="leading-tight">
                <p class="text-sm font-semibold text-slate-700">{{ $article->author?->name ?? 'ບໍ່ລະບຸ' }}</p>
                <time datetime="{{ $articleDate->toDateString() }}" class="text-xs text-gray-400">
                    {{ $articleDate->format('d/m/Y') }}
                </time>
            </div>
        </div>

        <div class="prose prose-slate prose-lg max-w-none prose-p:leading-loose prose-headings:font-bold prose-a:text-brand-green prose-img:rounded-2xl">
            {!! $article->content_html !!}
        </div>

        {{-- Share --}}
        <div class="flex items-center gap-3 mt-10 pt-8 border-t border-black/5">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">ແຊຣ໌</span>

            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
               target="_blank" rel="noopener noreferrer"
               aria-label="ແຊຣ໌ໄປ Facebook"
               class="w-9 h-9 rounded-full bg-brand-light-green text-brand-green flex items-center justify-center hover:bg-brand-green hover:text-white transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.191.312-.271.71-.271 1.206v1.844h3.744l-.494 1.847-.301 1.82h-2.949v7.98H9.101z"/>
                </svg>
            </a>

            <button type="button" id="copy-link-btn" data-url="{{ url()->current() }}"
                    aria-label="ສຳເນົາລິ້ງບົດຄວາມ"
                    class="w-9 h-9 rounded-full bg-brand-light-green text-brand-green flex items-center justify-center hover:bg-brand-green hover:text-white transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/>
                </svg>
            </button>
            <span id="copy-link-feedback" role="status" class="text-xs text-brand-green font-semibold opacity-0 transition-opacity duration-300">ສຳເນົາລິ້ງແລ້ວ</span>
        </div>

        @if ($recent->isNotEmpty())
            <div class="mt-16 pt-8 border-t border-black/5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-5">ຂ່າວອື່ນໆ</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($recent as $item)
                        @php
                            $recentMinutes = max(1, (int) ceil(\Illuminate\Support\Str::wordCount(strip_tags($item->content)) / 200));
                            $recentDate = $item->published_at ?? $item->created_at;
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
                                        <span class="text-xl text-brand-green/40">☸</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                    <time datetime="{{ $recentDate->toDateString() }}" class="text-[10px] font-medium text-gray-400 uppercase tracking-widest">
                                        {{ $recentDate->format('d/m/Y') }}
                                    </time>
                                </div>
                                <h4 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-brand-green transition-colors duration-300 line-clamp-2">
                                    {{ $item->title }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed flex-1 line-clamp-2">{{ $item->excerpt_or_summary }}</p>
                                <p class="text-[11px] text-gray-400 mt-2">{{ $recentMinutes }} ນາທີອ່ານ</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </article>

    <script>
        (function () {
            var bar = document.getElementById('reading-progress');
            if (bar) {
                var onScroll = function () {
                    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
                    var progress = docHeight > 0 ? (window.scrollY / docHeight) * 100 : 0;
                    bar.style.width = Math.min(100, Math.max(0, progress)) + '%';
                };
                document.addEventListener('scroll', onScroll, { passive: true });
                onScroll();
            }

            var copyBtn = document.getElementById('copy-link-btn');
            var feedback = document.getElementById('copy-link-feedback');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    navigator.clipboard.writeText(copyBtn.dataset.url).then(function () {
                        if (feedback) {
                            feedback.classList.remove('opacity-0');
                            setTimeout(function () { feedback.classList.add('opacity-0'); }, 2000);
                        }
                    });
                });
            }
        })();
    </script>

</x-layouts.public>
