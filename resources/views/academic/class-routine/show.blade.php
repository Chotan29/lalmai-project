@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Routine Details</h1>
        <a href="{{ route('routine.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $routine->department->department ?? '-' }}</dd>

                <dt class="col-sm-3">Faculty / Program</dt>
                <dd class="col-sm-9">{{ $routine->faculty->faculty ?? '-' }}</dd>

                <dt class="col-sm-3">Semester</dt>
                <dd class="col-sm-9">{{ $routine->semester->semester ?? '-' }}</dd>

                <dt class="col-sm-3">Batch</dt>
                <dd class="col-sm-9">{{ $routine->batch->title ?? '-' }}</dd>

                <dt class="col-sm-3">Subject</dt>
                <dd class="col-sm-9">{{ $routine->subject->title ?? '-' }}</dd>

                <dt class="col-sm-3">Teacher</dt>
                <dd class="col-sm-9">{{ $routine->teacher->first_name ?? '-' }}</dd>

                <dt class="col-sm-3">Day / Time</dt>
                <dd class="col-sm-9">{{ $routine->day_of_week }} — {{ date('h:i A', strtotime($routine->start_time)) }} to {{ date('h:i A', strtotime($routine->end_time)) }}</dd>

                <dt class="col-sm-3">Room</dt>
                <dd class="col-sm-9">{{ $routine->room_number }}</dd>

                <dt class="col-sm-3">Period</dt>
                <dd class="col-sm-9">{{ $routine->period ?? '-' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
