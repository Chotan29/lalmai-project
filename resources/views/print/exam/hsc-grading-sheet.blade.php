@extends('layouts.master')

@section('css')
    <style>
        :root {
            --brand: #0e4c8b; --brand-dark: #093763; --line: #cbd5e1; --line-dark: #475569;
            --ink: #111827; --muted: #64748b; --zebra: #f5f8fc;
        }
        body { font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif; font-size: 12px; color: var(--ink);
               -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @page { size: A4 portrait; margin: 8mm; }

        .hsc-wrapper { position: relative; width: 100%; max-width: 194mm; margin: 0 auto; background: #fff;
            padding: 0 0 10px; box-sizing: border-box; border: 1px solid var(--line); overflow: hidden; }
        .hsc-top-rule { height: 5px; background: linear-gradient(90deg, var(--brand-dark), var(--brand) 55%, #2e86d4); }
        .hsc-inner { padding: 12px 16px 0; }
        .hsc-watermark { position: absolute; top: 52%; left: 50%; transform: translate(-50%,-50%);
            width: 105mm; opacity: .05; z-index: 0; }
        .hsc-z { position: relative; z-index: 1; }

        .hsc-header { display: flex; align-items: center; gap: 10px; padding-bottom: 8px; border-bottom: 2.5px solid var(--brand); }
        .hsc-header-logo { width: 62px; flex: 0 0 62px; }
        .hsc-header-logo img { max-width: 62px; max-height: 62px; object-fit: contain; }
        .hsc-header-center { flex: 1; text-align: center; }
        .hsc-header-center h2 { margin: 0; font-size: 21px; color: var(--brand-dark); font-weight: 800; }
        .hsc-header-center .addr { font-size: 10.5px; color: var(--muted); margin-top: 1px; }
        .hsc-header-center h4 { margin: 2px 0 0; font-size: 12px; color: #334155; font-weight: 600; }
        .hsc-title-band { text-align: center; margin: 8px 0; }
        .hsc-title-band span { display: inline-block; background: var(--brand); color: #fff; font-size: 13px;
            font-weight: 700; letter-spacing: 3px; text-transform: uppercase; padding: 4px 30px; border-radius: 3px; }
        .hsc-header-right { width: 170px; flex: 0 0 170px; font-size: 10.5px; border: 1px solid var(--line);
            border-radius: 4px; padding: 5px 8px; line-height: 1.8; background: #fbfdff; }
        .hsc-header-right span { font-weight: 700; color: var(--brand-dark); }

        .hsc-info-table { width: 100%; border-collapse: collapse; margin: 7px 0; font-size: 11.5px; }
        .hsc-info-table td { padding: 2.5px 6px; }
        .hsc-info-table .label { font-weight: 700; color: #334155; white-space: nowrap; width: 150px; }
        .hsc-info-table .colon { width: 10px; }

        .hsc-table { width: 100%; border-collapse: collapse; font-size: 11px; margin: 4px 0; }
        .hsc-table th, .hsc-table td { border: 1px solid var(--line); padding: 4px 5px; text-align: center; vertical-align: middle; }
        .hsc-table thead th { background: var(--brand-dark); color: #fff; font-weight: 600; font-size: 10.5px;
            letter-spacing: .6px; text-transform: uppercase; border-color: var(--brand-dark); }
        .hsc-table tbody tr:nth-child(even) td { background: var(--zebra); }
        .hsc-table tfoot td { background: #eef4fb; font-weight: 700; color: var(--brand-dark); }
        .hsc-table .text-left { text-align: left; }
        .hsc-table td.subj { font-weight: 600; }
        .hsc-table td.fm { font-size: 10px; color: var(--muted); white-space: nowrap; }
        .hsc-table td.tot { font-weight: 700; color: var(--brand-dark); background: #eef4fb !important; }
        .opt-star { color: #b45309; font-weight: 700; }
        .ab-badge { display: inline-block; font-size: 9px; font-weight: 700; color: #b91c1c; background: #fee2e2;
            border: 1px solid #fecaca; border-radius: 3px; padding: 0 5px; letter-spacing: .5px; }

        .opt-note { font-size: 9.5px; color: #92400e; margin: 2px 0 0; }

        .hsc-bottom { display: flex; gap: 10px; margin-top: 8px; }
        .hsc-summary { flex: 1; }
        .hsc-summary table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        .hsc-summary td { border: 1px solid var(--line); padding: 3px 6px; }
        .hsc-summary td:first-child { font-weight: 600; color: #334155; background: #f8fafc; width: 55%; }
        .hsc-grade-scale { width: 280px; }
        .hsc-grade-scale table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .hsc-grade-scale th, .hsc-grade-scale td { border: 1px solid var(--line); padding: 2.5px 4px; text-align: center; }
        .hsc-grade-scale th { background: var(--brand-dark); color: #fff; border-color: var(--brand-dark); }
        .hsc-grade-scale-title { text-align: center; font-weight: 700; font-size: 10.5px; color: var(--brand-dark);
            margin-bottom: 3px; text-transform: uppercase; letter-spacing: 1px; }

        .hsc-rules { margin-top: 8px; border: 1px solid var(--line); border-left: 4px solid var(--brand);
            border-radius: 3px; background: #fbfdff; padding: 6px 10px; font-size: 10px; color: #334155; line-height: 1.6; }
        .hsc-rules strong { color: var(--brand-dark); }

        .hsc-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 20px; }
        .hsc-footer-date { font-size: 11px; color: #334155; }
        .hsc-footer-sign { text-align: center; font-size: 10.5px; }
        .hsc-footer-sign .sign-line { border-top: 1.3px dotted var(--line-dark); padding-top: 3px; margin-top: 28px;
            min-width: 170px; font-weight: 700; color: var(--brand-dark); }

        .badge-pass { background: #15803d; color: #fff; padding: 2px 9px; border-radius: 3px; font-size: 10.5px; font-weight: 700; }
        .badge-fail { background: #b91c1c; color: #fff; padding: 2px 9px; border-radius: 3px; font-size: 10.5px; font-weight: 700; }

        @media print {
            .no-print { display: none !important; }
            .hsc-wrapper { max-width: 100%; border: 1px solid var(--line); }
            body { margin: 0; padding: 0; }
            .page-content { padding: 0 !important; border: none !important; }
            .main-content, .main-content-inner { page-break-inside: avoid; }
        }
    </style>
@endsection

@section('content')
    @php
        /*Embed logo once as base64 so print never misses it*/
        $hgLogoSrc = '';
        if (isset($generalSetting->logo) && $generalSetting->logo) {
            $hgLogoFile = public_path('images/setting/general/'.$generalSetting->logo);
            if (is_file($hgLogoFile)) {
                $hgExt = strtolower(pathinfo($hgLogoFile, PATHINFO_EXTENSION));
                $hgMime = ($hgExt === 'jpg' || $hgExt === 'jpeg') ? 'jpeg' : $hgExt;
                $hgLogoSrc = 'data:image/'.$hgMime.';base64,'.base64_encode(file_get_contents($hgLogoFile));
            } else {
                $hgLogoSrc = asset('images/setting/general/'.$generalSetting->logo);
            }
        }
        $fmtMark = function ($v, $remark = '') {
            if (is_numeric($v)) return rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.').$remark;
            $s = trim((string) $v);
            return $s !== '' ? $s : '-';
        };
    @endphp

    @if(isset($data['student']) && $data['student']->count() > 0)
        @foreach($data['student'] as $student)
            <div class="main-content">
                <div class="col-sm-12 align-right no-print" style="margin-bottom:10px;">
                    <a href="#" class="btn btn-primary" onclick="window.print();">
                        <i class="ace-icon fa fa-print bigger-200"></i> Print
                    </a>
                </div>
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="hsc-wrapper">
                            <div class="hsc-top-rule"></div>
                            @if($hgLogoSrc)<img class="hsc-watermark" src="{{ $hgLogoSrc }}" alt="">@endif
                            <div class="hsc-inner hsc-z">

                                <div class="hsc-header">
                                    <div class="hsc-header-logo">
                                        @if($hgLogoSrc)<img src="{{ $hgLogoSrc }}">@endif
                                    </div>
                                    <div class="hsc-header-center">
                                        <h2>{{ $generalSetting->institute ?? 'Institution Name' }}</h2>
                                        <div class="addr">{{ $generalSetting->address ?? '' }}</div>
                                        <h4>{{ ViewHelper::getExamById($data['exam']) }} &mdash; {{ ViewHelper::getYearById($data['year']) }}</h4>
                                    </div>
                                    <div class="hsc-header-right">
                                        <div><span>Reg. No.:</span> {{ $student->reg_no }}</div>
                                        <div><span>Level:</span> {{ ViewHelper::getFacultyTitle($student->faculty) }}</div>
                                        <div><span>Sem./Sec.:</span> {{ ViewHelper::getSemesterTitle($student->semester) }}</div>
                                    </div>
                                </div>

                                <div class="hsc-title-band"><span>HSC Grade Sheet</span></div>

                                <table class="hsc-info-table">
                                    <tr>
                                        <td class="label">Name of the Student</td>
                                        <td class="colon">:</td>
                                        <td><strong>{{ strtoupper(trim($student->first_name.' '.$student->middle_name.' '.$student->last_name)) }}</strong></td>
                                        <td class="label">Date of Birth</td>
                                        <td class="colon">:</td>
                                        <td>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Program/Group</td>
                                        <td class="colon">:</td>
                                        <td>{{ ViewHelper::getFacultyTitle($student->faculty) }}</td>
                                        <td class="label">Session/Year</td>
                                        <td class="colon">:</td>
                                        <td>{{ ViewHelper::getYearById($data['year']) }}</td>
                                    </tr>
                                </table>

                                @php $hasOptional = false; @endphp
                                <table class="hsc-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="width:26px;">SN</th>
                                            <th rowspan="2" style="width:44px;">Code</th>
                                            <th rowspan="2" class="text-left">Subject</th>
                                            <th rowspan="2" style="width:78px;">Full Marks</th>
                                            <th colspan="4">Obtained Marks</th>
                                            <th rowspan="2" style="width:44px;">Grade</th>
                                            <th rowspan="2" style="width:40px;">GP</th>
                                            <th rowspan="2" style="width:52px;">Status</th>
                                        </tr>
                                        <tr>
                                            <th style="width:44px;">TH</th>
                                            <th style="width:44px;">MCQ</th>
                                            <th style="width:44px;">PR</th>
                                            <th style="width:52px;">Total</th>
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

                                                $fmParts = [];
                                                if ((float) ($subject->full_mark_theory ?? 0) > 0) $fmParts[] = (float) $subject->full_mark_theory + 0;
                                                if ((float) ($subject->full_mark_mcq ?? 0) > 0) $fmParts[] = (float) $subject->full_mark_mcq + 0;
                                                if ((float) ($subject->full_mark_practical ?? 0) > 0) $fmParts[] = (float) $subject->full_mark_practical + 0;
                                                $fmTotal = $subject->full_mark_total ?: array_sum($fmParts);
                                            @endphp
                                            <tr>
                                                <td>{{ $sn++ }}</td>
                                                <td>{{ $subject->code ?: ViewHelper::getSubjectCodeById($subject->subjects_id) }}</td>
                                                <td class="text-left subj">
                                                    {{ $subject->title ?: ViewHelper::getSubjectById($subject->subjects_id) }}@if($isOptional)<span class="opt-star">*</span>@endif
                                                </td>
                                                <td class="fm">{{ count($fmParts) ? implode('+', $fmParts).' = '.($fmTotal + 0) : ($fmTotal + 0) }}</td>
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
                                            <td colspan="4" class="text-left" style="text-align:right;">TOTAL OBTAINED</td>
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

                                <div class="hsc-rules">
                                    <strong>Rules:</strong>
                                    English uses combined pass at 33. Other subjects require separate pass in Theory and MCQ; practical subjects also require separate pass in Practical. Optional subject only adds bonus when GP is above 2.00.
                                </div>

                                <div class="hsc-footer">
                                    <div class="hsc-footer-date">
                                        <strong>Date of Issue:</strong> {{ \Carbon\Carbon::now()->format('d F Y') }}
                                    </div>
                                    <div class="hsc-footer-sign">
                                        <div class="sign-line">Controller of Examinations</div>
                                        <div>{{ $generalSetting->institute ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(!$loop->last)<div style="page-break-after:always;"></div>@endif
        @endforeach
    @endif
@endsection

@section('js')
    @include('includes.scripts.print_script')
@endsection
