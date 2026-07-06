<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Exam Routine - {{ ViewHelper::getExamById($data['exam']) }}</title>
    @php
        $gs = $generalSetting ?? null;
        $institute = $gs->institute ?? 'Lalmai Govt. College';
        $address = $gs->address ?? 'Cumilla Sadar South, Cumilla';
        $phone = $gs->phone ?? '';
        $email = $gs->email ?? '';
        $website = $gs->website ?? '';

        /*Embed logo as base64 so print/PDF never miss it*/
        $hasLogo = isset($gs->logo) && $gs->logo;
        $logoSrc = '';
        if ($hasLogo) {
            $logoFile = public_path('images/setting/general/'.$gs->logo);
            if (is_file($logoFile)) {
                $ext = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
                $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'jpeg' : $ext;
                $logoSrc = 'data:image/'.$mime.';base64,'.base64_encode(file_get_contents($logoFile));
            } else {
                $logoSrc = asset('images/setting/general/'.$gs->logo);
            }
        }
        $hasLogo = $hasLogo && $logoSrc;

        $examTitle = ViewHelper::getExamById($data['exam']);
        $yearTitle = ViewHelper::getYearById($data['year']);
        $facultyTitle = ViewHelper::getFacultyTitle($data['faculty']);
        $semesterTitle = ViewHelper::getSemesterTitle($data['semester']);

        /*Group subjects by exam date*/
        $sorted = $data['subjects']->sortBy('date')->values();
        $byDate = $sorted->groupBy(function ($s) { return \Carbon\Carbon::parse($s->date)->format('Y-m-d'); });

        /*If every schedule uses the same time, show it once at the top*/
        $timeSet = $sorted->map(function ($s) { return $s->start_time.'|'.$s->end_time; })->unique()->values();
        $singleTime = $timeSet->count() === 1;
        $timeLabel = function ($start, $end) {
            return \Carbon\Carbon::parse($start)->format('g:i A').' - '.\Carbon\Carbon::parse($end)->format('g:i A');
        };

        $firstDate = $sorted->count() ? \Carbon\Carbon::parse($sorted->first()->date) : null;
        $lastDate = $sorted->count() ? \Carbon\Carbon::parse($sorted->last()->date) : null;
    @endphp
    <style>
        :root {
            --brand: #0e4c8b; --brand-dark: #093763; --line: #cbd5e1; --line-dark: #475569;
            --ink: #111827; --muted: #64748b; --zebra: #f5f8fc;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { background: #e9edf2; }
        body { font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif; font-size: 13px;
               color: var(--ink); -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .sheet { position: relative; width: 210mm; min-height: 296mm; margin: 18px auto; background: #fff;
                 padding: 10mm 12mm 0; box-shadow: 0 4px 24px rgba(15,23,42,.18); overflow: hidden;
                 display: flex; flex-direction: column; }
        .top-rule { position: absolute; top: 0; left: 0; right: 0; height: 4.5px;
            background: linear-gradient(90deg, var(--brand-dark), var(--brand) 55%, #2e86d4); }
        .watermark { position: absolute; top: 52%; left: 50%; transform: translate(-50%,-50%);
            width: 112mm; opacity: .05; z-index: 0; }
        .content { position: relative; z-index: 1; flex: 1; }

        .gov-head { display: flex; align-items: center; gap: 10px; }
        .gh-logo { width: 76px; height: 76px; flex: 0 0 76px; display: flex; align-items: center; justify-content: center; }
        .gh-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .gh-center { flex: 1; text-align: center; }
        .gh-republic { font-size: 12px; font-weight: 600; color: #334155; letter-spacing: .8px; }
        .gh-office { display: inline-block; margin-top: 3px; font-size: 11.5px; font-weight: 600; color: var(--brand-dark);
            background: #eef4fb; border: 1px solid #cfe0f2; border-radius: 14px; padding: 1px 16px;
            letter-spacing: 1.5px; text-transform: uppercase; }
        .gh-name { font-size: 29px; font-weight: 800; color: var(--brand-dark); line-height: 1.25; margin-top: 2px; }
        .gh-addr { font-size: 12px; color: var(--muted); margin-top: 1px; }

        .eiin-band { margin-top: 8px; background: var(--brand-dark); color: #fff; text-align: center;
            font-size: 12px; font-weight: 600; letter-spacing: .8px; padding: 5px 6px; border-radius: 3px; }

        .memo { display: flex; justify-content: space-between; margin-top: 10px; font-size: 12.5px; }
        .memo .fill { border-bottom: 1px dotted var(--line-dark); min-width: 160px; display: inline-block; }

        .notice-title { text-align: center; margin: 11px 0 8px; }
        .notice-title span { display: inline-block; background: var(--brand); color: #fff; font-size: 14.5px;
            font-weight: 700; letter-spacing: 4px; text-transform: uppercase; padding: 5px 36px; border-radius: 3px; }

        .intro { font-size: 13px; line-height: 1.75; text-align: justify; color: #1f2937; }
        .intro b { color: var(--brand-dark); }

        .sched-head { text-align: center; margin: 12px 0 8px; }
        .sched-head .t1 { display: flex; align-items: center; gap: 14px; margin: 0 auto; max-width: 150mm; }
        .sched-head .t1 .rule { flex: 1; height: 2px;
            background: linear-gradient(90deg, transparent, var(--brand)); border-radius: 2px; }
        .sched-head .t1 .rule:last-child { background: linear-gradient(90deg, var(--brand), transparent); }
        .sched-head .t1 .txt { font-size: 16.5px; font-weight: 800; color: var(--brand-dark);
            letter-spacing: 3px; text-transform: uppercase; white-space: nowrap; }
        .sched-head .t2 { display: inline-block; margin-top: 5px; font-size: 13px; font-weight: 700; color: #7c4a03;
            background: #fffbea; border: 1.3px solid #f5c94c; border-radius: 16px; padding: 2px 22px;
            box-shadow: 0 1px 2px rgba(180,131,9,.15); }

        table.routine { width: 100%; border-collapse: collapse; }
        table.routine thead th { background: var(--brand-dark); color: #fff; font-size: 12px; font-weight: 600;
            letter-spacing: 1.2px; text-transform: uppercase; padding: 7px 8px; border: 1px solid var(--brand-dark); }
        table.routine td { border: 1px solid var(--line); padding: 7px 10px; font-size: 13px; vertical-align: middle; }
        table.routine tbody tr:nth-child(even) td { background: var(--zebra); }
        td.datecell { width: 40mm; text-align: center; }
        td.datecell .d { font-weight: 700; color: var(--brand-dark); font-size: 13.5px; }
        td.datecell .day { font-size: 11px; color: var(--muted); }
        td.datecell .tm { font-size: 10.5px; color: #92400e; margin-top: 1px; }
        td.subjcell { font-weight: 600; line-height: 1.65; }
        td.subjcell .code { font-weight: 400; color: var(--muted); font-size: 11px; }
        td.subjcell .sep { color: #94a3b8; font-weight: 400; padding: 0 5px; }

        .noteblock { margin-top: 11px; border: 1px solid var(--line); border-left: 4px solid var(--brand);
            border-radius: 3px; background: #fbfdff; padding: 8px 13px; font-size: 12px; line-height: 1.7;
            text-align: justify; color: #1f2937; }
        .noteblock .nb-title { font-weight: 700; color: var(--brand-dark); }
        .noteblock ul { margin: 3px 0 0 17px; }
        .noteblock li { margin-top: 2px; }

        .sign-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 16mm; }
        .sig { text-align: center; font-size: 12px; color: #1f2937; }
        .sig .line { border-top: 1.3px dotted var(--line-dark); margin-bottom: 4px; min-width: 210px; }
        .sig .role { font-weight: 700; color: var(--brand-dark); }

        .contact-strip { margin: 8mm -12mm 0; background: var(--brand-dark); color: #e2e8f0; text-align: center;
            font-size: 10.5px; letter-spacing: .4px; padding: 6px 10px; }
        .contact-strip b { color: #fff; }

        .toolbar { position: fixed; top: 14px; right: 16px; display: flex; gap: 8px; z-index: 60; }
        .toolbar button { padding: 9px 22px; border: none; border-radius: 5px; cursor: pointer; font-size: 13.5px;
            font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,.25); }
        .btn-print { background: var(--brand); color: #fff; }
        .btn-back { background: #475569; color: #fff; }

        @page { size: A4 portrait; margin: 0; }
        @media print {
            .toolbar { display: none; }
            html, body { background: #fff; }
            .sheet { margin: 0; box-shadow: none; width: auto; min-height: 296mm; }
            table.routine thead { display: table-header-group; }
            table.routine tr { page-break-inside: avoid; }
            .sign-row { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">&#128438; Print</button>
        <button class="btn-back" onclick="history.back()">Back</button>
    </div>

    <div class="sheet">
        <div class="top-rule"></div>
        @if($hasLogo)<img class="watermark" src="{{ $logoSrc }}" alt="">@endif

        <div class="content">
            <!-- Letterhead -->
            <div class="gov-head">
                <div class="gh-logo">@if($hasLogo)<img src="{{ $logoSrc }}" alt="">@endif</div>
                <div class="gh-center">
                    <div class="gh-republic">Government of the People's Republic of Bangladesh</div>
                    <div class="gh-office">Office of the Principal</div>
                    <div class="gh-name">{{ $institute }}</div>
                    <div class="gh-addr">{{ $address }}</div>
                </div>
                <div class="gh-logo">@if($hasLogo)<img src="{{ $logoSrc }}" alt="">@endif</div>
            </div>

            <div class="eiin-band">EIIN No.- 105746 &nbsp;&nbsp;|&nbsp;&nbsp; H.S.C Code No.- 7850 &nbsp;&nbsp;|&nbsp;&nbsp; N.U Code No.- 3729</div>

            <!-- Memo -->
            <div class="memo">
                <div>Ref. No : <span class="fill">&nbsp;</span></div>
                <div>Date : <span class="fill">&nbsp;{{ \Carbon\Carbon::now()->format('d.m.Y') }}&nbsp;</span></div>
            </div>

            <div class="notice-title"><span>Notice</span></div>

            <div class="intro">
                All students of <b>{{ $facultyTitle }} &mdash; {{ $semesterTitle }}</b> of {{ $institute }} are hereby informed that the
                <b>{{ $examTitle }} &mdash; {{ $yearTitle }}</b> will be held according to the following schedule.
                Participation in the examination is mandatory for all students.
            </div>

            <!-- Schedule -->
            <div class="sched-head">
                <div class="t1"><span class="rule"></span><span class="txt">Examination Schedule</span><span class="rule"></span></div>
                @if($singleTime && $sorted->count())
                    <div class="t2">Time : {{ $timeLabel($sorted->first()->start_time, $sorted->first()->end_time) }}</div>
                @endif
                @if($firstDate && $lastDate)
                    <div style="margin-top:3px; font-size:11.5px; color:var(--muted);">
                        {{ $firstDate->format('d M Y') }} &ndash; {{ $lastDate->format('d M Y') }} &nbsp;&bull;&nbsp; {{ $sorted->count() }} subject(s)
                    </div>
                @endif
            </div>

            <table class="routine">
                <thead>
                    <tr>
                        <th style="width:40mm;">Date / Day</th>
                        <th style="text-align:left;">Subject</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byDate as $date => $daySubjects)
                    <tr>
                        <td class="datecell">
                            <div class="d">{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</div>
                            <div class="day">{{ \Carbon\Carbon::parse($date)->format('l') }}</div>
                            @if(!$singleTime)
                                <div class="tm">{{ $timeLabel($daySubjects->first()->start_time, $daySubjects->first()->end_time) }}</div>
                            @endif
                        </td>
                        <td class="subjcell">
                            @foreach($daySubjects->values() as $k => $subject)
                                {{ $subject->title }} <span class="code">[{{ $subject->code }}]</span>@if($k < $daySubjects->count() - 1)<span class="sep">/</span>@endif
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Notes -->
            <div class="noteblock">
                <span class="nb-title">Instructions :</span>
                <ul>
                    <li>Students must take their seats 15 minutes before the examination starts.</li>
                    <li>Admit card is mandatory to enter the examination hall.</li>
                    <li>Mobile phones and any electronic devices are strictly prohibited in the examination hall.</li>
                    <li>This notice is for information only. For any query, please contact the Examination Department.</li>
                </ul>
            </div>

            <!-- Signatures -->
            <div class="sign-row">
                <div class="sig">
                    <div class="line"></div>
                    <div class="role">Controller of Examinations</div>
                </div>
                <div class="sig">
                    <div class="line"></div>
                    <div class="role">Principal</div>
                    <div>{{ $institute }}</div>
                </div>
            </div>
        </div>

        <div class="contact-strip">
            @if($phone)<b>Mobile:</b> {{ $phone }}@endif
            @if($phone && $email) &nbsp;&nbsp;|&nbsp;&nbsp; @endif
            @if($email)<b>E-mail:</b> {{ $email }}@endif
            @if(($phone || $email) && $website) &nbsp;&nbsp;|&nbsp;&nbsp; @endif
            @if($website)<b>Web:</b> {{ $website }}@endif
            &nbsp;&nbsp;|&nbsp;&nbsp; Printed on {{ \Carbon\Carbon::now()->format('d M Y, g:i A') }}
        </div>
    </div>
</body>
</html>
