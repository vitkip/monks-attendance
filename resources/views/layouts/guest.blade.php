<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — ເຂົ້າລະບົບ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-brand-tracker font-sans min-h-screen flex items-center justify-center relative overflow-hidden">

    {{-- Ambient background waves, echoing the tracker widget motif --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <svg class="w-full h-full scale-150 -rotate-12" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,200 Q100,50 200,200 T400,200" fill="none" opacity="0.3" stroke="white" stroke-width="20" />
            <path d="M0,220 Q100,70 200,220 T400,220" fill="none" opacity="0.2" stroke="white" stroke-width="15" />
            <path d="M0,240 Q100,90 200,240 T400,240" fill="none" opacity="0.1" stroke="white" stroke-width="10" />
        </svg>
    </div>

    <div class="relative z-10 w-full">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
