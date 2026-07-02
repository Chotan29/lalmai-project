@extends('layouts.master')
@section('title','Online Registration Students')

@section('content')
<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('setting.online-registration') }}">Registration Settings</a></li>
                    <li><span>Online Registration Students</span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(session()->has('message_success'))
                    <div class="alert alert-success">{{ session('message_success') }}</div>
                @endif
                @if(session()->has('message_warning'))
                    <div class="alert alert-warning">{{ session('message_warning') }}</div>
                @endif

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Online Students</h5>
                                <p class="card-text fs-3">{{ $data['total_online_students'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">New Students</h5>
                                <p class="card-text fs-3">{{ $data['new_students'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Old Students</h5>
                                <p class="card-text fs-3">{{ $data['old_students'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Active Students</h5>
                                <p class="card-text fs-3">{{ $data['active_students'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Search & Filter</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('setting.online-registration-student') }}" class="form-inline">
                            <div class="form-group mr-3 mb-2">
                                <input type="text" name="search" class="form-control" placeholder="Search by Reg #, Name, Email, Mobile" value="{{ request('search') }}">
                            </div>
                            <div class="form-group mr-3 mb-2">
                                <select name="student_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="new" {{ request('student_type') === 'new' ? 'selected' : '' }}>New Student</option>
                                    <option value="old" {{ request('student_type') === 'old' ? 'selected' : '' }}>Old Student</option>
                                </select>
                            </div>
                            <div class="form-group mr-3 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Search</button>
                            <a href="{{ route('setting.online-registration-student') }}" class="btn btn-secondary mb-2 ml-2">Reset</a>
                        </form>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Online Registration Students</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reg #</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['students'] as $student)
                                        <tr>
                                            <td><strong>{{ $student->reg_no }}</strong></td>
                                            <td>{{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->last_name ?? '' }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $student->mobile_1 }}</td>
                                            <td>
                                                @if($student->student_type === 'new')
                                                    <span class="badge badge-info">New</span>
                                                @else
                                                    <span class="badge badge-warning">Old</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($student->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($student->latest_payment)
                                                    @if($student->latest_payment->payment_status === 'completed')
                                                        <span class="badge badge-success">Paid</span>
                                                    @elseif($student->latest_payment->payment_status === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-danger">Failed</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Not Initiated</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('setting.online-registration-student.show', $student->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No students found matching your search criteria.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($data['students']->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $data['students']->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
