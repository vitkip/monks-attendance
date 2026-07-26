<!DOCTYPE html>
<html lang="lo">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath:wght@400;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-[#f1f3f4] font-sans text-slate-700">

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        {{-- Sidebar backdrop (mobile) --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-20 lg:hidden" style="display: none"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed top-0 left-0 h-full w-64 z-30 flex flex-col
                  bg-[#f8fafa] border-r border-gray-200
                  transition-transform duration-300 ease-in-out">

            {{-- Brand --}}
            <div class="flex items-center gap-3 px-6 py-6">
                @php $logoPath = \App\Models\Setting::get('logo'); @endphp
                @if($logoPath)
                    <div class="w-9 h-9 flex-shrink-0 rounded-lg overflow-hidden bg-brand-green flex items-center justify-center">
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                    </div>
                @else
                    <div x-data="{ spinning: false }" @mouseenter="spinning = true" @mouseleave="spinning = false"
                        class="w-9 h-9 flex-shrink-0 rounded-lg bg-brand-green flex items-center justify-center">
                        <span :class="spinning ? 'animate-spin' : ''" class="inline-block text-white text-base select-none cursor-default"
                            style="animation-duration: 2.5s">☸</span>
                    </div>
                @endif
                <div class="min-w-0 leading-tight">
                    <div class="text-slate-800 font-bold text-sm truncate">ລະບົບບັນທຶກການຂາດລາ</div>
                    <div class="text-gray-400 text-[10px] uppercase tracking-widest mt-0.5">Monk Attendance</div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 pb-4 space-y-1 overflow-y-auto">

                @auth

                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 px-4">ເມນູ</p>

                    {{-- Staff + Admin --}}
                    <a href="{{ route('absences.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('absences.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>ກວດສອບການຂາດລາ</span>
                    </a>

                    <a href="{{ route('balance.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('balance.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>ຍອດຄ້າງຊຳລະ</span>
                    </a>

                    {{-- Admin only --}}
                    @if(auth()->user()->isAdmin())

                        <a href="{{ route('electricity-bills.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('electricity-bills.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>ແຈ້ງບິນຄ່າໄຟຟ້າ</span>
                        </a>

                        <a href="{{ route('construction-projects.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('construction-projects.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 21h18M4 21V8l8-5 8 5v13M9 21v-6h6v6M9 12h.01M15 12h.01M9 9h.01M15 9h.01" />
                            </svg>
                            <span>ໂຄງການກໍ່ສ້າງ</span>
                        </a>

                        <a href="{{ route('news.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('news.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z" />
                            </svg>
                            <span>ຂ່າວສານ</span>
                        </a>

                        <a href="{{ route('chants.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('chants.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.253v13M12 6.253C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>ບົດສູດມົນ</span>
                        </a>

                        {{-- Content management: hero slides, news & chant categories --}}
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 mt-6 px-4">ຈັດການເນື້ອຫາ</p>

                        <a href="{{ route('hero-slides.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('hero-slides.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" />
                            </svg>
                            <span>ສະໄລ້ Hero</span>
                        </a>

                        <a href="{{ route('news-categories.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('news-categories.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 7h.01M7 3h5.586a1 1 0 01.707.293l6.414 6.414a1 1 0 010 1.414l-8.586 8.586a1 1 0 01-1.414 0l-6.414-6.414A1 1 0 013 12.586V7a4 4 0 014-4z" />
                            </svg>
                            <span>ໝວດໝູ່ຂ່າວ</span>
                        </a>

                        <a href="{{ route('chant-categories.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('chant-categories.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 7h.01M7 3h5.586a1 1 0 01.707.293l6.414 6.414a1 1 0 010 1.414l-8.586 8.586a1 1 0 01-1.414 0l-6.414-6.414A1 1 0 013 12.586V7a4 4 0 014-4z" />
                            </svg>
                            <span>ໝວດໝູ່ບົດສູດມົນ</span>
                        </a>

                        {{-- Monk management: monk records & duty schedules --}}
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 mt-6 px-4">ຈັດການພຣະສົງ</p>

                        <a href="{{ route('monks.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('monks.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>ພຣະສົງ/ສາມະເນນ</span>
                        </a>

                        <a href="{{ route('duty-schedules.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('duty-schedules.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>ໜ້າທີ່ປະຈຳວັນ</span>
                        </a>

                        {{-- System: fines, users, settings --}}
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 mt-6 px-4">ລະບົບ</p>

                        <a href="{{ route('fine-rates.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('fine-rates.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>ອັດຕາຄ່າປັບ</span>
                        </a>

                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('users.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>ຈັດການຜູ້ໃຊ້</span>
                        </a>

                        <a href="{{ route('settings.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('settings.*')
        ? 'sidebar-item-active'
        : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <span>ຕັ້ງຄ່າລະບົບ</span>
                        </a>

                    @endif

                @endauth

            </nav>

            {{-- Account card --}}
            @auth
                <div class="p-4 mt-auto">
                    <div class="bg-brand-green-dark text-white p-5 rounded-3xl relative overflow-hidden">
                        <div class="relative z-10">
                            <a href="{{ route('profile.show') }}" @click="sidebarOpen = false" class="flex items-center gap-3 group">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-white/10">
                                    <svg class="w-5 h-5 text-brand-bright-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold truncate group-hover:text-brand-bright-green transition-colors">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ auth()->user()->isAdmin() ? 'Admin' : 'Staff' }}</p>
                                </div>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-[11px] font-bold py-2.5 rounded-xl transition-colors">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    ອອກຈາກລະບົບ
                                </button>
                            </form>
                        </div>
                        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-brand-bright-green rounded-full opacity-20 blur-2xl"></div>
                    </div>
                </div>
            @endauth

        </aside>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-h-screen lg:ml-64">

            {{-- Mobile top bar --}}
            <header class="lg:hidden sticky top-0 z-10 flex items-center justify-between h-14 px-4 bg-white border-b border-gray-200">
                <button @click="sidebarOpen = true"
                    class="text-gray-500 hover:text-brand-green transition-colors p-1.5 -ml-1.5 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-slate-800 font-bold text-sm">ລະບົບບັນທຶກການຂາດລາ</span>
                @if($logoPath ?? false)
                    <div class="w-7 h-7 rounded-md overflow-hidden bg-brand-green flex items-center justify-center">
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                    </div>
                @else
                    <div class="w-7 h-7 rounded-md bg-brand-green flex items-center justify-center">
                        <span class="text-white text-sm select-none">☸</span>
                    </div>
                @endif
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

        </div>

    </div>

    {{-- Toast notifications --}}
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="pointer-events-auto flex items-center gap-3 px-4 py-3 min-w-[260px]
                        bg-white border-l-4 border-brand-green rounded-2xl
                        card-shadow shadow-xl text-brand-green text-sm font-medium">
                <span class="text-brand-green font-bold text-base leading-none flex-shrink-0">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="pointer-events-auto flex items-center gap-3 px-4 py-3 min-w-[260px]
                        bg-white border-l-4 border-red-500 rounded-2xl
                        card-shadow shadow-xl text-red-600 text-sm font-medium">
                <span class="text-red-500 font-bold text-base leading-none flex-shrink-0">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    @livewireScripts
</body>

</html>
