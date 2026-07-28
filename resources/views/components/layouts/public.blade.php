<!DOCTYPE html>
<html lang="lo">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ຂ່າວສານ' }} — {{ config('app.name') }}</title>
    @include('partials.seo-meta', ['title' => $title ?? null, 'description' => $description ?? null, 'image' => $image ?? null])
    @include('partials.favicon')
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
    @php
        $footerWhatsapp = \App\Models\Setting::get('contact_whatsapp');
        $footerFacebook = \App\Models\Setting::get('contact_facebook');
        $footerEmail = \App\Models\Setting::get('contact_email');
        $footerYoutube = \App\Models\Setting::get('contact_youtube');
    @endphp
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

            @if($footerWhatsapp || $footerFacebook || $footerEmail || $footerYoutube)
                <div class="flex items-center gap-3 shrink-0">
                    @if($footerWhatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $footerWhatsapp) }}" target="_blank" rel="noopener"
                            aria-label="WhatsApp" class="w-8 h-8 rounded-full bg-[#f8fafa] flex items-center justify-center text-slate-400 hover:text-brand-green hover:bg-brand-light-green transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 004.74 1.2h.005c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.87 9.87 0 0012.04 2zm5.8 14.05c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.95-.31-1.64-.6-2.88-1.24-4.76-4.14-4.9-4.33-.14-.19-1.17-1.56-1.17-2.98 0-1.42.74-2.11 1-2.4.26-.29.57-.36.76-.36.19 0 .38 0 .55.01.18.01.41-.07.64.49.24.58.81 2 .88 2.15.07.15.12.32.02.51-.09.19-.14.31-.28.48-.14.17-.29.37-.42.5-.14.14-.28.29-.12.57.16.29.72 1.19 1.55 1.93 1.07.95 1.96 1.25 2.25 1.39.29.14.46.12.63-.07.17-.19.71-.83.9-1.11.19-.29.38-.24.63-.14.26.1 1.65.78 1.93.92.29.14.48.21.55.33.07.12.07.68-.17 1.36z"/></svg>
                        </a>
                    @endif
                    @if($footerFacebook)
                        <a href="{{ $footerFacebook }}" target="_blank" rel="noopener"
                            aria-label="Facebook" class="w-8 h-8 rounded-full bg-[#f8fafa] flex items-center justify-center text-slate-400 hover:text-brand-green hover:bg-brand-light-green transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.18 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.24.2 2.24.2v2.47h-1.26c-1.24 0-1.63.78-1.63 1.57v1.88h2.78l-.44 2.91h-2.34V22c4.78-.76 8.44-4.92 8.44-9.94z"/></svg>
                        </a>
                    @endif
                    @if($footerYoutube)
                        <a href="{{ $footerYoutube }}" target="_blank" rel="noopener"
                            aria-label="YouTube" class="w-8 h-8 rounded-full bg-[#f8fafa] flex items-center justify-center text-slate-400 hover:text-brand-green hover:bg-brand-light-green transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.19a3.02 3.02 0 00-2.12-2.14C19.51 3.5 12 3.5 12 3.5s-7.51 0-9.38.55A3.02 3.02 0 00.5 6.19 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.81 3.02 3.02 0 002.12 2.14c1.87.55 9.38.55 9.38.55s7.51 0 9.38-.55a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.81zM9.6 15.5v-7l6.2 3.5-6.2 3.5z"/></svg>
                        </a>
                    @endif
                    @if($footerEmail)
                        <a href="mailto:{{ $footerEmail }}"
                            aria-label="Email" class="w-8 h-8 rounded-full bg-[#f8fafa] flex items-center justify-center text-slate-400 hover:text-brand-green hover:bg-brand-light-green transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </a>
                    @endif
                </div>
            @endif

            <span class="text-xs text-slate-400 shrink-0">&copy; {{ date('Y') }} {{ config('app.name') }}</span>
        </div>
    </footer>

</body>

</html>