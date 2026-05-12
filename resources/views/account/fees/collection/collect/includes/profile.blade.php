

<style>
    .student-profile-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 15px;
        margin-bottom: 15px;
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

    .info-value {
        color: #2c3e50;
        display: inline;
    }

    .badge {
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
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

    /* Responsive adjustments */
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
            align-items: flex-start;
            gap: 5px;
        }

        .student-name {
            font-size: 18px;
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

<div class="row student-profile-container">
    @php
        $currentInstallmentAmount = (float)($data['current_unpaid_installment']['installmentAmount'] ?? 0);
        $installmentDetails = isset($data['current_installment_detail']) && is_iterable($data['current_installment_detail'])
            ? $data['current_installment_detail']
            : [];
    @endphp

    <!-- Student Profile Picture Column -->
    <div class="col-md-2 text-center">
        <div class="profile-picture-container">
            <div class="profile-picture-wrapper">
                @if ($data['student']->student_image != '')
                    <img id="avatar" class="profile-picture" alt="Student Photo"
                        src="{{ asset('images/studentProfile/' . $data['student']->student_image) }}" />
                @else
                    <img id="avatar" class="profile-picture" alt="Student Photo"
                        src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" />
                @endif
            </div>
            <div class="profile-status">
                <span class="badge badge-success">ACTIVE</span>
                <div class="academic-status">REGULAR</div>
            </div>
        </div>
    </div>

    <!-- Student Information Column -->
    <div class="col-xs-7">
        <div class="student-details-card">
            <!-- Header Section -->
            <div class="student-header">
                <h3 class="student-name">
                    <a href="{{ route('student.view', ['id' => encrypt($data['student']->id)]) }}">
                        {{ $data['student']->first_name . ' ' . $data['student']->middle_name . ' ' . $data['student']->last_name }}
                    </a>
                </h3>
                <div class="student-meta">
                    <span class="reg-no"><i class="fa fa-id-card"></i> {{ $data['student']->reg_no }}</span>
                    @if ($data['student']->univ_reg)
                        <span class="univ-reg"><i class="fa fa-university"></i> {{ $data['student']->univ_reg }}</span>
                    @endif
                </div>
            </div>

            <!-- Compact Information Grid -->
            <div class="student-info-grid">
                <div class="info-row">
                    <div><span class="info-label">Faculty:</span>
                        {{ ViewHelper::getFacultyTitle($data['student']->faculty) }}</div>
                    <div><span class="info-label">Semester:</span>
                        {{ ViewHelper::getSemesterTitle($data['student']->semester) }}</div>
                </div>
                <div class="info-row">
                    <div><span class="info-label">Father:</span>
                        {{ $data['student']->father_first_name . ' ' . $data['student']->father_last_name }}</div>
                    <div><span class="info-label">Contact:</span> {{ $data['student']->mobile_1 ?: 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div><span class="info-label">Current Due:</span>
                        @if ($currentInstallmentAmount > 0)
                            {{ number_format($currentInstallmentAmount, 2) }}
                            &nbsp;&nbsp;
                        @endif
                    </div>
                    <div><span class="info-label">Total Due:</span>
                        @if ($data['student']->balance > 0)
                            {{ number_format($data['student']->balance, 2) }}
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Compact Installments Section -->
    <div class="col-md-3">
        <div class="card">
            {{-- <div class="card-header py-2">
                <h5 class="mb-0"><i class="fas fa-calendar-alt mr-1"></i> Payment Schedule</h5>
            </div> --}}
            <div class="card-body p-0">
                @foreach ($installmentDetails as $installment)
                    @php
                        $status = $installment['status'] ?? 'pending';
                        $percentage =
                            isset($installment['paid_amount'], $installment['initial_due_amount']) &&
                            $installment['initial_due_amount'] > 0
                                ? ($installment['paid_amount'] / $installment['initial_due_amount']) * 100
                                : 0;
                        $title = count($installmentDetails) > 1 ? '#'.$installment['number'] : 'Due';
                    @endphp
                    <div class="compact-installment">
                        <div class="installment-header">
                            <strong>{{ $title }}</strong>
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
                                {{ isset($installment['due_date']) ? \Carbon\Carbon::parse($installment['due_date'])->format('M d') : '' }}
                            </span>
                            <span>
                                <small class="text-muted">Amount:</small>
                                {{ isset($installment['due_amount']) ? number_format($installment['due_amount'], 2) : (isset($installment['initial_due_amount']) ? number_format($installment['initial_due_amount'], 2) : '0.00') }}
                            </span>
                            <span class="{{ ($installment['due_amount'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                <small class="text-muted">Balance:</small>
                                {{ isset($installment['due_amount']) ? number_format($installment['due_amount'], 2) : '0.00' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>


