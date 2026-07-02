@extends('layouts.master')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ── Reset ────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

/* ── Screen toolbar ───────────────────────────────────────── */
.admit-toolbar {
    max-width: 210mm;
    margin: 14px auto 10px;
    text-align: right;
}

/* ── Outer wrapper per student (A4) ──────────────────────── */
.admit-page-wrap {
    width: 210mm;
    min-height: 297mm;
    margin: 0 auto 20px;
    background: #fff;
    font-family: 'Poppins', sans-serif;
    color: #1a2b3c;
    position: relative;
    border: 2px solid #0f3e6a;
    padding: 3px;
}
.admit-page-inner {
    width: 100%;
    min-height: calc(297mm - 10px);
    border: 1px solid #3a6a9b;
    padding: 0 0 14mm;
    position: relative;
    overflow: hidden;
}

/* Watermark */
.admit-page-inner::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: var(--wm-url);
    background-repeat: no-repeat;
    background-size: 55%;
    background-position: center 45%;
    opacity: 0.05;
    pointer-events: none;
    z-index: 0;
}
.admit-page-inner > * { position: relative; z-index: 1; }

/* ── Top strip ────────────────────────────────────────────── */
.admit-top-strip { background: #0f3e6a; height: 7px; }

/* ── Title band ───────────────────────────────────────────── */
.admit-title-band {
    background: #f0f5fb;
    border-bottom: 1px solid #cddaeb;
    padding: 5px 14mm;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.atb-left  { font-size: 10.5px; color: #2d5580; font-weight: 600; letter-spacing: .3px; }
.atb-right { font-size: 10.5px; color: #2d5580; font-weight: 600; letter-spacing: .3px; text-align: right; }
.atb-center {
    font-size: 11px; font-weight: 800; letter-spacing: 3px;
    text-transform: uppercase; color: #0f3e6a; text-align: center;
    padding: 2px 16px;
    border-left: 1px solid #cddaeb;
    border-right: 1px solid #cddaeb;
}

/* ── Header ───────────────────────────────────────────────── */
.admit-header {
    display: grid;
    grid-template-columns: 26mm 1fr 30mm;
    gap: 8px;
    align-items: center;
    padding: 10px 14mm 10px;
    border-bottom: 1.5px solid #d0dcea;
}
.admit-logo-wrap { display: flex; align-items: center; justify-content: center; }
.admit-logo { width: 100%; max-height: 25mm; object-fit: contain; }

.admit-headline { text-align: center; line-height: 1.3; }
.ahl-republic { font-size: 10px; color: #2d5580; letter-spacing: .2px; margin-bottom: 1px; }
.ahl-college  { font-size: 18px; font-weight: 800; color: #0f3e6a; letter-spacing: .4px; font-family: 'Merriweather', serif; margin: 2px 0; white-space: nowrap; }
.ahl-web      { font-size: 10px; color: #4a7aaa; margin-bottom: 5px; }
.ahl-admit    {
    display: inline-block;
    font-size: 14px; font-weight: 800; letter-spacing: 3px;
    text-transform: uppercase; color: #fff;
    background: #0f3e6a;
    padding: 3px 18px; border-radius: 2px; margin: 3px 0;
}
.ahl-exam { font-size: 12px; font-weight: 700; color: #1d3550; margin-top: 3px; }
.ahl-exam-sub { font-size: 10.5px; font-weight: 400; color: #4a6a8a; }

.admit-photo-wrap { display: flex; flex-direction: column; align-items: center; gap: 3px; }
.admit-photo-box {
    width: 28mm; height: 33mm;
    border: 1.5px solid #3a6a9b; border-radius: 3px;
    overflow: hidden; display: flex; align-items: center; justify-content: center;
    background: #f4f8fc;
}
.admit-photo-box img { width: 100%; height: 100%; object-fit: cover; }
.admit-photo-label { font-size: 9px; color: #5a7a9a; font-weight: 600; letter-spacing: .5px; text-transform: uppercase; }

/* ── Student info table ───────────────────────────────────── */
.admit-info {
    margin: 10px 14mm 0;
    border: 1px solid #cddaeb;
    border-radius: 4px;
    overflow: hidden;
}
.admit-info-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-bottom: 1px solid #e0eaf5;
}
.admit-info-row:last-child { border-bottom: none; }
.admit-info-cell {
    display: grid;
    grid-template-columns: 110px 1fr;
    align-items: center;
    padding: 5px 10px;
    gap: 4px;
}
.admit-info-cell:first-child { border-right: 1px solid #e0eaf5; }
.info-label { font-weight: 700; color: #0f3e6a; font-size: 11px; white-space: nowrap; }
.info-value { font-weight: 500; color: #1a2b3c; font-size: 12px; }
.info-value.caps { text-transform: uppercase; font-weight: 600; }
.admit-info-row:nth-child(odd) .admit-info-cell { background: #f8fbff; }

/* ── Schedule table ───────────────────────────────────────── */
.schedule-wrap {
    margin: 10px 14mm 0;
    border: 1px solid #cddaeb;
    border-radius: 4px;
    overflow: hidden;
}
.sched-head-bar {
    background: #0f3e6a;
    color: #fff;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 5px 10px;
}
.schedule-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10.5px;
}
.schedule-table th {
    background: #e8f0f9;
    color: #0f3e6a;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: .4px;
    padding: 5px 6px;
    border-bottom: 2px solid #3a6a9b;
    border-right: 1px solid #cddaeb;
    text-align: center;
}
.schedule-table th:nth-child(4) { text-align: left; }
.schedule-table th:last-child { border-right: none; }
.schedule-table td {
    padding: 4px 6px;
    border-bottom: 1px solid #e0eaf5;
    border-right: 1px solid #e0eaf5;
    vertical-align: middle;
    color: #1a2b3c;
}
.schedule-table td:last-child { border-right: none; }
.schedule-table tbody tr:nth-child(even) td { background: #f6f9fd; }
.schedule-table tbody tr:last-child td { border-bottom: none; }
.schedule-table .tc { text-align: center; }
.schedule-table .subj-col { font-weight: 600; text-transform: uppercase; font-size: 10.5px; }
.schedule-table .code-tag {
    display: inline-block;
    background: #e8f0f9;
    color: #0f3e6a;
    border-radius: 3px;
    padding: 1px 5px;
    font-size: 10px;
    font-weight: 800;
    margin-left: 4px;
}
.sched-footer {
    background: #f6f9fd;
    border-top: 1px solid #cddaeb;
    padding: 5px 10px;
    font-size: 10.5px;
    color: #2d5580;
    font-weight: 600;
}
.optional-bar {
    background: #fffbf0;
    border-top: 1px dashed #e0d0a0;
    padding: 5px 10px;
    font-size: 11px;
    display: flex; align-items: center; gap: 6px;
}
.opt-label { font-weight: 800; color: #0f3e6a; white-space: nowrap; }
.opt-value  { font-weight: 600; color: #2d5580; text-transform: uppercase; }

/* ── Signature row ────────────────────────────────────────── */
.admit-sig-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding: 0 14mm;
    margin-top: 16px;
}
.sig-box { text-align: center; min-width: 120px; }
.sig-line {
    border-top: 1px solid #1a3550;
    padding-top: 4px;
    margin-top: 26px;
    font-size: 11px;
    font-weight: 700;
    color: #0f3e6a;
    letter-spacing: .5px;
}

/* ── Bottom strip ─────────────────────────────────────────── */
.admit-bottom-strip {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 5px;
    background: #0f3e6a;
}

/* ═══════════ PRINT ═══════════════════════════════════════ */
@media print {
    body { background: #fff !important; margin: 0; padding: 0; }

    /* Hide ACE admin navigation, sidebar, and UI chrome */
    .navbar-fixed-top, .navbar-fixed-bottom,
    .main-sidebar, .sidebar, .nav-list,
    .breadcrumbs, .page-header,
    .hidden-print, .admit-toolbar,
    .ace-settings-container, .btn-scroll-up,
    footer.main-footer { display: none !important; }

    /* Reset layout containers — do NOT hide them */
    .main-container, .main-content, .main-content-inner, .page-content {
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        width: 100% !important;
        min-height: auto !important;
        float: none !important;
        background: #fff !important;
    }

    .admit-page-wrap {
        width: 100%;
        min-height: auto;
        margin: 0 auto;
        border: 2px solid #0f3e6a;
        page-break-after: always;
    }
    .admit-page-inner { min-height: auto; }
    .schedule-wrap { page-break-inside: avoid; }
}
</style>
@endsection

@section('content')

@if($data['student'] && $data['student']->count() > 0)
    <div class="admit-toolbar hidden-print">
        <button class="btn btn-primary" onclick="window.print(); return false;">
            <i class="fa fa-print"></i> Print Admit Card(s)
        </button>
    </div>

    @foreach($data['student'] as $student)
    @php
        $admitSubjects    = $student->admit_subjects ?? collect();
        $optionalSubjects = $admitSubjects->filter(function($s) {
            return strtolower(trim((string)($s->subject_type ?? ''))) === 'optional';
        })->values();
    @endphp

    <div class="admit-page-wrap"
         @if(isset($generalSetting->logo) && $generalSetting->logo)
             style="--wm-url: url('{{ asset('images/setting/general/'.$generalSetting->logo) }}')"
         @endif>
        <div class="admit-page-inner">

            <div class="admit-top-strip"></div>

            <div class="admit-title-band">
                <span class="atb-left">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</span>
                <span class="atb-center">প্রবেশপত্র / ADMIT CARD</span>
                <span class="atb-right">The People's Republic of Bangladesh</span>
            </div>

            <div class="admit-header">
                <div class="admit-logo-wrap">
                    @if(isset($generalSetting->logo) && $generalSetting->logo)
                        <img class="admit-logo"
                             src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}"
                             alt="Logo">
                    @endif
                </div>

                <div class="admit-headline">
                    <div class="ahl-republic">Government College of Bangladesh Education Board</div>
                    <div class="ahl-college">Lalmai Government College, Cumilla</div>
                    <div class="ahl-web">Web: https://lalmaigc.edu.bd</div>
                    <div><span class="ahl-admit">ADMIT CARD</span></div>
                    <div class="ahl-exam">
                        HSC Examination-2026
                        <span class="ahl-exam-sub">(Class Test / Practical / Half Yearly / Year Final)</span>
                    </div>
                </div>

                <div class="admit-photo-wrap">
                    <div class="admit-photo-box">
                        @if($student->student_image)
                            <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_image) }}"
                                 alt="Photo">
                        @endif
                    </div>
                    <span class="admit-photo-label">Photo</span>
                </div>
            </div>

            {{-- Student info --}}
            <div class="admit-info">
                <div class="admit-info-row">
                    <div class="admit-info-cell">
                        <span class="info-label">Group:</span>
                        <span class="info-value">{{ ViewHelper::getFacultyTitle($student->faculty) ?: 'N/A' }}</span>
                    </div>
                    <div class="admit-info-cell">
                        <span class="info-label">Reg. No:</span>
                        <span class="info-value">{{ $student->reg_no ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="admit-info-row">
                    <div class="admit-info-cell">
                        <span class="info-label">Name:</span>
                        <span class="info-value caps">{{ strtoupper(trim($student->first_name.' '.$student->middle_name.' '.$student->last_name)) }}</span>
                    </div>
                    <div class="admit-info-cell">
                        <span class="info-label">Board Roll:</span>
                        <span class="info-value">{{ $student->university_reg ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="admit-info-row">
                    <div class="admit-info-cell">
                        <span class="info-label">Date of Birth:</span>
                        <span class="info-value">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : 'N/A' }}</span>
                    </div>
                    <div class="admit-info-cell">
                        <span class="info-label">Session:</span>
                        <span class="info-value">{{ ViewHelper::getStudentBatchById($student->batch) ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="admit-info-row">
                    <div class="admit-info-cell">
                        <span class="info-label">Gender:</span>
                        <span class="info-value">{{ $student->gender ?: 'N/A' }}</span>
                    </div>
                    <div class="admit-info-cell">
                        <span class="info-label">Blood Group:</span>
                        <span class="info-value">{{ $student->blood_group ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- Exam schedule table --}}
            <div class="schedule-wrap">
                <div class="sched-head-bar">Examination Schedule — Code &amp; Subject</div>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th style="width:28px">#</th>
                            <th style="width:76px">Date</th>
                            <th style="width:108px">Time</th>
                            <th>Code &amp; Subject</th>
                            <th style="width:46px">FM(T)</th>
                            <th style="width:46px">PM(T)</th>
                            <th style="width:46px">FM(P)</th>
                            <th style="width:46px">PM(P)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if($admitSubjects->count() > 0)
                        @php($rowNum = 1)
                        @foreach($admitSubjects as $subject)
                        <tr>
                            <td class="tc">{{ $rowNum++ }}</td>
                            <td class="tc">
                                @if(!empty($subject->date))
                                    {{ \Carbon\Carbon::parse($subject->date)->format('d M Y') }}
                                @else —
                                @endif
                            </td>
                            <td class="tc">
                                @if(!empty($subject->start_time) && !empty($subject->end_time))
                                    {{ \Carbon\Carbon::parse($subject->start_time)->format('g:i A') }}–{{ \Carbon\Carbon::parse($subject->end_time)->format('g:i A') }}
                                @else —
                                @endif
                            </td>
                            <td class="subj-col">
                                {{ strtoupper($subject->title) }}
                                @if(!empty($subject->code))<span class="code-tag">{{ $subject->code }}</span>@endif
                            </td>
                            <td class="tc">{{ $subject->full_mark_theory  ?: '—' }}</td>
                            <td class="tc">{{ $subject->pass_mark_theory  ?: '—' }}</td>
                            <td class="tc">{{ $subject->full_mark_practical ?: '—' }}</td>
                            <td class="tc">{{ $subject->pass_mark_practical ?: '—' }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="tc" style="padding:10px;color:#6a8aaa">No exam schedule found.</td></tr>
                    @endif
                    </tbody>
                </table>

                @if($optionalSubjects->count() > 0)
                <div class="optional-bar">
                    <span class="opt-label">Optional Subject:</span>
                    <span class="opt-value">
                        @foreach($optionalSubjects as $s){{ strtoupper($s->title) }}@if(!$loop->last), @endif@endforeach
                    </span>
                </div>
                @endif

                <div class="sched-footer">
                    FM = Full Mark &nbsp;|&nbsp; PM = Pass Mark &nbsp;|&nbsp; T = Theory &nbsp;|&nbsp; P = Practical
                </div>
            </div>{{-- /schedule-wrap --}}

            {{-- Signatures --}}
            <div class="admit-sig-row">
                <div class="sig-box">
                    <div class="sig-line">Examinee's Signature</div>
                </div>
                <div class="sig-box">
                    <div class="sig-line">Controller of Examinations</div>
                </div>
                <div class="sig-box">
                    <div class="sig-line">Principal</div>
                </div>
            </div>

            <div class="admit-bottom-strip"></div>
        </div>{{-- /admit-page-inner --}}
    </div>{{-- /admit-page-wrap --}}
    @endforeach
@endif

@endsection

@section('js')
@include('includes.scripts.print_script')
@endsection
