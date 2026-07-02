<table width="100%" class="">
    <thead>
        <tr>
            <th>{{ __('common.s_n')}}</th>
            <th>Reg. Num.</th>
            <th>Name of Student</th>
            <th>Contact No.</th>
            <th> </th>
            <th>{{__('form_fields.student.fields.semester')}}</th>
            <th>Head</th>
            <th>Due Date</th>
            <th>Amount</th>
            <th>Discount</th>
            <th>Fine</th>
            <th>Paid</th>
            <th>Balance</th>
        </tr>
    </thead>
    @if (isset($data['student']) && $data['student']->count() > 0)
    <tbody>
        @php($i=1)
        @foreach($data['student'] as $student)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $student->reg_no }}</td>
                <td> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</td>
                <td> {{ $student->mobile_1 }}</td>
                <td colspan="9"></td>

            </tr>
            @if($student->master && $student->master->count() > 0)
            @php($dsn=1)

            @foreach($student->master as $feeMaster)
                    <tr>
                        <td colspan="4" ></td>
                        <td class="center">{{ $dsn }} | </td>
                        <td>
                            {{ ViewHelper::getSemesterById($feeMaster->semester) }}
                        </td>
                        <td>
                            {{ ViewHelper::getFeeHeadById($feeMaster->fee_head) }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($feeMaster->fee_due_date)->format('Y-m-d') }}
                        </td>
                        <td class="text-right">
                            {{ $feeMaster->fee_amount?$feeMaster->fee_amount:'-' }}
                        </td>
                        <td class="text-right">
                            {{ $feeMaster->discount?$feeMaster->discount:'-' }}
                        </td>
                        <td class="text-right">
                            {{ $feeMaster->fine?$feeMaster->fine:'-' }}
                        </td>
                        <td class="text-right">
                            {{ $feeMaster->paid_amount?$feeMaster->paid_amount:'-' }}
                        </td>
                        <td class="text-right">
                            {{ $feeMaster->due?$feeMaster->due:'-'  }}
                        </td>
                    </tr>
                    @php($dsn++)
            @endforeach
            @endif
            <tr style="font-size: 14px; font-weight: bold; /*background: #438eb9;color: white;*/border-top: 1px #346da5 solid;border-bottom: 2px #346da5 solid;">
                <td colspan="8" class="text-right"> Total</td>
                <td  class="text-right">{{ $student->master->sum('fee_amount') }}</td>
                <td  class="text-right">{{ $student->master->sum('discount') }}</td>
                <td  class="text-right">{{ $student->master->sum('fine') }}</td>
                <td  class="text-right">{{ $student->master->sum('paid_amount') }}</td>
                <td  class="text-right">{{ $student->master->sum('due') }}</td>
            </tr>

            @php($i++)
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-size: 14px;font-weight: bold; background: orangered;color: white;">
            <td colspan="12" class="text-right"> Grand Total &nbsp;&nbsp; </td>
            {{--<td  class="text-right">{{ $data['student']->sum('fee_amount') }}</td>--}}
            <td  class="text-right"> {{ $data['student']->sum('balance') }} </td>
        </tr>
    </tfoot>
    @else
        <tr>
            <td colspan="12">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
        </tr>
    @endif
</table>
