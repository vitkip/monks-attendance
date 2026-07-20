<x-layouts.public :title="'ບົດສູດມົນ'">

    <div class="max-w-5xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

        <div class="mb-10">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຄຳສອນ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ບົດສູດມົນ</h1>
            <p class="text-slate-400 text-sm mt-1">ລວມບົດສູດມົນສຳລັບການປະຕິບັດທຳ</p>
        </div>

        @if ($categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-10">
                <a href="{{ route('chants.public.index') }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors
                          {{ ! $categorySlug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                    ທັງໝົດ
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('chants.public.index', ['category' => $category->slug]) }}"
                       class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors
                              {{ $categorySlug === $category->slug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                        {{ str_repeat('— ', $category->depth) }}{{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($chants->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-24">
                <span class="text-5xl text-brand-green/30 mb-4">☸</span>
                <p class="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີບົດສູດມົນ</p>
                <p class="text-slate-400 text-sm">ກະລຸນາກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($chants as $item)
                    <a href="{{ route('chants.public.show', $item->slug) }}"
                       class="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col p-5 transition-all hover:shadow-lg">
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
                        <h3 class="font-bold text-slate-800 text-base leading-snug group-hover:text-brand-green transition-colors">
                            {{ $item->title }}
                        </h3>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed flex-1 line-clamp-3">
                            {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 120) }}
                        </p>
                    </a>
                @endforeach
            </div>

            @if ($chants->hasPages())
                <div class="mt-10">{{ $chants->links() }}</div>
            @endif
        @endif

    </div>

</x-layouts.public>
