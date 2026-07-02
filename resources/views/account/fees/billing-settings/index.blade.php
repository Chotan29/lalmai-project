@extends('layouts.master')

@section('css')
<style>
    .setting-card  { border-left: 4px solid #2d6a9f; padding: 16px 20px; background: #f9fbff; border-radius: 4px; margin-bottom: 16px; }
    .time-picker-wrap select { display: inline-block; width: auto; font-size: 18px; font-weight: 700; padding: 6px 10px; }
    .audit-action  { font-size: 11px; }
    .audit-table td { font-size: 12px; vertical-align: middle; }
    .status-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 4px; }
    .dot-enabled  { background: #5cb85c; }
    .dot-disabled { background: #d9534f; }
</style>
@endsection

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')

            <div class="page-header">
                <h1>
                    Billing
                    <small><i class="ace-icon fa fa-angle-double-right"></i> Scheduler Settings & Audit Log</small>
                </h1>
            </div>

            <div class="row">
                @include('account.includes.buttons')
                <div class="col-xs-12">
                    @include('account.fees.includes.buttons')
                    @include('includes.flash_messages')

                    {{-- NAV BUTTONS --}}
                    <a href="{{ route('account.fees.billing-profile') }}" class="btn btn-sm btn-default" style="margin-bottom:10px">
                        <i class="fa fa-cogs"></i> Profiles
                    </a>
                    <a href="{{ route('account.fees.billing-run') }}" class="btn btn-sm btn-default" style="margin-bottom:10px">
                        <i class="fa fa-history"></i> Run History
                    </a>

                    {{-- SCHEDULER SETTINGS CARD --}}
                    <div class="widget-box">
                        <div class="widget-header">
                            <h5 class="widget-title"><i class="fa fa-clock-o"></i> Auto-Billing Scheduler</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main padding-16">

                                <div class="setting-card">
                                    <p class="text-muted" style="margin-bottom:12px">
                                        <i class="fa fa-info-circle"></i>
                                        The scheduler runs this command every day at the configured time:<br>
                                        <code>php artisan bill:generate-recurring</code><br>
                                        It checks all active billing profiles and generates bills for those whose <strong>billing day + billing month</strong> match today's date.
                                        <strong>Duplicate bills are always prevented</strong> automatically.
                                    </p>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p><strong>Current Scheduler Status:</strong><br>
                                                @if($setting->scheduler_enabled)
                                                    <span class="status-dot dot-enabled"></span> <strong class="text-success">Enabled</strong> — runs daily at <code>{{ $setting->scheduler_time }}</code>
                                                @else
                                                    <span class="status-dot dot-disabled"></span> <strong class="text-danger">Disabled</strong>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('account.fees.billing-settings.update') }}">
                                    @csrf

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Daily Run Time</label>
                                                <div class="time-picker-wrap">
                                                    <select name="scheduler_hour" class="form-control">
                                                        @for($h = 0; $h < 24; $h++)
                                                            <option value="{{ $h }}" {{ $setting->scheduler_hour == $h ? 'selected' : '' }}>
                                                                {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                    <strong style="font-size:20px; padding: 0 6px">:</strong>
                                                    <select name="scheduler_minute" class="form-control">
                                                        @foreach([0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55] as $m)
                                                            <option value="{{ $m }}" {{ $setting->scheduler_minute == $m ? 'selected' : '' }}>
                                                                {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted" style="margin-left:8px">{{ config('app.timezone', 'UTC') }} timezone</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Scheduler Status</label><br>
                                                <label class="checkbox inline" style="font-weight:normal">
                                                    <input type="checkbox" name="scheduler_enabled" value="1" {{ $setting->scheduler_enabled ? 'checked' : '' }}>
                                                    Enable automatic bill generation
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Save Scheduler Settings
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>

                    {{-- AUDIT LOG TABLE --}}
                    <div class="widget-box">
                        <div class="widget-header">
                            <h5 class="widget-title"><i class="fa fa-list-alt"></i> Full Audit Trail</h5>
                            <div class="widget-toolbar">
                                <small class="text-muted">All billing create / cancel / delete / approve actions</small>
                            </div>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main padding-8">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed audit-table">
                                        <thead>
                                            <tr class="bg-primary">
                                                <th width="5%">#</th>
                                                <th width="15%">Action</th>
                                                <th width="10%">Entity</th>
                                                <th width="8%">Run #</th>
                                                <th width="8%">Student</th>
                                                <th>Notes</th>
                                                <th width="10%">By</th>
                                                <th width="13%">At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($audit_logs as $log)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="audit-action">{!! $log->action_label !!}</td>
                                                <td>
                                                    <small class="text-muted">{{ $log->entity_type }}</small><br>
                                                    <small>#{{ $log->entity_id }}</small>
                                                </td>
                                                <td>
                                                    @if($log->billing_run_id)
                                                        <a href="{{ route('account.fees.billing-run.detail', $log->billing_run_id) }}" class="text-primary">
                                                            #{{ $log->billing_run_id }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($log->student_id)
                                                        <small>#{{ $log->student_id }}</small>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td style="max-width:260px; white-space:normal">
                                                    <small>{{ $log->notes }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ optional($log->performer)->name ?? '#' . $log->performed_by }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $log->created_at->format('d M Y H:i') }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    <i class="fa fa-info-circle"></i> No audit records yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $audit_logs->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
