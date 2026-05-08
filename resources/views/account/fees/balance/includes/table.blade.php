<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        {!! Form::open(['route' => 'info.smsemail.dueReminder', 'id' => 'send_reminder_message']) !!}
        <div class="clearfix">
            <span class="form-group due-reminder-submit">
                <span class="form-group due-reminder-submit">
                    <a class="btn-primary btn-sm generate-fee-voucher-btn" ><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;Generate Fee Voucher</a>
                </span>
                <label class="col-sm-1 control-label">Month</label>
                <div class="col-sm-2">
                    {!! Form::select('month', $data['months'], null, ['class' => 'form-control']) !!}
                </div>
                <a class="btn-primary btn-sm message-send-btn" ><i class="fa fa-envelope" aria-hidden="true"></i> Reminder SMS/Email Alert</a>
                <a class="btn-success btn-sm bulk-due-slip" >Bulk Due Detail Slip <i class="fa fa-print" aria-hidden="true"></i></a>
                <a class="btn-primary btn-sm short-due-slip" >Bulk Short Due Reminder Slip <i class="fa fa-print" aria-hidden="true"></i></a>


            </span>
            <span class="pull-right tableTools-container"></span>
        </div>
        {{--<div class="table-header">
            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.
        </div>--}}
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n')}}</th>
                        <th>{{__('form_fields.student.fields.faculty')}}</th>
                        <th>{{__('form_fields.student.fields.semester')}}</th>
                        <th>Reg. Num.</th>
                        <th>Name of Student</th>
                        <th>Father</th>
                        <th>Father Contact</th>
                        <th>Guardian</th>
                        <th>Guardian Contact</th>
                        <th>Total Fee</th>
                        <th>Balance</th>
                        <th></th>
                    </tr>
                </thead>
                @if (isset($data['student']) && $data['student']->count() > 0)

                <tbody>
                        @php($i=1)
                        @foreach($data['student'] as $student)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ $student->id }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i }}</td>
                                <td> {{  $student->faculty }}</td>
                                <td> {{  $student->semester }}</td>
                                <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}">{{ $student->reg_no }}</a></td>
                                <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}"> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                                <td>{{ $student->father_first_name.' '.$student->father_middle_name.' '. $student->father_last_name }}</td>
                                <td><a href="tel:{{ $student->father_mobile_1 }}">{{ $student->father_mobile_1 }}</a> </td>
                                <td>{{ $student->guardian_first_name.' '.$student->guardian_middle_name.' '. $student->guardian_last_name }}</td>
                                <td><a href="tel:{{ $student->guardian_mobile_1 }}">{{ $student->guardian_mobile_1 }}</a> </td>
                                <td>
                                    {{ $student->fee_amount }}
                                </td>
                                <td>
                                    {{ $student->balance }}
                                </td>
                                <td>
                                    <div class="btn-primary btn-sm action-buttons ">
                                        <a class="white" href="{{ route('account.fees.collection.view', ['id' => encrypt($student->id)]) }}">
                                            <i class="ace-icon fa fa-calculator bigger-130"></i>&nbsp;
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach

                </tbody>
                <tfoot>
                <tr style="border-top: 2px #346da5 solid;border-bottom: 3px #346da5 solid;font-weight: 600;">
                        <td colspan="10" class="text-right">Total</td>
                        <td  class="text-right">{{ $data['student']->sum('fee_amount') }}</td>
                        <td  class="text-right"> {{ $data['student']->sum('balance') }} </td>
                        <td class="hdidden-print"> </td>
                    </tr>
                </tfoot>
                @else
                    <tr>
                        <td colspan="10">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
            </table>
        </div>

        {!! Form::close() !!}
    </div>
</div>


