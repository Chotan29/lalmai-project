<div class="table-responsive">
    <table width="100%" class="table table-bordered">
        <tr>
            <td class="text-right">Reg No. : </td>
            <td class="text-left"><b>{{ $student->reg_no }}</b></td>

            <td class="text-right">Name : </td>
            <td class="text-left"><b>{{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</b></td>
        </tr>

        <tr>
            <td class="text-right">Faculty/Program/Class : </td>
            <td class="text-left"><b>{{ ViewHelper::getFacultyTitle($student->faculty) }}</b></td>
            <td class="text-right">Sem./Sec. : </td>
            <td class="text-left"><b>{{ ViewHelper::getSemesterTitle($student->semester) }}</b></td>
        </tr>

        <tr>
            <td class="text-right">Date of Birth : </td>
            <td class="text-left"><b>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y') }}</b></td>
           {{-- <td class="text-right">Son/Daughter of : </td>
            <td>{{$student->faculty}}</td>--}}
        </tr>
    </table>
</div>