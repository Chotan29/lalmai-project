@extends('user-student.layouts.master')

@section('css')
    <style>
        /* Compact Profile Section */
        /* Compact Profile Section */
        .compact-profile {
            padding: 20px 15px;
            border-radius: 10px;
        }

        .compact-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .compact-info {
            font-size: 13px;
            margin-top: 15px;
        }

        .compact-info-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .compact-info-item:last-child {
            border-bottom: none;
        }

        /* profile */
        .student-profile-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 15px;
            margin-bottom: 15px;
            height: 100%;
        }

        .profile-picture-container {
            position: relative;
            margin-bottom: 10px;
        }

        .profile-picture-wrapper {
            width: 120px;
            height: 120px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-status {
            text-align: center;
            margin-top: 8px;
        }

        .student-details-card {
            padding: 10px 0 0 0;
        }

        .student-header {
            margin-bottom: 12px;
            text-align: center;
        }

        .student-name {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .student-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            font-size: 13px;
            justify-content: center;
        }

        .reg-no,
        .univ-reg {
            color: #6c757d;
            display: inline-flex;
            align-items: center;
        }

        .reg-no i,
        .univ-reg i {
            margin-right: 4px;
            font-size: 14px;
        }

        .student-info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 13px;
            line-height: 1.4;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            display: inline-block;
            min-width: 60px;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .academic-status {
            font-size: 11px;
            color: #6c757d;
            margin-top: 3px;
        }

        @media (max-width: 768px) {
            .profile-picture-wrapper {
                width: 100px;
                height: 100px;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 5px;
            }

            .student-meta {
                flex-direction: column;
                align-items: center;
                gap: 5px;
            }

            .student-name {
                font-size: 18px;
            }
        }

        /* Payment Card Styles - Scoped to avoid conflicts */
        .payment-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .payment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .payment-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 15px 20px;
            position: relative;
        }

        .payment-card-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
        }

        .payment-card-icon {
            margin-right: 8px;
            font-size: 1rem;
        }

        .payment-badge {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.8rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .payment-progress-container {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .payment-progress-bar {
            height: 100%;
            background-color: white;
            border-radius: 0 4px 4px 0;
            transition: width 0.6s ease;
        }

        .payment-card-body {
            padding: 20px;
        }

        .payment-installment-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .payment-due-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .payment-due-info:last-child {
            margin-bottom: 0;
        }

        .payment-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }

        .payment-value {
            font-weight: 600;
            color: #343a40;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .payment-card-header {
                padding: 12px 15px;
            }

            .payment-card-title {
                font-size: 1rem;
            }

            .payment-badge {
                font-size: 0.7rem;
                padding: 4px 10px;
            }

            .payment-card-body {
                padding: 15px;
            }
        }

        /* Compact Installments */
        .compact-installment {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .installment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .installment-progress {
            height: 4px;
            margin: 5px 0;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .badge-sm {
            font-size: 12px;
            padding: 3px 6px;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                @if (session('message_success'))
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle mr-1"></i> {{ session('message_success') }}
                    </div>
                @endif

                @if (session('message_warning'))
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('message_warning') }}
                    </div>
                @endif

                @if (session('message_danger'))
                    <div class="alert alert-danger">
                        <i class="fa fa-times-circle mr-1"></i> {{ session('message_danger') }}
                    </div>
                @endif

                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="mb-0">
                            <i class="fas fa-money-bill-wave text-muted"></i> Fee Management
                        </h1>
                    </div>
                </div>
                @include('user-student.fees.includes.pay-online')

                @php
                    $singleFeeView = collect($data['current_installment_detail'] ?? [])->first();
                    $displayDueAmount = (float) ($data['student']->balance ?? 0);
                @endphp

                <div class="row">
                        <!-- Payment Section -->
                        <div class="col-md-4">
                                @if ($displayDueAmount > 0)
                                <div class="card payment-card">
                                    <div class="card-header payment-card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="payment-card-title mb-0">
                                                <i class="fas fa-credit-card payment-card-icon"></i>
                                                Pay Now
                                            </h5>
                                            <span class="badge payment-badge">
                                                <i class="fas fa-calendar-day mr-1"></i>
                                                {{ number_format($displayDueAmount, 2) }}
                                            </span>
                                        </div>
                                        <div class="payment-progress-container">
                                            <div class="payment-progress-bar" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <div class="card-body payment-card-body">
                                        <div class="payment-installment-info">
                                            <div class="payment-due-info">
                                                <span class="payment-label">Total Due:</span>
                                                <span class="payment-value">
                                                    {{ number_format($displayDueAmount, 2) }}
                                                </span>
                                            </div>
                                            <div class="payment-due-info">
                                                <span class="payment-label">Outstanding Balance:</span>
                                                <span class="payment-value">
                                                    {{ number_format($displayDueAmount, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- Pay Now Button - Moved inside the card -->
                                        <button class="payment-button" id="openPaymentModalStudent" data-open-payment-modal="1" type="button" onclick="if (window.openPaymentGatewayModal) { window.openPaymentGatewayModal(); }">
                                            <span>💳</span>
                                            <span>Pay Now</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        
                        </div>


                    <!-- Compact Installments Section -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header py-2">
                                <h5 class="mb-0"><i class="fas fa-calendar-alt mr-1"></i> Payment Summary</h5>
                            </div>
                            <div class="card-body p-0">
                                @if ($singleFeeView)
                                    @php
                                        $status = $singleFeeView['status'] ?? 'pending';
                                        $percentage =
                                            isset($singleFeeView['paid_amount'], $singleFeeView['initial_due_amount']) &&
                                            $singleFeeView['initial_due_amount'] > 0
                                                ? ($singleFeeView['paid_amount'] / $singleFeeView['initial_due_amount']) * 100
                                                : 0;
                                    @endphp
                                    <div class="compact-installment">
                                        <div class="installment-header">
                                            <strong>Current Due</strong>
                                            <span
                                                class="badge badge-sm badge-{{ $status == 'paid' ? 'success' : ($status == 'overdue' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </div>

                                        <div class="progress installment-progress">
                                            <div class="progress-bar bg-{{ $status == 'paid' ? 'success' : ($status == 'overdue' ? 'danger' : 'warning') }}"
                                                style="width: {{ $percentage }}%"></div>
                                        </div>

                                        <div class="amount-row">
                                            <span>
                                                <small class="text-muted">Due:</small>
                                                {{ isset($singleFeeView['due_date']) ? \Carbon\Carbon::parse($singleFeeView['due_date'])->format('M d') : '' }}
                                            </span>
                                            <span>
                                                <small class="text-muted">Amount:</small>
                                                {{ number_format($displayDueAmount, 2) }}
                                            </span>
                                            <span
                                                class="{{ ($data['student']->balance ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                                <small class="text-muted">Balance:</small>
                                                {{ number_format($data['student']->balance ?? 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>



                    <!-- Compact Profile Section -->
                    <div class="col-md-4">
                        <div class="student-profile-container">
                            <!-- Student Profile Picture Column -->
                            <div class="text-center">
                                <div class="profile-picture-container">
                                    <div class="profile-picture-wrapper">
                                        @if ($data['student']->student_image != '')
                                            <img class="profile-picture compact-avatar" alt="Student Photo"
                                                src="{{ asset('images/studentProfile/' . $data['student']->student_image) }}">
                                        @else
                                            <img class="profile-picture" alt="Student Photo"
                                                src="{{ asset('assets/images/avatars/profile-pic.jpg') }}">
                                        @endif
                                    </div>
                                    <div class="profile-status">
                                        @if ($data['student']->status == 'active')
                                            <span class="badge badge-success">
                                                ACTIVE
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
                                                IN-ACTIVE
                                            </span>
                                        @endif
                                        <div class="academic-status">
                                            @if ($data['student']->academic_status)
                                                {{ ViewHelper::getAcademicStatus($data['student']->academic_status) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Information Column -->
                            <div class="student-details-card">
                                <!-- Header Section -->
                                <div class="student-header">
                                    <h3 class="student-name">
                                        {{ $data['student']->first_name . ' ' . $data['student']->last_name }}
                                    </h3>
                                    <div class="student-meta">
                                        <span class="reg-no"><i class="fa fa-id-card"></i>
                                            {{ $data['student']->reg_no }}</span>
                                        @if ($data['student']->univ_reg)
                                            <span class="univ-reg"><i class="fa fa-university"></i>
                                                {{ $data['student']->reg_no }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Compact Information Grid -->
                                {{-- <div class="student-info-grid">
                                    <div class="info-row">
                                        <div><span class="info-label">Program:</span>
                                            {{ ViewHelper::getFacultyTitle($data['student']->faculty) }}</div>
                                        <div><span class="info-label">Semester:</span>
                                            {{ ViewHelper::getSemesterTitle($data['student']->semester) }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div><span class="info-label">Contact:</span>
                                            {{ $data['student']->mobile_1 ?: 'N/A' }}</div>
                                        <div><span class="info-label">Balance:</span>
                                            <span
                                                class="{{ $data['student']->balance > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($data['student']->balance, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <!-- Tabs Section -->
                <div class="card mt-12">
                    <div class="card mt-3">
                        <div class="tabbable">
                            <ul class="nav nav-tabs padding-12 hidden-print" id="feeTabs">
                                <li class="active">
                                    <a data-toggle="tab" href="#fees">
                                        <i class="green ace-icon fa fa-calculator"></i>
                                        Fees
                                    </a>
                                </li>

                                <li>
                                    <a data-toggle="tab" href="#pay-online">
                                        <i class="blue ace-icon fa fa-credit-card"></i>
                                        Online Payments
                                        @if (isset($data['onlinePayments']) && $data['onlinePayments']->where('status', 'in-active')->count() > 0)
                                            <span
                                                class="badge badge-warning">{{ $data['onlinePayments']->where('status', 'in-active')->count() }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content no-border">
                                <div id="fees" class="tab-pane in active">
                                    @include($view_path . '.fees.includes.table')
                                </div>

                                <div id="pay-online" class="tab-pane">
                                    @if ($data['student']->balance > 0)
                                        @include($view_path . '.fees.includes.online-payment-table')
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i> No outstanding balance for online
                                            payment.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('includes.scripts.dataTable_scripts')

    <script>
        $(document).ready(function() {

        });
    </script>
@endsection

{{-- @extends('user-student.layouts.master')

@section('css')
    <style>
        /* Modern Card Styles */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            padding: 15px 20px;
            border-radius: 10px 10px 0 0 !important;
        }

        /* Profile Section */
        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            width: 100%;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        /* Installment Timeline Styles */
        .installment-timeline {
            position: relative;
            padding-left: 40px;
            padding-right: 20px;
        }

        .timeline-item {
            padding: 15px 15px 15px 0;
            position: relative;
            border-bottom: 1px solid #f0f0f0;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-point {
            position: absolute;
            left: -30px;
            top: 20px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 50%;
            z-index: 2;
        }

        .timeline-point i {
            font-size: 18px;
        }

        .timeline-content {
            padding-left: 10px;
        }

        .due-date {
            font-size: 13px;
            font-weight: 500;
        }

        .amount-details {
            margin-top: 8px;
        }

        .payment-details {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 8px;
        }

        /* Status Colors */
        .paid .timeline-point {
            color: #28a745;
        }

        .overdue .timeline-point {
            color: #dc3545;
        }

        .pending .timeline-point {
            color: #ffc107;
        }

        /* Progress Bar Colors */
        .progress-bar-success {
            background-color: #28a745;
        }

        .progress-bar-danger {
            background-color: #dc3545;
        }

        .progress-bar-warning {
            background-color: #ffc107;
        }

        /* Tabs */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 20px;
        }

        .nav-tabs .nav-link.active {
            color: #3b7ddd;
            background: transparent;
            border-bottom: 2px solid #3b7ddd;
            margin-bottom: -2px;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 4px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: row;
                align-items: flex-start;
            }

            .profile-avatar {
                width: 80px;
                height: 80px;
                margin-right: 15px;
            }

            .profile-info {
                margin-top: 0;
            }

            .installment-timeline {
                padding-left: 30px;
            }

            .timeline-point {
                left: -20px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="mb-0">
                            <i class="fas fa-money-bill-wave text-muted"></i> Fee Management
                            <small class="text-muted">Payment details and transactions</small>
                        </h1>
                        @if ($data['student']->balance > 0)
                            <div class="alert alert-warning mb-0 py-2">
                                <strong>Outstanding Balance:</strong>
                                {{ number_format($data['student']->balance, 2) }}
                                ({{ ViewHelper::convertNumberToWord($data['student']->balance) }})
                            </div>
                        @endif
                    </div>
                </div><!-- /.page-header -->

                <div class="row">
                    <!-- Student Profile Section -->
                    <div class="col-md-4">
                        <div class="card profile-container">
                            @if ($data['student']->student_image != '')
                                <img class="profile-avatar"
                                    src="{{ asset('images/studentProfile/' . $data['student']->student_image) }}"
                                    alt="Student Photo">
                            @else
                                <img class="profile-avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}"
                                    alt="Student Photo">
                            @endif

                            <div class="profile-info">
                                <h4 class="text-center mb-3">
                                    {{ $data['student']->first_name . ' ' . $data['student']->middle_name . ' ' . $data['student']->last_name }}
                                </h4>

                                <div class="info-item">
                                    <span class="text-muted">Reg. No.</span>
                                    <span>{{ $data['student']->reg_no }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="text-muted">Faculty</span>
                                    <span>{{ ViewHelper::getFacultyTitle($data['student']->faculty) }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="text-muted">Semester</span>
                                    <span>{{ ViewHelper::getSemesterTitle($data['student']->semester) }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="text-muted">Contact</span>
                                    <span>{{ $data['student']->mobile_1 }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="text-muted">Email</span>
                                    <span>{{ $data['student']->email }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2"></i> Balance Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h3 class="{{ $data['student']->balance > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($data['student']->balance, 2) }}
                                    </h3>
                                    <p class="text-muted mb-0">
                                        {{ ViewHelper::convertNumberToWord($data['student']->balance) }} only
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Installments Section -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i> Payment Summary</h5>
                                <div class="installment-summary">
                                    @php
                                        // Calculate total paid if not provided
                                        $total_paid = $data['total_paid'] ?? 0;
                                        foreach ($data['current_installment_detail'] as $installment) {
                                            $total_paid += $installment['paid_amount'] ?? 0;
                                        }
                                    @endphp
                                    <span class="badge badge-success">Paid: {{ number_format($total_paid, 2) }}</span>
                                    <span class="badge badge-danger ml-2">Due:
                                        {{ number_format($displayDueAmount, 2) }}</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="installment-timeline">
                                    @if ($singleFeeView)
                                        @php
                                            $status = $singleFeeView['status'] ?? 'pending';
                                            $percentage =
                                                isset(
                                                    $singleFeeView['paid_amount'],
                                                    $singleFeeView['initial_due_amount'],
                                                ) && $singleFeeView['initial_due_amount'] > 0
                                                    ? ($singleFeeView['paid_amount'] /
                                                            $singleFeeView['initial_due_amount']) *
                                                        100
                                                    : 0;
                                        @endphp
                                        <div class="timeline-item {{ $status }}">
                                            <div class="timeline-point">
                                                @if ($status == 'paid')
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @elseif($status == 'overdue')
                                                    <i class="fas fa-exclamation-circle text-danger"></i>
                                                @else
                                                    <i class="far fa-clock text-warning"></i>
                                                @endif
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">Current Due</h6>
                                                    <span class="due-date {{ $status == 'overdue' ? 'text-danger' : '' }}">
                                                        {{ isset($singleFeeView['due_date']) ? \Carbon\Carbon::parse($singleFeeView['due_date'])->format('M d, Y') : '' }}
                                                    </span>
                                                </div>

                                                <div class="progress mt-2 mb-1" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $status == 'paid' ? 'success' : ($status == 'overdue' ? 'danger' : 'warning') }}"
                                                        role="progressbar" style="width: {{ $percentage }}%"
                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>

                                                <div class="amount-details">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Total Amount</small>
                                                            <div class="font-weight-bold">
                                                                {{ number_format($displayDueAmount, 2) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <small class="text-muted">Balance</small>
                                                            <div
                                                                class="font-weight-bold {{ ($data['student']->balance ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                                                {{ number_format($data['student']->balance ?? 0, 2) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if (($singleFeeView['paid_amount'] ?? 0) > 0 || ($singleFeeView['discount_amount'] ?? 0) > 0 || ($singleFeeView['fine'] ?? 0) > 0)
                                                    <div class="payment-details mt-2">
                                                        <div class="row text-center">
                                                            @if (($singleFeeView['paid_amount'] ?? 0) > 0)
                                                                <div class="col-4">
                                                                    <small class="text-muted">Paid</small>
                                                                    <div class="text-success">
                                                                        {{ number_format($singleFeeView['paid_amount'], 2) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if (($singleFeeView['discount_amount'] ?? 0) > 0)
                                                                <div class="col-4">
                                                                    <small class="text-muted">Discount</small>
                                                                    <div class="text-info">
                                                                        {{ number_format($singleFeeView['discount_amount'], 2) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if (($singleFeeView['fine'] ?? 0) > 0)
                                                                <div class="col-4">
                                                                    <small class="text-muted">Fine</small>
                                                                    <div class="text-danger">
                                                                        {{ number_format($singleFeeView['fine'], 2) }}</div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="col-md-4">
                        @if ($displayDueAmount > 0)
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-credit-card mr-2"></i> Make Payment </h5>
                                    <div class="installment-summary">                                    
                                    
                                    <span class="badge badge-danger ml-2">Total Due:
                                    {{ number_format($displayDueAmount, 2) }}
                                    </span>
                                </div>
                                </div>
                                <div class="card-body">
                                    @include($view_path . '.fees.includes.pay-online')
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tabs Section -->
                <div class="card mt-4">
    <div class="card-header p-0">
        <ul class="nav nav-tabs" id="feesTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="fees-tab" data-toggle="tab" href="#fees" role="tab" aria-controls="fees" aria-selected="true">
                    <i class="fas fa-receipt mr-1"></i> Fee Details
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="online-tab" data-toggle="tab" href="#pay-online" role="tab" aria-controls="pay-online" aria-selected="false">
                    <i class="fas fa-globe mr-1"></i> Online Payments
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="feesTabContent">
            <div class="tab-pane show active" id="fees" role="tabpanel" aria-labelledby="fees-tab">
                @include($view_path . '.fees.includes.table')
            </div>

            <div class="tab-pane show" id="pay-online" role="tabpanel" aria-labelledby="online-tab">
                @if ($data['student']->balance > 0)
                    @include($view_path . '.fees.includes.online-payment-table')
                @endif
            </div>
        </div>
    </div>
</div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    @include('includes.scripts.dataTable_scripts')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Add animation to timeline items
            $('.timeline-item').hover(
                function() {
                    $(this).css('transform', 'translateX(5px)');
                },
                function() {
                    $(this).css('transform', 'translateX(0)');
                }
            );
        });
    </script>
@endsection --}}
