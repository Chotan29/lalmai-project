@extends('user-student.layouts.master')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ── Base ─────────────────────────────────────────────────── */
body { font-family: 'Inter', sans-serif !important; background: #f0f4f9 !important; }
.page-content { background: #f0f4f9 !important; }
.page-header { display: none; }

/* ── Welcome hero ─────────────────────────────────────────── */
.sd-hero {
    background: linear-gradient(135deg, #0f3e6a 0%, #1a6fa3 50%, #0d2d4f 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.sd-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    pointer-events: none;
}
.sd-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; right: 120px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
    pointer-events: none;
}
.sd-hero-inner {
    display: flex;
    align-items: center;
    gap: 24px;
    position: relative;
    z-index: 1;
}
.sd-hero-photo {
    width: 84px; height: 84px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,.4);
    object-fit: cover;
    flex-shrink: 0;
    background: rgba(255,255,255,.15);
}
.sd-hero-photo-placeholder {
    width: 84px; height: 84px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,.4);
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: rgba(255,255,255,.8);
    flex-shrink: 0;
}
.sd-hero-info { flex: 1; }
.sd-welcome-line { font-size: 13px; font-weight: 400; opacity: .75; margin-bottom: 3px; }
.sd-hero-name { font-size: 24px; font-weight: 800; letter-spacing: -.2px; margin-bottom: 6px; }
.sd-hero-tags { display: flex; flex-wrap: wrap; gap: 8px; }
.sd-tag {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px;
    padding: 3px 12px;
    font-size: 12px; font-weight: 500;
}
.sd-tag i { font-size: 11px; opacity: .8; }
.sd-hero-right { text-align: right; flex-shrink: 0; }
.sd-date-label { font-size: 11px; opacity: .6; margin-bottom: 2px; }
.sd-date-val { font-size: 15px; font-weight: 700; }
.sd-college-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 11px; font-weight: 600;
    margin-top: 8px;
    opacity: .9;
}

