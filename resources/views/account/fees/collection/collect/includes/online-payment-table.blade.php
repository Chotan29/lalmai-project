<div class="col-xs-12">
    <div class="hr hr-8 hr-dotted"></div>
</div>
<div class="clearfix">
    {{-- <span class="pull-right tableTools-container"></span> --}}
</div>
<!-- div.table-responsive -->
<div class="table-responsive">
    <table {{-- id="dynamic-table-1" --}} class="table table-striped table-bordered table-hover">
        <thead class="header">
            <tr role="row">
                <th>S.No.</th>
                <th>Date</th>
                <th>REF No.</th>
                <th>Amount</th>
                <th>Gateway</th>
                <th>By</th>
                <th>Payment Status</th>
                <th>Verify Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>           
            @if (isset($data['onlinePayments']) && $data['onlinePayments']->count() > 0)
                @php($i = 1)
                @foreach ($data['onlinePayments'] as $payment)
                    <tr>
                        <td>{{ $i }}</td>
                        {{-- <td>{{ $payment->created_at}}</td> --}}
                        <td>{{ $payment->date }}</td>
                        <td> {{ $payment->ref_no }} </td>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->payment_gateway }}</td>
                        <td> {{ ViewHelper::getUserNameId($payment->created_by) }} </td>
                        <td> {{ $payment->payment_status }} </td>
                        <td class="text text-left">
                            @if ($payment->status == 'active')
                                <span class="label label-success">Verified</span>
                            @else
                                <span class="label label-danger">Not Verify</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-primary btn-sm action-buttons">
                                <a class="white" href="{{ route('account.fees.online-payment.view', ['id' => encrypt($payment->id)]) }}" target="_blank">
                                    <i class="ace-icon fa fa-eye bigger-130" title="View"></i>&nbsp;View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @php($i++)
                @endforeach

            @endif
        </tbody>
    </table>
</div>
