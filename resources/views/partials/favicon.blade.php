@php
    $faviconLogo = \App\Models\Setting::get('logo');
@endphp
@if($faviconLogo)
    <link rel="icon" href="{{ asset('storage/' . $faviconLogo) }}">
    <link rel="apple-touch-icon" href="{{ asset('storage/' . $faviconLogo) }}">
@else
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
@endif
