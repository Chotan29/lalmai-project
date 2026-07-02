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
                            <a href="{{ route('student.show', $data['student']->id) }}" class="btn btn-sm btn-info btn-block" target="_blank">
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
