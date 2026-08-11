{{-- Shared tabulation sheet markup: header + grade scale + class-wide result table.
     Used by tabulation-sheet.blade.php (web/print) and tabulation-sheet-pdf.blade.php (dompdf).

     Each subject shows only the components it actually has:
       science paper -> Theory | MCQ | Practical | Total | LG
       English       ->                            Total | LG --}}

@php
    $tabGs = $generalSetting ?? null;

    /* Compact draws the same result grid without the college letterhead and grade scale.
       Used where the page already says which exam this is - the student profile and the
       student's own panel - so only the row itself is repeated. */
    $tabCompact = isset($compact) && $compact;

    /* Embed the logo as base64 so it survives both the browser print dialog and the
       dompdf PDF renderer - a plain <img src="/images/..."> is dropped by dompdf when the
       app isn't reachable from the PDF worker. */
    $tabLogoSrc = '';
    if (isset($tabGs->logo) && $tabGs->logo) {
        $tabLogoFile = public_path('images/setting/general/'.$tabGs->logo);
        if (is_file($tabLogoFile)) {
            $tabLogoExt = strtolower(pathinfo($tabLogoFile, PATHINFO_EXTENSION));
            $tabLogoMime = ($tabLogoExt === 'jpg' || $tabLogoExt === 'jpeg') ? 'jpeg' : $tabLogoExt;
            $tabLogoSrc = 'data:image/'.$tabLogoMime.';base64,'.base64_encode(file_get_contents($tabLogoFile));
        }
    }
@endphp

