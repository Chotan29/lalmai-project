@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --info-color: #4895ef;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        .modern-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header-modern {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            border-bottom: none;
        }

        .chart-container {
    transition: all 0.3s ease;
}

.chart-container.loading .chart-fallback,
.chart-container.error .chart-fallback {
    display: block !important;
}

.chart-container.loading canvas,
.chart-container.error canvas {
    opacity: 0.3;
}

.chart-fallback {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 1;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

        .dashboard-widget {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            height: 100%;
        }

        .stat-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 10px;
            color: white;
            margin-bottom: 15px;
        }

        .stat-card i {
            font-size: 2rem;
            margin-right: 15px;
        }

        .stat-card .stat-info {
            flex-grow: 1;
        }

        .stat-card .stat-info h3 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .stat-card .stat-info p {
            margin: 0;
            opacity: 0.9;
        }

        .nav-pills-modern .nav-link {
            border-radius: 8px;
            margin-right: 5px;
            padding: 8px 15px;
            color: var(--dark-color);
            transition: all 0.3s;
        }

        .nav-pills-modern .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        @media (max-width: 768px) {
            .chart-container {
                padding: 10px;
            }

            .chart-wrapper {
                height: 250px;
            }
        }

        /* Loading indicator */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }

        .chart-container {
            position: relative;
            min-height: 300px;
        }

        .chart-container canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .chart-container.loading {
            opacity: 0.5;
            pointer-events: none;
        }

        /* Account specific styles */
        .financial-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .financial-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .financial-card i {
            font-size: 2.5rem;
            margin-right: 15px;
            opacity: 0.8;
        }

        .financial-card .content {
            flex-grow: 1;
        }

        .financial-card .content h3 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .financial-card .content p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .recent-transactions {
            margin-top: 20px;
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transaction-table th,
        .transaction-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .transaction-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .transaction-table tr:hover {
            background-color: #f8f9fa;
        }

        .badge-income {
            background-color: rgba(76, 201, 240, 0.1);
            color: #4cc9f0;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-expense {
            background-color: rgba(248, 150, 30, 0.1);
            color: #f8961e;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <!-- Modern Header Section -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="page-title">
                            <i class="fas fa-calculator"></i> Account Dashboard
                            <small>Financial Overview & Analytics</small>
                        </h1>
                        <div class="header-actions">
                            @include('includes.flash_messages')
                            {{-- @include('dashboard.includes.buttons') --}}
                        </div>
                    </div>
                </div><!-- /.page-header -->

                <!-- Loading overlay -->
                <div class="loading-overlay">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p class="mt-2">Loading financial data...</p>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="dashboardFilterForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="start_date">Start Date</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date"
                                                    value="{{ $data['filter']['start_date'] }}" max="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="end_date">End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date"
                                                    value="{{ $data['filter']['end_date'] }}" max="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="quick_range">Quick Range</label>
                                                <select class="form-control" id="quick_range" name="quick_range">
                                                    <option value="">Select Range</option>
                                                    <option value="today"
                                                        {{ isset($data['filter']['quick_range']) && $data['filter']['quick_range'] == 'today' ? 'selected' : '' }}>
                                                        Today</option>
                                                    <option value="week"
                                                        {{ isset($data['filter']['quick_range']) && $data['filter']['quick_range'] == 'week' ? 'selected' : '' }}>
                                                        This Week</option>
                                                    <option value="month"
                                                        {{ isset($data['filter']['quick_range']) && $data['filter']['quick_range'] == 'month' ? 'selected' : '' }}>
                                                        This Month</option>
                                                    <option value="quarter"
                                                        {{ isset($data['filter']['quick_range']) && $data['filter']['quick_range'] == 'quarter' ? 'selected' : '' }}>
                                                        This Quarter</option>
                                                    <option value="year"
                                                        {{ isset($data['filter']['quick_range']) && $data['filter']['quick_range'] == 'year' ? 'selected' : '' }}>
                                                        This Year</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="fas fa-filter"></i> Apply Filter
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="resetFilter">
                                                <i class="fas fa-sync-alt"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Content -->
                <div id="dashboardContent">
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
                    {{-- <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h5 class="mb-3"><i class="fas fa-chart-bar mr-2"></i>Monthly Financial Overview</h5>
                                <div class="chart-wrapper">
                                    <div id="feeSalaryChartContainer">
                                        {!! $data['feeSalaryChart']->container() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h5 class="mb-3"><i class="fas fa-chart-pie mr-2"></i>Income vs Expense</h5>
                                <div class="chart-wrapper">
                                    <div id="incomeVsExpenseChartContainer">
                                        {!! $data['incomeVsExpenseChart']->container() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h5 class="mb-3"><i class="fas fa-chart-pie mr-2"></i>Fee Collection Status</h5>
                                <div class="chart-wrapper">
                                    <div id="feeCompareContainer">
                                        {!! $data['feeCompare']->container() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h5 class="mb-3"><i class="fas fa-chart-line mr-2"></i>Transaction Trends</h5>
                                <div class="chart-wrapper">
                                    <div id="transactionChartContainer">
                                        {!! $data['transactionChart']->container() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <div class="row mb-4">
                        <div class="row mb-4">
    <!-- Monthly Financial Overview Chart -->
    <div class="col-md-6 mb-4">
        <div class="chart-container card shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0">
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas fa-chart-bar text-primary mr-2"></i>Monthly Financial Overview
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="chart-wrapper position-relative" style="height: 300px;">
                    <canvas id="feeSalaryChartContainer"></canvas>
                    <div class="chart-fallback text-center d-none" style="padding-top: 130px;">
                        <i class="fas fa-exclamation-circle fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Chart data loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Income vs Expense Chart -->
    <div class="col-md-6 mb-4">
        <div class="chart-container card shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0">
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas fa-chart-pie text-success mr-2"></i>Income vs Expense
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="chart-wrapper position-relative" style="height: 300px;">
                    <canvas id="incomeVsExpenseChartContainer"></canvas>
                    <div class="chart-fallback text-center d-none" style="padding-top: 130px;">
                        <i class="fas fa-exclamation-circle fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Chart data loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Collection Status Chart -->
    <div class="col-md-6 mb-4">
        <div class="chart-container card shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0">
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas fa-chart-pie text-info mr-2"></i>Fee Collection Status
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="chart-wrapper position-relative" style="height: 300px;">
                    <canvas id="feeCompareContainer"></canvas>
                    <div class="chart-fallback text-center d-none" style="padding-top: 130px;">
                        <i class="fas fa-exclamation-circle fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Chart data loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Trends Chart -->
    <div class="col-md-6 mb-4">
        <div class="chart-container card shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0">
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas fa-chart-line text-warning mr-2"></i>Transaction Trends
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="chart-wrapper position-relative" style="height: 300px;">
                    <canvas id="transactionChartContainer"></canvas>
                    <div class="chart-fallback text-center d-none" style="padding-top: 130px;">
                        <i class="fas fa-exclamation-circle fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Chart data loading...</p>
                    </div>
                </div>
            </div>
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
                                            @foreach ($data['recent_fees_collection'] as $collection)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($collection->date)->format('d M Y') }}
                                                    </td>
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
                                            @foreach ($data['recent_payroll_pay'] as $payment)
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
                                            @foreach ($data['recent_transaction'] as $transaction)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                                                    </td>
                                                    <td>{{ $transaction->tr_head }}</td>
                                                    <td>
                                                        @if ($transaction->dr_amount > 0)
                                                            <span class="badge-expense">-
                                                                {{ number_format($transaction->dr_amount, 2) }}</span>
                                                        @else
                                                            <span class="badge-income">+
                                                                {{ number_format($transaction->cr_amount, 2) }}</span>
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
                </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection
{{-- 
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
--}}
@section('js')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script>
       $(document).ready(function() {
    // Object to store all chart instances
    const chartInstances = {};
    let chartInitializationAttempts = 0;
    const MAX_INIT_ATTEMPTS = 3;

    // Initialize dashboard
    function initializeDashboard() {
        loadDashboardContent();
    }

    // Initialize all charts
    function initializeAllCharts() {
        console.groupCollapsed("Chart Initialization");
        chartInitializationAttempts++;
        
        const charts = [
            { container: 'feeSalaryChartContainer', name: 'feeSalaryChart' },
            { container: 'incomeVsExpenseChartContainer', name: 'incomeVsExpenseChart' },
            { container: 'feeCompareContainer', name: 'feeCompare' },
            { container: 'transactionChartContainer', name: 'transactionChart' }
        ];

        let successCount = 0;
        
        charts.forEach(chart => {
            if (initializeSingleChart(chart.container, chart.name)) {
                successCount++;
            }
        });

        console.log(`Successfully initialized ${successCount} of ${charts.length} charts`);
        
        if (successCount === charts.length) {
            console.log("All charts initialized successfully");
            hideLoadingState();
        } else if (chartInitializationAttempts < MAX_INIT_ATTEMPTS) {
            console.log(`Retrying initialization (attempt ${chartInitializationAttempts + 1} of ${MAX_INIT_ATTEMPTS})`);
            setTimeout(initializeAllCharts, 500 * chartInitializationAttempts);
        } else {
            console.error("Max initialization attempts reached");
            hideLoadingState();
            showAlert('warning', 'Some charts failed to load. Please refresh the page.');
        }
        
        console.groupEnd();
    }

    // Initialize single chart
    function initializeSingleChart(containerId, chartVarName) {
        try {
            // Check if chart configuration exists
            if (typeof window[chartVarName] === 'undefined') {
                console.warn(`${chartVarName} not found in global scope`);
                return false;
            }

            const container = document.getElementById(containerId);
            if (!container) {
                console.error(`Container ${containerId} not found`);
                return false;
            }

            const canvas = container.querySelector('canvas');
            if (!canvas) {
                console.error(`Canvas not found in ${containerId}`);
                return false;
            }

            // Destroy previous instance if exists
            if (chartInstances[chartVarName]) {
                chartInstances[chartVarName].destroy();
            }

            // Create new instance
            const ctx = canvas.getContext('2d');
            chartInstances[chartVarName] = new Chart(ctx, window[chartVarName]);
            
            console.log(`Initialized ${chartVarName} successfully`);
            return true;
        } catch (error) {
            console.error(`Error initializing ${chartVarName}:`, error);
            return false;
        }
    }

    // Load dashboard content
    function loadDashboardContent() {
        console.log("Loading dashboard content...");
        showLoadingState();
        chartInitializationAttempts = 0;

        const formData = $('#dashboardFilterForm').serialize();
        
        $.ajax({
            url: "{{ route('account.dashboard') }}",
            type: "GET",
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.groupCollapsed("Dashboard Response");
                
                // Update HTML
                if (response.html) {
                    $('#dashboardContent').html(response.html);
                }

                // Process chart scripts
                evaluateChartScripts(response);
                
                // Initialize charts after scripts are loaded
                setTimeout(initializeAllCharts, 300);
                
                console.groupEnd();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                hideLoadingState();
                showAlert('error', 'Failed to load dashboard data');
            }
        });
    }

    // Evaluate chart scripts from response
    function evaluateChartScripts(response) {
        console.log("Evaluating chart scripts...");
        
        // First remove any existing chart scripts
        removeChartScripts();
        
        // Then evaluate new ones
        const scripts = [
            { name: 'feeSalaryChart', content: response.feeSalaryChartScript },
            { name: 'incomeVsExpenseChart', content: response.incomeVsExpenseChartScript },
            { name: 'feeCompare', content: response.feeCompareScript },
            { name: 'transactionChart', content: response.transactionChartScript }
        ];

        scripts.forEach(script => {
            if (script.content) {
                try {
                    // Create a script element
                    const scriptEl = document.createElement('script');
                    scriptEl.type = 'text/javascript';
                    scriptEl.text = script.content;
                    
                    // Append to body (not head) to ensure execution
                    document.body.appendChild(scriptEl);
                    
                    console.log(`Evaluated script for ${script.name}`);
                } catch (error) {
                    console.error(`Error evaluating ${script.name}:`, error);
                }
            }
        });
    }

    // Remove existing chart scripts
    function removeChartScripts() {
        ['feeSalaryChart', 'incomeVsExpenseChart', 'feeCompare', 'transactionChart'].forEach(name => {
            // Clear from global scope
            delete window[name];
        });
    }

    // Show loading state
    function showLoadingState() {
        $('.loading-overlay').stop().fadeIn(200);
        $('.chart-container').addClass('loading');
    }

    // Hide loading state
    function hideLoadingState() {
        $('.loading-overlay').stop().fadeOut(200);
        $('.chart-container').removeClass('loading');
    }

    // Show alert message
    function showAlert(type, message) {
        const alert = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        `;
        $('#alert-container').html(alert).find('.alert').addClass('animated bounceIn');
    }

    // Event handlers
    $('#quick_range').change(function() {
        updateDateRange($(this).val());
        loadDashboardContent();
    });

    $('#resetFilter').click(function() {
        $('#start_date, #end_date, #quick_range').val('');
        loadDashboardContent();
    });

    $('#dashboardFilterForm').submit(function(e) {
        e.preventDefault();
        loadDashboardContent();
    });

    // Date range helper
    function updateDateRange(range) {
        let startDate = new Date();
        const endDate = new Date();

        switch(range) {
            case 'today': break;
            case 'week': startDate.setDate(startDate.getDate() - 7); break;
            case 'month': startDate.setMonth(startDate.getMonth() - 1); break;
            case 'quarter': startDate.setMonth(startDate.getMonth() - 3); break;
            case 'year': startDate.setFullYear(startDate.getFullYear() - 1); break;
            default: return;
        }

        $('#start_date').val(startDate.toISOString().split('T')[0]);
        $('#end_date').val(endDate.toISOString().split('T')[0]);
    }

    // Start the dashboard
    initializeDashboard();
});
    </script>
@endsection