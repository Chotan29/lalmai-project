@php
    $isAjax = request()->ajax();
@endphp

@if(!$isAjax)
    @extends('layouts.master')
    @section('content')
@endif

<!-- Quick Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #4361ee, #3f37c9);">
            <i class="fas fa-users"></i>
            <div class="stat-info">
                <h3>{{ $data['studentIndicator'] }}</h3>
                <p>Total Students</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #4cc9f0, #4895ef);">
            <i class="fas fa-chalkboard-teacher"></i>
            <div class="stat-info">
                <h3>{{ $data['staffIndicator'] }}</h3>
                <p>Total Staff</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f8961e, #f3722c);">
            <i class="fas fa-money-bill-wave"></i>
            <div class="stat-info">
                <h3>${{ number_format($data['feeCollectionIndicator'], 2) }}</h3>
                <p>Total Fees</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f72585, #b5179e);">
            <i class="fas fa-file-invoice-dollar"></i>
            <div class="stat-info">
                <h3>${{ number_format($data['salaryPayIndicator'], 2) }}</h3>
                <p>Total Salaries</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
@role(['super-admin', 'admin', 'account'])
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-bar mr-2"></i>Monthly Financial Overview</h5>
                <div class="chart-wrapper" id="feeSalaryChartContainer">
                    {!! $data['feeSalaryChart']->container() !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-pie mr-2"></i>Fee Collection Status</h5>
                <div class="chart-wrapper" id="feeCompareContainer">
                    {!! $data['feeCompare']->container() !!}
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-line mr-2"></i>Transaction Trends</h5>
                <div class="chart-wrapper" id="transactionChartContainer">
                    {!! $data['transactionChart']->container() !!}
                </div>
            </div>
        </div>
    </div>
@endrole

<!-- Main Content Row -->
<div class="row">
    <div class="col-lg-9">
        @role(['super-admin', 'admin', 'account'])
            <div class="dashboard-widget">
                @include('dashboard.includes.account')
            </div>
        @endrole

        @role(['super-admin', 'admin', 'library'])
            <div class="dashboard-widget">
                @include('dashboard.includes.library')
            </div>
        @endrole

        @role(['super-admin', 'admin'])
            <div class="dashboard-widget">
                @include('dashboard.includes.attendance')
            </div>
        @endrole

        <div class="dashboard-widget">
            @include('dashboard.includes.birthday')
        </div>
    </div>

    <div class="col-lg-3">
        <div class="dashboard-widget">
            @include('dashboard.includes.summary')
        </div>
    </div>
</div>

@if(!$isAjax)
    @endsection
@endif