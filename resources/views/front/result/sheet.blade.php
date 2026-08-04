{{-- One department's whole tabulation sheet, published by the college. Same markup as the
     printed sheet, so the web copy and the office copy can never differ. Addressed by token
     so the college can withdraw it and every copied link stops working at once. --}}
@php
    $gs = $data['generalSetting'] ?? null;
    $generalSetting = $gs;
    $cellCount = $data['tabulation_cell_count'] ?? (count($data['subject_columns']) * 2 + 4);
    $density = $cellCount > 55 ? 'is-tight' : ($cellCount > 35 ? 'is-wide' : '');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    @include('front.result.includes.head')
    <title>Tabulation Sheet - {{ $gs->institute ?? 'Lalmai Govt. College' }}</title>
    @include('front.result.includes.style')
    @include('print.exam.includes.tabulation-style')
    <style media="print">@page { size: legal landscape; margin: 6mm; }</style>
</head>
<body>
<div class="pr-wrap pr-wide">
    <div class="pr-noprint" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <a href="{{ route('public-result') }}" class="btn btn-outline-secondary btn-sm">&lsaquo; All Results</a>
        <button type="button" class="btn btn-primary btn-sm pr-btn" onclick="window.print();">Print</button>
    </div>

    <div class="tab-sheet-wrapper {{ $density }}">
        <div class="tab-sheet-scroll">
            @include('print.exam.includes.tabulation-table')
        </div>
    </div>

    <div class="pr-foot pr-noprint">
        Published by {{ $gs->institute ?? 'Lalmai Govt. College' }}. For any correction or an
        official transcript, please contact the college office.
    </div>
</div>
</body>
</html>
