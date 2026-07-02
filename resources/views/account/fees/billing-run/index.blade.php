@extends('layouts.master')

@section('css')
<style>
    .run-status-badge { font-size: 11px; font-weight: 600; }
    .stats-pill { font-size: 11px; }
    .filter-bar { background: #f5f5f5; border: 1px solid #e5e5e5; border-radius: 4px; padding: 10px 14px; margin-bottom: 12px; }
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
                        <i class="ace-icon fa fa-angle-double-right"></i> Run History
                    </small>
                </h1>
            </div>

            <div class="row">
                @include('account.includes.buttons')
                <div class="col-xs-12">
                    @include('account.fees.includes.buttons')
                    @include('includes.flash_messages')

                    {{-- FILTER BAR --}}
                    <div class="filter-bar">
                        <form method="GET" action="{{ route('account.fees.billing-run') }}" class="form-inline">
                            <div class="form-group" style="margin-right:8px">
                                <label>Profile:</label>
                                <select name="profile_id" class="form-control input-sm" style="width:200px">
                                    <option value="">All Profiles</option>
                                    @foreach($profiles as $p)
                                    <option value="{{ $p->id }}" {{ request('profile_id')==$p->id ? 'selected':'' }}>{{ $p->profile_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-right:8px">
                                <label>Status:</label>
                                <select name="status" class="form-control input-sm">
                                    <option value="">All</option>
                                    <option value="completed"  {{ request('status')=='completed'  ? 'selected':'' }}>Completed</option>
                                    <option value="approved"   {{ request('status')=='approved'   ? 'selected':'' }}>Approved</option>
                                    <option value="partial"    {{ request('status')=='partial'    ? 'selected':'' }}>Partial</option>
                                    <option value="cancelled"  {{ request('status')=='cancelled'  ? 'selected':'' }}>Cancelled</option>
                                    <option value="failed"     {{ request('status')=='failed'     ? 'selected':'' }}>Failed</option>
                                    <option value="running"    {{ request('status')=='running'    ? 'selected':'' }}>Running</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            <a href="{{ route('account.fees.billing-run') }}" class="btn btn-sm btn-default"><i class="fa fa-times"></i> Clear</a>
                        </form>
                    </div>

                    <div class="widget-box">
                        <div class="widget-header">
                            <h5 class="widget-title"><i class="fa fa-history"></i> Billing Runs</h5>
                            <div class="widget-toolbar">
                                <a href="{{ route('account.fees.billing-profile') }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-cogs"></i> Profiles
                                </a>
                                @ability('super-admin','fees-billing-settings')
                                <a href="{{ route('account.fees.billing-settings') }}" class="btn btn-sm btn-default" title="Scheduler Settings &amp; Audit Log">
                                    <i class="fa fa-clock-o"></i> Settings
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
                                                <th width="4%">#</th>
                                                <th>Profile</th>
                                                <th>Period</th>
                                                <th>Run Date</th>
                                                <th>Due Date</th>
                                                <th>Bills</th>
                                                <th>Total Amount</th>
                                                <th>Triggered By</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($runs as $run)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($run->billingProfile)
                                                        <a href="{{ route('account.fees.billing-run') }}?profile_id={{ $run->billing_profile_id }}">
                                                            {{ $run->billingProfile->profile_name }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">[Deleted]</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $run->period_label }}</strong>
                                                    <br><small class="text-muted">{{ $run->period_key }}</small>
                                                </td>
                                                <td>{{ $run->run_date ? $run->run_date->format('d M Y') : '—' }}</td>
                                                <td>{{ $run->due_date ? $run->due_date->format('d M Y') : '—' }}</td>
                                                <td>
                                                    <span class="text-success" title="Created"><i class="fa fa-check"></i> {{ $run->bills_created }}</span>
                                                    @if($run->bills_skipped)
                                                    <span class="text-muted" title="Skipped"> / <i class="fa fa-forward"></i> {{ $run->bills_skipped }}</span>
                                                    @endif
                                                    @if($run->bills_failed)
                                                    <span class="text-danger" title="Failed"> / <i class="fa fa-times"></i> {{ $run->bills_failed }}</span>
                                                    @endif
                                                    <br><small class="text-muted">of {{ $run->total_students }} students</small>
                                                </td>
                                                <td>
                                                    <strong>৳ {{ number_format($run->total_amount, 0) }}</strong>
                                                </td>
                                                <td>{!! $run->triggered_by_badge !!}</td>
                                                <td>{!! $run->status_badge !!}</td>
                                                <td>
                                                    <a href="{{ route('account.fees.billing-run.detail', $run->id) }}" class="btn btn-xs btn-primary" title="View Details">
                                                        <i class="fa fa-list"></i> Details
                                                    </a>

                                                    @ability('super-admin','fees-billing-run-approve')
                                                    @if($run->isModifiable())
                                                    <form method="POST" action="{{ route('account.fees.billing-run.approve', $run->id) }}" style="display:inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-success" title="Approve Run" onclick="return confirm('Approve this billing run?')">
                                                            <i class="fa fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                    @endability

                                                    @ability('super-admin','fees-billing-run-cancel')
                                                    @if($run->isCancellable())
                                                    <button type="button" class="btn btn-xs btn-warning" title="Cancel Run"
                                                        onclick="cancelRun({{ $run->id }})">
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                    @endif
                                                    @endability

                                                    @ability('super-admin','fees-billing-run-delete')
                                                    @if($run->status !== 'running')
                                                    <form method="POST" action="{{ route('account.fees.billing-run.delete', $run->id) }}" style="display:inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-danger" title="Delete Run"
                                                            onclick="return confirm('DELETE this entire run and all its generated fees?\nThis cannot be undone if fees have no payments.')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                    @endability

                                                    @if($run->sms_queued && ($run->bills_created > 0))
                                                    <form method="POST" action="{{ route('account.fees.billing-run.resend-sms', $run->id) }}" style="display:inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-default" title="Re-send failed SMS" onclick="return confirm('Resend SMS for failed entries in this run?')">
                                                            <i class="fa fa-envelope"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">
                                                    <i class="fa fa-info-circle"></i> No billing runs found.
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $runs->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- CANCEL RUN MODAL --}}
<div class="modal fade" id="cancelRunModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="cancelRunForm">
                @csrf
                <div class="modal-header"><h4 class="modal-title"><i class="fa fa-ban"></i> Cancel Billing Run</h4></div>
                <div class="modal-body">
                    <p class="text-warning"><i class="fa fa-exclamation-triangle"></i>
                        All unpaid bills in this run will be deactivated. Bills with existing payments will be protected.
                    </p>
                    <div class="form-group">
                        <label>Reason (optional)</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Data entry error, wrong period..." maxlength="500">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning"><i class="fa fa-ban"></i> Cancel Run</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
function cancelRun(runId) {
    var form = document.getElementById('cancelRunForm');
    form.action = '/account/fees/billing-run/' + runId + '/cancel';
    $('#cancelRunModal').modal('show');
}
</script>
@endsection
