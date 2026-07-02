<table >
    <tr>
        <td>
            <div class="row">

                <div class="col-md-11 text-center">
                    {{--Anton|Archivo+Black|Josefin+Sans|Poppins|Russo+One|--}}
                    <h6 class="no-margin-top" style="font-size: 8px">
                        {{isset($generalSetting->salogan)?$generalSetting->salogan:""}}
                    </h6>
                    <h2 class="no-margin-top text-uppercase" style="font-family: 'Bowlby+One+SC'; font-size: 14px; font-weight: 600">
                        @if(isset($generalSetting->logo))
                            <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" width="40px">
                        @endif
                        <strong>{{isset($generalSetting->institute)?$generalSetting->institute:""}}</strong>
                    </h2>
                    <h5 class="no-margin-top" style="font-size: 12px;">
                        {{isset($generalSetting->address)?$generalSetting->address:""}}, {{isset($generalSetting->phone)?$generalSetting->phone:""}}
                    </h5>
                    <h5 class="no-margin-top" style="font-size: 10px;">
                        {{isset($generalSetting->email)?$generalSetting->email:""}}, {{isset($generalSetting->website)?$generalSetting->website:""}}
                    </h5>

                </div>
            </div>
        </td>

    </tr>
</table>

@php
    // Use first voucher row safely; outer $key does not map to $student->master index reliably.
    $voucherMaster = isset($student->master) && method_exists($student->master, 'first')
        ? $student->master->first()
        : null;
@endphp

<table class="table table-bordered">
    <tr>
        <td>Student Name:</td>
        <td>{{$student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name}}</td>
    </tr>
    <tr>
        <td>Father Name:</td>
        <td>{{$student->father_first_name . ' ' . $student->father_middle_name . ' ' . $student->father_last_name}}</td>
    </tr>
    <tr>
        <td>Faculty/Program/Class:</td>
        <td>{{ ViewHelper::getFacultyTitle($student->faculty) }}</td>
    </tr>
    <tr>
        <td>Sec./Group:</td>
        <td>{{ ViewHelper::getSemesterTitle($student->semester) }}</td>
    </tr>
    <tr>
        <td>Roll No:</td>
        <td>{{$student->university_reg}}</td>
    </tr>
    <tr>
        <td>Reg No:</td>
        <td>{{$student->reg_no}}</td>
    </tr>
    <tr>
        <td>Issue Date:</td>
        {{-- old: <td>{{$student->master[$key]->created_at}}</td> --}}
        <td>{{ isset($voucherMaster->created_at) ? $voucherMaster->created_at : '-' }}</td>
    </tr>
    <tr>
        <td>Fee Month:</td>
        {{-- old: <td>{{$student->master[$key]->fee_due_date}}</td> --}}
        <td>{{ isset($voucherMaster->fee_due_date) ? $voucherMaster->fee_due_date : '-' }}</td>
    </tr>
</table>

<table class="table table-bordered">
    <tr>
        <td colspan="3" style="font-size: 12px;font-weight: 600; text-align: center;">FEE CHALLAN</td>
    </tr>
    <tr>
        <th>SN</th>
        <th>DESCRIPTION</th>
        <th>AMOUNT</th>
    </tr>
    @php($i=1)
    {{-- old: @foreach($student->master as $feeMaster) --}}
    @foreach(collect(isset($student->master) ? $student->master : []) as $feeMaster)
        @if(isset($feeMaster->due) && $feeMaster->due >0)
            <tr>
                <td class="center">{{ $i }}</td>
                <td>
                    {{ ViewHelper::getFeeHeadById($feeMaster->fee_head) }}
                </td>
                <td class="text-right">
                    {{ $feeMaster->fee_amount?$feeMaster->fee_amount:'-' }}
                </td>
            </tr>
            @php($i++)
        @endif
    @endforeach
    @if($i === 1)
        <tr>
            <td colspan="3" class="text-center">No due fee items found for selected month.</td>
        </tr>
    @endif
    <tr style="font-weight: 600;">
        <td colspan="2">Total Payment By Date</td>
        <td>{{$student->balance}}</td>
    </tr>
    <tr>
        <td colspan="3" class="text-capitalize">{{ ViewHelper::convertNumberToWord($student->balance) }}only.</td>
    </tr>
</table>

<table class="table table-bordered">
    <tr>
        <td style="font-size: 12px;font-weight: 600; text-align: center;">PAYMENT TERMS:</td>
    </tr>
    <tr>
        <td>
            1.) Fee must be paid by 15th of the Month.<br>
            2.) Late Fee fine will be charged Rs.20/- per day.<br>
            3.) Arrears of the last month will be charged with Rs.300/- as fine.<br>
            4.) In case fee is not deposited two months, the name of the student will be struck off from the college rolls automatically. The student will have to get re-admission by paying the 50% of admission fee.
        </td>
    </tr>
</table>