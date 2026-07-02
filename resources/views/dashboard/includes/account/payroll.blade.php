<div class="payroll-table-container">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thin-border-bottom bg-light-blue">
                <tr>
                    <th width="50"><i class="ace-icon fa fa-hashtag blue"></i> SN</th>
                    <th><i class="ace-icon fa fa-id-card blue"></i> Staff ID</th>
                    <th><i class="ace-icon fa fa-tasks blue"></i> Payment For</th>
                    <th width="120"><i class="ace-icon fa fa-calendar blue"></i> Payment Date</th>
                    <th width="120"><i class="ace-icon fa fa-money-bill-wave blue"></i> Amount</th>
                </tr>
            </thead>
            <tbody>
                @isset($data['recent_payroll_pay'])
                    @forelse($data['recent_payroll_pay'] as $i => $payment)
                        <tr class="payroll-row">
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <a class="text-primary staff-link" href="{{ route('account.salary.payment.view', ['id' => encrypt($payment->staff_id)]) }}">
                                    <i class="ace-icon fa fa-user"></i> {{ $payment->reg_no }}
                                </a>
                            </td>
                            <td>{{ $payment->title }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}</td>
                            <td class="text-success font-weight-bold">
                                {{ number_format($payment->paid_amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="no-payments">
                                    <i class="ace-icon fa fa-info-circle fa-2x text-muted"></i>
                                    <p class="mt-2 mb-0">No payroll payments recorded</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    
                    @if(isset($data['recent_payroll_pay']) && $data['recent_payroll_pay']->count() > 0)
                        <tr>
                            <td colspan="5" class="text-center">
                                <a href="{{ route('account.payroll.balance') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ace-icon fa fa-chevron-right"></i> View Full Payroll Records
                                </a>
                            </td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="alert alert-warning m-0">
                                <i class="ace-icon fa fa-exclamation-triangle"></i> Payroll data not available
                            </div>
                        </td>
                    </tr>
                @endisset
            </tbody>
        </table>
    </div>
</div>

<style>
.payroll-table-container {
    margin-bottom: 20px;
    border-radius: 4px;
    overflow: hidden;
}
.bg-light-blue {
    background-color: #f0f7ff;
}
.payroll-row:hover {
    background-color: #f8f9fa !important;
}
.staff-link {
    color: #3a7bd5;
    font-weight: 500;
}
.staff-link:hover {
    color: #2c5fb3;
    text-decoration: none;
}
.text-success {
    color: #28a745 !important;
}
.no-payments {
    color: #6c757d;
    padding: 15px 0;
}
.btn-outline-primary {
    border-color: #3a7bd5;
    color: #3a7bd5;
}
.btn-outline-primary:hover {
    background-color: #3a7bd5;
    color: white;
}
</style>

<script>
$(document).ready(function() {
    // Add click handler for entire row
    $('.payroll-row').click(function(e) {
        // Don't trigger if clicking on links
        if(!$(e.target).is('a') && !$(e.target).parents('a').length) {
            window.location = $(this).find('a.staff-link').attr('href');
        }
    });
});
</script>