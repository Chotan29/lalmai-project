@extends('user-student.layouts.master')

@section('css')
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --dark-color: #1a1a2e;
        --light-color: #f8f9fa;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --danger-color: #f87171;
    }

    .routine-dashboard { background-color:#f5f7fb; min-height:100vh; }

    .dashboard-header {
        background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,0.06);
        padding:20px; margin-bottom:30px; display:flex; justify-content:space-between; align-items:center;
    }

    .semester-info {
        background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));
        color:#fff; padding:15px 25px; border-radius:10px; display:inline-flex; align-items:center;
        box-shadow:0 4px 12px rgba(67,97,238,.3);
    }
    .semester-info i { margin-right:12px; font-size:1.8rem; }
    .chips { margin-top:6px; font-size:.9rem; opacity:.95 }
    .chip { background:rgba(255,255,255,.2); padding:3px 10px; border-radius:999px; margin-right:6px; display:inline-block }

    .stats-card {
        background:#fff; border-radius:12px; padding:12px 14px; box-shadow:0 4px 12px rgba(0,0,0,.05);
        display:flex; align-items:center; margin-left:12px;
    }
    .stats-icon {
        width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;
        margin-right:12px;font-size:1.2rem;background:rgba(67,97,238,.1);color:var(--primary-color)
    }

    .controls { display:flex; gap:10px; flex-wrap:wrap; }
    .form-chip { border:1px solid #dee2e6; border-radius:8px; padding:8px 10px; min-width:220px; background:#fff }
    .btn-primary { background:var(--primary-color); color:#fff; border:none; padding:8px 14px; border-radius:8px }
    .btn-outline { border:1px solid #dee2e6; background:#fff; color:#6c757d; padding:8px 14px; border-radius:8px }

    .day-card {
        background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.06);
        overflow:hidden; border:1px solid rgba(0,0,0,.05); margin-bottom:22px;
    }
    .day-header {
        background:linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color:#fff; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;
    }
    .badge-round { background:rgba(255,255,255,.2); padding:4px 10px; border-radius:999px; font-size:.85rem }

    .slots { padding:18px; }
    .slot {
        background:#ffffff; border:1px solid #e5e7eb; border-left:4px solid var(--accent-color);
        border-radius:12px; padding:16px; position:relative; min-height:112px;
        box-shadow:0 2px 6px rgba(0,0,0,.04); transition:box-shadow .2s ease;
        margin-bottom:16px; display:flex; flex-direction:column;
    }
    .slot:hover { box-shadow:0 8px 18px rgba(0,0,0,.08) }
    .ribbon {
        position:absolute; top:6px; left:-8px; background:#ff9800; color:#fff; font-weight:700; font-size:.8rem;
        padding:4px 10px 4px 16px; border-radius:0 6px 6px 0; box-shadow:0 2px 6px rgba(0,0,0,.18)
    }
    .ribbon:before {
        content:""; position:absolute; left:0; top:100%; border-width:6px 8px; border-style:solid;
        border-color:#c77700 transparent transparent transparent;
    }
    .slot-time { font-weight:700; font-size:1.2rem; }
    .slot-title { font-size:1.5rem; font-weight:600; margin:10px 0 6px 0; color:var(--primary) }
    .slot-meta { color:#6b7280; font-size:1.2rem }
    .slot-meta i { width:16px; text-align:center; margin-right:6px; color:#64748b }

    .empty {
        background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.06); padding:40px; text-align:center; color:#6c757d;
    }

    @media (max-width: 768px){
        .dashboard-header { flex-direction:column; align-items:flex-start; gap:12px }
        .controls { width:100% }
    }

    @media print {
        .dashboard-header, .controls, .btn-primary, .btn-outline { display:none !important }
        .day-card { box-shadow:none; border:none }
        body { background:#fff }
    }
</style>
@endsection

@section('content')
<div class="routine-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')

            {{-- Header --}}
            <div class="dashboard-header">
                <div class="semester-info">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <h4 style="margin:0;font-weight:600;">My Class Routine</h4>
                        <div class="chips">
                            @if($faculty)<span class="chip">Program: {{ $faculty->faculty }}</span>@endif
                            @if($semester)<span class="chip">Semester: {{ $semester->semester }}</span>@endif
                            @if($batch)<span class="chip">Batch: {{ $batch->title }}</span>@endif
                        </div>
                    </div>
                </div>

                <div class="controls">
                    <form method="GET" action="{{ route('user-student.routine') }}" class="d-flex gap-2">
                        <select name="subject_id" class="form-chip" onchange="this.form.submit()">
                            <option value="">— All Subjects —</option>
                            @foreach($subjects as $sid => $stitle)
                                <option value="{{ $sid }}" {{ (string)$subjectId === (string)$sid ? 'selected' : '' }}>
                                    {{ $stitle }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('subject_id'))
                            <a href="{{ route('user-student.routine') }}" class="btn-outline"><i class="fas fa-times"></i> Clear</a>
                        @endif
                        <button type="button" class="btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </form>
                </div>
            </div>

            @include('includes.flash_messages')

            {{-- Empty state --}}
            @if(empty($routines) || collect($routines)->flatten(1)->isEmpty())
                <div class="empty">
                    <div style="font-size:32px; margin-bottom:8px;">📅</div>
                    No routine found for your program/semester{{ $batch ? ' and batch' : '' }}.
                </div>
            @else
                {{-- Day cards --}}
                @php $days = $order ?? ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; @endphp

                @foreach($days as $day)
                    @php $list = collect($routines)->get($day, collect()); @endphp
                    @if($list->count())
                        <div class="day-card">
                            <div class="day-header">
                                <div><i class="far fa-calendar"></i> &nbsp; <strong>{{ $day }}</strong></div>
                                <span class="badge-round">{{ $list->count() }} {{ \Illuminate\Support\Str::plural('class', $list->count()) }}</span>
                            </div>

                            <div class="slots">
                                <div class="row g-3">
                                    @foreach($list as $routine)
                                        @php
                                            $teacher = trim(
                                                trim(($routine->teacher->first_name ?? '').' '.($routine->teacher->middle_name ?? ''))
                                                .' '.($routine->teacher->last_name ?? '')
                                            );
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="slot">
                                                @if(!empty($routine->period))
                                                    <div class="ribbon">Period {{ $routine->period }}</div>
                                                @endif

                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div class="slot-title">{{ $routine->subject->title ?? 'Subject' }}</div>
                                                    <div class="slot-time">
                                                        {{ \Carbon\Carbon::parse($routine->start_time)->format('h:i A') }}
                                                        –
                                                        {{ \Carbon\Carbon::parse($routine->end_time)->format('h:i A') }}
                                                    </div>
                                                </div>

                                                <div class="slot-meta mb-1">
                                                    <i class="fas fa-book"></i>{{ $routine->subject->code ?? '-' }}
                                                </div>
                                                <div class="slot-meta mb-1">
                                                    <i class="fas fa-chalkboard-teacher"></i>{{ $teacher !== '' ? $teacher : '-' }}
                                                </div>
                                                <div class="slot-meta mb-1">
                                                    <i class="fas fa-door-open"></i>Room {{ $routine->room_number ?? '-' }}
                                                </div>
                                                @if(!$batch)
                                                    <div class="slot-meta">
                                                        <i class="fas fa-users"></i>{{ $routine->batch->title ?? '' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

        </div><!-- /.page-content -->
    </div>
</div>
@endsection

@section('js')
<script>
    // small click animation
    document.querySelectorAll('.btn-primary').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.style.transform = 'scale(0.96)';
            setTimeout(()=> btn.style.transform = 'scale(1)', 120);
        });
    });
</script>
@endsection
