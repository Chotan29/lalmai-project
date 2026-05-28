@extends('layouts.master')

@section('css')
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        @page { size: A4 portrait; margin: 8mm; }
        .hsc-wrapper {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
            border: 3px solid #1a4f7a;
            padding: 14px 18px;
            background: #fff;
            box-sizing: border-box;
        }
        .hsc-header { display: flex; align-items: center; margin-bottom: 8px; }
        .hsc-header-logo { width: 80px; text-align: center; }
        .hsc-header-logo img { width: 70px; }
        .hsc-header-center { flex: 1; text-align: center; }
        .hsc-header-center h2 { margin: 0; font-size: 17px; color: #1a4f7a; font-weight: bold; }
        .hsc-header-center h4 { margin: 2px 0; font-size: 13px; color: #333; }
        .hsc-header-center .hsc-title { color: #c0392b; font-size: 16px; font-weight: bold; margin-top: 4px; letter-spacing: 1px; }
        .hsc-header-right { width: 180px; font-size: 11px; border: 1px solid #aaa; padding: 6px 8px; line-height: 1.8; }
        .hsc-header-right span { font-weight: bold; }
        .hsc-info-table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 12px; }
        .hsc-info-table td { padding: 3px 6px; }
        .hsc-info-table .label { font-weight: bold; white-space: nowrap; width: 160px; }
        .hsc-info-table .colon { width: 10px; }
        .hsc-divider { border: 1.5px solid #1a4f7a; margin: 6px 0; }
        .hsc-table { width: 100%; border-collapse: collapse; font-size: 10px; margin: 4px 0; }
        .hsc-table th, .hsc-table td { border: 1px solid #555; padding: 2px 3px; text-align: center; vertical-align: middle; }
        .hsc-table thead th { background-color: #dce8f5; color: #1a4f7a; font-weight: bold; }
        .hsc-table tfoot td { background-color: #f5f5f5; font-weight: bold; }
        .hsc-table .text-left { text-align: left; }
        .hsc-bottom { display: flex; gap: 8px; margin-top: 6px; }
        .hsc-summary { flex: 1; }
        .hsc-summary table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .hsc-summary td { border: 1px solid #555; padding: 3px 5px; }
        .hsc-summary td:first-child { font-weight: bold; }
        .hsc-grade-scale { width: 290px; }
        .hsc-grade-scale table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .hsc-grade-scale th, .hsc-grade-scale td { border: 1px solid #555; padding: 3px 4px; text-align: center; }
        .hsc-grade-scale th { background-color: #dce8f5; color: #1a4f7a; }
        .hsc-grade-scale-title { text-align: center; font-weight: bold; font-size: 11px; color: #1a4f7a; margin-bottom: 3px; text-transform: uppercase; }
        .hsc-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 22px; }
        .hsc-footer-date { font-size: 12px; }
        .hsc-footer-sign { text-align: center; font-size: 11px; }
        .hsc-footer-sign .sign-line { border-top: 1.5px solid #333; padding-top: 2px; margin-top: 30px; min-width: 160px; }
        .badge-pass { background: #27ae60; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
        .badge-fail { background: #c0392b; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
        @media print {
            .no-print { display: none !important; }
            .hsc-wrapper {
                border: 2px solid #1a4f7a;
                width: 100%;
                max-width: 100%;
                padding: 8px 10px;
            }
            body { margin: 0; padding: 0; }
            .page-content { padding: 0 !important; border: none !important; }
            .main-content, .main-content-inner { page-break-inside: avoid; }
        }
    </style>
@endsection

@section('content')
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
                            <div class="hsc-header">
                                <div class="hsc-header-logo">
                                    @if(isset($generalSetting->logo))
                                        <img src="{{ asset('images/setting/general/'.$generalSetting->logo) }}">
                                    @endif
                                </div>
                                <div class="hsc-header-center">
                                    <h2>{{ $generalSetting->institute ?? 'Institution Name' }}</h2>
                                    <h4>{{ ViewHelper::getExamById($data['exam']) }} Examination - {{ ViewHelper::getYearById($data['year']) }}</h4>
                                    <div class="hsc-title">HSC GRADE SHEET</div>
                                </div>
                                <div class="hsc-header-right">
                                    <div><span>Reg. No.:</span> {{ $student->reg_no }}</div>
                                    <div><span>Level:</span> {{ ViewHelper::getFacultyTitle($student->faculty) }}</div>
                                    <div><span>Sem./Sec.:</span> {{ ViewHelper::getSemesterTitle($student->semester) }}</div>
                                </div>
                            </div>

                            <div class="hsc-divider"></div>

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

                            <div class="hsc-divider"></div>

                            <table class="hsc-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">SN</th>
                                        <th rowspan="2">Code</th>
                                        <th rowspan="2" class="text-left">Subject</th>
                                        <th rowspan="2">Type</th>
                                        <th colspan="3">Full Mark</th>
                                        <th colspan="3">Pass Mark</th>
                                        <th colspan="3">Obtained</th>
                                        <th rowspan="2">Grade</th>
                                        <th rowspan="2">GP</th>
                                        <th rowspan="2">Status</th>
                                    </tr>
                                    <tr>
                                        <th>TH</th>
                                        <th>MCQ</th>
                                        <th>PR</th>
                                        <th>TH</th>
                                        <th>MCQ</th>
                                        <th>PR</th>
                                        <th>TH</th>
                                        <th>MCQ</th>
                                        <th>PR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sn = 1; @endphp
                                    @foreach($student->subjects as $subject)
                                        <tr>
                                            <td>{{ $sn++ }}</td>
                                            <td>{{ $subject->code ?: ViewHelper::getSubjectCodeById($subject->subjects_id) }}</td>
                                            <td class="text-left">
                                                {{ $subject->title ?: ViewHelper::getSubjectById($subject->subjects_id) }}
                                                <br>
                                                <small>
                                                    Full Mark: Theory {{ $subject->full_mark_theory ?: 0 }} | MCQ {{ $subject->full_mark_mcq ?: 0 }} | Practical {{ $subject->full_mark_practical ?: 0 }} | Total {{ $subject->full_mark_total ?: 0 }}
                                                </small>
                                            </td>
                                            <td>
                                                {{ strtoupper($subject->sub_type ?? 'Compulsory') }}
                                                <br>
                                                <small>{{ $subject->hsc_rule_label ?? '' }}</small>
                                            </td>
                                            <td>{{ $subject->full_mark_theory ?: '-' }}</td>
                                            <td>{{ $subject->full_mark_mcq ?: '-' }}</td>
                                            <td>{{ $subject->full_mark_practical ?: '-' }}</td>
                                            <td>{{ $subject->pass_mark_theory ?: '-' }}</td>
                                            <td>{{ $subject->pass_mark_mcq ?: '-' }}</td>
                                            <td>{{ $subject->pass_mark_practical ?: '-' }}</td>
                                            <td>{{ is_numeric($subject->obtain_mark_theory) ? $subject->obtain_mark_theory.($subject->th_remark ?? '') : ($subject->obtain_mark_theory ?: '-') }}</td>
                                            <td>{{ is_numeric($subject->obtain_mark_mcq) ? $subject->obtain_mark_mcq.($subject->mcq_remark ?? '') : ($subject->obtain_mark_mcq ?: '-') }}</td>
                                            <td>{{ is_numeric($subject->obtain_mark_practical) ? $subject->obtain_mark_practical.($subject->pr_remark ?? '') : ($subject->obtain_mark_practical ?: '-') }}</td>
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
                                        <td colspan="10" class="text-right">TOTAL OBTAINED</td>
                                        <td>{{ $student->total_mark_theory ?? '-' }}</td>
                                        <td>{{ $student->total_mark_mcq ?? '-' }}</td>
                                        <td>{{ $student->total_mark_practical ?? '-' }}</td>
                                        <td colspan="3"><strong>{{ $student->total_obtain ?? '-' }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>

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
                                            <td style="font-size:14px;">RESULT</td>
                                            <td>
                                                @if(($student->gpa_remark ?? 'Fail') === 'Pass')
                                                    <span class="badge-pass" style="font-size:13px; padding:3px 12px;">PASSED</span>
                                                @else
                                                    <span class="badge-fail" style="font-size:13px; padding:3px 12px;">FAILED</span>
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

                            <div style="font-size:10px; margin-top:8px; color:#555;">
                                <strong>Rules:</strong>
                                English uses combined pass at 33. Other subjects require separate pass in Theory and MCQ; practical subjects also require separate pass in Practical. Optional subject only adds bonus when GP is above 2.00.
                            </div>

                            <div class="hsc-footer">
                                <div class="hsc-footer-date">
                                    <strong>Date of Issue:</strong> {{ \Carbon\Carbon::now()->format('d F Y') }}
                                </div>
                                <div class="hsc-footer-sign">
                                    <div class="sign-line">Controller of Examination</div>
                                    <div>{{ $generalSetting->institute ?? '' }}</div>
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
