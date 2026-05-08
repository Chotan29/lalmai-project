<table class="tab-content">
    <tr>
        <td class="text-right">Name</td>
        <td> : </td>
        <td class="text-left"><b>{{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</b></td>

        <td class="text-right">Reg.No.</td>
        <td> : </td>
        <td class="text-left"><b>{{ $student->reg_no }}</b></td>
    </tr>
    <tr>
        <td colspan="6">
            <hr class="hr hr-2">
        </td>
    </tr>
    <tr>
        <td class="text-right">Father Name</td>
        <td> : </td>
        <td class="text-left"><b>{{ $student->father_first_name.' '.$student->father_middle_name.' '.$student->father_last_name }}</b></td>
        <td class="text-right">Batch/Session</td>
        <td> : </td>
        <td class="text-left"><b>{{ $student->title}}</b></td>
    </tr>
    <tr>
        <td colspan="6">
            <hr class="hr hr-2">
        </td>
    </tr>
    <tr>
        <td class="text-right">Faculty/Program/Class</td>
        <td> : </td>
        <td class="text-left"><b>{{ ViewHelper::getFacultyTitle($student->faculty) }}</b></td>
        <td class="text-right">Sem./Sec.</td>
        <td> : </td>
        <td class="text-left"><b>{{ ViewHelper::getSemesterTitle($student->semester) }}</b></td>
    </tr>

</table>