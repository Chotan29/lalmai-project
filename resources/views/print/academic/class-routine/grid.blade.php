<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class Routine Grid</title>
    <style>
        :root{--ink:#111827;--muted:#6b7280;--line:#e5e7eb;--brand:#111827}
        *{box-sizing:border-box}
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Helvetica Neue',Arial,'Noto Sans',sans-serif;margin:24px;color:var(--ink)}
        .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
        .meta{font-size:14px;color:var(--muted)}
        h1{font-size:22px;margin:0 0 4px}
        .grid{border:1px solid var(--line);border-radius:10px;overflow:hidden}
        .day{border-bottom:1px solid var(--line)}
        .day-title{background:#f9fafb;border-bottom:1px solid var(--line);padding:8px 12px;font-weight:700}
        .row{display:flex;gap:12px;flex-wrap:wrap;padding:12px}
        .cell{flex:1 1 46%;border:1px solid var(--line);border-radius:8px;padding:10px}
        .time{font-weight:700;font-size:13px;margin-bottom:4px}
        .small{color:var(--muted);font-size:12px}
        .toolbar{margin-bottom:10px}
        .toolbar button{padding:6px 10px;border:1px solid var(--line);background:#fff;border-radius:8px;cursor:pointer}
        @media print {.toolbar{display:none} body{margin:0} .head{margin:0 0 10px}}
    </style>
</head>
<body>
{{-- @include('print.student-fee.includes.institution-detail') --}}
<div class="toolbar">
    <button onclick="window.print()">Print</button>
</div>
<div class="head">
    <div>
        <h1>
            {{ $scope }} Routine
            @if($meta['subject']) — {{ $meta['subject']->title }} @endif
        </h1>
        <div class="meta">
            @if($meta['department']) Dept: {{ $meta['department']->department }} &nbsp;|&nbsp; @endif
            @if($meta['faculty']) Program: {{ $meta['faculty']->faculty }} &nbsp;|&nbsp; @endif
            @if($meta['semester']) Semester: {{ $meta['semester']->semester }} &nbsp;|&nbsp; @endif
            @if($meta['batch']) Batch: {{ $meta['batch']->title }} @endif
        </div>
    </div>
    <div class="meta">{{ now()->format('d M Y') }}</div>
</div>

<div class="grid">
    @php $order=['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; @endphp
    @foreach($order as $day)
        @if(isset($groupedRoutines[$day]) && count($groupedRoutines[$day]))
        <div class="day">
            <div class="day-title">{{ $day }} ({{ count($groupedRoutines[$day]) }})</div>
            <div class="row">
                @foreach($groupedRoutines[$day] as $r)
                <div class="cell">
                    <div class="time">{{ date('h:i A', strtotime($r->start_time)) }} – {{ date('h:i A', strtotime($r->end_time)) }}</div>
                    <div><strong>{{ $r->subject->title ?? 'Subject' }}</strong></div>
                    <div class="small">Teacher: {{ $r->teacher->first_name ?? '-' }}</div>
                    <div class="small">Room: {{ $r->room_number }}</div>
                    @if(!$meta['batch'])
                    <div class="small">Batch: {{ $r->batch->title ?? '-' }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
</div>
</body>
</html>
