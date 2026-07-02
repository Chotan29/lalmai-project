<div class="col-xs-12">
    <div class="clearfix">
        <span class="pull-right tableTools-container"></span>
    </div>
    <!-- div.table-responsive -->
    <div class="table-responsive">
        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead class="header">
            <tr role="row">
                <th>S.No.</th>
                <th class="sorting_disabled">{{__('form_fields.student.fields.semester')}}</th>
                <th class="sorting_disabled">FeeHead</th>
                <th class="sorting_disabled">DueDate</th>
                <th class="sorting_disabled">Amount </th>
                <th class="sorting_disabled">Mode</th>
                <th class="sorting_disabled">Date</th>
                <th class="sorting_disabled">Di </th>
                <th class="sorting_disabled">Fi </th>
                <th class="sorting_disabled">Paid </th>
                <th class="sorting_disabled">Balance</th>
                <th class="sorting_disabled">Remark</th>
                {{--<th>PayOnline</th>--}}
            </tr>
            </thead>
            <tbody>
            @if (isset($data['fee_master']) && $data['fee_master']->count() > 0)
                @php($i=1)
                @foreach($data['fee_master'] as $feemaster)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ ViewHelper::getSemesterById($feemaster->semester) }}</td>
                        <td>{{ ViewHelper::getFeeHeadById($feemaster->fee_head) }}</td>
                        <td>{{ \Carbon\Carbon::parse($feemaster->fee_due_date)->format('Y-m-d')}}</td>
                        <td>{{ $feemaster->fee_amount }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $feemaster->feeCollect()->sum('discount')?$feemaster->feeCollect()->sum('discount'):"-" }}</td>
                        <td>{{ $feemaster->feeCollect()->sum('fine')?$feemaster->feeCollect()->sum('fine'):'-' }}</td>
                        <td>{{ $feemaster->feeCollect()->sum('paid_amount')?$feemaster->feeCollect()->sum('paid_amount'):'-' }}</td>
                        <td>
                            @php($net_balance = ($feemaster->fee_amount - ($feemaster->feeCollect()->sum('paid_amount') + $feemaster->feeCollect()->sum('discount')))+ $feemaster->feeCollect()->sum('fine'))
                            {{ $net_balance?$net_balance:'-' }}
                        </td>
                        <td>
                            @if($net_balance == 0)
                                <span class="label label-success">Paid</span>
                            @elseif($net_balance < 0 )
                                <span class="label label-warning">Negative</span>
                            @elseif($net_balance < $feemaster->fee_amount)
                                <span class="label label-warning">Partial</span>
                            @else
                                <span class="label label-danger">Due</span>
                            @endif
                        </td>
                        {{--<td>
                            <a class="btn btn-xs btn-primary" href="{{ route('print-out.fees.master-receipt', ['id' => $feemaster->id]) }}" target="_blank">
                                <i class="fa fa-print"></i> Print
                            </a>
                            @if($net_balance > 0 && is_int($net_balance))
                                @include('account.fees.payment.online-payment')
                            @endif
                        </td>--}}
                    </tr>
                    @if (isset($data['fee_collection']) && $data['fee_collection']->count() > 0)
                        @php($i=1)
                        @foreach($data['fee_collection'] as $fee_collection)
                            @if($fee_collection->fee_masters_id == $feemaster->id)

                                <tr class="white-td even" role="row" >
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="align-right"><i class="fa fa-arrow-right"></i></td>
                                    <td>{{ $fee_collection->payment_method }}</td>
                                    <td> {{ \Carbon\Carbon::parse($fee_collection->date)->format('Y-m-d')}}</td>
                                    <td>{{ $fee_collection->discount?$fee_collection->discount:'-' }}</td>
                                    <td>{{ $fee_collection->fine?$fee_collection->fine:'-' }}</td>
                                    <td>{{ $fee_collection->paid_amount?$fee_collection->paid_amount:'-' }}</td>
                                    <td></td>
                                    <td>{{ ViewHelper::getUserNameId($fee_collection->created_by) }}</td>
                                </tr>
                                @php($i++)
                            @endif
                        @endforeach
                    @endif
                    @php($i++)
                @endforeach

            @endif
            </tbody>
            <tfoot>
            <tr style="font-size: 14px; background: orangered;color: white;">
                <td colspan="4">Total</td>
                <td>{{ $data['student']->fee_amount?$data['student']->fee_amount:'-' }}</td>
                <td></td>
                <td></td>
                <td>{{ $data['student']->discount?$data['student']->discount:'-' }}</td>
                <td>{{ $data['student']->fine?$data['student']->fine:'-' }}</td>
                <td>{{ $data['student']->paid_amount?$data['student']->paid_amount:'-' }}</td>
                <td>
                    {{ $data['student']->balance?$data['student']->balance:'-' }}
                </td>
                <td>
                    @if($data['student']->balance == 0)
                        <span class="label label-success">Paid</span>
                    @elseif($data['student']->balance < 0 )
                        <span class="label label-warning">Negative</span>
                    @elseif($data['student']->balance < $data['student']->fee_amount)
                        <span class="label label-warning">Partial</span>
                    @else
                        <span class="label label-danger">Due</span>
                    @endif
                </td>
                {{--<td></td>--}}
            </tr>
            </tfoot>
        </table>
    </div>
</div>
