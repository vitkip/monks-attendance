<!DOCTYPE html>
<html lang="lo">
@php
    // Server-rendered SEO / Open Graph tags. The client-side <SeoHead> handles
    // in-app navigation, but social crawlers (Facebook, LINE, …) don't run JS,
    // so a shared link relies entirely on what's in this initial HTML.
    // Controllers override per page via ->withViewData('meta', …).
    $meta = array_merge(\App\Support\PageMeta::defaults(), $meta ?? []);
    $metaImage = \Illuminate\Support\Str::startsWith($meta['image'], ['http://', 'https://'])
        ? $meta['image']
        : url($meta['image']);
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="ວັດປ່າໜອງບົວທອງໃຕ້, ວັດປ່າໜອງບົວທອງ, ວັດໜອງບົວທອງ, ພຣະສົງ, ສາມະເນນ, ແມ່ຂາວ, ບວດຂາວ, ໜອງບົວທອງ, ບຸນ, ບຸນປະເພນີ, ພຸດທະສາສະໜາ, ພຣະພຸດທະສາສະໜາ, ປະເທດລາວ, ການປະຕິບັດທຳ, ກຳມະຖານ, ນັ່ງສະມາທິ, ໄຫວ້ພຣະ, ບົດສູດມົນ, ທຳມະ, ວັດລາວ, ເມືອງສີໂຄດຕະບອງ, ນະຄອນຫຼວງວຽງຈັນ, Wat Pa Nongbuathong Tai">
    <meta name="author" content="ວັດປ່າໜອງບົວທອງໃຕ້">
    <meta name="theme-color" content="#1b3e2b">
    <title inertia>{{ $meta['title'] }}</title>

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $meta['type'] }}">
    <meta property="og:site_name" content="{{ \App\Support\PageMeta::SITE_NAME }}">
    <meta property="og:locale" content="lo_LA">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:image:secure_url" content="{{ $metaImage }}">
    <meta property="og:image:alt" content="{{ $meta['title'] }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta['title'] }}">
    <meta name="twitter:description" content="{{ $meta['description'] }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath:wght@400;700&display=swap" rel="stylesheet">
    @routes
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
