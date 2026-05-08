<div class="table-responsive">
    <table class="table table-hover table-bordered">
        <thead class="thead-light">
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 35%">Transaction Head</th>
                <th style="width: 20%">Date</th>
                <th style="width: 20%" class="text-right">Debit Amount</th>
                <th style="width: 20%" class="text-right">Credit Amount</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($data['recent_transaction']) && $data['recent_transaction']->count() > 0)
                @php($i=1)
                @foreach($data['recent_transaction'] as $recentTransaction)
                    <tr class="{{ $recentTransaction->dr_amount > 0 ? 'debit-row' : 'credit-row' }}">
                        <td>{{ $i }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exchange-alt mr-2 text-muted"></i>
                                <span>{{ $recentTransaction->tr_head }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light">
                                {{ \Carbon\Carbon::parse($recentTransaction->date)->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="text-right font-weight-bold text-danger">
                            {{ $recentTransaction->dr_amount > 0 ? number_format($recentTransaction->dr_amount, 2) : '-' }}
                        </td>
                        <td class="text-right font-weight-bold text-success">
                            {{ $recentTransaction->cr_amount > 0 ? number_format($recentTransaction->cr_amount, 2) : '-' }}
                        </td>
                    </tr>
                    @php($i++)
                @endforeach
                <tr>
                    <td colspan="5" class="text-center py-3">
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('account.transaction') }}">
                            <i class="fas fa-list mr-1"></i> View All Transactions
                        </a>
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-database fa-2x mb-2"></i><br>
                        No transactions found
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<style>
    .debit-row:hover {
        background-color: rgba(220, 53, 69, 0.05) !important;
    }
    .credit-row:hover {
        background-color: rgba(40, 167, 69, 0.05) !important;
    }
    .table thead th {
        border-top: none;
        border-bottom: 1px solid #e0e0e0;
    }
    .table td {
        vertical-align: middle;
    }
</style>