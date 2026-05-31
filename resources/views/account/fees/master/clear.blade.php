@extends('layouts.master')

@section('css')
<style>
/* ── Page wrap ───────────────────────────────────────────── */
.clr-wrap { max-width: 1100px; margin: 0 auto; }

/* ── Danger banner ───────────────────────────────────────── */
.danger-banner {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
    border: 1px solid #f5c6cb;
    border-left: 5px solid #dc3545;
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 22px;
}
.danger-banner .db-icon {
    font-size: 22px;
    color: #dc3545;
    flex-shrink: 0;
    margin-top: 2px;
}
.danger-banner h6 {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: 800;
    color: #721c24;
}
.danger-banner p { margin: 0; font-size: 12px; color: #856404; line-height: 1.6; }

/* ── Section cards ───────────────────────────────────────── */
.clr-card {
    background: #fff;
    border: 1px solid #dce3ea;
    border-radius: 8px;
    margin-bottom: 18px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    overflow: hidden;
}
.clr-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-bottom: 1px solid #eef1f5;
    background: #f7f9fc;
}
.clr-card-header .ch-icon {
    width: 28px; height: 28px;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.clr-card-header h6 {
    margin: 0;
    font-size: 13px;
    font-weight: 700;
    color: #3a4557;
}
.clr-card-body { padding: 16px 18px; }

/* Card accent colours */
.acc-blue  .clr-card-header { border-left: 4px solid #4a8dc8; }
.acc-blue  .ch-icon { background: #e8f0fb; color: #4a8dc8; }
.acc-teal  .clr-card-header { border-left: 4px solid #25a89b; }
.acc-teal  .ch-icon { background: #e3f7f5; color: #25a89b; }
.acc-red   .clr-card-header { border-left: 4px solid #dc3545; }
.acc-red   .ch-icon { background: #fde8ea; color: #dc3545; }

/* ── Form fields ─────────────────────────────────────────── */
.fl { font-size: 12px; font-weight: 700; color: #4a5568; margin-bottom: 4px; display: block; }
.fn { font-size: 11px; color: #8a9bb0; margin-top: 2px; }
.form-group { margin-bottom: 14px; }
.form-control.fc-sm { height: 34px; font-size: 13px; }

/* ── Status radio tiles ──────────────────────────────────── */
.status-tiles { display: flex; gap: 8px; flex-wrap: wrap; }
.st-tile input[type="radio"] { display: none; }
.st-tile label {
    padding: 6px 14px;
    border: 2px solid #dce3ea;
    border-radius: 20px;
    font-size: 12px; font-weight: 700;
    color: #5a6678;
    cursor: pointer;
    background: #fafbfd;
    transition: all .14s;
    white-space: nowrap;
}
.st-tile input:checked + label.safe-label   { border-color:#25a89b; background:#e3f7f5; color:#1a7a73; }
.st-tile input:checked + label.warn-label   { border-color:#e87d2a; background:#fff3e8; color:#c05e10; }
.st-tile input:checked + label.danger-label { border-color:#dc3545; background:#fde8ea; color:#9b1d27; }
.st-tile label:hover { border-color:#aaa; }

/* ── Action bar ──────────────────────────────────────────── */
.action-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 16px;
    background: #fafbfd;
    border: 1px solid #dce3ea;
    border-radius: 8px;
    margin: 20px 0;
    flex-wrap: wrap;
}
.btn-preview {
    background: #4a8dc8; color: #fff;
    border: none; border-radius: 6px;
    padding: 9px 24px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    transition: background .14s;
}
.btn-preview:hover { background: #3a72a5; color: #fff; }
.btn-execute {
    background: #dc3545; color: #fff;
    border: none; border-radius: 6px;
    padding: 9px 24px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    transition: background .14s;
}
.btn-execute:hover { background: #b52a38; color: #fff; }
.btn-execute:disabled { opacity: .45; cursor: not-allowed; background: #dc3545; }
.btn-reset {
    background: #fff; color: #6c757d;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 9px 18px;
    font-size: 13px; font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}
.btn-reset:hover { background: #f8f9fa; color: #495057; text-decoration: none; }

/* ── Preview result cards ────────────────────────────────── */
.preview-section { margin-top: 6px; }
.pv-cards { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; }
.pv-card {
    flex: 1;
    min-width: 200px;
    border-radius: 10px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.pv-card.pv-delete { background: linear-gradient(135deg,#fff0f0,#ffe0e0); border: 1px solid #f5c6cb; }
.pv-card.pv-safe   { background: linear-gradient(135deg,#f0fff4,#d4edda); border: 1px solid #c3e6cb; }
.pv-card.pv-amount { background: linear-gradient(135deg,#fff8e1,#ffecb3); border: 1px solid #ffd54f; }
.pv-card .pv-icon {
    width: 48px; height: 48px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.pv-delete .pv-icon { background: #dc3545; color: #fff; }
.pv-safe   .pv-icon { background: #28a745; color: #fff; }
.pv-amount .pv-icon { background: #ffc107; color: #5a3e00; }
.pv-card .pv-num  { font-size: 28px; font-weight: 900; line-height: 1; margin-bottom: 2px; }
.pv-card .pv-lbl  { font-size: 12px; font-weight: 600; color: #5a6678; }
.pv-delete .pv-num { color: #721c24; }
.pv-safe   .pv-num { color: #155724; }
.pv-amount .pv-num { color: #856404; }

.pv-notice {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 16px;
    border-radius: 7px;
    font-size: 13px;
    margin-top: 8px;
}
.pv-notice.pv-warn { background: #fff3cd; border: 1px solid #ffc107; color: #664d03; }
.pv-notice.pv-ok   { background: #d1e7dd; border: 1px solid #a3cfbb; color: #0a3622; }
.pv-notice i { font-size: 16px; margin-top: 1px; flex-shrink: 0; }

/* ── Applied filter chips ────────────────────────────────── */
.filter-chips-bar {
    display: flex; flex-wrap: wrap; gap: 6px;
    padding: 10px 14px;
    background: #f0f4f8;
    border-radius: 6px;
    margin-bottom: 14px;
}
.filter-chips-bar .fc-lbl { font-size: 11px; font-weight: 700; color: #6c7f94; align-self: center; white-space: nowrap; }
.filter-chip {
    display: inline-flex; align-items: center; gap: 4px;
    background: #4a8dc8; color: #fff;
    border-radius: 12px;
    padding: 3px 10px;
    font-size: 11px; font-weight: 700;
}
.filter-chip i { font-size: 10px; }

/* ── Confirm modal ───────────────────────────────────────── */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.modal-overlay.show { display: flex; }
.modal-box {
    background: #fff;
    border-radius: 12px;
    padding: 0;
    width: 100%; max-width: 460px;
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
    overflow: hidden;
}
.modal-box-header {
    background: linear-gradient(135deg,#dc3545,#9b1d27);
    color: #fff;
    padding: 18px 22px;
    display: flex; align-items: center; gap: 12px;
}
.modal-box-header i { font-size: 22px; }
.modal-box-header h5 { margin: 0; font-size: 15px; font-weight: 800; }
.modal-box-body { padding: 20px 22px; }
.modal-box-body p { font-size: 13px; color: #4a5568; margin-bottom: 10px; }
.modal-box-body .modal-count {
    font-size: 28px; font-weight: 900; color: #dc3545;
    text-align: center; padding: 10px; margin: 12px 0;
}
.modal-box-body .modal-note {
    background: #fff3cd; border: 1px solid #ffc107;
    border-radius: 6px; padding: 8px 12px;
    font-size: 12px; color: #664d03;
}
.modal-box-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 14px 22px 18px;
    background: #fafbfd;
    border-top: 1px solid #eef1f5;
}
.modal-cancel {
    background: none; border: 1px solid #ced4da;
    border-radius: 6px; padding: 8px 20px;
    font-size: 13px; font-weight: 600; color: #6c757d;
    cursor: pointer;
}
.modal-cancel:hover { background: #f8f9fa; }
.modal-confirm {
    background: #dc3545; color: #fff;
    border: none; border-radius: 6px;
    padding: 8px 24px;
    font-size: 13px; font-weight: 800;
    cursor: pointer;
    transition: background .13s;
}
.modal-confirm:hover { background: #b52a38; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 767px) {
    .pv-cards { flex-direction: column; }
    .action-bar { flex-direction: column; }
    .btn-preview, .btn-execute, .btn-reset { width: 100%; text-align: center; }
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
                    Fees
                    <small><i class="ace-icon fa fa-angle-double-right"></i> Clear / Delete Fees</small>
                </h1>
            </div>

            <div class="row">
                    @include('account.includes.buttons')
                    <div class="col-xs-12 ">
                        @include('account.fees.includes.buttons')
                        @include('includes.flash_messages')

                    <div class="clr-wrap">

                        {{-- ── Danger banner ── --}}
                        <div class="danger-banner">
                            <i class="fa fa-shield db-icon"></i>
                            <div>
                                <h6>Danger Zone — Permanent Deletion</h6>
                                <p>
                                    This tool <strong>permanently deletes</strong> fee records from the system.
                                    Fees that have existing <strong>confirmed payments</strong> are automatically protected and will never be deleted.
                                    Always run <strong>Preview</strong> first to review what will be removed before executing.
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('account.fees.master.clear.post') }}" id="clearForm">
                            @csrf

                            <div class="row">

                                {{-- ── LEFT: Student filters ── --}}
                                <div class="col-md-6">
                                    <div class="clr-card acc-blue">
                                        <div class="clr-card-header">
                                            <div class="ch-icon"><i class="fa fa-users"></i></div>
                                            <h6>Student Filters</h6>
                                        </div>
                                        <div class="clr-card-body">

                                            <div class="form-group">
                                                <label class="fl">Faculty / Department</label>
                                                <select name="faculty" class="form-control fc-sm">
                                                    <option value="">— All Faculties —</option>
                                                    @foreach($faculties as $facId => $facName)
                                                    @if($facId === '') @continue @endif
                                                    <option value="{{ $facId }}" {{ (old('faculty', $filter_query['faculty'] ?? '') == $facId) ? 'selected' : '' }}>
                                                        {{ $facName }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="fl">Semester / Year</label>
                                                <select name="semester_select" class="form-control fc-sm">
                                                    <option value="">— All Semesters —</option>
                                                    @for($s = 1; $s <= 12; $s++)
                                                    <option value="{{ $s }}" {{ (old('semester_select', $filter_query['semester_select'] ?? '') == $s) ? 'selected' : '' }}>
                                                        Semester / Year {{ $s }}
                                                    </option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="fl">Batch</label>
                                                <select name="batch" class="form-control fc-sm">
                                                    <option value="">— All Batches —</option>
                                                    @foreach($batch as $bId => $bName)
                                                    @if($bId === '') @continue @endif
                                                    <option value="{{ $bId }}" {{ (old('batch', $filter_query['batch'] ?? '') == $bId) ? 'selected' : '' }}>
                                                        {{ $bName }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="fl">Student Registration No.</label>
                                                <input type="text" name="reg_no" class="form-control fc-sm"
                                                    placeholder="Partial match — leave blank for all"
                                                    value="{{ old('reg_no', $filter_query['reg_no'] ?? '') }}">
                                                <span class="fn">Enter exact Reg No. to clear fees for one student only</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                {{-- ── RIGHT: Fee filters ── --}}
                                <div class="col-md-6">
                                    <div class="clr-card acc-teal">
                                        <div class="clr-card-header">
                                            <div class="ch-icon"><i class="fa fa-money"></i></div>
                                            <h6>Fee Filters</h6>
                                        </div>
                                        <div class="clr-card-body">

                                            <div class="form-group">
                                                <label class="fl">Fee Head</label>
                                                <select name="fee_heads" class="form-control fc-sm">
                                                    <option value="">— All Fee Heads —</option>
                                                    @foreach($fee_heads as $fhId => $fhName)
                                                    @if($fhId == 0 || $fhId === '') @continue @endif
                                                    <option value="{{ $fhId }}" {{ (old('fee_heads', $filter_query['fee_heads'] ?? '') == $fhId) ? 'selected' : '' }}>
                                                        {{ $fhName }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="fl">Billing Run</label>
                                                <select name="billing_run_id" class="form-control fc-sm">
                                                    <option value="">— Any / Manual —</option>
                                                    @foreach($billing_runs as $br)
                                                    <option value="{{ $br->id }}" {{ (old('billing_run_id', $filter_query['billing_run_id'] ?? '') == $br->id) ? 'selected' : '' }}>
                                                        #{{ $br->id }} — {{ $br->period_label }}
                                                        ({{ $br->run_date ? \Carbon\Carbon::parse($br->run_date)->format('d M Y') : '' }})
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <span class="fn">Leave blank to include manually added fees too</span>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="fl">Due Date From</label>
                                                        <input type="date" name="fee_due_date_start" class="form-control fc-sm"
                                                            value="{{ old('fee_due_date_start', $filter_query['fee_due_date_start'] ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="fl">Due Date To</label>
                                                        <input type="date" name="fee_due_date_end" class="form-control fc-sm"
                                                            value="{{ old('fee_due_date_end', $filter_query['fee_due_date_end'] ?? '') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="fl">Fee Status</label>
                                                <div class="status-tiles">
                                                    @php $curStatus = old('fee_status', $filter_query['fee_status'] ?? 'unpaid'); @endphp
                                                    <div class="st-tile">
                                                        <input type="radio" name="fee_status" id="st_unpaid" value="unpaid" {{ $curStatus==='unpaid' ? 'checked':'' }}>
                                                        <label for="st_unpaid" class="safe-label"><i class="fa fa-lock"></i> Unpaid Only</label>
                                                    </div>
                                                    <div class="st-tile">
                                                        <input type="radio" name="fee_status" id="st_inactive" value="inactive" {{ $curStatus==='inactive' ? 'checked':'' }}>
                                                        <label for="st_inactive" class="warn-label"><i class="fa fa-ban"></i> Cancelled Only</label>
                                                    </div>
                                                    <div class="st-tile">
                                                        <input type="radio" name="fee_status" id="st_all" value="all" {{ $curStatus==='all' ? 'checked':'' }}>
                                                        <label for="st_all" class="danger-label"><i class="fa fa-th"></i> All</label>
                                                    </div>
                                                </div>
                                                <span class="fn" style="margin-top:6px;display:block">
                                                    <i class="fa fa-info-circle"></i>
                                                    "Unpaid Only" is recommended — fees with payments are always protected regardless
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>{{-- /row --}}

                            {{-- ── Action bar ── --}}
                            <div class="action-bar">
                                <button type="submit" name="action" value="preview" class="btn-preview">
                                    <i class="fa fa-search"></i>&nbsp; Preview Matching Fees
                                </button>
                                <button type="button" class="btn-execute" id="openConfirmBtn"
                                    @disabled($preview === null || ($preview['clearable_count'] ?? 0) === 0)>
                                    <i class="fa fa-trash"></i>&nbsp; Delete Selected Fees
                                </button>
                                <a href="{{ route('account.fees.master.clear') }}" class="btn-reset">
                                    <i class="fa fa-times"></i>&nbsp; Reset Filters
                                </a>
                            </div>

                            {{-- hidden execute trigger --}}
                            <button type="submit" name="action" value="execute" id="execBtn" style="display:none"></button>

                        </form>

                        {{-- ── Preview result ── --}}
                        @if($preview !== null)
                        <div class="clr-card acc-red preview-section">
                            <div class="clr-card-header">
                                <div class="ch-icon"><i class="fa fa-eye"></i></div>
                                <h6>Preview Result</h6>
                            </div>
                            <div class="clr-card-body">

                                {{-- Active filter chips --}}
                                @if(!empty($filter_query))
                                <div class="filter-chips-bar">
                                    <span class="fc-lbl"><i class="fa fa-filter"></i> Active filters:</span>
                                    @foreach($filter_query as $fk => $fv)
                                    <span class="filter-chip">
                                        <i class="fa fa-tag"></i>
                                        {{ ucwords(str_replace('_', ' ', $fk)) }}: {{ $fv }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Stat cards --}}
                                <div class="pv-cards">
                                    <div class="pv-card pv-delete">
                                        <div class="pv-icon"><i class="fa fa-trash"></i></div>
                                        <div>
                                            <div class="pv-num">{{ $preview['clearable_count'] }}</div>
                                            <div class="pv-lbl">Will be DELETED</div>
                                        </div>
                                    </div>
                                    <div class="pv-card pv-safe">
                                        <div class="pv-icon"><i class="fa fa-lock"></i></div>
                                        <div>
                                            <div class="pv-num">{{ $preview['protected_count'] }}</div>
                                            <div class="pv-lbl">Protected (have payments)</div>
                                        </div>
                                    </div>
                                    <div class="pv-card pv-amount">
                                        <div class="pv-icon"><i class="fa fa-money"></i></div>
                                        <div>
                                            <div class="pv-num">৳ {{ number_format($preview['clearable_amount'], 0) }}</div>
                                            <div class="pv-lbl">Total amount to remove</div>
                                        </div>
                                    </div>
                                </div>

                                @if($preview['clearable_count'] > 0)
                                <div class="pv-notice pv-warn">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <div>
                                        <strong>{{ $preview['clearable_count'] }} fee record(s) will be permanently deleted.</strong>
                                        Click <em>Delete Selected Fees</em> above to proceed.
                                        @if($preview['protected_count'] > 0)
                                        <br><i class="fa fa-lock"></i> {{ $preview['protected_count'] }} fee(s) with payments will be automatically skipped.
                                        @endif
                                    </div>
                                </div>
                                @else
                                <div class="pv-notice pv-ok">
                                    <i class="fa fa-check-circle"></i>
                                    <div>
                                        <strong>Nothing to delete.</strong>
                                        @if($preview['protected_count'] > 0)
                                        {{ $preview['protected_count'] }} matching fee(s) exist but all are protected because they have confirmed payments.
                                        @else
                                        No fees match the selected filters.
                                        @endif
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                        @endif

                    </div>{{-- /clr-wrap --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Delete confirmation modal ── --}}
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <div class="modal-box-header">
            <i class="fa fa-exclamation-triangle"></i>
            <h5>Confirm Permanent Deletion</h5>
        </div>
        <div class="modal-box-body">
            <p>You are about to permanently delete fee records from the system. <strong>This action cannot be undone.</strong></p>
            @if($preview !== null && ($preview['clearable_count'] ?? 0) > 0)
            <div class="modal-count">
                {{ $preview['clearable_count'] }} fee(s) &mdash; ৳ {{ number_format($preview['clearable_amount'] ?? 0, 0) }}
            </div>
            @endif
            <div class="modal-note">
                <i class="fa fa-lock"></i>
                Fees with existing payments are automatically protected and will <strong>not</strong> be deleted.
            </div>
        </div>
        <div class="modal-box-footer">
            <button type="button" class="modal-cancel" id="cancelConfirmBtn">
                <i class="fa fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-confirm" id="finalDeleteBtn">
                <i class="fa fa-trash"></i> Yes, Delete Permanently
            </button>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(function () {

    /* ── Faculty → Semester dynamic load ── */
    var $faculty  = $('select[name="faculty"]');
    var $semester = $('select[name="semester_select"]');

    /* Keep the original static options so we can restore them */
    var originalOptions = $semester.html();

    $faculty.on('change', function () {
        var facId = $(this).val();

        if (!facId) {
            /* No faculty selected — restore the full list */
            $semester.html(originalOptions);
            return;
        }

        $semester.html('<option value="">Loading…</option>').prop('disabled', true);

        $.getJSON('{{ route("get-semesters") }}', { faculty_id: facId })
            .done(function (data) {
                var html = '<option value="">— All Semesters —</option>';
                var count = 0;
                $.each(data, function (id, name) {
                    html += '<option value="' + id + '">' + name + '</option>';
                    count++;
                });
                if (count === 0) {
                    html = '<option value="">No semesters found</option>';
                }
                $semester.html(html).prop('disabled', false);
            })
            .fail(function () {
                $semester.html(originalOptions).prop('disabled', false);
            });
    });

    /* If faculty already selected on page load (after form submit), trigger load */
    if ($faculty.val()) {
        $faculty.trigger('change');
        /* Restore old selected value after AJAX */
        var savedSem = '{{ old("semester_select", $filter_query["semester_select"] ?? "") }}';
        if (savedSem) {
            $faculty.one('change', function () {}); /* no-op; real restore below */
            $faculty.trigger('change');
            $(document).ajaxComplete(function restoreSelect() {
                if (savedSem) {
                    $semester.val(savedSem);
                    savedSem = '';
                    $(document).off('ajaxComplete', restoreSelect);
                }
            });
        }
    }

    /* ── Confirm modal ── */
    /* Open confirm modal */
    $('#openConfirmBtn').on('click', function () {
        if ($(this).is(':disabled')) return;
        $('#confirmModal').addClass('show');
    });

    /* Cancel */
    $('#cancelConfirmBtn').on('click', function () {
        $('#confirmModal').removeClass('show');
    });

    /* Click outside modal to close */
    $('#confirmModal').on('click', function (e) {
        if ($(e.target).is('#confirmModal')) $(this).removeClass('show');
    });

    /* Confirmed — submit form */
    $('#finalDeleteBtn').on('click', function () {
        $('#confirmModal').removeClass('show');
        $('#execBtn').trigger('click');
    });

    /* ESC key closes modal */
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') $('#confirmModal').removeClass('show');
    });

});
</script>
@endsection
