@php
    $seoSiteName = config('app.name');
    $seoTitle = ($title ?? $seoSiteName) . ' — ' . $seoSiteName;
    $seoDescription = $description ?? 'ວັດປ່າໜອງບົວທອງໃຕ້ — ຂ່າວສານ, ຂໍ້ມູນພຣະສົງ ແລະ ສາມະເນນ, ບົດສູດມົນ, ແລະ ໂຄງການກໍ່ສ້າງພາຍໃນວັດ';
    $seoImage = $image ?? (\App\Models\Setting::get('logo') ? asset('storage/' . \App\Models\Setting::get('logo')) : asset('favicon-512x512.png'));
    $seoUrl = url()->current();

    $contactWhatsapp = \App\Models\Setting::get('contact_whatsapp');
    $contactFacebook = \App\Models\Setting::get('contact_facebook');
    $contactEmail = \App\Models\Setting::get('contact_email');
    $contactYoutube = \App\Models\Setting::get('contact_youtube');
    $sameAs = array_values(array_filter([$contactFacebook, $contactYoutube]));
@endphp
<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ $seoUrl }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $seoSiteName }}">
<meta property="og:locale" content="lo_LA">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<script type="application/ld+json">
@php
    $ldJson = array_filter([
        '@' . 'context' => 'https://schema.org',
        '@' . 'type' => 'Organization',
        'name' => $seoSiteName,
        'url' => url('/'),
        'logo' => $seoImage,
        'email' => $contactEmail ?: null,
        'telephone' => $contactWhatsapp ?: null,
        'sameAs' => $sameAs ?: null,
    ]);
@endphp
{!! json_encode($ldJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
