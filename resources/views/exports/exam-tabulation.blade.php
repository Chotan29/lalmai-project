<table>
    <thead>
        <tr>
            <th colspan="{{ 4 + ($data['subject_columns']->count() * 2) }}" style="font-weight:bold; font-size:16px; text-align:center;">
                {{ $generalSetting->institute ?? 'Lalmai Govt. College' }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ 4 + ($data['subject_columns']->count() * 2) }}" style="font-weight:bold; text-align:center;">
                Results of {{ ViewHelper::getSemesterTitle($data['semester']) }} {{ ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) }}
            </th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight:bold;">Group: {{ ViewHelper::getFacultyTitle($data['faculty']) }}</th>
            <th colspan="{{ 2 + ($data['subject_columns']->count() * 2) }}" style="text-align:right;">Date: {{ \Carbon\Carbon::now()->format('d.m.Y') }}</th>
        </tr>
        <tr>
            <th style="font-weight:bold; border:1px solid #333;">Roll</th>
            <th style="font-weight:bold; border:1px solid #333;">Name</th>
            @foreach($data['subject_columns'] as $column)
                <th colspan="2" style="font-weight:bold; border:1px solid #333; text-align:center;">{{ $column->short_name ?? $column->title }}</th>
            @endforeach
            <th style="font-weight:bold; border:1px solid #333;">GPA</th>
            <th style="font-weight:bold; border:1px solid #333;">L.G</th>
        </tr>
        <tr>
            <th style="border:1px solid #333;"></th>
            <th style="border:1px solid #333;"></th>
            @foreach($data['subject_columns'] as $column)
                <th style="font-weight:bold; border:1px solid #333; text-align:center;">Mark</th>
                <th style="font-weight:bold; border:1px solid #333; text-align:center;">LG</th>
            @endforeach
            <th style="border:1px solid #333;"></th>
            <th style="border:1px solid #333;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['student'] as $student)
            <tr>
                <td style="border:1px solid #333;">{{ $student->reg_no }}</td>
                <td style="border:1px solid #333;">{{ trim($student->first_name.' '.$student->middle_name.' '.$student->last_name) }}</td>
                @foreach($data['subject_columns'] as $column)
                    @php($subject = $student->subjects->firstWhere('subjects_id', $column->subjects_id))
                    @if($subject)
                        <td style="border:1px solid #333; text-align:center;">{{ is_numeric($subject->total_obtain_mark) ? $subject->total_obtain_mark + 0 : $subject->total_obtain_mark }}</td>
                        <td style="border:1px solid #333; text-align:center;">{{ $subject->final_grade }}</td>
                    @else
                        <td style="border:1px solid #333; text-align:center;">-</td>
                        <td style="border:1px solid #333; text-align:center;">-</td>
                    @endif
                @endforeach
                <td style="border:1px solid #333; text-align:center; font-weight:bold;">{{ $student->gpa_average }}</td>
                <td style="border:1px solid #333; text-align:center; font-weight:bold;">{{ $student->gpa_grade }}</td>
            </tr>
        @endforeach
        <tr><td colspan="{{ 4 + ($data['subject_columns']->count() * 2) }}"></td></tr>
        <tr>
            <td colspan="{{ 4 + ($data['subject_columns']->count() * 2) }}" style="font-weight:bold;">Subject short names</td>
        </tr>
        @foreach($data['subject_columns'] as $column)
            <tr>
                <td style="font-weight:bold;">{{ $column->short_name ?? $column->title }}</td>
                <td colspan="{{ 3 + ($data['subject_columns']->count() * 2) }}">{{ $column->title }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
