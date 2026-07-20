<x-layouts.public :title="$chant->title">

    <article class="max-w-3xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        <a href="{{ route('chants.public.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-brand-green transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4l-6 6 6 6"/>
            </svg>
            ບົດສູດມົນທັງໝົດ
        </a>

        <div class="flex items-center gap-2 mb-3">
            @if ($chant->category)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600">
                    {{ $chant->category->name }}
                </span>
            @endif
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 leading-snug mb-6">{{ $chant->title }}</h1>

        <div class="bg-white rounded-3xl card-shadow border border-black/5 p-6 sm:p-10">
            <div class="prose prose-slate max-w-none text-base sm:text-lg leading-loose">{!! $chant->content_html !!}</div>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-16 pt-8 border-t border-black/5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-5">ບົດສູດມົນອື່ນໆ</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @foreach ($related as $item)
                        <a href="{{ route('chants.public.show', $item->slug) }}"
                           class="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col p-4">
                            <span class="w-8 h-8 rounded-lg bg-brand-light-green flex items-center justify-center mb-2">
                                <span class="text-sm text-brand-green">☸</span>
                            </span>
                            <h4 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-brand-green transition-colors">
                                {{ $item->title }}
                            </h4>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </article>

</x-layouts.public>
