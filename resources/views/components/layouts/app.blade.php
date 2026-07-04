<!DOCTYPE html>
<html lang="lo">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Lao:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-[#FAF6EE] font-sans">

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        {{-- Sidebar backdrop (mobile) --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/60 z-20 lg:hidden" style="display: none"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed top-0 left-0 h-full w-56 z-30 flex flex-col
                  bg-[#1A0A02] border-r border-[#D4A017]/20
                  transition-transform duration-300 ease-in-out">

            {{-- Brand --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-[#D4A017]/20">
                @php $logoPath = \App\Models\Setting::get('logo'); @endphp
                @if($logoPath)
                    <div class="w-9 h-9 flex-shrink-0 rounded-lg overflow-hidden border border-[#D4A017]/30 bg-white/5 flex items-center justify-center">
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                    </div>
                @else
                    <span x-data="{ spinning: false }" @mouseenter="spinning = true" @mouseleave="spinning = false"
                        :class="spinning ? 'animate-spin' : ''"
                        class="inline-block text-[22px] select-none cursor-default flex-shrink-0"
                        style="animation-duration: 2.5s">☸️</span>
                @endif
                <div class="min-w-0 leading-tight">
                    <div class="text-[#E8C97A] font-semibold text-sm truncate tracking-wide">ລະບົບບັນທຶກການຂາດລາ</div>
                    <div class="text-[#3A1C05] text-[9px] uppercase tracking-[0.18em] mt-0.5">Monk Attendance</div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

                @auth

                    {{-- Staff + Admin --}}
                    <a href="{{ route('absences.index') }}" @click="sidebarOpen = false" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('absences.*')
        ? 'bg-[#D4A017]/12 text-[#E8C97A]'
        : 'text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5' }}">
                        <svg class="w-[17px] h-[17px] flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>ກວດສອບການຂາດລາ</span>
                    </a>

                    <a href="{{ route('balance.index') }}" @click="sidebarOpen = false" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('balance.*')
        ? 'bg-[#D4A017]/12 text-[#E8C97A]'
        : 'text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5' }}">
                        <svg class="w-[17px] h-[17px] flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>ຍອດຄ້າງຊຳລະ</span>
                    </a>

                    <a href="{{ route('custom-webpage-design') }}" @click="sidebarOpen = false" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('custom-webpage-design*')
        ? 'bg-[#D4A017]/12 text-[#E8C97A]'
        : 'text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5' }}">
                        <svg class="w-[17px] h-[17px] flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Custom Webpage Design</span>
                    </a>

                    {{-- Admin only --}}
                    @if(auth()->user()->isAdmin())

                        <div class="px-3 pt-3 pb-1">
                            <p class="text-[#2A1205] text-[9px] uppercase tracking-[0.25em] font-semibold">ຈັດການລະບົບ</p>
                        </div>

                        <a href="{{ route('monks.index') }}" @click="sidebarOpen = false" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('monks.*')
        ? 'bg-[#D4A017]/12 text-[#E8C97A]'
        : 'text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5' }}">
                            <svg class="w-[17px] h-[17px] flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>ພຣະສົງ/ສາມະເນນ</span>
                        </a>

                        <a href="{{ route('fine-rates.index') }}" @click="sidebarOpen = false" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('fine-rates.*')
        ? 'bg-[#D4A017]/12 text-[#E8C97A]'
        : 'text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5' }}">
                            <svg class="w-[17px] h-[17px] flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>ອັດຕາຄ່າປັບ</span>
                        </a>

                        <a href="{{ route('duty-schedules.index') }}" @click="sidebarOpen = false" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('duty-schedules.*')
        ? 'bg-[#D4A017]/12 text-[#E8C97A]'
        : 'text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5' }}">
                            <svg class="w-[17px] h-[17px] flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>ໜ້າທີ່ປະຈຳວັນ</span>
                        </a>

                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('users.*')
        ? 'bg-[#D4A017]/12 text-[#E8C97A]'
        : 'text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5' }}">
                            <svg class="w-[17px] h-[17px] flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>ຈັດການຜູ້ໃຊ້</span>
                        </a>

                        <a href="{{ route('settings.index') }}" @click="sidebarOpen = false" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('settings.*')
        ? 'bg-[#D4A017]/12 text-[#E8C97A]'
        : 'text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5' }}">
                            <svg class="w-[17px] h-[17px] flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <span>ຕັ້ງຄ່າລະບົບ</span>
                        </a>

                    @endif

                @endauth

            </nav>

            {{-- Sidebar footer: user info + profile + logout --}}
            <div class="px-4 py-4 border-t border-[#D4A017]/15 space-y-2">
                @auth
                    <a href="{{ route('profile.show') }}" @click="sidebarOpen = false" class="flex items-center gap-2.5 min-w-0 px-2 py-1.5 rounded-lg
                          hover:bg-white/5 transition-all duration-150 group
                          {{ request()->routeIs('profile.*') ? 'bg-[#D4A017]/12' : '' }}">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0
                                {{ auth()->user()->isAdmin() ? 'bg-[#D4A017]/20' : 'bg-white/8' }}">
                            <svg class="w-3.5 h-3.5 {{ auth()->user()->isAdmin() ? 'text-[#D4A017]' : 'text-[#7A5A30]' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-[#C9A96A] text-xs font-medium truncate group-hover:text-[#E8C97A] transition-colors">
                                {{ auth()->user()->name }}</p>
                            <p class="text-[#3A1C05] text-[10px] truncate">
                                {{ auth()->user()->isAdmin() ? 'Admin' : 'Staff' }}
                            </p>
                        </div>
                        <svg class="w-3 h-3 text-[#3A1C05] group-hover:text-[#7A5A30] flex-shrink-0 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                   text-[#7A5A30] hover:text-[#C9A96A] hover:bg-white/5 transition-all duration-150">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            ອອກຈາກລະບົບ
                        </button>
                    </form>
                @endauth
            </div>

        </aside>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-h-screen lg:ml-56">

            {{-- Mobile top bar --}}
            <header class="lg:hidden sticky top-0 z-10 flex items-center justify-between h-14 px-4 bg-[#1A0A02]"
                style="box-shadow: 0 1px 0 0 #D4A017, 0 4px 16px rgba(0,0,0,0.4)">
                <button @click="sidebarOpen = true"
                    class="text-[#7A5A30] hover:text-[#E8C97A] transition-colors p-1.5 -ml-1.5 rounded-lg hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-[#E8C97A] font-semibold text-sm tracking-wider">ລະບົບບັນທຶກການຂາດລາ</span>
                @if($logoPath ?? false)
                    <div class="w-7 h-7 rounded-md overflow-hidden border border-[#D4A017]/30 bg-white/5 flex items-center justify-center">
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                    </div>
                @else
                    <span class="text-xl select-none">☸️</span>
                @endif
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-6">
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
                        bg-white border-l-4 border-[#2D7A3A] rounded-r-lg
                        shadow-xl shadow-black/10 text-[#1A4A24] text-sm">
                <span class="text-[#2D7A3A] font-bold text-base leading-none flex-shrink-0">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="pointer-events-auto flex items-center gap-3 px-4 py-3 min-w-[260px]
                        bg-white border-l-4 border-[#B83030] rounded-r-lg
                        shadow-xl shadow-black/10 text-[#7A1F1F] text-sm">
                <span class="text-[#B83030] font-bold text-base leading-none flex-shrink-0">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    @livewireScripts
</body>

</html>