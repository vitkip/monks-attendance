<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລາຍງານໜ້າທີ່ຮັບຜິດຊອບ — {{ $generatedAt->format('d/m/Y') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ─── Reset & base ─────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Noto Serif Lao', serif;
            font-size: 10.5pt;
            color: #1A0A02;
            line-height: 1.55;
            background: #F5F0E8;
        }

        /* ─── Screen wrapper ────────────────────────────────── */
        @media screen {
            .report-page {
                max-width: 960px;
                margin: 0 auto;
                padding: 24px 16px 60px;
            }
            .report-doc {
                background: white;
                box-shadow: 0 4px 32px rgba(0,0,0,0.14);
                border-radius: 10px;
                padding: 48px 52px;
            }
        }

        /* ─── Print ─────────────────────────────────────────── */
        @page {
            size: A4 portrait;
            margin: 1.6cm 2cm 2cm;
        }

        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .report-page { padding: 0; }
            .report-doc { box-shadow: none; border-radius: 0; padding: 0; }
            .page-break { break-before: page; }
            .avoid-break { break-inside: avoid; }
            tr { break-inside: avoid; }
        }

        /* ─── Controls bar (screen only) ────────────────────── */
        .controls-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            background: #1A0A02;
            border-radius: 10px;
            padding: 14px 20px;
            margin-bottom: 20px;
        }
        .controls-bar label {
            color: #C9A96A;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .controls-bar select,
        .controls-bar input[type="date"] {
            background: #2D1206;
            border: 1px solid rgba(212,160,23,0.30);
            color: #E8C97A;
            border-radius: 6px;
            padding: 6px 10px;
            font-family: inherit;
            font-size: 10pt;
            outline: none;
        }
        .controls-bar select:focus,
        .controls-bar input[type="date"]:focus {
            border-color: rgba(212,160,23,0.70);
        }
        .btn-filter {
            background: #2D1206;
            border: 1px solid rgba(212,160,23,0.40);
            color: #E8C97A;
            border-radius: 6px;
            padding: 7px 16px;
            font-family: inherit;
            font-size: 10pt;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-filter:hover { background: #3D1A08; }
        .btn-print {
            background: #D4A017;
            border: none;
            color: #1A0A02;
            border-radius: 6px;
            padding: 7px 20px;
            font-family: inherit;
            font-size: 10pt;
            font-weight: 700;
            cursor: pointer;
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: background 0.15s;
        }
        .btn-print:hover { background: #E8B520; }
        .btn-back {
            background: transparent;
            border: 1px solid rgba(212,160,23,0.25);
            color: #9B7A5A;
            border-radius: 6px;
            padding: 7px 14px;
            font-family: inherit;
            font-size: 10pt;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back:hover { border-color: rgba(212,160,23,0.50); color: #C9A96A; }
        .ctrl-sep {
            color: rgba(212,160,23,0.30);
            font-size: 16pt;
        }

        /* ─── Report header ─────────────────────────────────── */
        .rpt-header {
            border-bottom: 2px solid #D4A017;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }
        .rpt-header-inner {
            display: flex;
            align-items: flex-start;
            gap: 18px;
        }
        .rpt-emblem {
            font-size: 42pt;
            line-height: 1;
            flex-shrink: 0;
        }
        .rpt-title-block { flex: 1; }
        .rpt-meta-block {
            text-align: right;
            flex-shrink: 0;
            font-size: 9pt;
            color: #9B7A5A;
            line-height: 1.8;
        }
        .rpt-label {
            font-size: 8pt;
            font-weight: 600;
            color: #D4A017;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            margin-bottom: 3px;
        }
        .rpt-title {
            font-size: 20pt;
            font-weight: 700;
            color: #1A0A02;
            line-height: 1.2;
        }
        .rpt-subtitle {
            font-size: 10pt;
            color: #6B4A2A;
            margin-top: 4px;
        }
        .rpt-range-badge {
            display: inline-block;
            margin-top: 8px;
            font-size: 9pt;
            color: #6B4A2A;
            background: rgba(212,160,23,0.10);
            border: 1px solid rgba(212,160,23,0.30);
            border-radius: 4px;
            padding: 3px 10px;
        }

        /* ─── Stats row ─────────────────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }
        .stat-box {
            border-radius: 8px;
            padding: 14px 16px;
            border: 1px solid rgba(212,160,23,0.25);
            background: #FFFDF7;
        }
        .stat-label {
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 4px;
        }
        .stat-sub { font-size: 9pt; color: #9B7A5A; margin-bottom: 6px; }
        .stat-num {
            font-size: 22pt;
            font-weight: 700;
            line-height: 1;
        }
        .stat-unit { font-size: 9pt; color: #9B7A5A; margin-left: 4px; }

        /* ─── Notice ─────────────────────────────────────────── */
        .notice-bar {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(212,160,23,0.08);
            border: 1px solid rgba(212,160,23,0.28);
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 28px;
            font-size: 9.5pt;
            color: #6B4A2A;
        }
        .notice-icon { color: #D4A017; flex-shrink: 0; font-size: 13pt; line-height: 1.3; }

        /* ─── Section header ────────────────────────────────── */
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .section-icon {
            width: 34px; height: 34px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16pt; line-height: 1;
            flex-shrink: 0;
        }
        .section-title { font-size: 13pt; font-weight: 700; line-height: 1.2; }
        .section-sub { font-size: 9pt; opacity: 0.70; }
        .section-line {
            flex: 1;
            height: 1px;
        }
        .section-count {
            font-size: 9pt;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        /* ─── Day block ─────────────────────────────────────── */
        .day-block { margin-bottom: 20px; }
        .avoid-break { break-inside: avoid; }

        .day-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }
        .day-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            border-radius: 6px;
            padding: 5px 12px;
            flex-shrink: 0;
        }
        .day-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .day-name-text { font-size: 10pt; font-weight: 700; }
        .day-count-text { font-size: 9pt; font-weight: 500; }
        .day-line { flex: 1; height: 1px; }

        /* ─── Table ─────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 6px;
        }
        thead tr {
            font-size: 8.5pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        thead th {
            padding: 7px 8px;
            text-align: left;
            border-bottom: 1px solid rgba(0,0,0,0.10);
        }
        tbody td {
            padding: 8px 8px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: rgba(0,0,0,0.02); }
        .col-num   { width: 4%;  text-align: center; }
        .col-photo { width: 7%;  text-align: center; }
        .col-name  { width: 22%; }
        .col-type  { width: 9%;  }
        .col-pansa { width: 8%;  text-align: center; }
        .col-duty  { width: 30%; }

        .monk-photo {
            width: 38px; height: 38px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }
        .monk-photo-placeholder {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14pt;
            margin: 0 auto;
        }
        .monk-name { font-weight: 600; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8.5pt;
            font-weight: 500;
        }
        .badge-monk { background: rgba(74,122,26,0.12); color: #1A4A02; }
        .badge-novice { background: rgba(212,160,23,0.12); color: #6B3A00; }
        .sign-line {
            display: block;
            width: 70%;
            margin: 0 auto;
            border-bottom: 1px solid #D4A017;
            height: 22px;
        }

        /* ─── Date block (once section) ─────────────────────── */
        .date-block { margin-bottom: 20px; }
        .date-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            border-radius: 6px;
            padding: 5px 12px;
            flex-shrink: 0;
        }
        .date-text { font-size: 10pt; font-weight: 700; }
        .date-badge { font-size: 8.5pt; font-weight: 500; opacity: 0.75; }

        /* Past date opacity */
        .is-past { opacity: 0.65; }

        /* ─── Divider ────────────────────────────────────────── */
        .section-divider {
            height: 1px;
            background: rgba(212,160,23,0.18);
            margin: 30px 0;
        }

        /* ─── Footer ─────────────────────────────────────────── */
        .rpt-footer {
            margin-top: 36px;
            padding-top: 18px;
            border-top: 1px solid rgba(212,160,23,0.25);
        }
        .signature-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 20px;
        }
        .signature-box { text-align: center; }
        .sig-role {
            font-size: 9pt;
            color: #6B4A2A;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 40px;
        }
        .sig-line {
            border-bottom: 1px solid #D4A017;
            margin-bottom: 6px;
        }
        .sig-label { font-size: 8.5pt; color: #9B7A5A; }

        .footer-meta {
            display: flex;
            justify-content: space-between;
            font-size: 8.5pt;
            color: #9B7A5A;
        }
        .footer-system { font-weight: 600; color: #D4A017; }

        /* ─── Empty state ────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 28px;
            color: #9B7A5A;
            font-size: 10pt;
        }

        /* ─── Print hint (screen only) ──────────────────────── */
        .print-hint {
            font-size: 8.5pt;
            color: #9B7A5A;
            background: rgba(212,160,23,0.06);
            border: 1px solid rgba(212,160,23,0.20);
            border-radius: 6px;
            padding: 8px 14px;
            margin-top: 14px;
        }
    </style>
</head>
<body>
@php
    /* Day color palette — matches index page */
    $dayColors = [
        1 => ['pill_bg'=>'#3D2E00','text'=>'#F5D060','dot'=>'#F5D060','line'=>'rgba(245,208,96,0.30)','thead_bg'=>'rgba(245,208,96,0.10)','thead_text'=>'#5A3D00','border_left'=>'#D4A017'],
        2 => ['pill_bg'=>'#3D0A1A','text'=>'#F0A0B0','dot'=>'#F0A0B0','line'=>'rgba(240,160,176,0.30)','thead_bg'=>'rgba(240,160,176,0.10)','thead_text'=>'#6B0A22','border_left'=>'#D47080'],
        3 => ['pill_bg'=>'#1A3A02','text'=>'#A8D87A','dot'=>'#A8D87A','line'=>'rgba(168,216,122,0.30)','thead_bg'=>'rgba(74,122,26,0.10)','thead_text'=>'#1A4A02','border_left'=>'#4A7A1A'],
        4 => ['pill_bg'=>'#3D1A00','text'=>'#F5A060','dot'=>'#F5A060','line'=>'rgba(245,160,96,0.30)','thead_bg'=>'rgba(245,160,96,0.10)','thead_text'=>'#6B2E00','border_left'=>'#C07030'],
        5 => ['pill_bg'=>'#0A1A3D','text'=>'#90BCEE','dot'=>'#90BCEE','line'=>'rgba(144,188,238,0.30)','thead_bg'=>'rgba(144,188,238,0.10)','thead_text'=>'#0A2A6B','border_left'=>'#4070C0'],
        6 => ['pill_bg'=>'#2A0A3D','text'=>'#C090E0','dot'=>'#C090E0','line'=>'rgba(192,144,224,0.30)','thead_bg'=>'rgba(192,144,224,0.10)','thead_text'=>'#4A0A6B','border_left'=>'#7040B0'],
        7 => ['pill_bg'=>'#3D0A0A','text'=>'#EE9090','dot'=>'#EE9090','line'=>'rgba(238,144,144,0.30)','thead_bg'=>'rgba(238,144,144,0.10)','thead_text'=>'#6B0A0A','border_left'=>'#C04040'],
    ];
    $typeLabel = match($type) {
        'weekly' => 'ໝຸນວຽນ (ປະຈຳອາທິດ)',
        'once'   => 'ສະເພາະວັນ',
        default  => 'ທັງໝົດ',
    };
@endphp

<div class="report-page">

    {{-- ══════════════════════════════════════════════════════════
         CONTROLS BAR (screen only)
    ══════════════════════════════════════════════════════════ --}}
    <div class="controls-bar no-print">
        <a href="{{ route('duty-schedules.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 20 20">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5l-7 5 7 5"/>
            </svg>
            ກັບຄືນ
        </a>

        <span class="ctrl-sep">|</span>

        <form method="GET" action="{{ route('duty-schedules.report') }}" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <label>ປະເພດ:</label>
            <select name="type" onchange="this.form.submit()">
                <option value="all"    {{ $type === 'all'    ? 'selected' : '' }}>ທັງໝົດ</option>
                <option value="weekly" {{ $type === 'weekly' ? 'selected' : '' }}>ໝຸນວຽນ</option>
                <option value="once"   {{ $type === 'once'   ? 'selected' : '' }}>ສະເພາະວັນ</option>
            </select>

            @if ($type !== 'weekly')
                <label>ຈາກ:</label>
                <input type="date" name="from" value="{{ $from }}">
                <label>ຫາ:</label>
                <input type="date" name="to" value="{{ $to }}">
                <button type="submit" class="btn-filter">ສ້າງ</button>
            @endif
        </form>

        <button class="btn-print" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 20 20">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 7V4a1 1 0 011-1h8a1 1 0 011 1v3M5 13H3a1 1 0 01-1-1V9a1 1 0 011-1h14a1 1 0 011 1v3a1 1 0 01-1 1h-2M6 13v4h8v-4H6z"/>
            </svg>
            ພິມ / PDF
        </button>
    </div>

    <div class="print-hint no-print">
        💡 ກົດ "ພິມ / PDF" ຈາກນັ້ນເລືອກ <strong>"Save as PDF"</strong> ໃນກ່ອງ print dialog ເພື່ອໄດ້ໄຟລ໌ PDF
    </div>

    {{-- ══════════════════════════════════════════════════════════
         REPORT DOCUMENT
    ══════════════════════════════════════════════════════════ --}}
    <div class="report-doc" style="margin-top: 18px;">

        {{-- ── HEADER ────────────────────────────────────────── --}}
        <div class="rpt-header">
            <div class="rpt-header-inner">
                @php $logoPath = \App\Models\Setting::get('logo'); @endphp
                @if($logoPath)
                    <div class="rpt-emblem" style="font-size: 0;">
                        <img src="{{ asset("storage/$logoPath") }}" alt="Logo" style="width: 56px; height: 56px; object-fit: contain;">
                    </div>
                @else
                    <div class="rpt-emblem">☸️</div>
                @endif
                <div class="rpt-title-block">
                    <div class="rpt-label">ລາຍງານທາງການ</div>
                    <div class="rpt-title">ວັດປ່າມະຫາຣຸກຂາວະຣາຣາມ ບ້ານໜອງບົວທອງໃຕ້</div>
                    <div class="rpt-subtitle">ລະບົບຕິດຕາມການຂາດລາຂອງພຣະສົງ / ສາມະເນນ</div>
                    @if ($from || $to)
                        <div class="rpt-range-badge">
                            ໄລຍະ: {{ $from ? \Carbon\Carbon::parse($from)->format('d/m/Y') : '—' }}
                            ຫາ
                            {{ $to ? \Carbon\Carbon::parse($to)->format('d/m/Y') : '—' }}
                        </div>
                    @endif
                </div>
                <div class="rpt-meta-block">
                    <div><strong>ສ້າງວັນທີ:</strong> {{ $generatedAt->format('d/m/Y') }}</div>
                    <div><strong>ເວລາ:</strong> {{ $generatedAt->format('H:i') }}</div>
                    <div><strong>ປະເພດ:</strong> {{ $typeLabel }}</div>
                    <div style="margin-top:6px; font-size:8pt; color:#C4A882;">ໜ້າ <span class="page-num">1</span></div>
                </div>
            </div>
        </div>

        {{-- ── STATS ────────────────────────────────────────── --}}
        <div class="stats-row">
            <div class="stat-box" style="border-left: 3px solid rgba(74,122,26,0.55);">
                <div class="stat-label" style="color: #4A7A1A;">ໝຸນວຽນ</div>
                <div class="stat-sub">ໜ້າທີ່ປະຈຳອາທິດ</div>
                <div>
                    <span class="stat-num" style="color: #1A4A02;">{{ $totalWeekly }}</span>
                    <span class="stat-unit">ລາຍການ</span>
                </div>
            </div>
            <div class="stat-box" style="border-left: 3px solid rgba(212,160,23,0.55);">
                <div class="stat-label" style="color: #D4A017;">ສະເພາະວັນ</div>
                <div class="stat-sub">ໜ້າທີ່ວັນສະເພາະ</div>
                <div>
                    <span class="stat-num" style="color: #6B3A00;">{{ $totalOnce }}</span>
                    <span class="stat-unit">ລາຍການ</span>
                </div>
            </div>
            <div class="stat-box" style="border-left: 3px solid rgba(139,26,26,0.40);">
                <div class="stat-label" style="color: #8B1A1A;">ພຣະສົງ/ສາມະເນນ</div>
                <div class="stat-sub">ຜູ້ທີ່ຖືກມອບໝາຍ</div>
                <div>
                    <span class="stat-num" style="color: #1A0A02;">{{ $totalMonks }}</span>
                    <span class="stat-unit">ຮູບ</span>
                </div>
            </div>
        </div>

        {{-- ── NOTICE ────────────────────────────────────────── --}}
        <div class="notice-bar">
            <span class="notice-icon">ℹ</span>
            <span>ພຣະທີ່ຖືກມອບໝາຍໜ້າທີ່ (ທັງສະເພາະວັນ ແລະ ໝຸນວຽນ) <strong>ຈະບໍ່ສາມາດໝາຍຂາດ</strong> ໃນວັນນັ້ນໄດ້</span>
        </div>

        {{-- ══════════════════════════════════════════════════
             SECTION 1: WEEKLY (ໝຸນວຽນ)
        ══════════════════════════════════════════════════ --}}
        @if ($weeklyGroups->isNotEmpty())
            <div>
                {{-- Section heading --}}
                <div class="section-header">
                    <div class="section-icon" style="background: #1A3A02; color: #A8D87A;">↺</div>
                    <div>
                        <div class="section-title" style="color: #1A4A02;">ໜ້າທີ່ໝຸນວຽນ</div>
                        <div class="section-sub" style="color: #4A7A1A;">ປະຈຳທຸກໆອາທິດ</div>
                    </div>
                    <div class="section-line" style="background: linear-gradient(to right, rgba(74,122,26,0.30), transparent)"></div>
                    <div class="section-count" style="background: rgba(74,122,26,0.10); color: #1A4A02;">
                        {{ $weeklyGroups->flatten()->count() }} ລາຍ
                    </div>
                </div>

                {{-- Day-by-day blocks --}}
                @foreach ($weeklyGroups as $dayNum => $dayDuties)
                    @php $dc = $dayColors[$dayNum] ?? $dayColors[3]; @endphp
                    <div class="day-block avoid-break">
                        <div class="day-header">
                            <div class="day-pill" style="background: {{ $dc['pill_bg'] }};">
                                <div class="day-dot" style="background: {{ $dc['dot'] }};"></div>
                                <span class="day-name-text" style="color: {{ $dc['text'] }};">{{ $dayNames[$dayNum] }}</span>
                            </div>
                            <div class="day-line" style="background: linear-gradient(to right, {{ $dc['line'] }}, transparent);"></div>
                            <span class="day-count-text" style="color: {{ $dc['text'] }}; opacity: 0.80;">
                                {{ $dayDuties->count() }} ລາຍ
                            </span>
                        </div>

                        <table style="border-left: 3px solid {{ $dc['border_left'] }};">
                            <thead style="background: {{ $dc['thead_bg'] }}; color: {{ $dc['thead_text'] }};">
                                <tr>
                                    <th class="col-num">ລ/ດ</th>
                                    <th class="col-photo">ຮູບ</th>
                                    <th class="col-name">ຊື່ — ນາມສະກຸນ</th>
                                    <th class="col-type">ປະເພດ</th>
                                    <th class="col-pansa">ພັນສາ</th>
                                    <th class="col-duty">ໜ້າທີ່</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dayDuties as $i => $duty)
                                    <tr>
                                        <td class="col-num" style="color: #9B7A5A; font-size: 8.5pt;">{{ $i + 1 }}</td>
                                        <td class="col-photo">
                                            @if ($duty->monk->photo)
                                                <img src="{{ $duty->monk->photo_url }}"
                                                     alt="{{ $duty->monk->full_name }}"
                                                     class="monk-photo"
                                                     style="border: 2px solid {{ $dc['border_left'] }}40;">
                                            @else
                                                <div class="monk-photo-placeholder" style="background: {{ $dc['thead_bg'] }}; color: {{ $dc['thead_text'] }};">☸</div>
                                            @endif
                                        </td>
                                        <td class="col-name">
                                            <div class="monk-name">{{ $duty->monk->full_name }}</div>
                                        </td>
                                        <td class="col-type">
                                            <span class="badge {{ $duty->monk->type === 'monk' ? 'badge-monk' : 'badge-novice' }}">
                                                {{ $duty->monk->type_label }}
                                            </span>
                                        </td>
                                        <td class="col-pansa" style="color: #6B4A2A; font-size: 9pt;">
                                            {{ $duty->monk->pansa ?? '—' }}
                                        </td>
                                        <td class="col-duty" style="font-weight: 600; color: {{ $dc['thead_text'] }};">
                                            {{ $duty->duty_name }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Divider between sections --}}
        @if ($weeklyGroups->isNotEmpty() && $onceGroups->isNotEmpty())
            <div class="section-divider {{ $weeklyGroups->count() > 3 ? 'page-break' : '' }}"></div>
        @endif

        {{-- ══════════════════════════════════════════════════
             SECTION 2: ONCE / SPECIFIC DATES (ສະເພາະວັນ)
        ══════════════════════════════════════════════════ --}}
        @if ($onceGroups->isNotEmpty())
            <div>
                {{-- Section heading --}}
                <div class="section-header">
                    <div class="section-icon" style="background: #2D1206; color: #E8C97A;">▦</div>
                    <div>
                        <div class="section-title" style="color: #6B3A00;">ໜ້າທີ່ສະເພາະວັນ</div>
                        <div class="section-sub" style="color: #D4A017;">ກຳນົດໃຫ້ສະເພາະວັນທີ</div>
                    </div>
                    <div class="section-line" style="background: linear-gradient(to right, rgba(212,160,23,0.30), transparent)"></div>
                    <div class="section-count" style="background: rgba(212,160,23,0.10); color: #6B3A00;">
                        {{ $onceGroups->flatten()->count() }} ລາຍ
                    </div>
                </div>

                @php
                    $onceDateColors = [
                        'today'   => ['pill_bg'=>'#D4A017','pill_text'=>'#1A0A02','dot'=>'#1A0A02','line'=>'rgba(212,160,23,0.50)','border_left'=>'#D4A017','thead_bg'=>'rgba(212,160,23,0.15)','thead_text'=>'#6B3A00'],
                        'future'  => ['pill_bg'=>'#2D1206','pill_text'=>'#E8C97A','dot'=>'#E8C97A','line'=>'rgba(212,160,23,0.25)','border_left'=>'rgba(212,160,23,0.60)','thead_bg'=>'rgba(212,160,23,0.08)','thead_text'=>'#6B3A00'],
                        'past'    => ['pill_bg'=>'rgba(26,10,2,0.10)','pill_text'=>'#9B7A5A','dot'=>'#9B7A5A','line'=>'rgba(155,122,90,0.20)','border_left'=>'rgba(155,122,90,0.40)','thead_bg'=>'rgba(155,122,90,0.08)','thead_text'=>'#6B4A2A'],
                    ];
                @endphp

                @foreach ($onceGroups as $dateKey => $dateDuties)
                    @php
                        $dateCarbon = \Carbon\Carbon::parse($dateKey);
                        $isToday    = $dateCarbon->isToday();
                        $isFuture   = $dateCarbon->isFuture();
                        $isPast     = !$isToday && !$isFuture;
                        $statusKey  = $isToday ? 'today' : ($isPast ? 'past' : 'future');
                        $oc         = $onceDateColors[$statusKey];
                        $statusLbl  = $isToday ? 'ວັນນີ້' : ($isPast ? 'ຜ່ານໄປ' : ($dateCarbon->isTomorrow() ? 'ມື້ອື່ນ' : 'ກຳລັງຈະມາ'));
                        $dayOfWeekNum = (int)$dateCarbon->isoWeekday();
                    @endphp
                    <div class="date-block avoid-break {{ $isPast ? 'is-past' : '' }}">
                        <div class="day-header">
                            <div class="date-pill" style="background: {{ $oc['pill_bg'] }};">
                                <div class="day-dot" style="background: {{ $oc['dot'] }};"></div>
                                <span class="date-text" style="color: {{ $oc['pill_text'] }};">
                                    {{ $dateCarbon->format('d/m/Y') }}
                                    ({{ $dayNames[$dayOfWeekNum] }})
                                </span>
                                <span class="date-badge" style="color: {{ $oc['pill_text'] }};">{{ $statusLbl }}</span>
                            </div>
                            <div class="day-line" style="background: linear-gradient(to right, {{ $oc['line'] }}, transparent);"></div>
                            <span class="day-count-text" style="color: {{ $oc['pill_text'] }}; opacity: 0.80;">
                                {{ $dateDuties->count() }} ລາຍ
                            </span>
                        </div>

                        <table style="border-left: 3px solid {{ $oc['border_left'] }};">
                            <thead style="background: {{ $oc['thead_bg'] }}; color: {{ $oc['thead_text'] }};">
                                <tr>
                                    <th class="col-num">ລ/ດ</th>
                                    <th class="col-photo">ຮູບ</th>
                                    <th class="col-name">ຊື່ — ນາມສະກຸນ</th>
                                    <th class="col-type">ປະເພດ</th>
                                    <th class="col-pansa">ພັນສາ</th>
                                    <th class="col-duty">ໜ້າທີ່</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dateDuties as $i => $duty)
                                    <tr>
                                        <td class="col-num" style="color: #9B7A5A; font-size: 8.5pt;">{{ $i + 1 }}</td>
                                        <td class="col-photo">
                                            @if ($duty->monk->photo)
                                                <img src="{{ $duty->monk->photo_url }}"
                                                     alt="{{ $duty->monk->full_name }}"
                                                     class="monk-photo"
                                                     style="border: 2px solid {{ $oc['border_left'] }}40;">
                                            @else
                                                <div class="monk-photo-placeholder" style="background: {{ $oc['thead_bg'] }}; color: {{ $oc['thead_text'] }};">☸</div>
                                            @endif
                                        </td>
                                        <td class="col-name">
                                            <div class="monk-name">{{ $duty->monk->full_name }}</div>
                                        </td>
                                        <td class="col-type">
                                            <span class="badge {{ $duty->monk->type === 'monk' ? 'badge-monk' : 'badge-novice' }}">
                                                {{ $duty->monk->type_label }}
                                            </span>
                                        </td>
                                        <td class="col-pansa" style="color: #6B4A2A; font-size: 9pt;">
                                            {{ $duty->monk->pansa ?? '—' }}
                                        </td>
                                        <td class="col-duty" style="font-weight: 600; color: {{ $oc['thead_text'] }};">
                                            {{ $duty->duty_name }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Empty state --}}
        @if ($weeklyGroups->isEmpty() && $onceGroups->isEmpty())
            <div class="empty-state">
                <div style="font-size: 28pt; margin-bottom: 10px; opacity: 0.30;">☸</div>
                <div style="font-weight: 600; color: #1A0A02; margin-bottom: 4px;">ບໍ່ມີຂໍ້ມູນໜ້າທີ່</div>
                <div>ກະລຸນາປ່ຽນຕົວກອງ ຫຼື ເພີ່ມໜ້າທີ່ໃຫ້ກ່ອນ</div>
            </div>
        @endif

        {{-- ── FOOTER ────────────────────────────────────────── --}}
        <div class="rpt-footer">
            <div class="signature-row">
                <div class="signature-box">
                    <div class="sig-role">ເຈົ້າອະທິວັດ</div>
                    <div class="sig-line"></div>
                    <div class="sig-label">ຊື່-ນາມສະກຸນ / ລາຍເຊັນ</div>
                </div>
                <div class="signature-box">
                   
                </div>
                <div class="signature-box">
                    <div class="sig-role">ຄະນະຮັບຜິດຊອບ</div>
                    <div class="sig-line"></div>
                    <div class="sig-label">ຊື່-ນາມສະກຸນ / ລາຍເຊັນ</div>
                </div>
            </div>
           
        </div>

    </div>{{-- /report-doc --}}
</div>{{-- /report-page --}}

<script>
    if (window.location.search.includes('autoprint=1')) {
        window.addEventListener('load', () => setTimeout(() => window.print(), 800));
    }
</script>
</body>
</html>
