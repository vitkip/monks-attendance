<!DOCTYPE html>
<html lang="lo">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ຂ່າວສານ' }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#faf8f2] font-sans text-slate-700 min-h-screen flex flex-col">

    {{-- Top bar --}}
    <header class="bg-brand-green-dark text-white sticky top-0 z-20">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('news.public.index') }}" class="flex items-center gap-3 group shrink-0">
                <span class="w-9 h-9 rounded-lg bg-brand-green flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-base select-none">☸</span>
                </span>
                <span class="leading-tight hidden sm:block">
                    <span
                        class="block text-sm font-bold group-hover:text-brand-bright-green transition-colors">ວັດປ່າໜອງບົວທອງໃຕ້</span>
                    <span class="block text-[10px] text-white/50 uppercase tracking-widest">ວັດ · ຂ່າວສານຊຸມຊົນ</span>
                </span>
            </a>

            <nav class="flex items-center gap-1 overflow-x-auto no-scrollbar">
                <a href="{{ route('news.public.index') }}"
                    class="shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                          {{ request()->routeIs('news.public.*') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white' }}">
                    ຂ່າວສານ
                </a>
                <a href="{{ route('monks.public.index') }}"
                    class="shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                          {{ request()->routeIs('monks.public.*') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white' }}">
                    ພຣະສົງ
                </a>
                <a href="{{ route('chants.public.index') }}"
                    class="shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                          {{ request()->routeIs('chants.public.*') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white' }}">
                    ບົດສູດມົນ
                </a>
                <a href="{{ route('electricity-bills.public.index') }}"
                    class="shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                          {{ request()->routeIs('electricity-bills.public.*') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white' }}">
                    ລາຍການຄ່າໄຟຟ້າ
                </a>
                <a href="{{ route('construction-projects.public.index') }}"
                    class="shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                          {{ request()->routeIs('construction-projects.public.*') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white' }}">
                    ໂຄງການກໍ່ສ້າງ
                </a>
            </nav>

            <a href="{{ route('login') }}"
                class="text-xs font-semibold text-white/70 hover:text-white transition-colors flex items-center gap-1.5 shrink-0">
                ເຂົ້າສູ່ລະບົບ
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 4l6 6-6 6" />
                </svg>
            </a>
        </div>
        <div class="h-[3px] bg-gradient-to-r from-transparent via-brand-bright-green/70 to-transparent"
            aria-hidden="true"></div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-black/5 mt-16">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-6">
            <a href="{{ route('news.public.index') }}" class="flex items-center gap-3 shrink-0">
                <span class="w-9 h-9 rounded-lg bg-brand-green flex items-center justify-center">
                    <span class="text-white text-base select-none">☸</span>
                </span>
                <span class="leading-tight">
                    <span class="block text-sm font-bold text-slate-700">ວັດປ່າໜອງບົວທອງໃຕ້</span>
                    <span class="block text-[11px] text-slate-400">ວັດ · ຂ່າວສານຊຸມຊົນ</span>
                </span>
            </a>

            <nav class="flex items-center gap-5 text-xs font-semibold text-slate-500">
                <a href="{{ route('news.public.index') }}" class="hover:text-brand-green transition-colors">ຂ່າວສານ</a>
                <a href="{{ route('monks.public.index') }}" class="hover:text-brand-green transition-colors">ພຣະສົງ</a>
                <a href="{{ route('chants.public.index') }}"
                    class="hover:text-brand-green transition-colors">ບົດສູດມົນ</a>
                <a href="{{ route('electricity-bills.public.index') }}"
                    class="hover:text-brand-green transition-colors">ຄ່າໄຟຟ້າ</a>
                <a href="{{ route('construction-projects.public.index') }}"
                    class="hover:text-brand-green transition-colors">ກໍ່ສ້າງ</a>
            </nav>

            <span class="text-xs text-slate-400 shrink-0">&copy; {{ date('Y') }} {{ config('app.name') }}</span>
        </div>
    </footer>

</body>

</html>