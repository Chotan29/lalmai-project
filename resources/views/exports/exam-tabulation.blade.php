@php
    $width = 4 + $data['subject_columns']->sum('span');
@endphp
<table>
    <thead>
        <tr>
            <th colspan="{{ $width }}" style="font-weight:bold; font-size:16px; text-align:center; background-color:#0f5132; color:#ffffff;">
                {{ $generalSetting->institute ?? 'Lalmai Govt. College' }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ $width }}" style="font-weight:bold; text-align:center;">
                Results of {{ ViewHelper::getSemesterTitle($data['semester']) }} {{ ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) }}
            </th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight:bold;">Group: {{ ViewHelper::getFacultyTitle($data['faculty']) }}</th>
            <th colspan="{{ $width - 2 }}" style="text-align:right;">Date: {{ \Carbon\Carbon::now()->format('d.m.Y') }}</th>
        </tr>
        <tr>
            <th style="font-weight:bold; border:1px solid #333; background-color:#0f5132; color:#ffffff;">Roll</th>
            <th style="font-weight:bold; border:1px solid #333; background-color:#0f5132; color:#ffffff;">Name</th>
            @foreach($data['subject_columns'] as $column)
                <th colspan="{{ $column->span }}" style="font-weight:bold; border:1px solid #333; text-align:center; background-color:#0f5132; color:#ffffff;">{{ $column->short_name ?? $column->title }}</th>
            @endforeach
            <th style="font-weight:bold; border:1px solid #333; background-color:#0f5132; color:#ffffff;">GPA</th>
            <th style="font-weight:bold; border:1px solid #333; background-color:#0f5132; color:#ffffff;">L.G</th>
        </tr>
        <tr>
            <th style="border:1px solid #333;"></th>
            <th style="border:1px solid #333;"></th>
            @foreach($data['subject_columns'] as $column)
                @if($column->has_theory)<th style="font-weight:bold; border:1px solid #333; text-align:center; background-color:#e7f3ec;">T</th>@endif
                @if($column->has_mcq)<th style="font-weight:bold; border:1px solid #333; text-align:center; background-color:#e7f3ec;">M</th>@endif
                @if($column->has_practical)<th style="font-weight:bold; border:1px solid #333; text-align:center; background-color:#e7f3ec;">P</th>@endif
                <th style="font-weight:bold; border:1px solid #333; text-align:center; background-color:#e7f3ec;">Tot</th>
                <th style="font-weight:bold; border:1px solid #333; text-align:center; background-color:#e7f3ec;">LG</th>
            @endforeach
            <th style="border:1px solid #333;"></th>
            <th style="border:1px solid #333;"></th>
        </tr>
        <tr>
            <th style="border:1px solid #333;">Full Marks</th>
            <th style="border:1px solid #333;"></th>
            @foreach($data['subject_columns'] as $column)
                @if($column->has_theory)<th style="border:1px solid #333; text-align:center;">{{ $column->full_theory + 0 }}</th>@endif
                @if($column->has_mcq)<th style="border:1px solid #333; text-align:center;">{{ $column->full_mcq + 0 }}</th>@endif
                @if($column->has_practical)<th style="border:1px solid #333; text-align:center;">{{ $column->full_practical + 0 }}</th>@endif
                <th style="border:1px solid #333; text-align:center;">{{ $column->full_total + 0 }}</th>
                <th style="border:1px solid #333; text-align:center;">-</th>
            @endforeach
            <th style="border:1px solid #333;"></th>
            <th style="border:1px solid #333;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['student'] as $student)
            @php($rowBg = $loop->even ? 'background-color:#f4f9f6;' : '')
            <tr>
                <td style="border:1px solid #333; {{ $rowBg }}">{{ $student->reg_no }}</td>
                <td style="border:1px solid #333; {{ $rowBg }}">{{ trim($student->first_name.' '.$student->middle_name.' '.$student->last_name) }}</td>
                @foreach($data['subject_columns'] as $column)
                    @php($subject = $student->subjects->firstWhere('subjects_id', $column->subjects_id))
                    @if($subject)
                        @if($column->has_theory)
                            <td style="border:1px solid #333; text-align:center; {{ $subject->th_remark ? 'background-color:#fdecea;color:#c0392b;font-weight:bold;' : $rowBg }}">{{ is_numeric($subject->obtain_mark_theory) ? $subject->obtain_mark_theory + 0 : $subject->obtain_mark_theory }}</td>
                        @endif
                        @if($column->has_mcq)
                            <td style="border:1px solid #333; text-align:center; {{ $subject->mcq_remark ? 'background-color:#fdecea;color:#c0392b;font-weight:bold;' : $rowBg }}">{{ is_numeric($subject->obtain_mark_mcq) ? $subject->obtain_mark_mcq + 0 : $subject->obtain_mark_mcq }}</td>
                        @endif
                        @if($column->has_practical)
                            <td style="border:1px solid #333; text-align:center; {{ $subject->pr_remark ? 'background-color:#fdecea;color:#c0392b;font-weight:bold;' : $rowBg }}">{{ is_numeric($subject->obtain_mark_practical) ? $subject->obtain_mark_practical + 0 : $subject->obtain_mark_practical }}</td>
                        @endif
                        <td style="border:1px solid #333; text-align:center; {{ $rowBg }}">{{ is_numeric($subject->total_obtain_mark) ? $subject->total_obtain_mark + 0 : $subject->total_obtain_mark }}</td>
                        <td style="border:1px solid #333; text-align:center; {{ $subject->final_grade == 'F' ? 'background-color:#fdecea;color:#c0392b;font-weight:bold;' : $rowBg }}">{{ $subject->final_grade }}</td>
                    @else
                        @for($i = 0; $i < $column->span; $i++)
                            <td style="border:1px solid #333; text-align:center; {{ $rowBg }}">-</td>
                        @endfor
                    @endif
                @endforeach
                <td style="border:1px solid #333; text-align:center; font-weight:bold; {{ $rowBg }}">{{ $student->gpa_average }}</td>
                <td style="border:1px solid #333; text-align:center; font-weight:bold; {{ $student->gpa_grade == 'F' ? 'background-color:#fdecea;color:#c0392b;' : $rowBg }}">{{ $student->gpa_grade }}</td>
            </tr>
        @endforeach
        <tr><td colspan="{{ $width }}"></td></tr>
        <tr><td colspan="{{ $width }}" style="font-weight:bold;">T = Theory, M = MCQ, P = Practical, Tot = Total, LG = Letter Grade, AB = Absent</td></tr>
        <tr><td colspan="{{ $width }}" style="font-weight:bold;">Subject short names</td></tr>
        @foreach($data['subject_columns'] as $column)
            <tr>
                <td style="font-weight:bold;">{{ $column->short_name ?? $column->title }}</td>
                <td colspan="{{ $width - 1 }}">{{ $column->title }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
