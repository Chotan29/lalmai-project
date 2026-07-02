<div class="widget-box accounting-widget" id="accounting-widget">
    <div class="widget-header">
        <div class="widget-header-content">
            <div class="widget-title-container">
                <i class="ace-icon fa fa-calculator"></i>
                <h4 class="widget-title">
                    Accounting Dashboard
                    <small class="widget-subtitle">Financial overview and quick actions</small>
                </h4>
            </div>
            <div class="widget-actions">
                <button class="btn btn-xs btn-light refresh-btn" title="Refresh Data">
                    <i class="ace-icon fa fa-refresh"></i>
                </button>
                <div class="accounting-summary">
                    <span class="summary-item" title="Today's Collections">
                        <i class="ace-icon fa fa-coins text-success"></i>
                        <span class="amount">${{ number_format($data['today_collection'] ?? 0, 2) }}</span>
                    </span>
                    <span class="summary-item" title="Pending Payments">
                        <i class="ace-icon fa fa-clock text-warning"></i>
                        <span class="amount">${{ number_format($data['pending_payments'] ?? 0, 2) }}</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="widget-toolbar no-border">
            <ul class="nav nav-pills nav-accounting">
                <li class="nav-item active">
                    <a class="nav-link active" data-toggle="tab" href="#fee-tab">
                        <i class="ace-icon fa fa-money-bill-wave"></i>
                        Fees Collection
                        <span class="badge badge-pill badge-light">{{ $data['recent_fees_collection']->count() ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#payroll-tab">
                        <i class="ace-icon fa fa-user-tie"></i>
                        Staff Payroll
                        <span class="badge badge-pill badge-light">{{ $data['recent_payroll_pay']->count() ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#transaction-tab">
                        <i class="ace-icon fa fa-exchange-alt"></i>
                        Transactions
                        <span class="badge badge-pill badge-light">{{ $data['recent_transaction']->count() ?? 0 }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="widget-body">
        <div class="widget-main no-padding">
            <div class="tab-content">
                <!-- Fees Tab -->
                <div id="fee-tab" class="tab-pane active">
                    @if(isset($data['recent_fees_collection']))
                        @include('dashboard.includes.account.fees')
                    @else
                        <div class="alert alert-warning">No fees collection data available</div>
                    @endif
                </div>
                
                <!-- Payroll Tab -->
                <div id="payroll-tab" class="tab-pane">
                    @if(isset($data['recent_payroll_pay']))
                        @include('dashboard.includes.account.payroll')
                    @else
                        <div class="alert alert-warning">No payroll data available</div>
                    @endif
                </div>
                
                <!-- Transactions Tab -->
                <div id="transaction-tab" class="tab-pane">
                    @if(isset($data['recent_transaction']))
                        @include('dashboard.includes.account.transaction')
                    @else
                        <div class="alert alert-warning">No transaction data available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Refresh button functionality
    $('.refresh-btn').click(function() {
        var activeTab = $('.nav-accounting li.active a');
        var target = activeTab.attr("href");
        
        // Show refresh animation
        $(this).find('i').addClass('fa-spin');
        
        // Reload the tab content
        $(target).load(window.location.href + ' ' + target, function() {
            $('.refresh-btn i').removeClass('fa-spin');
        });
    });
});
</script>