@extends('layouts.master')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .admit-print-page {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        background: #fff;
        border: 1.5px solid #0f3e6a;
        border-radius: 8px;
        padding: 14mm 12mm;
        font-family: 'Poppins', sans-serif;
        color: #1a2b3c;
        box-sizing: border-box;
    }

    .admit-print-toolbar {
        text-align: right;
        margin-bottom: 10px;
    }

    .admit-header {
        display: grid;
        grid-template-columns: 22mm 1fr 26mm;
        gap: 10px;
        align-items: start;
        border-bottom: 1px solid #d7e2ef;
        padding-bottom: 10px;
    }

    .admit-logo,
    .admit-photo {
        width: 100%;
        max-height: 26mm;
        object-fit: contain;
        border-radius: 4px;
    }

    .admit-photo-wrap {
        border: 1px solid #cbd8e6;
        border-radius: 4px;
        padding: 2px;
        min-height: 30mm;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .admit-headline {
        text-align: center;
        line-height: 1.35;
        font-family: 'Merriweather', serif;
    }

    .subtitle-normal {
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
    }

    .admit-headline .small-line {
        font-size: 12px;
        letter-spacing: 0.2px;
    }

    .admit-headline .org {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin: 2px 0;
        white-space: nowrap;
    }

    .admit-headline .web {
        font-size: 12px;
        color: #395c7d;
        margin-bottom: 6px;
    }

    .admit-title {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 1.1px;
        margin: 2px 0;
        text-transform: uppercase;
    }

    .admit-subtitle {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .admit-meta {
        margin-top: 10px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 20px;
    }

    .meta-col {
        border: 1px solid #d7e2ef;
        border-radius: 6px;
        padding: 8px 10px;
    }

    .meta-row {
        font-size: 12px;
        margin-bottom: 5px;
    }

    .meta-row.single-line {
        white-space: nowrap;
    }

    .meta-row:last-child {
        margin-bottom: 0;
    }

    .meta-label {
        font-weight: 700;
    }

    .schedule-wrap {
        margin-top: 12px;
        border: 1px solid #d8e2ee;
        border-radius: 6px;
        overflow: hidden;
    }

    .schedule-head {
        padding: 8px 10px;
        background: #f4f8fc;
        border-bottom: 1px solid #d8e2ee;
        font-size: 16px;
        font-weight: 700;
        text-align: center;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .schedule-table th,
    .schedule-table td {
        border: 1px solid #dde6f1;
        padding: 5px 6px;
        text-align: left;
        vertical-align: top;
    }

    .schedule-table th {
        background: #f8fbff;
        font-weight: 700;
        text-transform: uppercase;
    }

    .schedule-table .center {
        text-align: center;
    }

    .schedule-table th:nth-child(4),
    .schedule-table td:nth-child(4) {
        text-align: center;
    }

    .abbr-note {
        padding: 7px 9px;
        border-top: 0;
        font-size: 11px;
        background: #fbfdff;
    }

    .optional-summary {
        padding: 8px 9px;
        border-top: 1px dashed #d8e2ee;
        font-size: 11px;
        text-transform: uppercase;
        background: #f8fbff;
    }

    .optional-summary .label {
        font-weight: 700;
        color: #193653;
    }

    .optional-summary .value {
        font-weight: 600;
        color: #24415f;
    }

    .admit-signature {
        margin-top: 24px;
        display: flex;
        justify-content: flex-end;
    }

    .signature-box {
        text-align: center;
        min-width: 170px;
        font-size: 12px;
    }

    .signature-line {
        border-top: 1px solid #203245;
        margin-top: 22px;
        padding-top: 4px;
        font-weight: 600;
    }

    .admit-note {
        margin-top: 15px;
        padding: 8px 10px;
        border-radius: 5px;
        background: #f7fafc;
        border: 1px solid #dbe4ef;
        font-size: 12px;
    }

    @media print {
        body {
            background: #fff;
        }

        .main-content,
        .main-content-inner,
        .page-content {
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            width: auto !important;
            min-height: auto !important;
        }

        .hidden-print,
        .admit-print-toolbar {
            display: none !important;
        }

        .admit-print-page {
            border: 1.2px solid #0f3e6a;
            border-radius: 0;
            width: 100%;
            min-height: auto;
            margin: 0;
            padding: 10mm 8mm;
            page-break-inside: avoid;
        }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }
    }
</style>
@endsection

@section('content')
@if($data['student'] && $data['student']->count() > 0)
    @foreach($data['student'] as $student)
        <div class="main-content">
            <div class="admit-print-toolbar hidden-print">
                <a href="#" class="btn btn-primary" onclick="window.print(); return false;">
                    <i class="ace-icon fa fa-print bigger-120"></i> Print
                </a>
            </div>

            <div class="admit-print-page">
                <div class="admit-header">
                    <div>
                        @if(isset($generalSetting->logo) && $generalSetting->logo)
                            <img class="admit-logo" src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" alt="Logo">
                        @endif
                    </div>

                    <div class="admit-headline">
                        <div class="small-line">The People's Republic of Bangladesh</div>
                        <div class="org">Lalmai Government College, Cumilla</div>
                        <div class="web">Web:https://lalmaigc.edu.bd</div>
                        <div class="admit-title">ADMIT CARD</div>
                        <p class="admit-subtitle">
                            HSC Examination-2026
                            <span class="subtitle-normal">(Class Test/Practical/Half Yearly/Year Final)</span>
                        </p>
                    </div>

                    <div class="admit-photo-wrap">
                        @if($student->student_image)
                            <img class="admit-photo" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_image) }}" alt="Student Photo">
                        @endif
                    </div>
                </div>

                <div class="admit-meta">
                    <div class="meta-col">
                        <div class="meta-row"><span class="meta-label">Group:</span> {{ ViewHelper::getFacultyTitle($student->faculty) ?: 'N/A' }}</div>
                        <div class="meta-row single-line"><span class="meta-label">Name (Capital Letter):</span> {{ strtoupper(trim($student->first_name.' '.$student->middle_name.' '.$student->last_name)) }}</div>
                        <div class="meta-row"><span class="meta-label">Subject:</span> Compulsory/ Optional</div>
                    </div>

                    <div class="meta-col">
                        <div class="meta-row"><span class="meta-label">Reg. No:</span> {{ $student->reg_no ?: 'N/A' }}</div>
                        <div class="meta-row"><span class="meta-label">Board Roll:</span> {{ $student->university_reg ?: 'N/A' }}</div>
                        <div class="meta-row"><span class="meta-label">Session:</span> {{ ViewHelper::getStudentBatchById($student->batch) ?: 'N/A' }}</div>
                    </div>
                </div>

                <div class="schedule-wrap">
                    <div class="schedule-head">Code &amp; Subject</div>
                    @php
                        $admitSubjects = $student->admit_subjects ?? collect();

                        $optionalSubjects = $admitSubjects->filter(function ($subject) {
                            return strtolower(trim((string) ($subject->subject_type ?? ''))) === 'optional';
                        })->values();
                    @endphp
                    <table class="schedule-table text-uppercase">
                        <thead>
                        <tr>
                            <th class="center" style="width:30px;">#</th>
                            <th style="width:82px;">Date</th>
                            <th style="width:112px;">Time</th>
                            <th>Code &amp; Subject</th>
                            <th class="center" style="width:50px;">FM(T)</th>
                            <th class="center" style="width:50px;">PM(T)</th>
                            <th class="center" style="width:50px;">FM(P)</th>
                            <th class="center" style="width:50px;">PM(P)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($admitSubjects->count() > 0)
                            @php($i = 1)
                            @foreach($admitSubjects as $subject)
                                <tr>
                                    <td class="center">{{ $i }}</td>
                                    <td>
                                        @if(!empty($subject->date))
                                            {{ \Carbon\Carbon::parse($subject->date)->format('Y-m-d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($subject->start_time) && !empty($subject->end_time))
                                            {{ \Carbon\Carbon::parse($subject->start_time)->format('g:i A') }} -
                                            {{ \Carbon\Carbon::parse($subject->end_time)->format('g:i A') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $subject->title }}@if(!empty($subject->code)) [{{ $subject->code }}]@endif</td>
                                    <td class="center">{{ $subject->full_mark_theory ? $subject->full_mark_theory : '-' }}</td>
                                    <td class="center">{{ $subject->pass_mark_theory ? $subject->pass_mark_theory : '-' }}</td>
                                    <td class="center">{{ $subject->full_mark_practical ? $subject->full_mark_practical : '-' }}</td>
                                    <td class="center">{{ $subject->pass_mark_practical ? $subject->pass_mark_practical : '-' }}</td>
                                </tr>
                                @php($i++)
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="center">No schedule found.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    @if($optionalSubjects->count() > 0)
                        <div class="optional-summary">
                            <span class="label">Optional Subject:</span>
                            <span class="value">
                                @foreach($optionalSubjects as $subject)
                                    {{ strtoupper($subject->title) }}@if(!$loop->last), @endif
                                @endforeach
                            </span>
                        </div>
                    @endif
                    <div class="abbr-note">Abbreviations: FM = Full Mark, PM = Pass Mark, T = Theory, P = Practical</div>
                </div>

                <div class="admit-signature">
                    <div class="signature-box">
                        <div class="signature-line">Principal</div>
                    </div>
                </div>

                <div class="admit-note">
                    Note: Student must follow all examination rules and regulations.
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection

@section('js')
@include('includes.scripts.print_script')
@endsection