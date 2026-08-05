<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Fees List</h4>
        <div class="clearfix">
            <a class="label label-primary label-lg white" href="{{ route('print-out.fees.student-ledger', ['id' => encrypt($data['student']->id)]) }}" target="_blank">
                Ledger
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>
            <a class="label label-warning label-lg white" href="{{ route('print-out.fees.student-due-detail', ['id' => encrypt($data['student']->id)]) }}" target="_blank">
                Due Detail Slip
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>

            <a class="label label-warning label-lg white" href="{{ route('print-out.fees.student-due', ['id' => encrypt($data['student']->id)]) }}" target="_blank">
                Total Balance
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>
            <a class="label label-success label-lg white" href="{{ route('print-out.fees.today-receipt-detail', ['id' => encrypt($data['student']->id)]) }}" target="_blank">
                Today Receipt Detail
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>
            <a class="label label-success label-lg white" href="{{ route('print-out.fees.today-receipt', ['id' => encrypt($data['student']->id)]) }}" target="_blank">
                Receipt
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>
            <a href="{{ route('account.fees.due.view', ['id' => encrypt($data['student']->id)]) }}" target="_blank" class="label label-primary label-lg white">
                <i class="ace-icon fa fa-calculator  align-top bigger-125 icon-on-right"></i>
                Collect Balance
            </a>
            <span class="hidden-print">
                <a class="btn-primary btn-sm" href="{{ route('account.fees.collection.view', ['id' => encrypt($data['student']->id)]) }}">
                     <i class="fa fa-calculator" aria-hidden="true"></i> View Ledger
                 </a>
            </span>

            <div class="hr hr-4 hr-dotted"></div>
            <div class="row text-uppercase">
                <div class="col-sm-5 pull-right align-right">
                    {{--<strong>Total Balance :</strong>{{$data['student']->balance}}/---}}
                    <label class="label label-info label-lg white">Total Balance : {{ number_format($data['student']->balance, 2) }}/-</label>
                </div>
                <div class="col-sm-7 pull-left">

                    <strong>Balance In Word:</strong> {{$data['student']->id}}only.
                </div>
            </div>
            <div class="hr hr-8 hr-dotted"></div>
        </div>
        <!-- div.table-responsive -->
        <form id="bulk_action_form" method="get" action="{{route('print-out.fees.selected-master-receipt')}}">
        <input type="hidden" name="studentId" value="{{ encrypt($data['student']->id) }}" class="ace" />
        <div class="table-responsive">
            <table id="dynamic-table-1" class="table table-striped table-bordered table-hover">
                <thead class="header">
                    <tr role="row">
                        <th>S.No.</th>
                        <th>Sem</th>
                        <th>Head</th>
                        <th>DueDate</th>
                        <th>Amount </th>
                        <th>Dis. </th>
                        <th>Fine </th>
                        <th>Paid </th>
                        <th>Due </th>
                        <th>{{ __('common.status')}}</th>
                        <th>
                            <a class="btn-primary btn-sm bulk-action-btn" target="_blank">
                                <i class="fa fa-print"></i> Print
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data['fee_rows']) && $data['fee_rows']->count() > 0)
                        @php($i=1)
                        {{-- One line per fee. A Main Fee Head is 26 rows in the accounts but a
                             single charge to the student, so it prints as one. --}}
                        @foreach($data['fee_rows'] as $feerow)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ ViewHelper::getSemesterById($feerow->semester) }}</td>
                                <td>
                                    {{ $feerow->label }}
                                    @if($feerow->is_group)
                                        <span class="label label-info" title="Charged as one fee, kept as {{ $feerow->head_count }} heads in the accounts">
                                            {{ $feerow->head_count }} heads
                                        </span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($feerow->due_date)->format('Y-m-d')}}</td>
                                <td>{{ $feerow->amount }}</td>
                                <td>{{ $feerow->discount?$feerow->discount:'-' }}</td>
                                <td>{{ $feerow->fine?$feerow->fine:'-' }}</td>
                                <td>{{ $feerow->paid?$feerow->paid:'-' }}</td>
                                <td>
                                    @php($net_balance = ($feerow->amount - ($feerow->paid + $feerow->discount)) + $feerow->fine)
                                    {{ $net_balance?$net_balance:'-' }}
                                </td>
                                <td align="left" class="text text-left">
                                    @if($net_balance == 0)
                                        <span class="label label-success">Paid</span>
                                    @elseif($net_balance < 0 )
                                        <span class="label label-warning">Negative</span>
                                    @elseif($net_balance < $feerow->amount)
                                        <span class="label label-info">Partial</span>
                                    @else
                                        <span class="label label-danger">Due</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- The visible box stands for every head inside this fee, so
                                         printing a package prints all of it, not just its first head. --}}
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ $feerow->ids[0] }}"
                                               class="ace fee-row-check"
                                               data-extra-ids="{{ implode(',', array_slice($feerow->ids, 1)) }}" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                            <tr style="font-size: 14px; background: orangered;color: white;">
                                <td colspan="4">Total</td>
                                <td>{{ $data['student']->fee_amount?$data['student']->fee_amount:'-' }}</td>
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
                                <td></td>
                            </tr>
                    @endif
                </tbody>
            </table>
        </div>
        </form>
    </div>
</div>

<script>
    /* A fee line stands for every head inside it. Ticking one has to send them all, or printing
       a package would print only its first head and the receipt would not match the charge. */
    $(document).on('change', '.fee-row-check', function () {
        var $box = $(this);
        var extra = ($box.data('extra-ids') || '').toString();

        $box.closest('td').find('input.fee-row-extra').remove();

        if (!$box.is(':checked') || extra === '') {
            return;
        }

        extra.split(',').forEach(function (id) {
            if (!id) return;
            $box.closest('td').append(
                $('<input>').attr({type: 'hidden', name: 'chkIds[]', value: id}).addClass('fee-row-extra')
            );
        });
    });
</script>