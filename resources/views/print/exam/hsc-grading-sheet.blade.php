<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HSC Grade Sheet | {{ $generalSetting->institute ?? 'EduFirm' }}</title>
    @php
        $gs = $generalSetting ?? null;
        $institute = $gs->institute ?? 'Lalmai Govt. College';
        $address = $gs->address ?? 'Cumilla Sadar South, Cumilla';
        $phone = $gs->phone ?? '';
        $email = $gs->email ?? '';
        $website = $gs->website ?? '';

        /*Embed logo as base64 so print/PDF never miss it*/
        $hgLogoSrc = '';
        if (isset($gs->logo) && $gs->logo) {
            $hgLogoFile = public_path('images/setting/general/'.$gs->logo);
            if (is_file($hgLogoFile)) {
                $hgExt = strtolower(pathinfo($hgLogoFile, PATHINFO_EXTENSION));
                $hgMime = ($hgExt === 'jpg' || $hgExt === 'jpeg') ? 'jpeg' : $hgExt;
                $hgLogoSrc = 'data:image/'.$hgMime.';base64,'.base64_encode(file_get_contents($hgLogoFile));
            } else {
                $hgLogoSrc = asset('images/setting/general/'.$gs->logo);
            }
        }

        $fmtMark = function ($v, $remark = '') {
            if (is_numeric($v)) return rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.').$remark;
            $s = trim((string) $v);
            return $s !== '' ? $s : '-';
        };
    @endphp
    <style>
        :root {
            --brand: #0e4c8b; --brand-dark: #093763; --line: #cbd5e1; --line-dark: #475569;
            --ink: #111827; --muted: #64748b; --zebra: #f5f8fc;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { background: #e9edf2; }
        body { font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif; font-size: 12px;
               color: var(--ink); -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .sheet { position: relative; width: 210mm; min-height: 296mm; margin: 18px auto; background: #fff;
                 padding: 10mm 12mm 0; box-shadow: 0 4px 24px rgba(15,23,42,.18); overflow: hidden;
                 display: flex; flex-direction: column; }
        .top-rule { position: absolute; top: 0; left: 0; right: 0; height: 4.5px;
            background: linear-gradient(90deg, var(--brand-dark), var(--brand) 55%, #2e86d4); }
        .watermark { position: absolute; top: 52%; left: 50%; transform: translate(-50%,-50%);
            width: 110mm; opacity: .05; z-index: 0; }
        .content { position: relative; z-index: 1; flex: 1; }

        .gov-head { display: flex; align-items: center; gap: 10px; }
        .gh-logo { width: 72px; height: 72px; flex: 0 0 72px; display: flex; align-items: center; justify-content: center; }
        .gh-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .gh-center { flex: 1; text-align: center; }
        .gh-republic { font-size: 12px; font-weight: 600; color: #334155; letter-spacing: .8px; }
        .gh-office { display: inline-block; margin-top: 3px; font-size: 11.5px; font-weight: 600; color: var(--brand-dark);
            background: #eef4fb; border: 1px solid #cfe0f2; border-radius: 14px; padding: 1px 16px;
            letter-spacing: 1.5px; text-transform: uppercase; }
        .gh-name { font-size: 28px; font-weight: 800; color: var(--brand-dark); line-height: 1.25; margin-top: 2px; }
        .gh-addr { font-size: 12px; color: var(--muted); margin-top: 1px; }

        .eiin-band { margin-top: 8px; background: var(--brand-dark); color: #fff; text-align: center;
            font-size: 12px; font-weight: 600; letter-spacing: .8px; padding: 5px 6px; border-radius: 3px; }

        .title-band { text-align: center; margin: 10px 0 8px; }
        .title-band span { display: inline-block; background: var(--brand); color: #fff; font-size: 14.5px;
            font-weight: 700; letter-spacing: 4px; text-transform: uppercase; padding: 5px 36px; border-radius: 3px; }
        .title-band .sub { margin-top: 4px; font-size: 12.5px; font-weight: 600; color: #334155; }

        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11.5px; }
        table.meta td { border: 1px solid var(--line); padding: 5px 9px; }
        table.meta td.k { width: 16%; background: #f1f5f9; font-weight: 600; color: #334155; white-space: nowrap; }

        table.hsc-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.hsc-table th, table.hsc-table td { border: 1px solid var(--line); padding: 4.5px 5px; text-align: center; vertical-align: middle; }
        table.hsc-table thead th { background: var(--brand-dark); color: #fff; font-weight: 600; font-size: 10.5px;
            letter-spacing: .6px; text-transform: uppercase; border-color: var(--brand-dark); }
        table.hsc-table tbody tr:nth-child(even) td { background: var(--zebra); }
        table.hsc-table tfoot td { background: #eef4fb; font-weight: 700; color: var(--brand-dark); }
        table.hsc-table .text-left { text-align: left; }
        table.hsc-table td.subj { font-weight: 600; }
        table.hsc-table td.tot { font-weight: 700; color: var(--brand-dark); background: #eef4fb !important; }
        .opt-star { color: #b45309; font-weight: 700; }
        .opt-note { font-size: 9.5px; color: #92400e; margin: 2px 0 0; }

        .hsc-bottom { display: flex; gap: 10px; margin-top: 10px; }
        .hsc-summary { flex: 1; }
        .hsc-summary table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        .hsc-summary td { border: 1px solid var(--line); padding: 3.5px 7px; }
        .hsc-summary td:first-child { font-weight: 600; color: #334155; background: #f8fafc; width: 55%; }
        .hsc-grade-scale { width: 280px; }
        .hsc-grade-scale table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .hsc-grade-scale th, .hsc-grade-scale td { border: 1px solid var(--line); padding: 2.5px 4px; text-align: center; }
        .hsc-grade-scale th { background: var(--brand-dark); color: #fff; border-color: var(--brand-dark); }
        .hsc-grade-scale-title { text-align: center; font-weight: 700; font-size: 10.5px; color: var(--brand-dark);
            margin-bottom: 3px; text-transform: uppercase; letter-spacing: 1px; }

        .noteblock { margin-top: 10px; border: 1px solid var(--line); border-left: 4px solid var(--brand);
            border-radius: 3px; background: #fbfdff; padding: 7px 11px; font-size: 10px; color: #334155;
            line-height: 1.6; text-align: justify; }
        .noteblock strong { color: var(--brand-dark); }

        .sign-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 13mm; }
        .issue-date { font-size: 11px; color: #334155; }
        .sig { text-align: center; font-size: 11px; color: #1f2937; }
        .sig .line { border-top: 1.3px dotted var(--line-dark); margin-bottom: 4px; min-width: 190px; }
        .sig .role { font-weight: 700; color: var(--brand-dark); }

        .contact-strip { margin: 7mm -12mm 0; background: var(--brand-dark); color: #e2e8f0; text-align: center;
            font-size: 10.5px; letter-spacing: .4px; padding: 6px 10px; }
        .contact-strip b { color: #fff; }

        .badge-pass { background: #15803d; color: #fff; padding: 2px 9px; border-radius: 3px; font-size: 10.5px; font-weight: 700; }
        .badge-fail { background: #b91c1c; color: #fff; padding: 2px 9px; border-radius: 3px; font-size: 10.5px; font-weight: 700; }

        .toolbar { position: fixed; top: 14px; right: 16px; display: flex; gap: 8px; z-index: 60; }
        .toolbar button { padding: 9px 22px; border: none; border-radius: 5px; cursor: pointer; font-size: 13.5px;
            font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,.25); }
        .btn-print { background: var(--brand); color: #fff; }
        .btn-back { background: #475569; color: #fff; }

        @page { size: A4 portrait; margin: 0; }
        @media print {
            .toolbar { display: none; }
            html, body { background: #fff; }
            .sheet { margin: 0; box-shadow: none; width: auto; min-height: 296mm; page-break-after: always; }
            .sheet:last-child { page-break-after: auto; }
            table.hsc-table tr { page-break-inside: avoid; }
            .sign-row { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">&#128438; Print</button>
        <button class="btn-back" onclick="history.back()">Back</button>
    </div>

    @if(isset($data['student']) && $data['student']->count() > 0)
        @foreach($data['student'] as $student)
        <div class="sheet">
            <div class="top-rule"></div>
            @if($hgLogoSrc)<img class="watermark" src="{{ $hgLogoSrc }}" alt="">@endif

            <div class="content">
                <!-- Letterhead -->
                <div class="gov-head">
                    <div class="gh-logo">@if($hgLogoSrc)<img src="{{ $hgLogoSrc }}" alt="">@endif</div>
                    <div class="gh-center">
                        <div class="gh-republic">Government of the People's Republic of Bangladesh</div>
                        <div class="gh-office">Office of the Principal</div>
                        <div class="gh-name">{{ $institute }}</div>
                        <div class="gh-addr">{{ $address }}</div>
                    </div>
                    <div class="gh-logo">@if($hgLogoSrc)<img src="{{ $hgLogoSrc }}" alt="">@endif</div>
                </div>

                <div class="eiin-band">EIIN No.- 105746 &nbsp;&nbsp;|&nbsp;&nbsp; H.S.C Code No.- 7850 &nbsp;&nbsp;|&nbsp;&nbsp; N.U Code No.- 3729</div>

                <div class="title-band">
                    <span>HSC Grade Sheet</span>
                    <div class="sub">{{ ViewHelper::getExamById($data['exam']) }} &mdash; {{ ViewHelper::getYearById($data['year']) }}</div>
                </div>

                <!-- Student info -->
                <table class="meta">
                    <tr>
                        <td class="k">Name of Student</td>
                        <td colspan="3"><strong>{{ strtoupper(trim($student->first_name.' '.$student->middle_name.' '.$student->last_name)) }}</strong></td>
                        <td class="k">Reg. No.</td>
                        <td><strong>{{ $student->reg_no }}</strong></td>
                    </tr>
                    <tr>
                        <td class="k">Program/Group</td>
                        <td>{{ ViewHelper::getFacultyTitle($student->faculty) }}</td>
                        <td class="k">Sem./Sec.</td>
                        <td>{{ ViewHelper::getSemesterTitle($student->semester) }}</td>
                        <td class="k">Date of Birth</td>
                        <td>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') }}</td>
                    </tr>
                </table>

                @php $hasOptional = false; @endphp
                <table class="hsc-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:26px;">SN</th>
                            <th rowspan="2" style="width:46px;">Code</th>
                            <th rowspan="2" class="text-left">Subject</th>
                            <th colspan="4">Obtained Marks</th>
                            <th rowspan="2" style="width:46px;">Grade</th>
                            <th rowspan="2" style="width:42px;">GP</th>
                            <th rowspan="2" style="width:54px;">Status</th>
                        </tr>
                        <tr>
                            <th style="width:46px;">TH</th>
                            <th style="width:46px;">MCQ</th>
                            <th style="width:46px;">PR</th>
                            <th style="width:54px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sn = 1; @endphp
                        @foreach($student->subjects as $subject)
                            @php
                                $isOptional = strtolower(trim((string) ($subject->sub_type ?? ''))) === 'optional';
                                if ($isOptional) $hasOptional = true;

                                $obTh = is_numeric($subject->obtain_mark_theory) ? (float) $subject->obtain_mark_theory : 0;
                                $obMcq = is_numeric($subject->obtain_mark_mcq) ? (float) $subject->obtain_mark_mcq : 0;
                                $obPr = is_numeric($subject->obtain_mark_practical) ? (float) $subject->obtain_mark_practical : 0;
                                $obTotal = $obTh + $obMcq + $obPr;
                            @endphp
                            <tr>
                                <td>{{ $sn++ }}</td>
                                <td>{{ $subject->code ?: ViewHelper::getSubjectCodeById($subject->subjects_id) }}</td>
                                <td class="text-left subj">
                                    {{ $subject->title ?: ViewHelper::getSubjectById($subject->subjects_id) }}@if($isOptional)<span class="opt-star">*</span>@endif
                                </td>
                                <td>{{ $fmtMark($subject->obtain_mark_theory, $subject->th_remark ?? '') }}</td>
                                <td>{{ $fmtMark($subject->obtain_mark_mcq, $subject->mcq_remark ?? '') }}</td>
                                <td>{{ $fmtMark($subject->obtain_mark_practical, $subject->pr_remark ?? '') }}</td>
                                <td class="tot">{{ rtrim(rtrim(number_format($obTotal, 2, '.', ''), '0'), '.') }}</td>
                                <td><strong>{{ $subject->final_grade }}</strong></td>
                                <td><strong>{{ $subject->grade_point }}</strong></td>
                                <td>
                                    @if($subject->subject_result === 'Pass')
                                        <span class="badge-pass">Pass</span>
                                    @else
                                        <span class="badge-fail">Fail</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;">TOTAL OBTAINED</td>
                            <td>{{ $student->total_mark_theory ?? '-' }}</td>
                            <td>{{ $student->total_mark_mcq ?? '-' }}</td>
                            <td>{{ $student->total_mark_practical ?? '-' }}</td>
                            <td>{{ $student->total_obtain ?? '-' }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
                @if($hasOptional)
                    <div class="opt-note"><span class="opt-star">*</span> Optional subject &mdash; GPA bonus applied as per rules.</div>
                @endif

                <div class="hsc-bottom">
                    <div class="hsc-summary">
                        <table>
                            <tr>
                                <td>Total Marks Obtained</td>
                                <td><strong>{{ $student->total_obtain ?? '0' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Percentage</td>
                                <td><strong>{{ number_format((float) ($student->percentage ?? 0), 2) }}%</strong></td>
                            </tr>
                            <tr>
                                <td>Base GPA (Compulsory / 6)</td>
                                <td><strong>{{ $student->gpa_base ?? '0.00' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Optional Bonus</td>
                                <td><strong>{{ $student->optional_bonus ?? '0.00' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Final GPA</td>
                                <td><strong>{{ $student->gpa_average ?? '0.00' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Overall Grade</td>
                                <td><strong>{{ $student->gpa_grade ?? 'F' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Rank</td>
                                <td>{{ $student->rank ?? 'X' }}</td>
                            </tr>
                            <tr>
                                <td style="font-size:12px;">RESULT</td>
                                <td>
                                    @if(($student->gpa_remark ?? 'Fail') === 'Pass')
                                        <span class="badge-pass" style="font-size:12px; padding:3px 12px;">PASSED</span>
                                    @else
                                        <span class="badge-fail" style="font-size:12px; padding:3px 12px;">FAILED</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="hsc-grade-scale">
                        <div class="hsc-grade-scale-title">HSC Grade Scale</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                    <th>GP</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['grade-scale-range'] as $grade)
                                    <tr>
                                        <td>{{ rtrim(rtrim(number_format($grade->percentage_from, 2, '.', ''), '0'), '.') }} - {{ rtrim(rtrim(number_format($grade->percentage_to, 2, '.', ''), '0'), '.') }}</td>
                                        <td>{{ $grade->name }}</td>
                                        <td>{{ number_format((float) $grade->grade_point, 2) }}</td>
                                        <td>{{ $grade->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="noteblock">
                    <strong>Rules:</strong>
                    English uses combined pass at 33. Other subjects require separate pass in Theory and MCQ; practical subjects also require separate pass in Practical. Optional subject only adds bonus when GP is above 2.00.
                </div>

                <div class="sign-row">
                    <div class="issue-date"><strong>Date of Issue:</strong> {{ \Carbon\Carbon::now()->format('d F Y') }}</div>
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
                &nbsp;&nbsp;|&nbsp;&nbsp; This is a computer generated grade sheet
            </div>
        </div>
        @endforeach
    @endif
</body>
</html>
