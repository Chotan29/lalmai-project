<table width="100%" class="table-bordered">
    <thead>
        <tr>
            <th>{{ __('common.s_n')}}</th>
            <th>{{__('form_fields.student.fields.semester')}}</th>
            <th>Reg. Num.</th>
            <th>Name of Student</th>
            <th>StudentContactNo.</th>
            <th>Guardian</th>
            <th>GuardianContactNo.</th>
            {{--<th>Total Fee</th>--}}
            <th>Balance</th>
        </tr>
    </thead>
    @if (isset($data['student']) && $data['student']->count() > 0)

    <tbody>
        @php($i=1)
        @foreach($data['student'] as $student)
            <tr>
                <td>{{ $i }}</td>
                <td> {{  ViewHelper::getSemesterTitle( $student->semester ) }}</td>
                <td>{{ $student->reg_no }}</td>
                <td> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</td>
                <td> {{ $student->mobile_1 }}</td>
                <td> {{ $student->guardian_first_name.' '.$student->guardian_middle_name.' '. $student->guardian_last_name }}</td>
                <td> {{ $student->guardian_mobile_1 }}</td>
               {{-- <td>
                    {{ $student->fee_amount }}
                </td>--}}
                <td class="text-right">
                    {{ $student->balance }}
                </td>
            </tr>
            @php($i++)
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-size: 14px;font-weight: bold; background: orangered;color: white;">
            <td colspan="7" class="text-right">Total</td>
            {{--<td  class="text-right">{{ $data['student']->sum('fee_amount') }}</td>--}}
            <td  class="text-right"> {{ $data['student']->sum('balance') }} </td>
        </tr>
    </tfoot>
    @else
        <tr>
            <td colspan="7">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
        </tr>
    @endif
</table>
