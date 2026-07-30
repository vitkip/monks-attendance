<x-layouts.public :title="$chant->title" :description="\Illuminate\Support\Str::limit(strip_tags($chant->content), 150)">

    <article class="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        <a href="{{ route('chants.public.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-brand-green transition-colors duration-300 mb-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green rounded">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4l-6 6 6 6"/>
            </svg>
            ບົດສູດມົນທັງໝົດ
        </a>

        @php
            $chantMinutes = max(1, (int) ceil(\Illuminate\Support\Str::wordCount(strip_tags($chant->content)) / 150));
        @endphp

        <div class="flex items-center gap-2 mb-3 flex-wrap">
            @if ($chant->category)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-light-green text-brand-green">
                    {{ $chant->category->name }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2" aria-hidden="true">
                    <circle cx="10" cy="10" r="7"/>
                    <path stroke-linecap="round" d="M10 6v4l3 2"/>
                </svg>
                {{ $chantMinutes }} ນາທີ
            </span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 leading-snug mb-6">{{ $chant->title }}</h1>

        <div class="bg-white rounded-3xl card-shadow border border-black/5 p-6 sm:p-10">
            <div class="prose prose-slate max-w-none text-base sm:text-lg leading-loose">{!! $chant->content_html !!}</div>
        </div>

        {{-- Share --}}
        <div class="flex items-center gap-3 mt-8">
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
                    aria-label="ສຳເນົາລິ້ງບົດສູດມົນ"
                    class="w-9 h-9 rounded-full bg-brand-light-green text-brand-green flex items-center justify-center hover:bg-brand-green hover:text-white transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/>
                </svg>
            </button>
            <span id="copy-link-feedback" role="status" class="text-xs text-brand-green font-semibold opacity-0 transition-opacity duration-300">ສຳເນົາລິ້ງແລ້ວ</span>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-16 pt-8 border-t border-black/5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-5">ບົດສູດມົນອື່ນໆ</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @foreach ($related as $item)
                        <a href="{{ route('chants.public.show', $item->slug) }}"
                           class="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col p-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                            <span class="w-8 h-8 rounded-lg bg-brand-light-green flex items-center justify-center mb-2">
                                <span class="text-sm text-brand-green">☸</span>
                            </span>
                            <h4 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-brand-green transition-colors duration-300">
                                {{ $item->title }}
                            </h4>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </article>

    <script>
        (function () {
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
