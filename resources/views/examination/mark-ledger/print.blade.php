<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mark Entry Print - {{ $data['subject']->title ?? '' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, "Segoe UI", sans-serif; font-size: 13px; color: #222; margin: 25px; }
        .header { text-align: center; margin-bottom: 8px; }
        .header h2 { margin: 0 0 2px; font-size: 20px; }
        .header .sub { font-size: 13px; color: #444; }
        .meta { width: 100%; margin: 12px 0 6px; font-size: 13px; }
        .meta td { padding: 2px 6px; }
        table.marks { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.marks th, table.marks td { border: 1px solid #444; padding: 5px 7px; text-align: center; }
        table.marks th { background: #f0f0f0; }
        table.marks td.name { text-align: left; }
        .footer { margin-top: 35px; width: 100%; }
        .footer .sign { float: right; text-align: center; width: 220px; border-top: 1px solid #333; padding-top: 4px; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 18px; background: #1b6fba; color: #fff;
                     border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .badge { display: inline-block; padding: 1px 8px; border-radius: 8px; font-size: 11px; background: #fdecea; color: #b71c1c; }
        @media print { .print-btn { display: none; } body { margin: 10mm; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print</button>

    <div class="header">
        <h2>Lalmai Govt. College</h2>
        <div class="sub">Cumilla Sadar South, Cumilla</div>
        <div class="sub"><b>Exam Mark Entry Sheet{{ $data['teacher_only'] ? ' (Own Entries)' : '' }}</b></div>
    </div>

    <table class="meta">
        <tr>
            <td><b>Exam:</b> {{ $data['exam']->title ?? '-' }}</td>
            <td><b>Year:</b> {{ $data['year']->title ?? '-' }}</td>
            <td><b>Month:</b> {{ $data['month']->title ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Class/Program:</b> {{ $data['faculty']->faculty ?? '-' }}</td>
            <td><b>Sem./Sec.:</b> {{ $data['semester']->semester ?? '-' }}</td>
            <td><b>Subject:</b> {{ $data['subject']->title ?? '-' }} {{ !empty($data['subject']->code) ? '('.$data['subject']->code.')' : '' }}</td>
        </tr>
        <tr>
            <td><b>Entered By:</b> {{ $data['teacher_only'] ? $data['printed_by'] : 'All Teachers' }}</td>
            <td><b>Total Students:</b> {{ count($data['rows']) }}</td>
            <td><b>Printed:</b> {{ date('d-m-Y h:i A') }}</td>
        </tr>
    </table>

    @if(count($data['rows']) == 0)
        <p style="text-align:center; padding:30px; border:1px dashed #999;">
            No mark entry found for this selection{{ $data['teacher_only'] ? ' entered by you' : '' }}.
        </p>
    @else
    <table class="marks">
        <thead>
            <tr>
                <th style="width:40px;">SL</th>
                <th style="width:90px;">Roll / Reg. No</th>
                <th>Student Name</th>
                @if(($data['limits']['theory'] ?? 0) > 0)<th>Theory ({{ rtrim(rtrim(number_format($data['limits']['theory'],2,'.',''),'0'),'.') }})</th>@endif
                @if(($data['limits']['mcq'] ?? 0) > 0)<th>MCQ ({{ rtrim(rtrim(number_format($data['limits']['mcq'],2,'.',''),'0'),'.') }})</th>@endif
                @if(($data['limits']['practical'] ?? 0) > 0)<th>Practical ({{ rtrim(rtrim(number_format($data['limits']['practical'],2,'.',''),'0'),'.') }})</th>@endif
                <th>Total</th>
                @if(!$data['teacher_only'])<th>Entered By</th>@endif
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->reg_no }}</td>
                <td class="name">{{ trim($row->first_name.' '.$row->middle_name.' '.$row->last_name) }}</td>
                @if(($data['limits']['theory'] ?? 0) > 0)
                    <td>@if($row->absent_theory == 1)<span class="badge">Absent</span>@else{{ $row->obtain_mark_theory + 0 }}@endif</td>
                @endif
                @if(($data['limits']['mcq'] ?? 0) > 0)
                    <td>{{ $row->obtain_mark_mcq + 0 }}</td>
                @endif
                @if(($data['limits']['practical'] ?? 0) > 0)
                    <td>@if($row->absent_practical == 1)<span class="badge">Absent</span>@else{{ $row->obtain_mark_practical + 0 }}@endif</td>
                @endif
                <td><b>{{ ($row->absent_theory == 1 ? 0 : $row->obtain_mark_theory) + $row->obtain_mark_mcq + ($row->absent_practical == 1 ? 0 : $row->obtain_mark_practical) + 0 }}</b></td>
                @if(!$data['teacher_only'])<td>{{ $row->entered_by_name ?: '-' }}</td>@endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <div class="sign">Teacher's Signature</div>
        <div style="clear:both;"></div>
    </div>
</body>
</html>
