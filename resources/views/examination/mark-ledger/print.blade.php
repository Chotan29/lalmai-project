<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mark Entry Sheet - {{ $data['subject']->title ?? '' }}</title>
    @php
        $gs = $generalSetting ?? null;
        $institute = $gs->institute ?? 'Lalmai Govt. College';
        $address = $gs->address ?? 'Cumilla Sadar South, Cumilla';
        $phone = $gs->phone ?? '';
        $email = $gs->email ?? '';
        $website = $gs->website ?? '';
        $hasLogo = isset($gs->logo) && $gs->logo;
        $logoSrc = '';
        if ($hasLogo) {
            $logoFile = public_path('images/setting/general/'.$gs->logo);
            if (is_file($logoFile)) {
                $ext = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
                $mime = $ext === 'jpg' || $ext === 'jpeg' ? 'jpeg' : $ext;
                $logoSrc = 'data:image/'.$mime.';base64,'.base64_encode(file_get_contents($logoFile));
            } else {
                $logoSrc = asset('images/setting/general/'.$gs->logo);
            }
        }
        $hasLogo = $hasLogo && $logoSrc;

        $rows = collect($data['rows']);
        $showTh = ($data['limits']['theory'] ?? 0) > 0;
        $showMcq = ($data['limits']['mcq'] ?? 0) > 0;
        $showPr = ($data['limits']['practical'] ?? 0) > 0;
        $fullMark = ($showTh ? $data['limits']['theory'] : 0) + ($showMcq ? $data['limits']['mcq'] : 0) + ($showPr ? $data['limits']['practical'] : 0);

        $fmt = function ($n) { return rtrim(rtrim(number_format((float)$n, 2, '.', ''), '0'), '.'); };

        $totals = $rows->map(function ($r) {
            return ($r->absent_theory == 1 ? 0 : (float) $r->obtain_mark_theory)
                 + (float) $r->obtain_mark_mcq
                 + ($r->absent_practical == 1 ? 0 : (float) $r->obtain_mark_practical);
        });
        /*Component-wise absent counts: CQ (theory) and Practical shown separately.
          "Appeared" = students absent in NO component that this subject has.*/
        $absentTh = $showTh ? $rows->where('absent_theory', 1)->count() : 0;
        $absentPr = $showPr ? $rows->where('absent_practical', 1)->count() : 0;
        $fullyAbsent = $rows->filter(function ($r) use ($showTh, $showPr) {
            $thAb = !$showTh || $r->absent_theory == 1;
            $prAb = !$showPr || $r->absent_practical == 1;
            return ($showTh || $showPr) && $thAb && $prAb;
        })->count();
        $presentTotals = $rows->filter(function ($r) { return !($r->absent_theory == 1); })->map(function ($r) {
            return ($r->absent_theory == 1 ? 0 : (float) $r->obtain_mark_theory)
                 + (float) $r->obtain_mark_mcq
                 + ($r->absent_practical == 1 ? 0 : (float) $r->obtain_mark_practical);
        });
        $highest = $presentTotals->count() ? $presentTotals->max() : 0;
        $average = $presentTotals->count() ? $presentTotals->avg() : 0;
    @endphp
    <style>
        :root {
            --brand: #0e4c8b;
            --brand-dark: #093763;
            --line: #cbd5e1;
            --line-dark: #64748b;
            --ink: #1e293b;
            --muted: #64748b;
            --zebra: #f4f7fb;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { background: #e9edf2; }
        body {
            font-family: "Segoe UI", "Helvetica Neue", Arial, "Noto Sans Bengali", sans-serif;
            font-size: 12.5px; color: var(--ink); -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .sheet {
            position: relative; width: 210mm; min-height: 296mm; margin: 18px auto; background: #fff;
            padding: 14mm 13mm 16mm; box-shadow: 0 4px 24px rgba(15,23,42,.18); overflow: hidden;
        }
        .top-rule { position: absolute; top: 0; left: 0; right: 0; height: 5px;
            background: linear-gradient(90deg, var(--brand-dark), var(--brand) 55%, #2e86d4); }

        .watermark {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 118mm; opacity: .045; z-index: 0; pointer-events: none;
        }
        .content { position: relative; z-index: 1; }

        /* Letterhead */
        .letterhead { display: flex; align-items: center; gap: 14px; padding-bottom: 10px;
            border-bottom: 2.5px solid var(--brand); }
        .lh-logo { width: 64px; height: 64px; flex: 0 0 64px; display: flex; align-items: center; justify-content: center; }
        .lh-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .lh-text { flex: 1; text-align: center; }
        .lh-name { font-size: 24px; font-weight: 700; color: var(--brand-dark); letter-spacing: .4px; line-height: 1.15; }
        .lh-address { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .lh-contact { font-size: 10.5px; color: var(--muted); margin-top: 1px; }
        .lh-spacer { width: 64px; flex: 0 0 64px; }

        /* Title band */
        .title-band { text-align: center; margin: 12px 0 12px; }
        .title-band .doc-title {
            display: inline-block; background: var(--brand); color: #fff; font-size: 14.5px; font-weight: 600;
            letter-spacing: 2.5px; text-transform: uppercase; padding: 6px 34px; border-radius: 3px;
        }
        .title-band .doc-sub { margin-top: 5px; font-size: 12px; color: var(--muted); letter-spacing: .3px; }

        /* Meta grid */
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 12px; }
        table.meta td { border: 1px solid var(--line); padding: 5.5px 9px; }
        table.meta td.k { width: 13%; background: #f1f5f9; font-weight: 600; color: #334155; white-space: nowrap; }
        table.meta td.v { width: 20%; }

        /* Marks table */
        table.marks { width: 100%; border-collapse: collapse; }
        table.marks thead th {
            background: var(--brand-dark); color: #fff; font-size: 11.5px; font-weight: 600;
            letter-spacing: .5px; text-transform: uppercase; padding: 7px 6px; border: 1px solid var(--brand-dark);
        }
        table.marks thead th .fm { display: block; font-size: 9.5px; font-weight: 400; opacity: .85; text-transform: none; }
        table.marks td { border: 1px solid var(--line); padding: 5.5px 7px; text-align: center; font-size: 12.5px; }
        table.marks tbody tr:nth-child(even) td { background: var(--zebra); }
        table.marks td.name { text-align: left; }
        table.marks td.num { font-variant-numeric: tabular-nums; }
        table.marks td.total { font-weight: 700; color: var(--brand-dark); background: #eef4fb !important; }
        .ab { display: inline-block; font-size: 10px; font-weight: 700; color: #b91c1c;
              background: #fee2e2; border: 1px solid #fecaca; border-radius: 3px; padding: 1px 7px; letter-spacing: .5px; }

        /* Summary strip */
        .summary { display: flex; gap: 10px; margin-top: 12px; }
        .sum-card { flex: 1; border: 1px solid var(--line); border-top: 3px solid var(--brand); border-radius: 4px;
            text-align: center; padding: 7px 4px 8px; background: #fbfdff; }
        .sum-card .val { font-size: 17px; font-weight: 700; color: var(--brand-dark); }
        .sum-card .lbl { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 1px; }

        /* Signatures */
        .signs { display: flex; justify-content: space-between; gap: 24px; margin-top: 22mm; }
        .sign { flex: 1; text-align: center; font-size: 11.5px; color: #334155; }
        .sign .line { border-top: 1.3px dotted var(--line-dark); margin-bottom: 5px; }
        .sign .role { font-weight: 600; }
        .sign .hint { font-size: 10px; color: var(--muted); }

        /* Footer */
        .foot { margin-top: 10mm; padding-top: 6px; border-top: 1px solid var(--line);
            display: flex; justify-content: space-between; font-size: 9.8px; color: var(--muted); }

        .empty-note { text-align: center; padding: 40px 20px; border: 1.5px dashed var(--line-dark);
            color: var(--muted); font-size: 14px; border-radius: 6px; margin-top: 8px; }

        /* Toolbar (screen only) */
        .toolbar { position: fixed; top: 14px; right: 16px; z-index: 50; display: flex; gap: 8px; }
        .toolbar button {
            padding: 9px 22px; border: none; border-radius: 5px; cursor: pointer; font-size: 13.5px; font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }
        .btn-print { background: var(--brand); color: #fff; }
        .btn-close { background: #475569; color: #fff; }

        @page { size: A4 portrait; margin: 0; }
        @media print {
            html, body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; min-height: auto; margin: 0; box-shadow: none; padding: 11mm 11mm 12mm; }
            table.marks thead { display: table-header-group; }
            table.marks tr { page-break-inside: avoid; }
            .signs { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">&#128438; Print</button>
        <button class="btn-close" onclick="window.close()">Close</button>
    </div>

    <div class="sheet">
        <div class="top-rule"></div>
        @if($hasLogo)<img class="watermark" src="{{ $logoSrc }}" alt="">@endif

        <div class="content">
            <!-- Letterhead -->
            <div class="letterhead">
                <div class="lh-logo">@if($hasLogo)<img src="{{ $logoSrc }}" alt="Logo">@endif</div>
                <div class="lh-text">
                    <div class="lh-name">{{ $institute }}</div>
                    <div class="lh-address">{{ $address }}</div>
                    <div class="lh-contact">
                        {{ $phone ? 'Phone: '.$phone : '' }}{{ $phone && $email ? ' | ' : '' }}{{ $email ? 'Email: '.$email : '' }}{{ ($phone || $email) && $website ? ' | ' : '' }}{{ $website }}
                    </div>
                </div>
                <div class="lh-spacer"></div>
            </div>

            <!-- Title -->
            <div class="title-band">
                <span class="doc-title">Subject-wise Mark Entry Sheet</span>
                <div class="doc-sub">
                    {{ $data['exam']->title ?? '' }} &mdash; {{ $data['month']->title ?? '' }} {{ $data['year']->title ?? '' }}
                    @if($data['teacher_only']) &nbsp;|&nbsp; Entries of: <b>{{ $data['printed_by'] }}</b> @endif
                </div>
            </div>

            <!-- Meta -->
            <table class="meta">
                <tr>
                    <td class="k">Class / Program</td><td class="v">{{ $data['faculty']->faculty ?? '-' }}</td>
                    <td class="k">Session / Sec.</td><td class="v">{{ $data['semester']->semester ?? '-' }}</td>
                    <td class="k">Exam</td><td class="v">{{ $data['exam']->title ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="k">Subject</td>
                    <td class="v"><b>{{ $data['subject']->title ?? '-' }}</b>{{ !empty($data['subject']->code) ? ' ('.$data['subject']->code.')' : '' }}</td>
                    <td class="k">Full Mark</td><td class="v">{{ $fmt($fullMark) }}</td>
                    <td class="k">Entered By</td><td class="v">{{ $data['teacher_only'] ? $data['printed_by'] : 'All Teachers' }}</td>
                </tr>
            </table>

            @if($rows->count() == 0)
                <div class="empty-note">
                    No mark entry found for this selection{{ $data['teacher_only'] ? ' entered by you' : '' }}.
                </div>
            @else
            <!-- Marks -->
            <table class="marks">
                <thead>
                    <tr>
                        <th style="width:34px;">SL</th>
                        <th style="width:88px;">Roll / Reg.</th>
                        <th style="text-align:left;">Student Name</th>
                        @if($showTh)<th style="width:74px;">Theory<span class="fm">Full: {{ $fmt($data['limits']['theory']) }}</span></th>@endif
                        @if($showMcq)<th style="width:70px;">MCQ<span class="fm">Full: {{ $fmt($data['limits']['mcq']) }}</span></th>@endif
                        @if($showPr)<th style="width:74px;">Practical<span class="fm">Full: {{ $fmt($data['limits']['practical']) }}</span></th>@endif
                        <th style="width:76px;">Total<span class="fm">of {{ $fmt($fullMark) }}</span></th>
                        @if(!$data['teacher_only'])<th style="width:110px;">Entered By</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    @php
                        $rowTotal = ($row->absent_theory == 1 ? 0 : (float) $row->obtain_mark_theory)
                                  + (float) $row->obtain_mark_mcq
                                  + ($row->absent_practical == 1 ? 0 : (float) $row->obtain_mark_practical);
                    @endphp
                    <tr>
                        <td class="num">{{ $i + 1 }}</td>
                        <td class="num">{{ $row->reg_no }}</td>
                        <td class="name">{{ trim($row->first_name.' '.$row->middle_name.' '.$row->last_name) }}</td>
                        @if($showTh)
                            <td class="num">@if($row->absent_theory == 1)<span class="ab">AB</span>@else{{ $fmt($row->obtain_mark_theory) }}@endif</td>
                        @endif
                        @if($showMcq)
                            <td class="num">{{ $fmt($row->obtain_mark_mcq) }}</td>
                        @endif
                        @if($showPr)
                            <td class="num">@if($row->absent_practical == 1)<span class="ab">AB</span>@else{{ $fmt($row->obtain_mark_practical) }}@endif</td>
                        @endif
                        <td class="num total">{{ $fmt($rowTotal) }}</td>
                        @if(!$data['teacher_only'])<td style="font-size:11px;">{{ $row->entered_by_name ?: '-' }}</td>@endif
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary -->
            <div class="summary">
                <div class="sum-card"><div class="val">{{ $rows->count() }}</div><div class="lbl">Students</div></div>
                <div class="sum-card"><div class="val">{{ $rows->count() - $fullyAbsent }}</div><div class="lbl">Appeared</div></div>
                @if($showTh)
                    <div class="sum-card"><div class="val">{{ $absentTh }}</div><div class="lbl">Absent (CQ)</div></div>
                @endif
                @if($showPr)
                    <div class="sum-card"><div class="val">{{ $absentPr }}</div><div class="lbl">Absent (Practical)</div></div>
                @endif
                <div class="sum-card"><div class="val">{{ $fmt($highest) }}</div><div class="lbl">Highest</div></div>
                <div class="sum-card"><div class="val">{{ $fmt(round($average, 2)) }}</div><div class="lbl">Average</div></div>
            </div>
            @endif

            <!-- Signatures -->
            <div class="signs">
                <div class="sign">
                    <div class="line"></div>
                    <div class="role">Subject Teacher</div>
                    <div class="hint">{{ $data['teacher_only'] ? $data['printed_by'] : '' }}&nbsp;</div>
                </div>
                <div class="sign">
                    <div class="li