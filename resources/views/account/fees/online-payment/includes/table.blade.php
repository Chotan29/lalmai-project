<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i> {{$panel}} History </h4>
        <div class="clearfix">
            <span class="pull-right tableTools-container"></span>
        </div>
        {{--<div class="table-header">
            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.
        </div>--}}
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table-1" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th >S.N.</th>
                            <th>{{__('form_fields.student.fields.faculty')}}</th>
                            <th>{{__('form_fields.student.fields.semester')}}</th>
                            <th>Reg.Num.</th>
                            <th>Name</th>
                            <th>Balance</th>
                            <th>Date</th>
                            <th>REF No.</th>
                            <th>Amount</th>
                            <th>Gateway</th>
                            <th>Payment Status</th>
                            <th>Verify Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['student']) && $data['student']->count() > 0)
                        @php($i=1)
                        @foreach($data['student'] as $student)
                            <tr>
                                <td>{{ $i }}</td>
                                <td> {{  ViewHelper::getFacultyTitle( $student->faculty )}}</td>
                                <td> {{ ViewHelper::getSemesterTitle( $student->semester ) }}</td>
                                <td> <a href="{{ route('student.view', ['id' => $student->students_id]) }}">{{ $student->reg_no }}</a></td>
                                <td> <a href="{{ route('student.view', ['id' => $student->students_id]) }}">{{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                                <td class="text-right">{{ $student->balance }}</td>
                                <td>{{ $student->date }} </td>
                                {{-- <td>{{ \Carbon\Carbon::parse($student->date)->format('Y-m-d')}} </td> --}}
                                <td>{{ $student->ref_no }}</td>
                                <td class="text-right">{{ $student->amount }}</td>                                
                                <td>{{ $student->payment_gateway }}</td>
                                <td>{{ $student->payment_status }}</td>
                                <td>
                                    @if($student->verify_status == 0)
                                        <span class="label label-danger">Not Verify</span>
                                    @else
                                        <span class="label label-success">Verified</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-primary btn-sm action-buttons">
                                        <a class="white" href="{{ route('account.fees.online-payment.view', ['id' => encrypt($student->payment_id)]) }}">
                                            <i class="ace-icon fa fa-eye bigger-130" title="View"></i>&nbsp;View
                                        </a>
                                    </div>
                                </td>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="13">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                        </tr>
                    @endif
                    </tbody>
                    <tfoot>
                        <tr style="font-size: 14px; background: orangered;color: white;">
                            <td colspan="5" class="text-right">Total</td>
                            <td  class="text-right">{{ $data['student']->sum('amount') }}</td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                    </tfoot>
                </table>
        </div>
        {!! Form::close() !!}
    </div>
</div>


