@extends('layouts.master')

@section('css')
    <style>
        /* Same CSS as before (student profile, payment section, etc.) */
        .payment-verification-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            margin-top: 20px;
            padding: 20px;
        }

        .section-title {
            background-color: #e9ecef;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 1rem;
            margin-top: 30px;
            border-radius: 5px;
        }

        .field-label {
            font-weight: 500;
            color: #495057;
            background-color: #f8f9fa;
            white-space: nowrap;
        }

        .verification-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .verification-actions {
                align-items: flex-start;
            }
        }

        .student-profile-container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .profile-picture-container {
            position: relative;
            width: 140px;
            text-align: center;
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
            margin-top: 10px;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 4px 10px;
            font-size: 11px;
            border-radius: 4px;
            font-weight: 600;
        }

        .academic-status {
            font-size: 11px;
            color: #6c757d;
        }

        .student-info-details {
            flex: 1;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .info-label {
            width: 130px;
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #2c3e50;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .transaction-details pre {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 0.9rem;
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        @include($view_path . '.includes.breadcrumb-primary')
                        <small><i class="ace-icon fa fa-angle-double-right"></i> View</small>
                    </h1>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        @include('account.includes.buttons')
                        @include('account.fees.includes.buttons')
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')

                        <div class="payment-verification-container">
                            @if (isset($data['student']) && $data['student']->count() > 0)
                                @foreach ($data['student'] as $student)
                                    {{-- ✅ Student Info Section --}}
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="student-profile-container">
                                                <div class="profile-picture-container">
                                                    <div class="profile-picture-wrapper">
                                                        @if ($student->student_image != '')
                                                            <img class="profile-picture" alt="Student Photo"
                                                                src="{{ asset('images/studentProfile/' . $student->student_image) }}" />
                                                        @else
                                                            <img class="profile-picture" alt="Student Photo"
                                                                src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" />
                                                        @endif
                                                    </div>
                                                    <div class="profile-status">
                                                        <span class="badge badge-success">ACTIVE</span>
                                                        <div class="academic-status">REGULAR</div>
                                                    </div>
                                                </div>

                                                <div class="student-info-details">
                                                    <div class="info-row">
                                                        <div class="info-label">Name:</div>
                                                        <div class="info-value">
                                                            <a
                                                                href="{{ route('student.view', ['id' => encrypt($student->id)]) }}">
                                                                {{ $student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-label">Reg. No:</div>
                                                        <div class="info-value">{{ $student->reg_no }}</div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-label">Faculty:</div>
                                                        <div class="info-value">
                                                            {{ ViewHelper::getFacultyTitle($student->faculty) }}</div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-label">Semester:</div>
                                                        <div class="info-value">
                                                            {{ ViewHelper::getSemesterTitle($student->semester) }}</div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-label">Father:</div>
                                                        <div class="info-value">
                                                            {{ $student->father_first_name . ' ' . $student->father_last_name }}
                                                        </div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-label">Contact:</div>
                                                        <div class="info-value">{{ $student->mobile_1 ?: 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="col-md-4 d-flex flex-column align-items-center justify-content-start mt-2">
                                            @if ($student->payment_status == 0)
                                                <div class="text-center mb-2">
                                                    <span class="badge badge-warning text-dark py-2 px-3"
                                                        style="font-size: 13px;">
                                                        <i class="fa fa-clock-o me-1"></i> Pending Verification
                                                    </span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary open-AddFeeDialog"
                                                    data-toggle="modal" data-target="#feeCollectionModal"
                                                    data-students-id="{{ encrypt($student->id) }}"
                                                    data-id="{{ encrypt($student->payment_id) }}"
                                                    data-date="{{ \Carbon\Carbon::parse($student->date)->format('Y-m-d') }}"
                                                    data-amount="{{ $student->amount }}"
                                                    data-gateway="{{ $student->payment_gateway }}">
                                                    <i class="fa fa-check-circle me-1"></i> Verify Now
                                                </button>
                                            @else
                                                <div class="text-center">
                                                    <span class="badge badge-success py-2 px-3" style="font-size: 13px;">
                                                        <i class="fa fa-check-circle me-1"></i> Verified on
                                                        {{ \Carbon\Carbon::parse($student->verified_at)->format('M d, Y h:i A') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                    </div>

                                    {{-- ✅ Payment Details Section --}}

                                    {{-- ✅ Payment Details Table --}}
                                    <div class="section-title">Payment Details</div>
                                    <table class="table table-bordered">
                                        <colgroup>
                                            <col style="width: 40%;">
                                            <col style="width: 60%;">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <td class="field-label">Payment Date</td>
                                                <td>{{ \Carbon\Carbon::parse($student->date)->format('F j, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="field-label">Amount</td>
                                                <td class="text-success fw-bold">{{ number_format($student->amount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="field-label">Payment Gateway</td>
                                                <td>{{ $student->payment_gateway }}</td>
                                            </tr>
                                            <tr>
                                                <td class="field-label">Processed By</td>
                                                <td>{{ ViewHelper::getUserNameId($student->paid_by) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="field-label">Reference No</td>
                                                <td><code>{{ $student->ref_no }}</code></td>
                                            </tr>
                                            <tr>
                                                <td class="field-label">Notes</td>
                                                <td>{{ $student->note ?: 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    {{-- ✅ Transaction Details --}}
                                    <div class="section-title">Transaction Details</div>
                                    @if ($student->ref_text)
                                        @php $refData = json_decode($student->ref_text, true); @endphp
                                        @if (is_array($refData))
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <colgroup>
                                                        <col style="width: 40%;">
                                                        <col style="width: 60%;">
                                                    </colgroup>
                                                    <tbody>
                                                        @foreach ($refData as $key => $value)
                                                            <tr>
                                                                <td class="field-label text-uppercase">
                                                                    {{ str_replace('_', ' ', $key) }}</td>
                                                                <td>
                                                                    @if (in_array($key, ['amount', 'store_amount', 'currency_amount']))
                                                                        {{ number_format($value, 2) }}
                                                                        {{ $refData['currency'] ?? 'BDT' }}
                                                                    @elseif($key === 'tran_date')
                                                                        {{ \Carbon\Carbon::parse($value)->format('M d, Y h:i A') }}
                                                                    @elseif(is_array($value) || is_object($value))
                                                                        <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                    @elseif($value === null)
                                                                        <span class="text-muted">N/A</span>
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="raw-data">
                                                <pre>{{ $student->ref_text }}</pre>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info">No transaction details available</div>
                                    @endif

                                    <hr />
                                @endforeach
                            @else
                                <div class="alert alert-warning text-center mb-0">No payment records found</div>
                            @endif
                        </div>

                        @include($view_path . '.includes.add_model')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.inputMask_script')
    @include($view_path . '.includes.modal_values_script')
@endsection
