{{-- The department list as a spreadsheet. Plain numbers, no formatting in the cells: a figure
     written as "1,270,700.00" arrives in Excel as text and cannot be added up. --}}
<table>
    <thead>
        <tr>
            <th colspan="6" style="font-weight:bold;">{{ $title }}</th>
        </tr>
        @if($period)
            <tr>
                <th colspan="6">Collection period {{ $period }}</th>
            </tr>
        @endif
        <tr><th colspan="6"></th></tr>
        <tr>
            <th style="font-weight:bold;">Department</th>
            <th style="font-weight:bold;">Students</th>
            <th style="font-weight:bold;">Paid Department Part</th>
            <th style="font-weight:bold;">College</th>
            <th style="font-weight:bold;">Department</th>
            <th style="font-weight:bold;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $r)
            <tr>
                <td>{{ $r->department }}</td>
                <td>{{ (int) $r->students }}</td>
                <td>{{ (int) $r->dept_students }}</td>
                <td>{{ (float) $r->college_amount }}</td>
                <td>{{ (float) $r->department_amount }}</td>
                <td>{{ (float) $r->total_amount }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="font-weight:bold;">All Departments</td>
            <td style="font-weight:bold;">{{ (int) $rows->sum('students') }}</td>
            <td style="font-weight:bold;">{{ (int) $rows->sum('dept_students') }}</td>
            <td style="font-weight:bold;">{{ (float) $collegeTotal }}</td>
            <td style="font-weight:bold;">{{ (float) $departmentTotal }}</td>
            <td style="font-weight:bold;">{{ (float) $grandTotal }}</td>
        </tr>
    </tbody>
</table>
