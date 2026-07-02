<!-- Financial Summary Cards -->
<div class="financial-summary">
    <div class="financial-card" style="background: linear-gradient(135deg, #4361ee, #3f37c9);">
        <i class="fas fa-hand-holding-usd"></i>
        <div class="content">
            <h3>{{ number_format($data['feeCollectionIndicator'], 2) }}</h3>
            <p>Total Fee Collection</p>
        </div>
    </div>
    <div class="financial-card" style="background: linear-gradient(135deg, #f72585, #b5179e);">
        <i class="fas fa-money-bill-wave"></i>
        <div class="content">
            <h3>{{ number_format($data['salaryPayIndicator'], 2) }}</h3>
            <p>Total Salary Paid</p>
        </div>
    </div>
    <div class="financial-card" style="background: linear-gradient(135deg, #4cc9f0, #4895ef);">
        <i class="fas fa-arrow-down"></i>
        <div class="content">
            <h3>{{ number_format($data['totalIncome'], 2) }}</h3>
            <p>Total Income</p>
        </div>
    </div>
    <div class="financial-card" style="background: linear-gradient(135deg, #f8961e, #f3722c);">
        <i class="fas fa-arrow-up"></i>
        <div class="content">
            <h3>{{ number_format($data['totalExpense'], 2) }}</h3>
            <p>Total Expense</p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="mb-3"><i class="fas fa-chart-bar mr-2"></i>Monthly Financial Overview</h5>
            <div class="chart-wrapper" id="feeSalaryChartContainer">
                {!! $data['feeSalaryChart']->container() !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="mb-3"><i class="fas fa-chart-pie mr-2"></i>Income vs Expense</h5>
            <div class="chart-wrapper" id="incomeVsExpenseChartContainer">
                {!! $data['incomeVsExpenseChart']->container() !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="mb-3"><i class="fas fa-chart-pie mr-2"></i>Fee Collection Status</h5>
            <div class="chart-wrapper" id="feeCompareContainer">
                {!! $data['feeCompare']->container() !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="mb-3"><i class="fas fa-chart-line mr-2"></i>Transaction Trends</h5>
            <div class="chart-wrapper" id="transactionChartContainer">
                {!! $data['transactionChart']->container() !!}
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Section -->
<div class="row">
    <div class="col-md-4">
        <div class="dashboard-widget">
            <h5><i class="fas fa-file-invoice-dollar mr-2"></i>Recent Fee Collections</h5>
            <div class="recent-transactions">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['recent_fees_collection'] as $collection)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($collection->date)->format('d M Y') }}</td>
                            <td>{{ $collection->reg_no }}</td>
                            <td>{{ number_format($collection->paid_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dashboard-widget">
            <h5><i class="fas fa-money-check-alt mr-2"></i>Recent Salary Payments</h5>
            <div class="recent-transactions">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Staff</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['recent_payroll_pay'] as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</td>
                            <td>{{ $payment->reg_no }}</td>
                            <td>{{ number_format($payment->paid_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dashboard-widget">
            <h5><i class="fas fa-exchange-alt mr-2"></i>Recent Transactions</h5>
            <div class="recent-transactions">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Head</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['recent_transaction'] as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
                            <td>{{ $transaction->tr_head }}</td>
                            <td>
                                @if($transaction->dr_amount > 0)
                                    <span class="badge-expense">- {{ number_format($transaction->dr_amount, 2) }}</span>
                                @else
                                    <span class="badge-income">+ {{ number_format($transaction->cr_amount, 2) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>