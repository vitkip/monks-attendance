<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="description" content="ເວັບໄຊທາງການ ວັດປ່າໜອງບົວທອງໃຕ້ — ພຣະສົງ, ໜອງບົວທອງ, ບຸນ, ພຸດທະສາສະໜາ, ປະເທດລາວ, ການປະຕິບັດທຳ, ກຳມະຖານ, ບວດຂາວ, ໄຫວ້ພຣະ ແລະ ບົດສູດມົນ">
    <meta name="keywords" content="ວັດປ່າໜອງບົວທອງໃຕ້, ວັດປ່າໜອງບົວທອງ, ວັດໜອງບົວທອງ, ພຣະສົງ, ສາມະເນນ, ແມ່ຂາວ, ບວດຂາວ, ໜອງບົວທອງ, ບຸນ, ບຸນປະເພນີ, ພຸດທະສາສະໜາ, ພຣະພຸດທະສາສະໜາ, ປະເທດລາວ, ການປະຕິບັດທຳ, ກຳມະຖານ, ນັ່ງສະມາທິ, ໄຫວ້ພຣະ, ບົດສູດມົນ, ທຳມະ, ວັດລາວ, ເມືອງສີໂຄດຕະບອງ, ນະຄອນຫຼວງວຽງຈັນ, Wat Pa Nongbuathong Tai">
    <meta name="author" content="ວັດປ່າໜອງບົວທອງໃຕ້">
    <meta name="theme-color" content="#1b3e2b">
    <title inertia>{{ config('app.name') }}</title>
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