@unless($tabCompact)
<table class="tab-sheet-head">
    <tr>
        <td class="tab-head-logo-cell">
            @if($tabLogoSrc)
                <img src="{{ $tabLogoSrc }}" alt="Logo" class="tab-head-logo">
            @endif
        </td>
        <td>
            <div class="tab-title">
                <h2>{{ $generalSetting->institute ?? 'Lalmai Govt. College' }}</h2>
                <h4>Results of {{ ViewHelper::getSemesterTitle($data['semester']) }} {{ ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) }}</h4>
            </div>
            <div class="tab-meta">
                <span class="group-box">Group-{{ ViewHelper::getFacultyTitle($data['faculty']) }}</span>
                <span style="float:right;">Date: {{ \Carbon\Carbon::now()->format('d.m.Y') }}</span>
            </div>

            {{-- Appeared, passed and the rate. Counted across the whole exam even on a sheet
                 that lists only those who passed - otherwise the sheet would say 100% and
                 mean nothing. A sheet that leaves people off says so, in writing, or a reader
                 has no way to know the list is not everybody. --}}
            @if(!empty($data['result_summary']))
                @php($tabSum = $data['result_summary'])
                <div class="tab-summary">
                    <span class="tab-sum-item"><b>Appeared</b> {{ number_format($tabSum['appeared']) }}</span>
                    <span class="tab-sum-item"><b>Passed</b> {{ number_format($tabSum['passed']) }}</span>
                    <span class="tab-sum-item"><b>Failed</b> {{ number_format($tabSum['failed']) }}</span>
                    <span class="tab-sum-item tab-sum-rate"><b>Pass Rate</b> {{ number_format($tabSum['pass_rate'], 2) }}%</span>
                    @if(!empty($tabSum['passed_only']))
                        <span class="tab-sum-note">This sheet lists the passed students only.</span>
                    @endif
                </div>
            @endif
        </td>
        <td style="width:180px;">
            <table class="tab-scale-table">
                <tr><th>Marks</th><th>GPA</th></tr>
                @foreach($data['grade-scale-range'] as $grade)
                    @if($grade->name != 'F')
                        <tr>
                            <td>{{ (int) $grade->percentage_from }}-{{ (int) round($grade->percentage_to) }}</td>
                            <td>{{ rtrim(rtrim(number_format($grade->grade_point, 2), '0'), '.') }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </td>
    </tr>
</table>
@endunless

<table class="tab-main-table">
    <thead>
        <tr>
            <th rowspan="3" class="col-roll">Roll</th>
            <th rowspan="3" class="text-left col-name">Name</th>
            @foreach($data['subject_columns'] as $column)
                <th colspan="{{ $column->span }}" class="tab-subject-head" title="{{ $column->title }}">{{ $column->short_name ?? $column->title }}</th>
            @endforeach
            <th rowspan="3" class="col-gpa">GPA</th>
            <th rowspan="3" class="col-gpa">L.G</th>
        </tr>
        <tr>
            @foreach($data['subject_columns'] as $column)
                @if($column->has_theory)<th>T</th>@endif
                @if($column->has_mcq)<th>M</th>@endif
                @if($column->has_practical)<th>P</th>@endif
                <th>Tot</th>
                <th>LG</th>
            @endforeach
        </tr>
        <tr class="tab-fullmark-row">
            @foreach($data['subject_columns'] as $column)
                @if($column->has_theory)<th>{{ $column->full_theory + 0 }}</th>@endif
                @if($column->has_mcq)<th>{{ $column->full_mcq + 0 }}</th>@endif
                @if($column->has_practical)<th>{{ $column->full_practical + 0 }}</th>@endif
                <th>{{ $column->full_total + 0 }}</th>
                <th>&mdash;</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data['student'] as $student)
            <tr class="{{ $loop->even ? 'tab-row-even' : '' }}">
                <td>{{ $student->reg_no }}</td>
                <td class="text-left">{{ trim($student->first_name.' '.$student->middle_name.' '.$student->last_name) }}</td>
                @foreach($data['subject_columns'] as $column)
                    @php($subject = $student->subjects->firstWhere('subjects_id', $column->subjects_id))
                    @if($subject)
                        @if($column->has_theory)
                            <td class="{{ $subject->th_remark ? 'tab-fail' : '' }}">{{ is_numeric($subject->obtain_mark_theory) ? $subject->obtain_mark_theory + 0 : $subject->obtain_mark_theory }}</td>
                        @endif
                        @if($column->has_mcq)
                            <td class="{{ $subject->mcq_remark ? 'tab-fail' : '' }}">{{ is_numeric($subject->obtain_mark_mcq) ? $subject->obtain_mark_mcq + 0 : $subject->obtain_mark_mcq }}</td>
                        @endif
                        @if($column->has_practical)
                            <td class="{{ $subject->pr_remark ? 'tab-fail' : '' }}">{{ is_numeric($subject->obtain_mark_practical) ? $subject->obtain_mark_practical + 0 : $subject->obtain_mark_practical }}</td>
                        @endif
                        <td>{{ is_numeric($subject->total_obtain_mark) ? $subject->total_obtain_mark + 0 : $subject->total_obtain_mark }}</td>
                        <td class="{{ $subject->final_grade == 'F' ? 'tab-fail' : '' }}">{{ $subject->final_grade }}</td>
                    @else
                        @for($i = 0; $i < $column->span; $i++)
                            <td>-</td>
                        @endfor
                    @endif
                @endforeach
                <td><strong>{{ rtrim(rtrim(number_format((float) $student->gpa_average, 2), '0'), '.') }}</strong></td>
                <td class="{{ $student->gpa_grade == 'F' ? 'tab-fail' : '' }}"><strong>{{ $student->gpa_grade }}</strong></td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- The columns carry short names to keep the sheet on one page; this spells them out. --}}
<div class="tab-subject-legend">
    <div><strong>T</strong> = Theory &nbsp;|&nbsp; <strong>M</strong> = MCQ &nbsp;|&nbsp; <strong>P</strong> = Practical &nbsp;|&nbsp; <strong>Tot</strong> = Total &nbsp;|&nbsp; <strong>LG</strong> = Letter Grade &nbsp;|&nbsp; <strong>AB</strong> = Absent &nbsp;|&nbsp; the third heading row shows full marks.</div>
    <div><strong>Subjects:</strong>
        @foreach($data['subject_columns'] as $column)
            <span><b>{{ $column->short_name ?? $column->title }}</b> = {{ $column->title }}</span>@if(!$loop->last) &nbsp;|&nbsp; @endif
        @endforeach
    </div>
</div>
