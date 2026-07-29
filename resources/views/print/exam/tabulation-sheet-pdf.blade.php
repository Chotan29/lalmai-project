@php
    /* Font size follows both how many cells there are and how wide the paper is. */
    $cellCount = $data['tabulation_cell_count'] ?? (count($data['subject_columns']) * 2 + 4);
    $paper = $data['paper'] ?? 'legal';

    $scale = [
        'a4'    => ['tight' => 6,   'wide' => 7, 'normal' => 9],
        'legal' => ['tight' => 7.5, 'wide' => 8, 'normal' => 10],
        'a3'    => ['tight' => 9,   'wide' => 10, 'normal' => 11],
    ];
    $sizes = isset($scale[$paper]) ? $scale[$paper] : $scale['legal'];
    $bodyFont = $cellCount > 55 ? $sizes['tight'] : ($cellCount > 35 ? $sizes['wide'] : $sizes['normal']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tabulation Sheet</title>
    <style>
        /* dompdf renders a fixed page, not a live browser, so colours are written as plain
           values here rather than CSS custom properties (safer across dompdf versions). */
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; margin: 0; }
        .tab-sheet-wrapper { width: 100%; border: 2px solid #0f5132; border-radius: 4px; padding: 8px 10px; box-sizing: border-box; }
        .tab-sheet-head { width: 100%; border-collapse: collapse; margin-bottom: 5px; background: #e7f3ec; border: 1px solid #0f5132; }
        .tab-sheet-head td { vertical-align: middle; padding: 3px 6px; }
        .tab-head-logo-cell { width: 55px; text-align: center; }
        .tab-head-logo { max-width: 48px; max-height: 48px; }
        .tab-scale-table { border-collapse: collapse; font-size: 9px; width: 100%; }
        .tab-scale-table th { border: 1px solid #0f5132; padding: 1px 6px; text-align: center; background: #0f5132; color: #fff; }
        .tab-scale-table td { border: 1px solid #0f5132; padding: 1px 6px; text-align: center; background: #fff; }
        .tab-title { text-align: center; }
        .tab-title h2 { margin: 0; font-size: 18px; font-weight: bold; color: #0a3a24; }
        .tab-title h4 { margin: 3px 0; font-size: 13px; text-decoration: underline; color: #0a3a24; }
        .tab-meta { font-size: 11px; font-weight: bold; margin-top: 4px; color: #0a3a24; }
        .tab-meta .group-box { border: 1px solid #0f5132; background: #fff; padding: 1px 10px; display: inline-block; }
        .tab-main-table { width: 100%; border-collapse: collapse; font-size: {{ $bodyFont }}px; table-layout: fixed; }
        .tab-main-table th, .tab-main-table td { border: 1px solid #9ec5ae; padding: 1px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        .tab-main-table thead th { font-weight: bold; background: #0f5132; color: #fff; }
        .tab-main-table .text-left { text-align: left; }
        .tab-main-table .col-roll { width: 40px; }
        .tab-main-table .col-name { width: 90px; }
        .tab-main-table .col-gpa { width: 24px; }
        .tab-subject-head { white-space: nowrap; }
        .tab-fullmark-row th { font-weight: normal; font-style: italic; background: #0a3a24; color: #e7f3ec; }
        .tab-main-table tbody tr.tab-row-even { background: #f4f9f6; }
        .tab-fail { color: #c0392b; font-weight: bold; background: #fdecea; }
        .tab-subject-legend { margin-top: 6px; font-size: 8px; line-height: 1.5; color: #333; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="tab-sheet-wrapper">
        @include('print.exam.includes.tabulation-table')
    </div>
</body>
</html>
