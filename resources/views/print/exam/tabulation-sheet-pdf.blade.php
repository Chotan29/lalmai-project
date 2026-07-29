<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tabulation Sheet</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; margin: 0; }
        .tab-sheet-wrapper { width: 100%; border: 2px solid #333; padding: 8px 10px; box-sizing: border-box; }
        .tab-sheet-head { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .tab-sheet-head td { vertical-align: top; padding: 2px 4px; }
        .tab-scale-table { border-collapse: collapse; font-size: 9px; }
        .tab-scale-table th, .tab-scale-table td { border: 1px solid #333; padding: 1px 6px; text-align: center; }
        .tab-title { text-align: center; }
        .tab-title h2 { margin: 0; font-size: 18px; font-weight: bold; }
        .tab-title h4 { margin: 3px 0; font-size: 13px; text-decoration: underline; }
        .tab-meta { font-size: 11px; font-weight: bold; margin-top: 4px; }
        .tab-meta .group-box { border: 1px solid #333; padding: 1px 10px; display: inline-block; }
        .tab-main-table { width: 100%; border-collapse: collapse; font-size: {{ count($data['subject_columns']) > 10 ? '7' : '9' }}px; table-layout: fixed; }
        .tab-main-table th, .tab-main-table td { border: 1px solid #333; padding: 1px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        .tab-main-table thead th { font-weight: bold; }
        .tab-main-table .text-left { text-align: left; }
        .tab-subject-head { white-space: nowrap; }
        .tab-fail { color: #c0392b; font-weight: bold; }
        .tab-subject-legend { margin-top: 6px; font-size: 8px; line-height: 1.5; }
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