/* ── Stat cards row ───────────────────────────────────────── */
.sd-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
.sd-stat {
    background: #fff;
    border-radius: 12px;
    padding: 18px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    gap: 14px;
    border-left: 4px solid transparent;
    transition: box-shadow .2s, transform .2s;
}
.sd-stat:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1); transform: translateY(-2px); }
.sd-stat.blue  { border-left-color: #1a6fa3; }
.sd-stat.green { border-left-color: #16a34a; }
.sd-stat.red   { border-left-color: #dc3545; }
.sd-stat.amber { border-left-color: #f59e0b; }
.sd-stat-icon {
    width: 46px; height: 46px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.sd-stat.blue  .sd-stat-icon { background: #e8f2fb; color: #1a6fa3; }
.sd-stat.green .sd-stat-icon { background: #dcfce7; color: #16a34a; }
.sd-stat.red   .sd-stat-icon { background: #fee2e2; color: #dc3545; }
.sd-stat.amber .sd-stat-icon { background: #fef3c7; color: #d97706; }
.sd-stat-body {}
.sd-stat-label { font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
.sd-stat-value { font-size: 20px; font-weight: 800; color: #111827; line-height: 1; }
.sd-stat-sub   { font-size: 11px; color: #9ca3af; margin-top: 3px; }

/* ── Main content grid ────────────────────────────────────── */
.sd-grid { display: grid; grid-template-columns: 1fr 380px; gap: 18px; margin-bottom: 22px; }

/* ── Card shell ───────────────────────────────────────────── */
.sd-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    overflow: hidden;
}
.sd-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}
.sd-card-title {
    display: flex; align-items: center; gap: 9px;
    font-size: 14px; font-weight: 700; color: #111827;
}
.sd-card-title i {
    width: 30px; height: 30px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
}
.ic-blue  { background: #e8f2fb; color: #1a6fa3; }
.ic-amber { background: #fef3c7; color: #d97706; }
.sd-card-body { padding: 18px 20px; }

/* ── Chart section ────────────────────────────────────────── */
.sd-chart-wrap { display: flex; flex-direction: column; align-items: center; padding: 16px 20px; }
.sd-chart-canvas { max-width: 220px; max-height: 220px; }
.sd-chart-legend { display: flex; gap: 20px; margin-top: 16px; }
.sd-legend-item { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: #374151; }
.sd-legend-dot  { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.sd-installment-alert {
    margin: 0 20px 16px;
    background: linear-gradient(135deg, #fff7ed, #fef3c7);
    border: 1px solid #fcd34d;
    border-radius: 10px;
    padding: 12px 16px;
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; font-weight: 600; color: #92400e;
}
.sd-installment-alert i { font-size: 16px; color: #f59e0b; flex-shrink: 0; }

/* ── Notices ──────────────────────────────────────────────── */
.sd-notices-list { padding: 8px 12px; max-height: 360px; overflow-y: auto; }
.sd-notice-item {
    border-radius: 10px;
    border: 1px solid #f3f4f6;
    margin-bottom: 8px;
    overflow: hidden;
    transition: box-shadow .2s;
}
.sd-notice-item:last-child { margin-bottom: 0; }
.sd-notice-item:hover { box-shadow: 0 3px 12px rgba(0,0,0,.08); }
.sd-notice-head {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px;
    background: #f9fafb;
    cursor: pointer;
}
.sd-notice-dot { width: 8px; height: 8px; border-radius: 50%; background: #1a6fa3; flex-shrink: 0; }
.sd-notice-title { font-size: 13px; font-weight: 600; color: #1f2937; flex: 1; }
.sd-notice-date { font-size: 11px; color: #9ca3af; white-space: nowrap; }
.sd-notice-body { display: none; padding: 10px 14px; font-size: 12px; color: #4b5563; line-height: 1.6; border-top: 1px solid #f3f4f6; }
.sd-notice-body.open { display: block; }
.sd-notice-attach { margin-top: 8px; }
.sd-notice-attach a { font-size: 12px; color: #1a6fa3; text-decoration: none; font-weight: 600; }
.sd-notice-attach a:hover { text-decoration: underline; }
.sd-no-notices { text-align: center; padding: 32px 20px; color: #9ca3af; font-size: 13px; }
.sd-no-notices i { font-size: 28px; margin-bottom: 8px; display: block; }

/* ── Quick access modules ─────────────────────────────────── */
.sd-modules { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 22px; }
.sd-module-card {
    background: #fff;
    border-radius: 12px;
    padding: 16px 10px;
    text-align: center;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(0,0,0,.05);
    transition: box-shadow .2s, transform .2s;
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    border: 1px solid #f3f4f6;
}
.sd-module-card:hover { box-shadow: 0 8px 22px rgba(0,0,0,.12); transform: translateY(-3px); text-decoration: none !important; }
.sd-module-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.sd-module-label { font-size: 11.5px; font-weight: 600; color: #374151; }
.m-navy   { background: #dbeafe; color: #1e40af; }
.m-teal   { background: #ccfbf1; color: #0f766e; }
.m-green  { background: #dcfce7; color: #15803d; }
.m-purple { background: #ede9fe; color: #7c3aed; }
.m-orange { background: #ffedd5; color: #c2410c; }
.m-red    { background: #fee2e2; color: #b91c1c; }
.m-indigo { background: #e0e7ff; color: #4338ca; }
.m-pink   { background: #fce7f3; color: #be185d; }
.m-yellow { background: #fef9c3; color: #a16207; }
.m-cyan   { background: #cffafe; color: #0e7490; }
.m-slate  { background: #f1f5f9; color: #475569; }
.m-rose   { background: #ffe4e6; color: #be123c; }

/* ── Flash messages ───────────────────────────────────────── */
.sd-flash { margin-bottom: 16px; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 1199px) {
    .sd-modules { grid-template-columns: repeat(4, 1fr); }
}
@media (max-width: 991px) {
    .sd-grid { grid-template-columns: 1fr; }
    .sd-stats { grid-template-columns: repeat(2, 1fr); }
    .sd-modules { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 600px) {
    .sd-stats { grid-template-columns: repeat(2, 1fr); }
    .sd-modules { grid-template-columns: repeat(3, 1fr); }
    .sd-hero-inner { flex-direction: column; text-align: center; }
    .sd-hero-tags { justify-content: center; }
    .sd-hero-right { text-align: center; }
}
</style>
@endsection

@section('content')
<div class="main-content">
<div class="main-content-inner">
<div class="page-content" style="padding-top:16px">
@include('user-student.layouts.includes.template_setting')

<div class="sd-flash">@include('includes.flash_messages')</div>

{{-- ── HERO ──────────────────────────────────────────── --}}
@php
    $student  = $data['student'];
    $fullName = trim($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name);
    $feeAmt   = $student->fee_amount  ?? 0;
    $paid     = $student->paid_amount ?? 0;
    $disc     = $student->discount    ?? 0;
    $fine     = $student->fine        ?? 0;
    $balance  = $student->balance     ?? 0;
    $installAmt = isset($data['current_unpaid_installment']['installmentAmount'])
                    ? $data['current_unpaid_installment']['installmentAmount'] : 0;
@endphp

<div class="sd-hero">
    <div class="sd-hero-inner">

        @if($student->student_image)
            <img class="sd-hero-photo"
                 src="{{ asset('images/studentProfile/'.$student->student_image) }}"
                 alt="{{ $fullName }}">
        @else
            <div class="sd-hero-photo-placeholder"><i class="fa fa-user"></i></div>
        @endif

        <div class="sd-hero-info">
            <div class="sd-welcome-line">Welcome back,</div>
            <div class="sd-hero-name">{{ $fullName }}</div>
            <div class="sd-hero-tags">
                <span class="sd-tag"><i class="fa fa-id-card"></i> {{ $student->reg_no }}</span>
                @if($student->university_reg)
                    <span class="sd-tag"><i class="fa fa-university"></i> {{ $student->university_reg }}</span>
                @endif
                <span class="sd-tag"><i class="fa fa-graduation-cap"></i> {{ ViewHelper::getFacultyTitle($student->faculty) }}</span>
                <span class="sd-tag"><i class="fa fa-calendar"></i> {{ ViewHelper::getStudentBatchId($student->batch) }}</span>
                <span class="sd-tag" style="background:rgba(22,163,74,.25);border-color:rgba(22,163,74,.4)">
                    <i class="fa fa-check-circle"></i> Active
                </span>
            </div>
        </div>

        <div class="sd-hero-right">
            <div class="sd-date-label">Today</div>
            <div class="sd-date-val">{{ \Carbon\Carbon::now()->format('d M Y') }}</div>
            <div class="sd-college-badge">
                <i class="fa fa-building"></i> Lalmai Govt. College
            </div>
        </div>

    </div>
</div>

{{-- ── STAT CARDS ────────────────────────────────────────── --}}
<div class="sd-stats">
    <div class="sd-stat blue">
        <div class="sd-stat-icon"><i class="fa fa-money"></i></div>
        <div class="sd-stat-body">
            <div class="sd-stat-label">Total Fees</div>
            <div class="sd-stat-value">৳ {{ number_format($feeAmt, 0) }}</div>
            <div class="sd-stat-sub">Assigned this session</div>
        </div>
    </div>
    <div class="sd-stat green">
        <div class="sd-stat-icon"><i class="fa fa-check-circle"></i></div>
        <div class="sd-stat-body">
            <div class="sd-stat-label">Paid</div>
            <div class="sd-stat-value">৳ {{ number_format($paid + $disc, 0) }}</div>
            <div class="sd-stat-sub">Paid + Discount</div>
        </div>
    </div>
    <div class="sd-stat red">
        <div class="sd-stat-icon"><i class="fa fa-exclamation-circle"></i></div>
        <div class="sd-stat-body">
            <div class="sd-stat-label">Balance Due</div>
            <div class="sd-stat-value">৳ {{ number_format(max(0, $balance), 0) }}</div>
            <div class="sd-stat-sub">Outstanding amount</div>
        </div>
    </div>
    <div class="sd-stat amber">
        <div class="sd-stat-icon"><i class="fa fa-clock-o"></i></div>
        <div class="sd-stat-body">
            <div class="sd-stat-label">Current Installment</div>
            <div class="sd-stat-value">৳ {{ number_format($installAmt, 0) }}</div>
            <div class="sd-stat-sub">Amount now due</div>
        </div>
    </div>
</div>

{{-- ── QUICK ACCESS MODULES ──────────────────────────────── --}}
<div class="sd-modules">
    @permission('student-profile')
    <a href="{{ route('user-student.profile') }}" class="sd-module-card">
        <div class="sd-module-icon m-indigo"><i class="fa fa-user"></i></div>
        <span class="sd-module-label">Profile</span>
    </a>
    @endpermission
    @permission('student-fees')
    <a href="{{ route('user-student.fees') }}" class="sd-module-card">
        <div class="sd-module-icon m-green"><i class="fa fa-money"></i></div>
        <span class="sd-module-label">Fees</span>
    </a>
    @endpermission
    @permission('student-exam')
    <a href="{{ route('user-student.exams') }}" class="sd-module-card">
        <div class="sd-module-icon m-navy"><i class="fa fa-line-chart"></i></div>
        <span class="sd-module-label">Exam</span>
    </a>
    @endpermission
    @permission('student-attendance')
    <a href="{{ route('user-student.attendance') }}" class="sd-module-card">
        <div class="sd-module-icon m-teal"><i class="fa fa-calendar-check-o"></i></div>
        <span class="sd-module-label">Attendance</span>
    </a>
    @endpermission
    @permission('student-course')
    <a href="{{ route('user-student.subject') }}" class="sd-module-card">
        <div class="sd-module-icon m-purple"><i class="fa fa-book"></i></div>
        <span class="sd-module-label">Course</span>
    </a>
    @endpermission
    <a href="{{ route('user-student.routine') }}" class="sd-module-card">
        <div class="sd-module-icon m-orange"><i class="fa fa-calendar"></i></div>
        <span class="sd-module-label">Routine</span>
    </a>
    @permission('student-library')
    <a href="{{ route('user-student.library') }}" class="sd-module-card">
        <div class="sd-module-icon m-cyan"><i class="fa fa-book"></i></div>
        <span class="sd-module-label">Library</span>
    </a>
    @endpermission
    @permission('student-notice')
    <a href="{{ route('user-student.notice') }}" class="sd-module-card">
        <div class="sd-module-icon m-red"><i class="fa fa-bullhorn"></i></div>
        <span class="sd-module-label">Notice</span>
    </a>
    @endpermission
    @permission('student-assignment')
    <a href="{{ route('user-student.assignment') }}" class="sd-module-card">
        <div class="sd-module-icon m-yellow"><i class="fa fa-tasks"></i></div>
        <span class="sd-module-label">Assignment</span>
    </a>
    @endpermission
    @permission('student-download')
    <a href="{{ route('user-student.download') }}" class="sd-module-card">
        <div class="sd-module-icon m-slate"><i class="fa fa-download"></i></div>
        <span class="sd-module-label">Download</span>
    </a>
    @endpermission
    @permission('student-application')
    <a href="{{ route('user-student.application') }}" class="sd-module-card">
        <div class="sd-module-icon m-pink"><i class="fa fa-file-text-o"></i></div>
        <span class="sd-module-label">Application</span>
    </a>
    @endpermission
    @permission('student-meeting')
    <a href="{{ route('user-student.meeting') }}" class="sd-module-card">
        <div class="sd-module-icon m-rose"><i class="fa fa-video-camera"></i></div>
        <span class="sd-module-label">Meeting</span>
    </a>
    @endpermission
</div>

{{-- ── MAIN GRID: Chart + Notices ───────────────────────── --}}
<div class="sd-grid">

    {{-- Fee chart --}}
    <div class="sd-card">
        <div class="sd-card-header">
            <div class="sd-card-title">
                <span class="sd-card-title-icon ic-blue"><i class="fa fa-pie-chart"></i></span>
                Fee Payment Overview
            </div>
        </div>
        @if($installAmt > 0)
        <div class="sd-installment-alert">
            <i class="fa fa-info-circle"></i>
            <span>Current installment due: <strong>৳ {{ number_format($installAmt, 2) }}</strong> — Please pay this exact amount.</span>
        </div>
        @endif
        <div class="sd-chart-wrap">
            <div class="sd-chart-canvas">
                {!! $data['feeCompare']->container() !!}
            </div>
            <div class="sd-chart-legend">
                <div class="sd-legend-item">
                    <div class="sd-legend-dot" style="background:#1a6fa3"></div>
                    Paid: ৳ {{ number_format($paid, 0) }}
                </div>
                <div class="sd-legend-item">
                    <div class="sd-legend-dot" style="background:#ef4444"></div>
                    Due: ৳ {{ number_format(max(0,$balance), 0) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Notices --}}
    <div class="sd-card">
        <div class="sd-card-header">
            <div class="sd-card-title">
                <span class="sd-card-title-icon ic-amber"><i class="fa fa-bullhorn"></i></span>
                Important Notices
            </div>
            @if($data['notice_display'] && $data['notice_display']->count() > 0)
            <span style="font-size:11px;color:#6b7280;font-weight:600">
                {{ $data['notice_display']->count() }} notice{{ $data['notice_display']->count() > 1 ? 's' : '' }}
            </span>
            @endif
        </div>

        @if($data['notice_display'] && $data['notice_display']->count() > 0)
        <div class="sd-notices-list">
            @foreach($data['notice_display'] as $notice)
            <div class="sd-notice-item">
                <div class="sd-notice-head" onclick="toggleNotice(this)">
                    <div class="sd-notice-dot"></div>
                    <div class="sd-notice-title">{{ $notice->title }}</div>
                    @if($notice->created_at)
                    <div class="sd-notice-date">{{ $notice->created_at->format('d M') }}</div>
                    @endif
                    <i class="fa fa-chevron-down" style="font-size:10px;color:#9ca3af;transition:transform .2s"></i>
                </div>
                <div class="sd-notice-body">
                    {!! $notice->message !!}
                    @if($notice->attachment)
                    <div class="sd-notice-attach">
                        <a href="{{ asset($notice->attachment) }}" target="_blank">
                            <i class="fa fa-paperclip"></i> View Attachment
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="sd-no-notices">
            <i class="fa fa-check-circle" style="color:#d1fae5"></i>
            No notices at this time
        </div>
        @endif
    </div>

</div>{{-- /sd-grid --}}

</div>
</div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
{!! $data['feeCompare']->script() !!}
<script>
function toggleNotice(header) {
    var body   = header.nextElementSibling;
    var icon   = header.querySelector('.fa-chevron-down, .fa-chevron-up');
    var isOpen = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    if (icon) {
        icon.className = isOpen
            ? 'fa fa-chevron-down'
            : 'fa fa-chevron-up';
        icon.style.transform = isOpen ? '' : 'rotate(0deg)';
    }
}
</script>
@endsection
