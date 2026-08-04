{{-- One student's result. Drawn with the office's own tabulation markup, so what a parent
     reads here is the same row the college prints - not a second rendering that could drift.
     Nothing beyond roll, name and marks is shown: no phone, no address, no guardian. --}}
@php
    $gs = $data['generalSetting'] ?? null;
    $me = $data['result']['student']->first();
    $cellCount = $data['result']['tabulation_cell_count'] ?? (count($data['result']['subject_columns']) * 2 + 4);
    $density = $cellCount > 55 ? 'is-tight' : ($cellCount > 35 ? 'is-wide' : '');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    @include('front.result.includes.head')
    <title>Result {{ $me->reg_no }} - {{ $gs->institute ?? 'Lalmai Govt. College' }}</title>
    @include('front.result.includes.style')
    @include('print.exam.includes.tabulation-style')
</head>
<body>
<div class="pr-wrap pr-wide">
    <div class="pr-card">
        <div class="pr-head">
            @if($gs && $gs->logo && is_file(public_path('images/setting/general/'.$gs->logo)))
                <img src="{{ asset('images/setting/general/'.$gs->logo) }}" alt="Logo">
            @endif
            <h4>{{ $gs->institute ?? 'Lalmai Govt. College' }}</h4>
            <div class="sub">{{ $data['exam_title'] }}</div>
        </div>

        <div class="pr-student">
            <div>
                <div class="nm">{{ trim($me->first_name.' '.$me->middle_name.' '.$me->last_name) }}</div>
                <div class="rl">Roll {{ $me->reg_no }}</div>
            </div>
            <div class="pr-gpa {{ $me->gpa_remark === 'Pass' ? 'is-pass' : 'is-fail' }}">
                GPA {{ number_format((float) $me->gpa_average, 2) }}
                &nbsp;|&nbsp; {{ $me->gpa_grade }}
                &nbsp;|&nbsp; {{ $me->gpa_remark }}
            </div>
        </div>

        <div class="tab-sheet-wrapper {{ $density }}">
            <div class="tab-sheet-scroll">
                @include('print.exam.includes.tabulation-table', ['data' => $data['result'], 'compact' => true])
            </div>
        </div>

        <div class="pr-noprint" style="margin-top:18px;">
            <button type="button" class="btn btn-primary pr-btn" onclick="window.print();">Print</button>
            <a href="{{ route('public-result') }}" class="btn btn-outline-secondary">Another Result</a>
        </div>

        <div class="pr-note">
            This is an online copy for information only. For any correction or an official
            transcript, please contact the college office.
        </div>
    </div>

    <div class="pr-foot">
        {{ $gs->institute ?? 'Lalmai Govt. College' }} &middot; Cumilla Sadar South
    </div>
</div>
</body>
</html>
