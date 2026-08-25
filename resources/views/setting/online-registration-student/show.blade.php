@extends('layouts.master')
@section('title','Student Details - ' . $data['student']->first_name)

@section('content')
<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('setting.online-registration') }}">Registration Settings</a></li>
                    <li><a href="{{ route('setting.online-registration-student') }}">Online Students</a></li>
                    <li><span>{{ $data['student']->first_name }}</span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-content container-fluid">
        <div class="row">
            <!-- Student Info Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Student Information</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Registration #:</th>
                                <td><strong>{{ $data['student']->reg_no }}</strong></td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td>{{ $data['student']->first_name }} {{ $data['student']->middle_name ?? '' }} {{ $data['student']->last_name ?? '' }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $data['student']->email }}</td>
                            </tr>
                            <tr>
                                <th>Mobile:</th>
                                <td>{{ $data['student']->mobile_1 }}</td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td>
                                    @if($data['student']->student_type === 'new')
                                        <span class="badge badge-info">New Student</span>
                                    @else
                                        <span class="badge badge-warning">Old Student</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($data['student']->status == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Registration Date:</th>
                                <td>{{ $data['student']->reg_date ? date('M d, Y', strtotime($data['student']->reg_date)) : 'N/A' }}</td>
                            </tr>
                        </table>

                        <!-- Action Buttons -->
                        <div class="mt-3">
                            {{-- student.show does not exist and never has: the student routes name
                                 this one student.view. Asking for a route that is not defined does
                                 not fail quietly - it throws while the page is being built, so this
                                 whole screen answered 500 rather than showing anything. --}}
                            <a href="{{ route('student.view', $data['student']->id) }}" class="btn btn-sm btn-info btn-block" target="_blank">
                                <i class="fa fa-eye"></i> View Full Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Info Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Payment Information</h4>
                    </div>
                    <div class="card-body">
                        @if(count($data['payments']) > 0)
                            @foreach($data['payments'] as $payment)
                                <div class="mb-3 pb-3 border-bottom">
                                    <p class="mb-1"><strong>Amount:</strong> ৳ {{ number_format($payment->amount, 2) }}</p>
                                    <p class="mb-1"><strong>Gateway:</strong> {{ $payment->payment_gateway }}</p>
                                    <p class="mb-1"><strong>Date:</strong> {{ date('M d, Y H:i', strtotime($payment->date)) }}</p>
                                    <p class="mb-1">
                                        <strong>Status:</strong> 
                                        @if($payment->payment_status === 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($payment->payment_status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-danger">{{ $payment->payment_status }}</span>
                                        @endif
                                    </p>
                                    @if($payment->ref_no)
                                        <p class="mb-0"><strong>Ref:</strong> {{ $payment->ref_no }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No payment records yet. 
                                <br>Admin can initiate payment using the button on the right.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Admin Actions</h4>
                    </div>
                    <div class="card-body">
                        @if($data['payments'] && $data['payments']->where('payment_status', 'completed')->count() > 0)
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> Payment completed on {{ date('M d, Y', strtotime($data['payments']->where('payment_status', 'completed')->first()->date)) }}
                            </div>
                        @else
                            <button type="button" class="btn btn-primary btn-block" onclick="initiatePayment()">
                                <i class="fa fa-money"></i> Initiate Payment Collection
                            </button>
                            <p class="small text-muted mt-2">Click to start payment process for this student.</p>
                        @endif

                        <hr>

                        <a href="{{ route('online-registration.print', encrypt($data['student']->id)) }}" class="btn btn-secondary btn-block" target="_blank">
                            <i class="fa fa-print"></i> Print Registration Form
                        </a>

                        <a href="{{ route('online-registration.pdf', encrypt($data['student']->id)) }}" class="btn btn-secondary btn-block mt-2" target="_blank">
                            <i class="fa fa-file-pdf"></i> Download as PDF
                        </a>

                        <a href="{{ route('setting.online-registration-student') }}" class="btn btn-default btn-block mt-3">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                {{--
                    Paid, then did not take admission.

                    Two separate things, and it is easy to do only one of them. The student stops
                    being counted and comes off the attendance device; and, separately, the money
                    is recorded as having gone back. Nothing is deleted either way - the mark can
                    be lifted, and a refund is a new row beside the payment rather than a change
                    to it, so the month's collection still adds up afterwards.
                --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Admission not taken</h4>
                    </div>
                    <div class="card-body">

                        @if($data['student']->admission_cancelled_at)
                            <div class="alert alert-warning mb-2">
                                <strong>Marked as not admitted</strong><br>
                                {{ date('d M Y, g:i a', strtotime($data['student']->admission_cancelled_at)) }}
                                @if($data['student']->admission_cancel_note)
                                    <br><span class="text-muted">{{ $data['student']->admission_cancel_note }}</span>
                                @endif
                            </div>
                            <p class="small text-muted">
                                Not counted in student totals, and taken off the attendance device.
                            </p>

                            <form method="POST" action="{{ route('setting.online-registration-student.restore-admission', $data['student']->id) }}"
                                  onsubmit="return confirm('Put this student back into the counts and onto the device?');">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fa fa-undo"></i> Restore admission
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('setting.online-registration-student.cancel-admission', $data['student']->id) }}"
                                  onsubmit="return confirm('Mark this student as not admitted? Nothing is deleted and it can be undone.');">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="small">Reason</label>
                                    <input type="text" name="note" class="form-control" maxlength="255"
                                           placeholder="e.g. took admission elsewhere">
                                </div>
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fa fa-user-times"></i> Mark as not admitted
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Money in, money back, and what is left --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Refunds</h4>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="alert alert-info mb-0">
                                    <strong>Received</strong><br>
                                    ৳ {{ number_format($data['paid_total'], 2) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-secondary mb-0">
                                    <strong>Already returned</strong><br>
                                    ৳ {{ number_format($data['refunded_total'], 2) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success mb-0">
                                    <strong>Can still be returned</strong><br>
                                    ৳ {{ number_format($data['refundable'], 2) }}
                                </div>
                            </div>
                        </div>

                        @if(count($data['refunds']) > 0)
                            <div class="table-responsive mb-3">
                                <table class="table table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Voucher</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>How</th>
                                            <th>Reference</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['refunds'] as $refund)
                                            <tr @if($refund->status != 1) class="text-muted" @endif>
                                                <td>{{ $refund->voucher_no ?? '-' }}</td>
                                                <td>{{ date('d M Y', strtotime($refund->date)) }}</td>
                                                <td>৳ {{ number_format($refund->amount, 2) }}</td>
                                                <td>{{ $data['refund_methods'][$refund->method] ?? $refund->method }}</td>
                                                <td>{{ $refund->ref_no ?? '-' }}</td>
                                                <td>{{ $refund->note ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if($data['refundable'] > 0)
                            <form method="POST" action="{{ route('setting.online-registration-student.refund', $data['student']->id) }}"
                                  onsubmit="return confirm('Record this refund? The original payment is not changed.');">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="small">Amount</label>
                                        <input type="number" step="0.01" min="0.01" max="{{ $data['refundable'] }}"
                                               name="amount" class="form-control" value="{{ $data['refundable'] }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small">Date</label>
                                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small">How was it returned</label>
                                        <select name="method" class="form-control">
                                            @foreach($data['refund_methods'] as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small">Reference</label>
                                        <input type="text" name="ref_no" class="form-control" maxlength="100"
                                               placeholder="cheque / trx id">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small">Note</label>
                                        <input type="text" name="note" class="form-control" maxlength="255">
                                    </div>
                                </div>

                                @if(count($data['payments']) > 0)
                                    <div class="row mt-2">
                                        <div class="col-md-5">
                                            <label class="small">Against which payment</label>
                                            <select name="online_payment_id" class="form-control">
                                                <option value="">-- not tied to one payment --</option>
                                                @foreach($data['payments'] as $payment)
                                                    <option value="{{ $payment->id }}">
                                                        {{ $payment->invoice_id ?? ('#' . $payment->id) }}
                                                        · ৳ {{ number_format($payment->amount, 2) }}
                                                        · {{ date('d M Y', strtotime($payment->date)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <button type="submit" class="btn btn-danger mt-3">
                                    <i class="fa fa-reply"></i> Record refund
                                </button>
                                <span class="small text-muted ml-2">
                                    The payment record is left as it is. This is written beside it, so the
                                    month's collection still adds up.
                                </span>
                            </form>
                        @else
                            <div class="alert alert-secondary mb-0">
                                @if($data['paid_total'] <= 0)
                                    No money has been received from this student, so there is nothing to return.
                                @else
                                    Everything received has already been returned.
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Collection History -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Fee Collection History</h4>
                    </div>
                    <div class="card-body">
                        @if(count($data['fees']) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Payment Method</th>
                                            <th>Note</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['fees'] as $fee)
                                            <tr>
                                                <td>{{ date('M d, Y', strtotime($fee->created_at)) }}</td>
                                                <td>৳ {{ number_format($fee->paid_amount, 2) }}</td>
                                                <td>{{ $fee->payment_method ?? '-' }}</td>
                                                <td>{{ $fee->note ?? '-' }}</td>
                                                <td>{{ $fee->status ?? 'Active' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No fee collection records yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function initiatePayment() {
    if(confirm('Initiate payment collection for {{ $data['student']->first_name }}?')) {
        $.ajax({
            url: '{{ route("setting.online-registration-student.payment", $data['student']->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    alert(response.message + '\n\nRedirect to payment gateway...');
                    // Here you would redirect to payment gateway
                    // window.location.href = paymentGatewayUrl;
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    }
}
</script>
@endsection
