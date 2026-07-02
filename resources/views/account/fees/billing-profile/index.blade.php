@extends('layouts.master')

@section('css')
<style>
    .profile-card { border-left: 4px solid #4a90d9; }
    .scope-badge { font-size: 11px; }
    .cycle-badge { font-size: 11px; }
    .amount-cell { font-weight: 600; color: #2d6a9f; }
    .action-btns .btn { margin: 1px; }
    .status-dot { width: 10px; height: 10px; display: inline-block; border-radius: 50%; }
    .status-dot.active { background: #5cb85c; }
    .status-dot.inactive { background: #d9534f; }
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
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i> List
                    </small>
                </h1>
            </div>

            <div class="row">
                @include('account.includes.buttons')
                <div class="col-xs-12">
                    @include('account.fees.includes.buttons')
                    @include('includes.flash_messages')

                    <div class="widget-box">
                        <div class="widget-header">
                            <h5 class="widget-title">
                                <i class="fa fa-repeat"></i> Billing Profiles
                            </h5>
                            <div class="widget-toolbar">
                                @ability('super-admin', 'fees-billing-profile-add')
                                <a href="{{ route('account.fees.billing-profile.create') }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus"></i> Create Profile
                                </a>
                                @endability
                            </div>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main padding-8">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-condensed">
                                        <thead>
                                            <tr class="bg-primary">
                                                <th width="5%">#</th>
                                                <th>Profile Name</th>
                                                <th>Scope</th>
                                                <th>Cycle</th>
                                                <th>Fee Heads</th>
                                                <th>Total / Student</th>
                                                <th>Next Billing Day</th>
                                                <th>Fine</th>
                                                <th>SMS</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($billing_profiles as $profile)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $profile->profile_name }}</strong>
                                                    @if($profile->description)
                                                    <br><small class="text-muted">{{ Str::limit($profile->description, 60) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="label label-info scope-badge">{{ $profile->scope_label }}</span>
                                                </td>
                                                <td>
                                                    <span class="label label-default cycle-badge">{{ $profile->cycle_label }}</span>
                                                    @if($profile->billing_day)
                                                    <br><small class="text-muted">Day {{ $profile->billing_day }}</small>
                                                    @endif
                                                    @if($profile->one_time_date)
                                                    <br><small class="text-muted">{{ $profile->one_time_date->format('d M Y') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach($profile->profileItems as $item)
                                                        <span class="badge">{{ $item->fee_head_title }}</span>
                                                    @endforeach
                                                </td>
                                                <td class="amount-cell">
                                                    ৳ {{ number_format($profile->total_amount, 0) }}
                                                </td>
                                                <td>
                                                    @if($profile->billing_cycle === 'one_time')
                                                        <span class="text-muted">—</span>
                                                    @else
                                                        @if($profile->billing_months)
                                                            {{ implode(', ', array_map(fn($m) => date('M', mktime(0,0,0,$m,1)), $profile->billing_months)) }}
                                                            (Day {{ $profile->billing_day }})
                                                        @else
                                                            Every month — Day {{ $profile->billing_day }}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($profile->fine_type !== 'none')
                                                        <span class="text-danger">
                                                            {{ ucfirst(str_replace('_', ' ', $profile->fine_type)) }}:
                                                            ৳{{ number_format($profile->fine_amount, 0) }}
                                                            @if($profile->fine_grace_days) <br><small>({{ $profile->fine_grace_days }}d grace)</small>@endif
                                                        </span>
                                                    @else
                                                        <span class="text-muted">None</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($profile->sms_on_generation)
                                                        <i class="fa fa-check-circle text-success" title="SMS on generation enabled"></i>
                                                    @else
                                                        <i class="fa fa-times-circle text-muted" title="SMS disabled"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="status-dot {{ $profile->status ? 'active' : 'inactive' }}"></span>
                                                    {{ $profile->status ? 'Active' : 'Inactive' }}
                                                </td>
                                                <td class="action-btns">
                                                    {{-- EDIT --}}
                                                    <a href="{{ route('account.fees.billing-profile.edit', $profile->id) }}" class="btn btn-xs btn-info" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    {{-- VIEW RUNS --}}
                                                    <a href="{{ route('account.fees.billing-run') }}?profile_id={{ $profile->id }}" class="btn btn-xs btn-primary" title="View Runs">
                                                        <i class="fa fa-list"></i>
                                                    </a>
                                                    {{-- MANUAL RUN --}}
                                                    @if($profile->status)
                                                    <form method="POST" action="{{ route('account.fees.billing-profile.trigger', $profile->id) }}" style="display:inline" onsubmit="return confirm('Run billing for \'{{ addslashes($profile->profile_name) }}\' now?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-success" title="Run Now">
                                                            <i class="fa fa-play"></i> Run Now
                                                        </button>
                                                    </form>
                                                    @endif
                                                    {{-- TOGGLE STATUS --}}
                                                    @if($profile->status)
                                                    <a href="{{ route('account.fees.billing-profile.in-active', $profile->id) }}" class="btn btn-xs btn-warning" title="Deactivate" onclick="return confirm('Deactivate this profile?')">
                                                        <i class="fa fa-pause"></i>
                                                    </a>
                                                    @else
                                                    <a href="{{ route('account.fees.billing-profile.active', $profile->id) }}" class="btn btn-xs btn-success" title="Activate">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                    @endif
                                                    {{-- DELETE --}}
                                                    <a href="{{ route('account.fees.billing-profile.delete', $profile->id) }}" class="btn btn-xs btn-danger" title="Delete" onclick="return confirm('Delete this profile? This cannot be undone.')">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center text-muted">
                                                    <i class="fa fa-info-circle"></i> No billing profiles found.
                                                    <a href="{{ route('account.fees.billing-profile.create') }}">Create the first one</a>.
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $billing_profiles->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
