<div class="fee-table-container">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thin-border-bottom">
                <tr>
                    <th><i class="ace-icon fa fa-caret-right blue"></i>SN</th>
                    <th><i class="ace-icon fa fa-user blue"></i>Reg.No.</th>
                    <th><i class="ace-icon fa fa-tag blue"></i>Fees Title</th>
                    <th><i class="ace-icon fa fa-calendar blue"></i>Date</th>
                    <th><i class="ace-icon fa fa-money blue"></i>Amount</th>
                </tr>
            </thead>
            <tbody>
                @isset($data['recent_fees_collection'])
                    @forelse($data['recent_fees_collection'] as $i => $fee_collection)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <a class="text-primary" href="{{ route('account.fees.collection.view', ['id' => encrypt($fee_collection->students_id)]) }}">
                                    {{ $fee_collection->reg_no }}
                                </a>
                            </td>
                            <td>{{ $fee_collection->fee_head_title }}</td>
                            <td>{{ \Carbon\Carbon::parse($fee_collection->date)->format('M d, Y') }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($fee_collection->paid_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="ace-icon fa fa-info-circle"></i> No fee records found
                            </td>
                        </tr>
                    @endforelse
                    
                    @if($data['recent_fees_collection']->count() > 0)
                        <tr>
                            <td colspan="5" class="text-center">
                                <a class="btn btn-sm btn-success" href="{{ route('account.fees') }}">
                                    <i class="ace-icon fa fa-chevron-right"></i> View More
                                </a>
                            </td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="alert alert-warning m-0">
                                <i class="ace-icon fa fa-exclamation-triangle"></i> Fee data not loaded
                            </div>
                        </td>
                    </tr>
                @endisset
            </tbody>
        </table>
    </div>
</div>

<style>
.fee-table-container {
    margin-bottom: 20px;
}
.table-hover tbody tr:hover {
    background-color: #f5f5f5;
}
.text-primary {
    color: #428bca;
}
.text-success {
    color: #5cb85c;
}
.font-weight-bold {
    font-weight: bold;
}
</style>