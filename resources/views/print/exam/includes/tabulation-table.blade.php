{{-- Shared tabulation sheet markup: header + grade scale + class-wide result table.
     Used by tabulation-sheet.blade.php (web/print) and tabulation-sheet-pdf.blade.php (dompdf). --}}

<table class="tab-sheet-head">
    <tr>
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
        <td>
            <div class="tab-title">
                <h2>{{ $generalSetting->institute ?? 'Lalmai Govt. College' }}</h2>
                <h4>Results of {{ ViewHelper::getSemesterTitle($data['semester']) }} {{ ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) }}</h4>
            </div>
            <div class="tab-meta">
                <span class="group-box">Group-{{ ViewHelper::getFacultyTitle($data['faculty']) }}</span>
                <span style="float:right;">Date: {{ \Carbon\Carbon::now()->format('d.m.Y') }}</span>
            </div>
        </td>
    </tr>
</table>

<table class="tab-main-table">
    <thead>
        <tr>
            <th rowspan="2" style="width:70px;">Roll</th>
            <th rowspan="2" class="text-left" style="min-width:120px;">Name</th>
            @foreach($data['subject_columns'] as $column)
                <th colspan="2" class="tab-subject-head" title="{{ $column->title }}">{{ $column->short_name ?? $column->title }}</th>
            @endforeach
            <th rowspan="2">GPA</th>
            <th rowspan="2">L.G</th>
        </tr>
        <tr>
            @foreach($data['subject_columns'] as $column)
                <th>Mark</th>
                <th>LG</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data['student'] as $student)
            <tr>
                <td>{{ $student->reg_no }}</td>
                <td class="text-left">{{ trim($student->first_name.' '.$student->middle_name.' '.$student->last_name) }}</td>
                @foreach($data['subject_columns'] as $column)
                    @php($subject = $student->subjects->firstWhere('subjects_id', $column->subjects_id))
                    @if($subject)
                        <td>{{ is_numeric($subject->total_obtain_mark) ? $subject->total_obtain_mark + 0 : $subject->total_obtain_mark }}</td>
                        <td class="{{ $subject->final_grade == 'F' ? 'tab-fail' : '' }}">{{ $subject->final_grade }}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
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
    <strong>Subjects:</strong>
    @foreach($data['subject_columns'] as $column)
        <span><b>{{ $column->short_name ?? $column->title }}</b> = {{ $column->title }}</span>@if(!$loop->last) &nbsp;|&nbsp; @endif
    @endforeach
</div>
