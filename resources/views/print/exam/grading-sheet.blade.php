@extends('layouts.master')

@section('css')
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .gs-wrapper {
            width: 740px;
            margin: 0 auto;
            border: 3px solid #1a4f7a;
            padding: 14px 18px;
            background: #fff;
        }
        /* Header */
        .gs-header { display: flex; align-items: center; margin-bottom: 8px; }
        .gs-header-logo { width: 80px; text-align: center; }
        .gs-header-logo img { width: 70px; }
        .gs-header-center { flex: 1; text-align: center; }
        .gs-header-center h2 { margin: 0; font-size: 17px; color: #1a4f7a; font-weight: bold; }
        .gs-header-center h4 { margin: 2px 0; font-size: 13px; color: #333; }
        .gs-header-center .gs-title { color: #c0392b; font-size: 16px; font-weight: bold; margin-top: 4px; letter-spacing: 1px; }
        .gs-header-right { width: 160px; font-size: 11px; border: 1px solid #aaa; padding: 6px 8px; line-height: 1.8; }
        .gs-header-right span { font-weight: bold; }
        /* Student info */
        .gs-info-table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 12px; }
        .gs-info-table td { padding: 3px 6px; }
        .gs-info-table .label { font-weight: bold; white-space: nowrap; width: 160px; }
        .gs-info-table .colon { width: 10px; }
        /* Divider */
        .gs-divider { border: 1.5px solid #1a4f7a; margin: 6px 0; }
        /* Marks table */
        .gs-table { width: 100%; border-collapse: collapse; font-size: 12px; margin: 6px 0; }
        .gs-table th, .gs-table td { border: 1px solid #555; padding: 4px 6px; text-align: center; vertical-align: middle; }
        .gs-table thead th { background-color: #dce8f5; color: #1a4f7a; font-weight: bold; }
        .gs-table tfoot td { background-color: #f0f0f0; font-weight: bold; }
        .gs-table .text-left { text-align: left; }
        /* Summary & Grade scale */
        .gs-bottom { display: flex; gap: 12px; margin-top: 10px; }
        .gs-summary { flex: 1; }
        .gs-summary table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .gs-summary table td { border: 1px solid #555; padding: 4px 8px; }
        .gs-summary table td:first-child { font-weight: bold; }
        .gs-grade-scale { width: 260px; }
        .gs-grade-scale table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .gs-grade-scale table th { background-color: #dce8f5; border: 1px solid #555; padding: 3px 5px; text-align: center; font-weight: bold; color: #1a4f7a; }
        .gs-grade-scale table td { border: 1px solid #555; padding: 3px 5px; text-align: center; }
        .gs-grade-scale-title { text-align: center; font-weight: bold; font-size: 11px; color: #1a4f7a; margin-bottom: 3px; text-transform: uppercase; }
        /* Footer */
        .gs-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 22px; }
        .gs-footer-date { font-size: 12px; }
        .gs-footer-sign { text-align: center; font-size: 11px; }
        .gs-footer-sign .sign-line { border-top: 1.5px solid #333; padding-top: 2px; margin-top: 30px; min-width: 160px; }
        /* Result badge */
        .badge-pass { background: #27ae60; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
        .badge-fail { background: #c0392b; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
        /* Print */
        @media print {
            .no-print { display: none !important; }
            .gs-wrapper { border: 3px solid #1a4f7a; width: 100%; }
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
                    <div class="gs-wrapper">

                        {{-- ===== HEADER ===== --}}
                        <div class="gs-header">
                            <div class="gs-header-logo">
                                @if(isset($generalSetting->logo))
                                    <img src="{{ asset('images/setting/general/'.$generalSetting->logo) }}">
                                @endif
                            </div>
                            <div class="gs-header-center">
                                <h2>{{ $generalSetting->institute ?? 'Institution Name' }}</h2>
                                <h4>{{ ViewHelper::getExamById($data['exam']) }} Examination — {{ ViewHelper::getYearById($data['year']) }}</h4>
                                <div class="gs-title">GRADE SHEET</div>
                            </div>
                            <div class="gs-header-right">
                                <div><span>Reg. No.:</span> {{ $student->reg_no }}</div>
                                <div><span>Level:</span> {{ ViewHelper::getFacultyTitle($student->faculty) }}</div>
                                <div><span>Sem./Sec.:</span> {{ ViewHelper::getSemesterTitle($student->semester) }}</div>
                            </div>
                        </div>

                        <div class="gs-divider"></div>

                        {{-- ===== STUDENT INFO ===== --}}
                        <table class="gs-info-table">
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

                        <div class="gs-divider"></div>

                        {{-- ===== GRADES TABLE ===== --}}
                        <table class="gs-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:36px;">SN</th>
                                    <th rowspan="2" style="width:60px;">Subject Code</th>
                                    <th rowspan="2" class="text-left" style="min-width:160px;">Subject</th>
                                    <th rowspan="2" style="width:48px;">Credit</th>
                                    <th colspan="2">Obtained Grade</th>
                                    <th rowspan="2" style="width:70px;">Final Grade</th>
                                    <th rowspan="2" style="width:70px;">Grade Point</th>
                                </tr>
                                <tr>
                                    <th style="width:60px;">Theory</th>
                                    <th style="width:60px;">Practical</th>
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
                                        <td>{{ ViewHelper::getSubCreditById($subject->subjects_id) ?: '-' }}</td>
                                        <td>{{ $subject->obtain_score_theory ?: '-' }}</td>
                                        <td>{{ $subject->obtain_score_practical ?: '-' }}</td>
                                        <td><strong>{{ $subject->final_grade ?: '-' }}</strong></td>
                                        <td><strong>{{ $subject->grade_point ?: '-' }}</strong></td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right">GRADE POINT AVERAGE (GPA)</td>
                                    <td colspan="2"></td>
                                    <td><strong>{{ $student->gpa_grade ?? '-' }}</strong></td>
                                    <td><strong>{{ $student->gpa_average ?? '-' }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- ===== SUMMARY + GRADE SCALE ===== --}}
                        <div class="gs-bottom">
                            <div class="gs-summary">
                                <table>
                                    <tr>
                                        <td>Average Grade</td>
                                        <td><strong>{{ $student->gpa_grade ?? '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Grade Point Average (GPA)</td>
                                        <td><strong>{{ $student->gpa_average ?? '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Rank</td>
                                        <td>{{ $student->rank ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:14px;">RESULT</td>
                                        <td>
                                            @php $isFail = isset($student->gpa_remark) && strtolower($student->gpa_remark) == 'fail'; @endphp
                                            @if($isFail)
                                                <span class="badge-fail" style="font-size:13px; padding:3px 12px;">FAILED</span>
                                            @else
                                                <span class="badge-pass" style="font-size:13px; padding:3px 12px;">{{ strtoupper($student->gpa_remark ?? 'PASSED') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="gs-grade-scale">
                                <div class="gs-grade-scale-title">Details of Grade Scale</div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Marks (%)</th>
                                            <th>Grade</th>
                                            <th>Description</th>
                                            <th>GPA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($data['grade-scale-range']) && $data['grade-scale-range']->count() > 0)
                                            @foreach($data['grade-scale-range'] as $gs)
                                            <tr>
                                                <td>{{ $gs->percentage_from }}–{{ $gs->percentage_to }}</td>
                                                <td>{{ $gs->name }}</td>
                                                <td>{{ $gs->description }}</td>
                                                <td>{{ $gs->grade_point }}</td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ===== ABBREVIATIONS ===== --}}
                        <div style="font-size:10px; margin-top:8px; color:#555;">
                            <strong>Abbreviations:</strong>
                            <strong>TH</strong>: Theory &nbsp;|&nbsp; <strong>PR</strong>: Practical &nbsp;|&nbsp;
                            <strong>*AB</strong>: Absent &nbsp;|&nbsp; <strong>*NG</strong>: No Grade &nbsp;|&nbsp;
                            <strong>*MG</strong>: Missing Grade &nbsp;|&nbsp; <strong>*MP</strong>: Missing Point
                        </div>

                        {{-- ===== FOOTER ===== --}}
                        <div class="gs-footer">
                            <div class="gs-footer-date">
                                <strong>Date of Issue:</strong> {{ \Carbon\Carbon::now()->format('d F Y') }}
                            </div>
                            <div class="gs-footer-sign">
                                <div class="sign-line">Class Teacher</div>
                            </div>
                            <div class="gs-footer-sign">
                                <div class="sign-line">Controller of Examination</div>
                                <div>{{ $generalSetting->institute ?? '' }}</div>
                            </div>
                        </div>

                        <div style="text-align:center; font-size:10px; margin-top:10px; border-top:1px solid #ccc; padding-top:4px;">
                            ** This Grade Sheet is issued without any alteration or erasure **
                        </div>

                    </div><!-- /.gs-wrapper -->
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