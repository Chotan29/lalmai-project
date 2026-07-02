@extends('layouts.master')

@section('css')
<style>
    .stat-card { border-radius: 6px; padding: 14px 18px; color: #fff; margin-bottom: 12px; }
    .stat-card.created  { background: #5cb85c; }
    .stat-card.skipped  { background: #9e9e9e; }
    .stat-card.failed   { background: #d9534f; }
    .stat-card.amount   { background: #2d6a9f; }
    .stat-card h4 { margin: 0 0 4px; font-size: 24px; font-weight: 700; }
    .stat-card p  { margin: 0; font-size: 12px; opacity: 0.9; }
    .run-info-table td { padding: 5px 10px; vertical-align: top; }
    .run-info-table td:first-child { color: #777; width: 160px; }
    .student-name { font-weight: 600; }
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
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Run #{{ $run->id }} — {{ $run->period_label }}
                    </small>
                </h1>
            </div>

            <div class="row">
                @include('account.includes.buttons')
                <div class="col-xs-12">
                    @include('account.fees.includes.buttons')
                    @include('includes.flash_messages')

                    {{-- BACK BUTTON --}}
                    <a href="{{ route('account.fees.billing-run') }}" class="btn btn-sm btn-default" style="margin-bottom:10px">
                        <i class="fa fa-arrow-left"></i> Back to Runs
                    </a>

                    {{-- STATS ROW --}}
                    <div class="row">
                        <div class="col-xs-6 col-sm-3">
                            <div class="stat-card created">
                                <h4>{{ $run->bills_created }}</h4>
                                <p>Bills Created</p>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <div class="stat-card skipped">
                                <h4>{{ $run->bills_skipped }}</h4>
                                <p>Skipped</p>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <div class="stat-card failed">
                                <h4>{{ $run->bills_failed }}</h4>
                                <p>Failed</p>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <div class="stat-card amount">
                                <h4>৳{{ number_format($run->total_amount, 0) }}</h4>
                                <p>Total Billed</p>
                            </div>
                        </div>
                    </div>

                    {{-- RUN INFO --}}
                    <div class="widget-box">
                        <div class="widget-header">
                            <h5 class="widget-title"><i class="fa fa-info-circle"></i> Run Information</h5>
                            <div class="widget-toolbar">
                                {!! $run->status_badge !!}
                                &nbsp;
                                {!! $run->triggered_by_badge !!}

                                @ability('super-admin','fees-billing-run-approve')
                                @if($run->isModifiable())
                                <form method="POST" action="{{ route('account.fees.billing-run.approve', $run->id) }}" style="display:inline; margin-left:4px">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success" onclick="return confirm('Approve this billing run?')">
                                        <i class="fa fa-check-circle"></i> Approve
                                    </button>
                                </form>
                                @endif
                                @endability

                                @ability('super-admin','fees-billing-run-cancel')
                                @if($run->isCancellable())
                                <button type="button" class="btn btn-xs btn-warning" style="margin-left:4px"
                                    onclick="cancelRun({{ $run->id }})">
                                    <i class="fa fa-ban"></i> Cancel Run
                                </button>
                                @endif
                                @endability

                                @ability('super-admin','fees-billing-run-delete')
                                @if($run->status !== 'running')
                                <form method="POST" action="{{ route('account.fees.billing-run.delete', $run->id) }}" style="display:inline; margin-left:4px">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-danger"
                                        onclick="return confirm('DELETE this entire run and all its fees?\nBills with payments will be protected.')">
                                        <i class="fa fa-trash"></i> Delete Run
                                    </button>
                                </form>
                                @endif
                                @endability

                                @if($run->bills_created > 0)
                                <form method="POST" action="{{ route('account.fees.billing-run.resend-sms', $run->id) }}" style="display:inline; margin-left:4px">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-default" onclick="return confirm('Resend SMS for failed entries?')">
                                        <i class="fa fa-envelope"></i> Resend SMS
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main padding-12">
                                <table class="run-info-table">
                                    <tr>
                                        <td>Profile</td>
                                        <td>
                                            @if($run->billingProfile)
                                            <a href="{{ route('account.fees.billing-profile.edit', $run->billing_profile_id) }}">
                                                {{ $run->billingProfile->profile_name }}
                                            </a>
                                            @else
                                            <span class="text-muted">[Deleted]</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr><td>Period</td><td><strong>{{ $run->period_label }}</strong> <small class="text-muted">({{ $run->period_key }})</small></td></tr>
                                    <tr><td>Run Date</td><td>{{ $run->run_date ? $run->run_date->format('d M Y') : '—' }}</td></tr>
                                    <tr><td>Due Date</td><td>{{ $run->due_date ? $run->due_date->format('d M Y') : '—' }}</td></tr>
                                    <tr><td>Total Students</td><td>{{ $run->total_students }}</td></tr>
                                    <tr><td>Success Rate</td><td>{{ $run->success_rate }}%</td></tr>
                                    <tr><td>SMS Queued</td><td>{{ $run->sms_queued }}</td></tr>
                                    <tr><td>Started At</td><td>{{ $run->started_at ? $run->started_at->format('d M Y H:i:s') : '—' }}</td></tr>
                                    <tr><td>Finished At</td><td>{{ $run->finished_at ? $run->finished_at->format('d M Y H:i:s') : '—' }}</td></tr>
                                    @if($run->approved_at)
                                    <tr><td>Approved At</td><td class="text-success">{{ $run->approved_at->format('d M Y H:i:s') }}</td></tr>
                                    @endif
                                    @if($run->cancelled_at)
                                    <tr><td>Cancelled At</td><td class="text-danger">{{ $run->cancelled_at->format('d M Y H:i:s') }}</td></tr>
                                    <tr><td>Cancel Reason</td><td class="text-danger">{{ $run->cancel_reason }}</td></tr>
                                    @endif
                                    @if($run->error_log)
                                    <tr>
                                        <td>Error Log</td>
                                        <td><pre class="small text-danger" style="max-height:100px;overflow:auto">{{ $run->error_log }}</pre></td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- DETAIL TABLE with BULK ACTION --}}
                    <form method="POST" action="{{ route('account.fees.billing-run.bulk-action', $run->id) }}" id="bulkForm">
                    @csrf
                    <div class="widget-box">
                        <div class="widget-header">
                            <h5 class="widget-title"><i class="fa fa-users"></i> Per-Student Details</h5>
                            <div class="widget-toolbar">
                                @ability('super-admin','fees-billing-run-cancel')
                                <div style="display:inline-flex; align-items:center; gap:6px">
                                    <input type="text" name="reason" class="form-control input-sm" style="width:200px" placeholder="Reason (optional)">
                                    <button type="button" class="btn btn-xs btn-warning" onclick="bulkSubmit('cancel')" title="Cancel selected bills">
                                        <i class="fa fa-ban"></i> Bulk Cancel
                                    </button>
                                    <button type="button" class="btn btn-xs btn-info" onclick="bulkSubmit('restore')" title="Restore selected bills">
                                        <i class="fa fa-undo"></i> Bulk Restore
                                    </button>
                                    <label style="margin:0; font-weight:normal; font-size:11px; cursor:pointer">
                                        <input type="checkbox" id="selectAll"> All
                                    </label>
                                </div>
                                @endability
                            </div>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main padding-8">
                                <input type="hidden" name="action" id="bulkAction" value="">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-condensed">
                                        <thead>
                                            <tr class="bg-primary">
                                                <th width="3%"></th>
                                                <th width="4%">#</th>
                                                <th>Student</th>
                                                <th>Reg No</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>SMS</th>
                                                <th>Note / Cancel Reason</th>
                                                <th width="8%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($details as $detail)
                                            <tr class="{{ $detail->status === 'cancelled' ? 'text-muted' : '' }}">
                                                <td>
                                                    @ability('super-admin','fees-billing-run-cancel')
                                                    <input type="checkbox" name="detail_ids[]" value="{{ $detail->id }}" class="detail-check">
                                                    @endability
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="student-name" style="{{ $detail->status === 'cancelled' ? 'text-decoration:line-through' : '' }}">
                                                    @if($detail->student)
                                                        {{ $detail->student->first_name ?? '' }} {{ $detail->student->last_name ?? '' }}
                                                    @else
                                                        <span class="text-muted">[Deleted]</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($detail->student)
                                                        <small>{{ $detail->student->reg_no ?? $detail->student_id }}</small>
                                                    @else
                                                        <small class="text-muted">{{ $detail->student_id }}</small>
                                                    @endif
                                                </td>
                                                <td>৳ {{ number_format($detail->amount, 0) }}</td>
                                                <td>{!! $detail->status_badge !!}</td>
                                                <td>{!! $detail->sms_badge !!}</td>
                                                <td style="max-width:200px; white-space:normal">
                                                    @if($detail->cancel_reason)
                                                        <small class="text-danger"><i class="fa fa-ban"></i> {{ $detail->cancel_reason }}</small>
                                                    @elseif($detail->skip_reason)
                                                        <small class="text-muted"><i class="fa fa-info-circle"></i> {{ $detail->skip_reason }}</small>
                                                    @elseif($detail->error_message)
                                                        <small class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ Str::limit($detail->error_message, 60) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @ability('super-admin','fees-billing-run-cancel')
                                                    @if($detail->status === 'created')
                                                    <button type="button" class="btn btn-xs btn-warning"
                                                        onclick="cancelDetail({{ $detail->id }})">
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                    @elseif($detail->status === 'cancelled')
                                                    <form method="POST" action="{{ route('account.fees.billing-run.detail.restore', $detail->id) }}" style="display:inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-info"
                                                            onclick="return confirm('Restore this bill?')">
                                                            <i class="fa fa-undo"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                    @endability
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">
                                                    <i class="fa fa-info-circle"></i> No detail records found.
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $details->links() }}
                            </div>
                        </div>
                    </div>
                    </form>{{-- end bulk form --}}

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
                        All unpaid bills in this run will be deactivated. Bills with existing payments are protected.
                    </p>
                    <div class="form-group">
                        <label>Reason (optional)</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Wrong billing period..." maxlength="500">
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

{{-- CANCEL DETAIL MODAL --}}
<div class="modal fade" id="cancelDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="cancelDetailForm">
                @csrf
                <div class="modal-header"><h4 class="modal-title"><i class="fa fa-ban"></i> Cancel Student Bill</h4></div>
                <div class="modal-body">
                    <p class="text-muted">The fee will be deactivated for this student. You can restore it later if needed.</p>
                    <div class="form-group">
                        <label>Reason (optional)</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Student exempted..." maxlength="300">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning"><i class="fa fa-ban"></i> Cancel Bill</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(function () {
    // Select all checkboxes
    $('#selectAll').on('change', function () {
        $('.detail-check').prop('checked', this.checked);
    });
});

function bulkSubmit(action) {
    var checked = $('.detail-check:checked').length;
    if (checked === 0) {
        alert('Please select at least one student bill first.');
        return;
    }
    if (!confirm(checked + ' bill(s) will be ' + action + 'd. Continue?')) return;
    $('#bulkAction').val(action);
    $('#bulkForm').submit();
}

function cancelRun(runId) {
    var form = document.getElementById('cancelRunForm');
    form.action = '/account/fees/billing-run/' + runId + '/cancel';
    $('#cancelRunModal').modal('show');
}

function cancelDetail(detailId) {
    var form = document.getElementById('cancelDetailForm');
    form.action = '/account/fees/billing-run/detail/' + detailId + '/cancel';
    $('#cancelDetailModal').modal('show');
}
</script>
@endsection
