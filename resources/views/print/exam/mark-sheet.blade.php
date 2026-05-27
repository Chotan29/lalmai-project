@extends('layouts.master')

@section('css')
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .ms-wrapper {
            width: 720px;
            margin: 0 auto;
            border: 3px solid #1a4f7a;
            padding: 14px 18px;
            background: #fff;
        }
        /* Header */
        .ms-header { display: flex; align-items: center; margin-bottom: 8px; }
        .ms-header-logo { width: 80px; text-align: center; }
        .ms-header-logo img { width: 70px; }
        .ms-header-center { flex: 1; text-align: center; }
        .ms-header-center h2 { margin: 0; font-size: 17px; color: #1a4f7a; font-weight: bold; }
        .ms-header-center h4 { margin: 2px 0; font-size: 13px; color: #333; }
        .ms-header-center .ms-title { color: #c0392b; font-size: 16px; font-weight: bold; margin-top: 4px; letter-spacing: 1px; }
        .ms-header-right { width: 160px; font-size: 11px; border: 1px solid #aaa; padding: 6px 8px; line-height: 1.8; }
        .ms-header-right span { font-weight: bold; }
        /* Student info */
        .ms-info-table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 12px; }
        .ms-info-table td { padding: 3px 6px; }
        .ms-info-table .label { font-weight: bold; white-space: nowrap; width: 160px; }
        .ms-info-table .colon { width: 10px; }
        /* Divider */
        .ms-divider { border: 1.5px solid #1a4f7a; margin: 6px 0; }
        /* Marks table */
        .ms-table { width: 100%; border-collapse: collapse; font-size: 12px; margin: 6px 0; }
        .ms-table th, .ms-table td { border: 1px solid #555; padding: 4px 6px; text-align: center; vertical-align: middle; }
        .ms-table thead th { background-color: #dce8f5; color: #1a4f7a; font-weight: bold; }
        .ms-table tfoot td { background-color: #f5f5f5; font-weight: bold; }
        .ms-table .text-left { text-align: left; }
        /* Summary & Grade scale side by side */
        .ms-bottom { display: flex; gap: 10px; margin-top: 8px; }
        .ms-summary { flex: 1; }
        .ms-summary table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .ms-summary table td { border: 1px solid #555; padding: 4px 8px; }
        .ms-summary table td:first-child { font-weight: bold; }
        .ms-grade-scale { width: 200px; }
        .ms-grade-scale table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .ms-grade-scale table th { background-color: #dce8f5; border: 1px solid #555; padding: 3px 5px; text-align: center; font-weight: bold; }
        .ms-grade-scale table td { border: 1px solid #555; padding: 3px 5px; text-align: center; }
        /* Footer */
        .ms-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 20px; }
        .ms-footer-date { font-size: 12px; }
        .ms-footer-sign { text-align: center; font-size: 11px; }
        .ms-footer-sign .sign-line { border-top: 1.5px solid #333; padding-top: 2px; margin-top: 30px; min-width: 160px; }
        /* Result badge */
        .badge-pass { background: #27ae60; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
        .badge-fail { background: #c0392b; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
        /* Print */
        @media print {
            .no-print { display: none !important; }
            .ms-wrapper { border: 3px solid #1a4f7a; width: 100%; }
            body { margin: 0; padding: 0; }
            .page-content { padding: 0 !important; border: none !important; }
        }
    </style>
@endsection

@section('content')
    @if($data['student'] && $data['student']->count() > 0)
        @foreach($data['student'] as $student)
        <div class="main-content">
            <div class="col-sm-12 align-right no-print" style="margin-bottom:10px;">
                <a href="#" class="btn btn-primary" onclick="window.print();">
                    <i class="ace-icon fa fa-print bigger-200"></i> Print
                </a>
            </div>
            <div class="main-content-inner">
                <div class="page-content">
                    <div class="ms-wrapper">

                        {{-- ===== HEADER ===== --}}
                        <div class="ms-header">
                            <div class="ms-header-logo">
                                @if(isset($generalSetting->logo))
                                    <img src="{{ asset('images/setting/general/'.$generalSetting->logo) }}">
                                @endif
                            </div>
                            <div class="ms-header-center">
                                <h2>{{ $generalSetting->institute ?? 'Institution Name' }}</h2>
                                <h4>{{ ViewHelper::getExamById($data['exam']) }} Examination — {{ ViewHelper::getYearById($data['year']) }}</h4>
                                <div class="ms-title">MARKS SHEET</div>
                            </div>
                            <div class="ms-header-right">
                                <div><span>Reg. No.:</span> {{ $student->reg_no }}</div>
                                <div><span>Level:</span> {{ ViewHelper::getFacultyTitle($data['faculty']) }}</div>
                                <div><span>Sem./Sec.:</span> {{ ViewHelper::getSemesterTitle($data['semester']) }}</div>
                            </div>
                        </div>

                        <div class="ms-divider"></div>

                        {{-- ===== STUDENT INFO ===== --}}
                        <table class="ms-info-table">
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
                                <td>{{ ViewHelper::getFacultyTitle($data['faculty']) }}</td>
                                <td class="label">Session/Year</td>
                                <td class="colon">:</td>
                                <td>{{ ViewHelper::getYearById($data['year']) }}</td>
                            </tr>
                        </table>

                        <div class="ms-divider"></div>

                        {{-- ===== MARKS TABLE ===== --}}
                        <table class="ms-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:40px;">SN</th>
                                    <th rowspan="2" style="width:60px;">Subject Code</th>
                                    <th rowspan="2" class="text-left" style="min-width:160px;">Subject</th>
                                    <th colspan="2">Full Mark</th>
                                    <th colspan="2">Pass Mark</th>
                                    <th colspan="2">Marks Obtained</th>
                                    <th rowspan="2" style="width:50px;">Total</th>
                                    <th rowspan="2" style="width:55px;">Status</th>
                                </tr>
                                <tr>
                                    <th>TH</th><th>PR</th>
                                    <th>TH</th><th>PR</th>
                                    <th>TH</th><th>PR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($student->subjects && $student->subjects->count() > 0)
                                    @php $sn = 1; @endphp
                                    @foreach($student->subjects as $subject)
                                    <tr>
                                        <td>{{ $sn++ }}</td>
                                        <td>{{ ViewHelper::getSubjectCodeById($subject->subjects_id) }}</td>
                                        <td class="text-left">{{ ViewHelper::getSubjectById($subject->subjects_id) }}</td>
                                        <td>{{ $subject->full_mark_theory ?: '-' }}</td>
                                        <td>{{ $subject->full_mark_practical ?: '-' }}</td>
                                        <td>{{ $subject->pass_mark_theory ?: '-' }}</td>
                                        <td>{{ $subject->pass_mark_practical ?: '-' }}</td>
                                        <td>{{ is_numeric($subject->obtain_mark_theory) ? $subject->obtain_mark_theory.($subject->th_remark ?? '') : ($subject->obtain_mark_theory ?: '-') }}</td>
                                        <td>{{ is_numeric($subject->obtain_mark_practical) ? $subject->obtain_mark_practical.($subject->pr_remark ?? '') : ($subject->obtain_mark_practical ?: '-') }}</td>
                                        <td><strong>{{ $subject->total_obtain_mark ?: '-' }}</strong></td>
                                        <td>
                                            @if($subject->remark == '*')
                                                <span class="badge-fail">Fail</span>
                                            @else
                                                <span class="badge-pass">Pass</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right" style="font-weight:bold;">TOTAL MARKS</td>
                                    <td>{{ $student->subjects->sum('full_mark_theory') ?: '-' }}</td>
                                    <td>{{ $student->subjects->sum('full_mark_practical') ?: '-' }}</td>
                                    <td>{{ $student->subjects->sum('pass_mark_theory') ?: '-' }}</td>
                                    <td>{{ $student->subjects->sum('pass_mark_practical') ?: '-' }}</td>
                                    <td>{{ $student->total_mark_theory ?: '-' }}</td>
                                    <td>{{ $student->total_mark_practical ?: '-' }}</td>
                                    <td><strong>{{ $student->total_obtain ?: '-' }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- ===== SUMMARY + GRADE SCALE ===== --}}
                        <div class="ms-bottom">
                            <div class="ms-summary">
                                <table>
                                    <tr>
                                        <td>Total Marks Obtained</td>
                                        <td><strong>{{ $student->total_obtain }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Percentage</td>
                                        <td><strong>{{ number_format((float)$student->percentage, 2) }}%</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Position on Total</td>
                                        <td>{{ $student->position }}</td>
                                    </tr>
                                    @php
                                        $remarksArr = $student->subjects->pluck('remark')->toArray();
                                        $prRemarksArr = $student->subjects->pluck('pr_remark')->toArray();
                                        $isFail = in_array('*', $remarksArr) || in_array('*', $prRemarksArr);
                                    @endphp
                                    <tr>
                                        <td>Rank</td>
                                        <td>{{ $isFail ? 'X' : $student->rank }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:14px;">RESULT</td>
                                        <td>
                                            @if($isFail)
                                                <span class="badge-fail" style="font-size:13px; padding:3px 12px;">FAILED</span>
                                            @else
                                                <span class="badge-pass" style="font-size:13px; padding:3px 12px;">PASSED</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="ms-grade-scale">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Marks (%)</th>
                                            <th>Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>80 – 100</td><td>A+ (Distinction)</td></tr>
                                        <tr><td>70 – 79</td><td>A (Very Good)</td></tr>
                                        <tr><td>60 – 69</td><td>B (Good)</td></tr>
                                        <tr><td>50 – 59</td><td>C (Average)</td></tr>
                                        <tr><td>40 – 49</td><td>D (Pass)</td></tr>
                                        <tr><td>Below 40</td><td>F (Fail)</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ===== FOOTER ===== --}}
                        <div class="ms-footer">
                            <div class="ms-footer-date">
                                <strong>Date of Publication:</strong> {{ \Carbon\Carbon::now()->format('d F Y') }}
                            </div>
                            <div class="ms-footer-sign">
                                <div class="sign-line">Controller of Examination</div>
                                <div>{{ $generalSetting->institute ?? '' }}</div>
                            </div>
                        </div>

                        <div style="text-align:center; font-size:10px; margin-top:10px; border-top:1px solid #ccc; padding-top:4px;">
                            ** This Mark Sheet is issued without any alteration or erasure **
                        </div>

                    </div><!-- /.ms-wrapper -->
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
        </div>
            @endif
@endsection

@section('js')
    <!-- inline scripts related to this page -->
   @include('includes.scripts.print_script')
@endsection