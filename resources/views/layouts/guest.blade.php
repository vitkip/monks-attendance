<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — ເຂົ້າລະບົບ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans min-h-screen bg-[#faf8f2] text-slate-700 antialiased">

    <div class="min-h-screen lg:grid lg:grid-cols-[minmax(0,7fr)_minmax(0,8fr)]">

        {{-- Brand panel — temple identity --}}
        <div class="relative overflow-hidden bg-brand-green-dark text-white flex flex-col justify-between
                    px-6 py-8 sm:px-10 sm:py-10 lg:px-14 lg:py-14 lg:min-h-screen">

            {{-- Dawn glow rising behind the temple roofline --}}
            <div class="glow-dawn absolute left-1/2 bottom-10 sm:bottom-14 lg:bottom-20 -translate-x-1/2 w-[420px] h-[420px] rounded-full pointer-events-none" aria-hidden="true"></div>

            {{-- Brand mark, doubles as a way back to the public site --}}
            <a href="{{ route('news.public.index') }}" class="relative z-10 flex items-center gap-3 group w-fit">
                @php $logoPath = \App\Models\Setting::get('logo'); @endphp
                @if($logoPath)
                    <span class="w-10 h-10 rounded-xl overflow-hidden bg-white/10 backdrop-blur-md flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-full h-full object-contain p-1">
                    </span>
                @else
                    <span class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center flex-shrink-0">
                        <span class="text-xl select-none">☸</span>
                    </span>
                @endif
                <span class="leading-tight">
                    <span class="block text-sm font-bold group-hover:text-brand-bright-green transition-colors">ວັດປ່າໜອງບົວທອງໃຕ້</span>
                    <span class="block text-[10px] text-white/50 uppercase tracking-widest group-hover:text-white/70 transition-colors">← ກັບໜ້າຫຼັກ</span>
                </span>
            </a>

            {{-- Heading + what the system actually covers --}}
            <div class="relative z-10 mt-8 lg:mt-0">
                <p class="text-[11px] font-bold text-brand-bright-green uppercase tracking-[0.25em] mb-3">Monk Attendance System</p>
                <h1 class="text-2xl sm:text-3xl font-bold leading-snug mb-5 max-w-xs">ລະບົບກວດສອບ<br>ການຂາດລາ</h1>
                <ul class="hidden sm:flex flex-wrap gap-x-4 gap-y-2 text-xs text-white/60">
                    <li class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-brand-bright-green"></span>ພຣະສົງ</li>
                    <li class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-brand-bright-green"></span>ສາມະເນນ</li>
                    <li class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-brand-bright-green"></span>ການຂາດລາ</li>
                    <li class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-brand-bright-green"></span>ຄ່າປັບ</li>
                </ul>
            </div>

            {{-- Signature: layered temple roofline catching first light at the finial --}}
            <svg class="relative z-10 w-full max-w-[340px] mx-auto lg:mx-0 h-auto mt-8 lg:mt-0" viewBox="0 0 400 170" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                <defs>
                    <linearGradient id="roofGrad" x1="0" y1="170" x2="0" y2="0" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#8f4e00"/>
                        <stop offset="1" stop-color="#ff9933"/>
                    </linearGradient>
                </defs>
                <polygon points="10,170 390,170 340,140 60,140" fill="url(#roofGrad)" opacity="0.5"/>
                <polygon points="60,140 340,140 295,112 105,112" fill="url(#roofGrad)" opacity="0.68"/>
                <polygon points="105,112 295,112 258,86 142,86" fill="url(#roofGrad)" opacity="0.84"/>
                <polygon points="142,86 258,86 228,60 172,60" fill="url(#roofGrad)"/>
                <rect x="197" y="24" width="6" height="38" fill="url(#roofGrad)"/>
                <circle cx="200" cy="18" r="7" fill="url(#roofGrad)"/>
            </svg>
        </div>

        {{-- Form panel --}}
        <div class="flex flex-col justify-center px-6 py-10 sm:px-12 lg:px-16 xl:px-24">
            <div class="w-full max-w-sm mx-auto">
                {{ $slot }}
            </div>
        </div>

    </div>

    @livewireScripts
</body>
</html>
