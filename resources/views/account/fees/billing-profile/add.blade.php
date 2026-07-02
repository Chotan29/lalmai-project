@extends('layouts.master')

@section('css')
<style>
/* ── Core layout ──────────────────────────────────────────── */
.bp-form-wrap {
    max-width: 1160px;
    margin: 0 auto;
    width: 100%;
    padding: 0 4px;
}

/* ── Step cards ───────────────────────────────────────────── */
.bp-card {
    background: #fff;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden;
}
.bp-card-header {
    display: flex;
    align-items: center;
    padding: 13px 18px;
    border-bottom: 1px solid #e8edf2;
    background: #f7f9fc;
}
.bp-step-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px; height: 26px;
    border-radius: 50%;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    margin-right: 10px;
}
.bp-card-header h5 {
    margin: 0;
    font-size: 13px;
    font-weight: 700;
    color: #3a4557;
    letter-spacing: .3px;
    flex: 1;
}
.bp-card-header .card-icon {
    font-size: 15px;
    margin-right: 9px;
    opacity: .7;
}
.bp-card-body { padding: 18px 20px 14px; }

/* Step colours */
.step-blue   .bp-step-badge { background: #4a8dc8; }
.step-teal   .bp-step-badge { background: #25a89b; }
.step-orange .bp-step-badge { background: #e87d2a; }
.step-green  .bp-step-badge { background: #399743; }
.step-red    .bp-step-badge { background: #c9423f; }
.step-purple .bp-step-badge { background: #7b5ea7; }
.step-indigo .bp-step-badge { background: #3d6bb8; }

.step-blue   .bp-card-header { border-left: 4px solid #4a8dc8; }
.step-teal   .bp-card-header { border-left: 4px solid #25a89b; }
.step-orange .bp-card-header { border-left: 4px solid #e87d2a; }
.step-green  .bp-card-header { border-left: 4px solid #399743; }
.step-red    .bp-card-header { border-left: 4px solid #c9423f; }
.step-purple .bp-card-header { border-left: 4px solid #7b5ea7; }
.step-indigo .bp-card-header { border-left: 4px solid #3d6bb8; }

/* ── Scope tiles ──────────────────────────────────────────── */
.scope-tiles { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
.scope-tile input[type="radio"] { display: none; }
.scope-tile label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 110px; height: 76px;
    border: 2px solid #dce3ea;
    border-radius: 7px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    color: #5a6678;
    background: #fafbfd;
    transition: border-color .18s, background .18s, color .18s;
    text-align: center;
    gap: 6px;
}
.scope-tile label i { font-size: 20px; }
.scope-tile input:checked + label {
    border-color: #4a8dc8;
    background: #edf4fb;
    color: #2b6cb0;
}
.scope-tile label:hover { border-color: #7ab0df; background: #f0f6fc; }

/* ── Month checkboxes ─────────────────────────────────────── */
.month-grid { display: flex; flex-wrap: wrap; gap: 6px; }
.month-chip input[type="checkbox"] { display: none; }
.month-chip label {
    display: inline-block;
    padding: 5px 13px;
    border: 1px solid #c8d5e0;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #5a6678;
    cursor: pointer;
    background: #f4f7fa;
    transition: all .15s;
    user-select: none;
}
.month-chip input:checked + label {
    background: #e87d2a;
    border-color: #e87d2a;
    color: #fff;
}
.month-chip label:hover { border-color: #e87d2a; }

/* ── Fee head table ───────────────────────────────────────── */
.fee-table { width: 100%; border-collapse: collapse; }
.fee-table thead th {
    background: #f0f4f8;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #5a6678;
    padding: 8px 10px;
    border-bottom: 2px solid #dce3ea;
}
.fee-table tbody tr { border-bottom: 1px solid #eef1f5; }
.fee-table tbody tr:hover { background: #fafbfc; }
.fee-table td { padding: 8px 8px; vertical-align: middle; }
.fee-table .form-control { height: 32px; font-size: 13px; }
.fee-table .btn-xs { width: 28px; height: 28px; padding: 0; line-height: 28px; }
.fee-total-row td {
    background: #f0f7ff;
    font-weight: 700;
    font-size: 13px;
    color: #2b6cb0;
    padding: 8px 10px;
    border-top: 2px solid #c3daf7;
}
.amount-preview-badge {
    display: inline-block;
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
    border-radius: 4px;
    padding: 2px 7px;
    font-size: 12px;
    font-weight: 700;
}

/* ── Installment split rows ───────────────────────────────── */
.split-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 10px;
    background: #f9fbfd;
    border: 1px solid #e2eaf2;
    border-radius: 5px;
    margin-bottom: 6px;
}
.split-row .split-label {
    min-width: 110px;
    font-size: 12px;
    font-weight: 600;
    color: #4a5568;
}
.split-row .split-total-indicator {
    font-size: 11px;
    color: #6c8ebf;
}
#splitSumAlert {
    display: none;
    margin-top: 6px;
}

/* ── SMS template box ─────────────────────────────────────── */
.sms-template-area {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    resize: vertical;
}
.var-chips { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 6px; }
.var-chip {
    display: inline-block;
    padding: 2px 9px;
    background: #eef1fb;
    border: 1px solid #c5cff5;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    color: #3b5998;
    cursor: pointer;
    transition: background .12s;
}
.var-chip:hover { background: #c5cff5; }

/* ── Summary sidebar ──────────────────────────────────────── */
.bp-summary {
    background: #f7f9fc;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    padding: 16px;
    position: sticky;
    top: 70px;
}
.bp-summary h6 {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #6c7f94;
    margin: 0 0 12px;
    border-bottom: 1px solid #dce3ea;
    padding-bottom: 8px;
}
.bp-sum-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 8px;
    font-size: 12px;
}
.bp-sum-label { color: #6c7f94; font-weight: 600; }
.bp-sum-val   { color: #3a4557; font-weight: 700; text-align: right; max-width: 60%; word-break: break-word; }
.bp-sum-divider { border: 0; border-top: 1px dashed #d5dce6; margin: 10px 0; }
.bp-sum-total { font-size: 14px; color: #2b6cb0; font-weight: 800; }

/* ── Fine toggle ──────────────────────────────────────────── */
.fine-type-row { display: flex; gap: 8px; }
.fine-type-btn { flex: 1; }
.fine-type-btn input[type="radio"] { display: none; }
.fine-type-btn label {
    display: block;
    text-align: center;
    padding: 7px 4px;
    border: 2px solid #dce3ea;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    color: #5a6678;
    cursor: pointer;
    transition: all .15s;
}
.fine-type-btn input:checked + label { border-color: #c9423f; background: #fff0f0; color: #c9423f; }
.fine-type-btn label:hover { border-color: #e99; }

/* ── Cycle radio tiles ────────────────────────────────────── */
.cycle-tiles { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
.cycle-tile input[type="radio"] { display: none; }
.cycle-tile label {
    padding: 7px 16px;
    border: 2px solid #dce3ea;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    color: #5a6678;
    cursor: pointer;
    background: #fafbfd;
    transition: all .15s;
    white-space: nowrap;
}
.cycle-tile input:checked + label { border-color: #25a89b; background: #e8f7f6; color: #1b7a72; }
.cycle-tile label:hover { border-color: #6ec9c2; }

/* ── Action buttons ───────────────────────────────────────── */
.bp-actions { position: sticky; bottom: 0; background: rgba(255,255,255,.97); border-top: 1px solid #dce3ea; padding: 14px 20px; margin: 0 -20px -14px; border-radius: 0 0 6px 6px; display: flex; gap: 10px; align-items: center; z-index: 10; }

/* ── Misc helpers ─────────────────────────────────────────── */
.required-star { color: #d9534f; }
.field-label { font-size: 12px; font-weight: 700; color: #4a5568; margin-bottom: 5px; display: block; }
.help-note { font-size: 11px; color: #8a9bb0; margin-top: 3px; }
.section-divider { border: 0; border-top: 1px solid #eef1f5; margin: 14px 0; }
.input-icon-wrap { position: relative; }
.input-icon-wrap .fa { position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #b0bdc9; pointer-events: none; }
.sms-char-count { font-size: 11px; color: #8a9bb0; text-align: right; margin-top: 3px; }
.toggle-sms-wrap { display: none; margin-top: 12px; }

/* ── Full Calendar widget ─────────────────────────────────── */
.bpcal {
    background: #fff;
    border: 1px solid #dce3ea;
    border-radius: 10px;
    overflow: hidden;
    width: 100%;
    max-width: 340px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    user-select: none;
}
.bpcal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #e87d2a;
    padding: 12px 14px;
    color: #fff;
}
.bpcal-nav {
    background: rgba(255,255,255,.2);
    border: none;
    color: #fff;
    width: 30px; height: 30px;
    border-radius: 50%;
    font-size: 16px;
    cursor: pointer;
    line-height: 1;
    transition: background .15s;
    display: flex; align-items: center; justify-content: center;
}
.bpcal-nav:hover { background: rgba(255,255,255,.35); }
.bpcal-title {
    font-size: 15px;
    font-weight: 700;
    letter-spacing: .3px;
    cursor: pointer;
}
.bpcal-title:hover { text-decoration: underline; }
.bpcal-dow {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #fdf2e8;
    border-bottom: 1px solid #f0d8be;
}
.bpcal-dow span {
    text-align: center;
    font-size: 10px;
    font-weight: 800;
    color: #b36a1a;
    padding: 6px 0;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.bpcal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    padding: 8px;
    background: #fff;
}
.bpcal-day {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 36px;
    border-radius: 50%;
    font-size: 13px;
    font-weight: 600;
    color: #3a4557;
    cursor: pointer;
    transition: background .13s, color .13s;
    position: relative;
}
.bpcal-day:hover:not(.bpcal-empty):not(.bpcal-other) {
    background: #fff3e8;
    color: #e87d2a;
}
.bpcal-day.bpcal-other { color: #c8d5e0; cursor: default; }
.bpcal-day.bpcal-today {
    border: 2px solid #e87d2a;
    color: #e87d2a;
    font-weight: 800;
}
.bpcal-day.bpcal-selected {
    background: #e87d2a !important;
    color: #fff !important;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(232,125,42,.4);
}
.bpcal-day.bpcal-today.bpcal-selected { border-color: #c05e10; }
.bpcal-day.bpcal-empty { cursor: default; }
.bpcal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px 10px;
    border-top: 1px solid #f0f3f7;
    background: #fafbfd;
}
.bpcal-footer-label {
    font-size: 12px; color: #7a8c9e; font-weight: 600;
}
.bpcal-today-btn {
    background: none;
    border: 1px solid #e87d2a;
    color: #e87d2a;
    border-radius: 12px;
    padding: 3px 12px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all .12s;
}
.bpcal-today-btn:hover { background: #e87d2a; color: #fff; }

/* Year/Month picker overlay */
.bpcal-ympicker {
    display: none;
    padding: 10px 8px;
}
.bpcal-ympicker.show { display: block; }
.bpcal-ym-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 4px;
    margin-bottom: 8px;
}
.bpcal-ym-cell {
    text-align: center;
    padding: 7px 2px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    color: #4a5568;
    background: #f4f7fa;
    border: 1px solid transparent;
    transition: all .12s;
}
.bpcal-ym-cell:hover { background: #fff3e8; color: #e87d2a; border-color: #e87d2a; }
.bpcal-ym-cell.active { background: #e87d2a; color: #fff; border-color: #e87d2a; }
.bpcal-year-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #3a4557;
}
.bpcal-year-btn {
    background: none; border: none;
    color: #e87d2a; font-size: 16px;
    cursor: pointer; padding: 0 6px; line-height: 1;
}
.bpcal-year-btn:hover { color: #c05e10; }

/* ── Due-days stepper ─────────────────────────────────────── */
.due-days-stepper {
    display: flex;
    align-items: center;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    overflow: hidden;
    width: 130px;
}
.due-step-btn {
    width: 36px; height: 36px;
    border: none;
    background: #f0f4f8;
    color: #5a6678;
    font-size: 13px;
    cursor: pointer;
    flex-shrink: 0;
    transition: background .12s;
}
.due-step-btn:hover { background: #e87d2a; color: #fff; }
.due-days-input {
    width: 58px !important;
    height: 36px !important;
    border: none !important;
    border-left: 1px solid #dce3ea !important;
    border-right: 1px solid #dce3ea !important;
    border-radius: 0 !important;
    text-align: center;
    font-weight: 700;
    font-size: 15px;
    padding: 0 4px;
    box-shadow: none !important;
}

/* ── Time picker ──────────────────────────────────────────── */
.time-picker-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 12px;
}
.time-picker-row label { font-size: 12px; font-weight: 700; color: #4a5568; margin: 0; }
.time-select {
    height: 34px;
    border: 1px solid #dce3ea;
    border-radius: 5px;
    font-size: 14px;
    font-weight: 700;
    color: #3a4557;
    padding: 0 6px;
    background: #fff;
    cursor: pointer;
}
.time-sep { font-size: 18px; font-weight: 900; color: #e87d2a; line-height: 1; }

/* ── Schedule preview ─────────────────────────────────────── */
.sched-preview {
    background: #f0f7ff;
    border: 1px solid #c3daf7;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 14px;
}
.sched-preview-title {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #4a8dc8;
    margin-bottom: 8px;
}
.sched-date-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
    font-size: 12px;
    flex-wrap: wrap;
}
.sched-date-pill {
    background: #e87d2a;
    color: #fff;
    border-radius: 12px;
    padding: 2px 10px;
    font-size: 11px;
    font-weight: 700;
}
.sched-due-pill {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
    border-radius: 12px;
    padding: 2px 10px;
    font-size: 11px;
    font-weight: 700;
}
.sched-arrow { color: #b0bdc9; }

/* ── Month selector grid ──────────────────────────────────── */
.month-cal-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
    max-width: 380px;
}
.month-cal-cell input[type="checkbox"] { display: none; }
.month-cal-cell label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 4px 6px;
    border: 2px solid #dce3ea;
    border-radius: 7px;
    cursor: pointer;
    background: #fafbfd;
    transition: all .15s;
    gap: 2px;
}
.month-cal-cell .mcal-abbr { font-size: 13px; font-weight: 700; color: #4a5568; }
.month-cal-cell .mcal-num  { font-size: 10px; color: #9aabbc; font-weight: 600; }
.month-cal-cell input:checked + label { background: #e87d2a; border-color: #e87d2a; }
.month-cal-cell input:checked + label .mcal-abbr,
.month-cal-cell input:checked + label .mcal-num { color: #fff; }
.month-cal-cell label:hover { border-color: #e87d2a; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 991px) {
    .bp-form-wrap { padding: 0; }
    .bp-card-body { padding: 14px 14px 10px; }
}
@media (max-width: 767px) {
    .bp-card-body { padding: 12px 10px 8px; }
    .bp-card-header { padding: 10px 12px; }
    .scope-tile label { width: 82px; height: 68px; font-size: 11px; }
    .scope-tile label i { font-size: 17px; }
    .fine-type-row { flex-wrap: wrap; gap: 6px; }
    .fine-type-btn { min-width: calc(33% - 4px); }
    .cycle-tiles { gap: 6px; }
    .cycle-tile label { padding: 6px 11px; font-size: 11px; }
    .month-chip label { padding: 4px 9px; font-size: 11px; }
    .fee-table thead th,
    .fee-table td { padding: 6px 5px; font-size: 12px; }
    .split-row { flex-wrap: wrap; gap: 6px; }
    .var-chips { gap: 4px; }
    .var-chip { font-size: 10px; padding: 2px 7px; }
}
@media (max-width: 480px) {
    .bp-card-header h5 { font-size: 12px; }
    .scope-tiles { gap: 7px; }
    .scope-tile label { width: 72px; height: 62px; }
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
                    @include($view_path . '.includes.breadcrumb-primary')
                    <small><i class="ace-icon fa fa-angle-double-right"></i> Create New Profile</small>
                </h1>
            </div>

            <div class="row">
                @include('account.includes.buttons')
                <div class="col-xs-12">
                    @include('account.fees.includes.buttons')
                    @include('includes.flash_messages')
                    @include('includes.validation_error_messages')

                    <form method="POST" action="{{ route('account.fees.billing-profile.store') }}" id="billingProfileForm" novalidate>
                        @csrf

                        <div class="bp-form-wrap">
                        <div class="row">

                            {{-- ═══════════════════════════════════════════════
                                 LEFT COLUMN  (8/12)
                            ════════════════════════════════════════════════ --}}
                            <div class="col-md-8 col-xs-12">

                                {{-- ┌─ STEP 1: Basic Info ─────────────────── --}}
                                <div class="bp-card step-blue">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">1</span>
                                        <i class="fa fa-info-circle card-icon" style="color:#4a8dc8"></i>
                                        <h5>Basic Information</h5>
                                    </div>
                                    <div class="bp-card-body">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <label class="field-label">Profile Name <span class="required-star">*</span></label>
                                                    <input type="text" name="profile_name" id="profileName"
                                                        class="form-control"
                                                        value="{{ old('profile_name') }}"
                                                        placeholder="e.g., Class 10 Monthly Tuition Fee"
                                                        required maxlength="200"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="field-label">Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label">Description <small class="text-muted" style="font-weight:400">(optional)</small></label>
                                            <textarea name="description" class="form-control" rows="2"
                                                placeholder="Add notes about this billing profile…">{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- ┌─ STEP 2: Student Scope ──────────────── --}}
                                <div class="bp-card step-teal">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">2</span>
                                        <i class="fa fa-users card-icon" style="color:#25a89b"></i>
                                        <h5>Student Scope — Who will be billed?</h5>
                                    </div>
                                    <div class="bp-card-body">

                                        {{-- Scope tiles --}}
                                        <label class="field-label">Apply To <span class="required-star">*</span></label>
                                        <div class="scope-tiles" id="scopeTiles">
                                            <div class="scope-tile">
                                                <input type="radio" name="scope_type" id="scope_all" value="all" {{ old('scope_type','all')==='all' ? 'checked':'' }}>
                                                <label for="scope_all"><i class="fa fa-globe" style="color:#25a89b"></i>All Students</label>
                                            </div>
                                            <div class="scope-tile">
                                                <input type="radio" name="scope_type" id="scope_faculty" value="faculty" {{ old('scope_type')==='faculty' ? 'checked':'' }}>
                                                <label for="scope_faculty"><i class="fa fa-building" style="color:#4a8dc8"></i>By Faculty</label>
                                            </div>
                                            <div class="scope-tile">
                                                <input type="radio" name="scope_type" id="scope_semester" value="semester" {{ old('scope_type')==='semester' ? 'checked':'' }}>
                                                <label for="scope_semester"><i class="fa fa-graduation-cap" style="color:#e87d2a"></i>By Class</label>
                                            </div>
                                            <div class="scope-tile">
                                                <input type="radio" name="scope_type" id="scope_batch" value="batch" {{ old('scope_type')==='batch' ? 'checked':'' }}>
                                                <label for="scope_batch"><i class="fa fa-layer-group" style="color:#7b5ea7"><i class="fa fa-th-large" style="color:#7b5ea7"></i></i>By Batch</label>
                                            </div>
                                        </div>

                                        {{-- Conditional scope selects --}}
                                        <div id="scopeFaculty" class="scope-sub" style="display:none">
                                            <label class="field-label">Select Faculty <span class="required-star">*</span></label>
                                            <select name="faculty_id" class="form-control">
                                                <option value="">— Select Faculty —</option>
                                                @foreach($faculties as $f)
                                                <option value="{{ $f->id }}" {{ old('faculty_id')==$f->id ? 'selected':'' }}>{{ $f->faculty }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="scopeSemester" class="scope-sub" style="display:none">
                                            <label class="field-label">Select Class / Semester <span class="required-star">*</span></label>
                                            <select name="semester_id" class="form-control">
                                                <option value="">— Select Semester —</option>
                                                @foreach($semesters as $s)
                                                <option value="{{ $s->id }}" {{ old('semester_id')==$s->id ? 'selected':'' }}>{{ $s->semester }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="scopeBatch" class="scope-sub" style="display:none">
                                            <label class="field-label">Select Batch <span class="required-star">*</span></label>
                                            <select name="batch_id" class="form-control">
                                                <option value="">— Select Batch —</option>
                                                @foreach($batches as $b)
                                                <option value="{{ $b->id }}" {{ old('batch_id')==$b->id ? 'selected':'' }}>{{ $b->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <hr class="section-divider">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="field-label" style="font-weight:400">
                                                    <input type="checkbox" name="only_active_students" value="1" {{ old('only_active_students','1') ? 'checked':'' }}>
                                                    &nbsp;Only Active Students
                                                </label>
                                                <p class="help-note">Skip inactive / withdrawn students</p>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="field-label" style="font-weight:400">
                                                    <input type="checkbox" name="only_regular_status" value="1" {{ old('only_regular_status','1') ? 'checked':'' }}>
                                                    &nbsp;Only Regular Status
                                                </label>
                                                <p class="help-note">Skip suspended / on-hold students</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ┌─ STEP 3: Billing Cycle ──────────────── --}}
                                <div class="bp-card step-orange">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">3</span>
                                        <i class="fa fa-calendar card-icon" style="color:#e87d2a"></i>
                                        <h5>Billing Cycle &amp; Schedule</h5>
                                    </div>
                                    <div class="bp-card-body">

                                        {{-- Cycle type tiles --}}
                                        <label class="field-label">Cycle Type <span class="required-star">*</span></label>
                                        <div class="cycle-tiles" style="margin-bottom:18px">
                                            @foreach($cycles as $key => $cLabel)
                                            <div class="cycle-tile">
                                                <input type="radio" name="billing_cycle" id="cycle_{{ $key }}" value="{{ $key }}"
                                                    {{ old('billing_cycle', 'monthly') === $key ? 'checked' : '' }}>
                                                <label for="cycle_{{ $key }}">{{ $cLabel }}</label>
                                            </div>
                                            @endforeach
                                        </div>

                                        {{-- ── Recurring: full navigable calendar ── --}}
                                        <div id="billingDayGroup">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label class="field-label" style="margin-bottom:8px">
                                                        <i class="fa fa-calendar-o" style="color:#e87d2a;margin-right:4px"></i>
                                                        Select Billing Day <span class="required-star">*</span>
                                                    </label>
                                                    <div class="bpcal" id="recurCal">
                                                        <div class="bpcal-header">
                                                            <button type="button" class="bpcal-nav" id="recurPrev">&#8249;</button>
                                                            <div class="bpcal-title" id="recurTitle"></div>
                                                            <button type="button" class="bpcal-nav" id="recurNext">&#8250;</button>
                                                        </div>
                                                        <div class="bpcal-ympicker" id="recurYmPicker">
                                                            <div class="bpcal-year-nav">
                                                                <button type="button" class="bpcal-year-btn" id="recurYearDown">&#8249;</button>
                                                                <span id="recurYearLabel"></span>
                                                                <button type="button" class="bpcal-year-btn" id="recurYearUp">&#8250;</button>
                                                            </div>
                                                            <div class="bpcal-ym-grid" id="recurMonthGrid"></div>
                                                        </div>
                                                        <div id="recurCalBody">
                                                            <div class="bpcal-dow">
                                                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                                            </div>
                                                            <div class="bpcal-grid" id="recurGrid"></div>
                                                        </div>
                                                        <div class="bpcal-footer">
                                                            <span class="bpcal-footer-label">Day <strong id="selectedDayLabel" style="color:#e87d2a">{{ old('billing_day',1) }}</strong> of each period</span>
                                                            <button type="button" class="bpcal-today-btn" id="recurTodayBtn">Today</button>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="billing_day" id="billingDayHidden" value="{{ old('billing_day',1) }}">
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="field-label">
                                                        <i class="fa fa-clock-o" style="color:#4a8dc8;margin-right:4px"></i>
                                                        Due After (days) <span class="required-star">*</span>
                                                    </label>
                                                    <div class="due-days-stepper">
                                                        <button type="button" class="due-step-btn" id="dueDaysDown"><i class="fa fa-minus"></i></button>
                                                        <input type="number" name="due_days" id="dueDaysInput" class="due-days-input" min="0" max="365" value="{{ old('due_days',15) }}">
                                                        <button type="button" class="due-step-btn" id="dueDaysUp"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                    <p class="help-note" style="margin-top:6px">days after billing date before due</p>

                                                    <div class="sched-preview" id="schedPreview" style="margin-top:14px">
                                                        <div class="sched-preview-title"><i class="fa fa-list"></i>&nbsp; Upcoming billing schedule</div>
                                                        <div id="schedDates"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ── Month selector (half_yearly / yearly) ── --}}
                                        <div id="billing_months_group" style="display:none; margin-top:16px">
                                            <label class="field-label">
                                                <i class="fa fa-calendar" style="color:#e87d2a;margin-right:4px"></i>
                                                Generate in Which Months? <span class="required-star">*</span>
                                            </label>
                                            <div class="month-cal-grid">
                                                @foreach($months as $num => $name)
                                                <div class="month-cal-cell">
                                                    <input type="checkbox" name="billing_months[]" id="month_{{ $num }}" value="{{ $num }}"
                                                        {{ in_array($num, old('billing_months', [])) ? 'checked' : '' }}>
                                                    <label for="month_{{ $num }}">
                                                        <span class="mcal-abbr">{{ substr($name, 0, 3) }}</span>
                                                        <span class="mcal-num">{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}</span>
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                            <p class="help-note" style="margin-top:8px">
                                                Select 2 months for Half-Yearly &mdash; 1 month for Yearly
                                            </p>
                                        </div>

                                        {{-- ── One-time: full date + time picker ── --}}
                                        <div id="one_time_date_group" style="display:none">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label class="field-label" style="margin-bottom:8px">
                                                        <i class="fa fa-calendar-check-o" style="color:#e87d2a;margin-right:4px"></i>
                                                        Billing Date <span class="required-star">*</span>
                                                    </label>
                                                    <div class="bpcal" id="onetimeCal">
                                                        <div class="bpcal-header">
                                                            <button type="button" class="bpcal-nav" id="otPrev">&#8249;</button>
                                                            <div class="bpcal-title" id="otTitle"></div>
                                                            <button type="button" class="bpcal-nav" id="otNext">&#8250;</button>
                                                        </div>
                                                        <div class="bpcal-ympicker" id="otYmPicker">
                                                            <div class="bpcal-year-nav">
                                                                <button type="button" class="bpcal-year-btn" id="otYearDown">&#8249;</button>
                                                                <span id="otYearLabel"></span>
                                                                <button type="button" class="bpcal-year-btn" id="otYearUp">&#8250;</button>
                                                            </div>
                                                            <div class="bpcal-ym-grid" id="otMonthGrid"></div>
                                                        </div>
                                                        <div id="otCalBody">
                                                            <div class="bpcal-dow">
                                                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                                            </div>
                                                            <div class="bpcal-grid" id="otGrid"></div>
                                                        </div>
                                                        <div class="bpcal-footer">
                                                            <span class="bpcal-footer-label" id="otFooterLabel">No date selected</span>
                                                            <button type="button" class="bpcal-today-btn" id="otTodayBtn">Today</button>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="one_time_date" id="onetimeDateHidden" value="{{ old('one_time_date') }}">

                                                    {{-- Time picker --}}
                                                    <div class="time-picker-row">
                                                        <label><i class="fa fa-clock-o" style="color:#e87d2a"></i> Time:</label>
                                                        <select class="time-select" id="tHour">
                                                            @for($h=1;$h<=12;$h++)
                                                            <option value="{{ str_pad($h,2,'0',STR_PAD_LEFT) }}" {{ $h==8?'selected':'' }}>{{ str_pad($h,2,'0',STR_PAD_LEFT) }}</option>
                                                            @endfor
                                                        </select>
                                                        <span class="time-sep">:</span>
                                                        <select class="time-select" id="tMin">
                                                            @foreach(['00','05','10','15','20','25','30','35','40','45','50','55'] as $mn)
                                                            <option value="{{ $mn }}" {{ $mn=='00'?'selected':'' }}>{{ $mn }}</option>
                                                            @endforeach
                                                        </select>
                                                        <select class="time-select" id="tAmPm">
                                                            <option value="AM">AM</option>
                                                            <option value="PM">PM</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="field-label">
                                                        <i class="fa fa-clock-o" style="color:#4a8dc8;margin-right:4px"></i>
                                                        Due After (days)
                                                    </label>
                                                    <div class="due-days-stepper">
                                                        <button type="button" class="due-step-btn" id="dueDaysDownOt"><i class="fa fa-minus"></i></button>
                                                        <input type="number" name="due_days_onetime" id="dueDaysOnetime" class="due-days-input" min="0" max="365" value="{{ old('due_days',15) }}">
                                                        <button type="button" class="due-step-btn" id="dueDaysUpOt"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                    <p class="help-note" style="margin-top:6px">days after billing date</p>

                                                    <div class="sched-preview" id="onetimePreview" style="display:none; margin-top:14px">
                                                        <div class="sched-preview-title"><i class="fa fa-calendar-check-o"></i>&nbsp; Schedule</div>
                                                        <div id="onetimeDueDate"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ┌─ STEP 4: Fee Heads ──────────────────── --}}
                                <div class="bp-card step-green">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">4</span>
                                        <i class="fa fa-list-ul card-icon" style="color:#399743"></i>
                                        <h5>Fee Heads <small class="text-muted" style="font-size:11px; font-weight:400">— select all fees to include in this profile</small></h5>
                                        <span class="amount-preview-badge" id="totalFeeBadge">৳ 0</span>
                                    </div>
                                    <div class="bp-card-body" style="padding-bottom:0">

                                        <div style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
                                        <table class="fee-table" id="feeHeadTable" style="min-width:520px">
                                            <thead>
                                                <tr>
                                                    <th style="width:38%">Fee Head <span class="required-star">*</span></th>
                                                    <th style="width:22%">Amount Override (৳)</th>
                                                    <th style="width:16%">Default (৳)</th>
                                                    <th style="width:14%" class="text-center">Optional</th>
                                                    <th style="width:10%" class="text-center">Remove</th>
                                                </tr>
                                            </thead>
                                            <tbody id="feeHeadRows">
                                                <tr class="fee-head-row">
                                                    <td>
                                                        <select name="fee_head_id[]" class="form-control fee-head-select" required>
                                                            <option value="">— Select Fee Head —</option>
                                                            @foreach($fee_heads as $h)
                                                            <option value="{{ $h->id }}" data-amount="{{ $h->fee_head_amount ?? 0 }}">{{ $h->fee_head_title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="amount_override[]" class="form-control amount-override" min="0" step="0.01" placeholder="Override">
                                                    </td>
                                                    <td>
                                                        <span class="default-amount text-muted" style="font-size:12px">—</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="is_optional[]" value="1" title="Mark as optional">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-danger remove-fee-row" title="Remove row">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="fee-total-row">
                                                    <td colspan="2"><i class="fa fa-calculator"></i> &nbsp;Total Fee Amount</td>
                                                    <td colspan="3" id="feeGrandTotal">৳ 0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        </div>{{-- /overflow-x --}}

                                        <div style="padding: 12px 0 14px">
                                            <button type="button" class="btn btn-sm btn-success" id="addFeeHead">
                                                <i class="fa fa-plus"></i> Add Fee Head
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                {{-- ┌─ STEP 5: Fine Settings ──────────────── --}}
                                <div class="bp-card step-red">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">5</span>
                                        <i class="fa fa-exclamation-triangle card-icon" style="color:#c9423f"></i>
                                        <h5>Fine / Late Payment Policy</h5>
                                    </div>
                                    <div class="bp-card-body">

                                        <label class="field-label">Fine Type</label>
                                        <div class="fine-type-row" style="margin-bottom:14px">
                                            <div class="fine-type-btn">
                                                <input type="radio" name="fine_type" id="fine_none" value="none" {{ old('fine_type','none')==='none' ? 'checked':'' }}>
                                                <label for="fine_none"><i class="fa fa-ban"></i><br>No Fine</label>
                                            </div>
                                            <div class="fine-type-btn">
                                                <input type="radio" name="fine_type" id="fine_flat" value="flat" {{ old('fine_type')==='flat' ? 'checked':'' }}>
                                                <label for="fine_flat"><i class="fa fa-money"></i><br>Flat Amount</label>
                                            </div>
                                            <div class="fine-type-btn">
                                                <input type="radio" name="fine_type" id="fine_per_day" value="per_day" {{ old('fine_type')==='per_day' ? 'checked':'' }}>
                                                <label for="fine_per_day"><i class="fa fa-clock-o"></i><br>Per Day</label>
                                            </div>
                                        </div>

                                        <div id="fine_settings_group" style="display:none">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Fine Amount (৳)</label>
                                                        <input type="number" name="fine_amount" class="form-control" min="0" step="0.01" value="{{ old('fine_amount', 0) }}">
                                                        <p class="help-note" id="fineHelpText">Amount charged per day after grace period</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Grace Period (days)</label>
                                                        <input type="number" name="fine_grace_days" class="form-control" min="0" value="{{ old('fine_grace_days', 0) }}">
                                                        <p class="help-note">Fine starts after this many days</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Max Fine Cap (৳) <small class="text-muted">optional</small></label>
                                                        <input type="number" name="max_fine" class="form-control" min="0" step="0.01" value="{{ old('max_fine') }}" placeholder="No cap">
                                                        <p class="help-note">Fine will not exceed this amount</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ┌─ STEP 6: Installments ───────────────── --}}
                                <div class="bp-card step-purple">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">6</span>
                                        <i class="fa fa-calendar-o card-icon" style="color:#7b5ea7"></i>
                                        <h5>Installment Plan <small class="text-muted" style="font-size:11px; font-weight:400">— split the bill into multiple payments</small></h5>
                                    </div>
                                    <div class="bp-card-body">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="field-label">Number of Installments</label>
                                                    <input type="number" name="installment_count" class="form-control" min="1" max="12" id="installmentCount" value="{{ old('installment_count', 1) }}">
                                                    <p class="help-note">Set to 1 for full payment at once</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="installment_group" style="display:none">
                                            <label class="field-label">Split Percentages <small class="text-muted" style="font-weight:400">(must total 100%)</small></label>
                                            <div id="installmentSplitRows"></div>
                                            <div id="splitSumAlert" class="alert alert-warning" style="padding:6px 12px;font-size:12px;display:none">
                                                <i class="fa fa-warning"></i> Percentages must add up to exactly 100%
                                            </div>
                                            <div id="splitSumOk" class="alert alert-success" style="padding:6px 12px;font-size:12px;display:none">
                                                <i class="fa fa-check"></i> Percentages sum to 100% ✓
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ┌─ STEP 7: SMS Notification ───────────── --}}
                                <div class="bp-card step-indigo">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">7</span>
                                        <i class="fa fa-comment card-icon" style="color:#3d6bb8"></i>
                                        <h5>SMS Notification on Bill Generation</h5>
                                    </div>
                                    <div class="bp-card-body">

                                        <div class="form-group">
                                            <label class="field-label" style="font-weight:400; font-size:13px">
                                                <input type="checkbox" name="sms_on_generation" id="smsToggle" value="1" {{ old('sms_on_generation') ? 'checked' : '' }}>
                                                &nbsp;<strong>Send SMS automatically when bills are generated</strong>
                                            </label>
                                            <p class="help-note">An SMS will be sent to each student's mobile immediately after bills are created</p>
                                        </div>

                                        <div class="toggle-sms-wrap" id="smsSettingsBox">
                                            <hr class="section-divider">
                                            <div class="form-group">
                                                <label class="field-label">Alert Event Key</label>
                                                <input type="text" name="alert_event_key" class="form-control" style="max-width:260px"
                                                    value="{{ old('alert_event_key', 'BillingGenerated') }}" placeholder="BillingGenerated">
                                                <p class="help-note">Must match an Alert Setting event configured in the system</p>
                                            </div>

                                            <div class="form-group">
                                                <label class="field-label">SMS Message Template</label>
                                                <textarea name="sms_template" id="smsTemplate" class="form-control sms-template-area" rows="4"
                                                    placeholder="Dear {name}, your {period} bill of BDT {amount} has been generated. Due date: {due_date}. Reg: {reg_no} - Lalmai Institute">{{ old('sms_template') }}</textarea>
                                                <div class="sms-char-count"><span id="smsCharCount">0</span> characters</div>
                                                <p class="help-note">Click a variable below to insert it into the template</p>
                                                <div class="var-chips">
                                                    <span class="var-chip" data-var="{name}">&#123;name&#125;</span>
                                                    <span class="var-chip" data-var="{reg_no}">&#123;reg_no&#125;</span>
                                                    <span class="var-chip" data-var="{amount}">&#123;amount&#125;</span>
                                                    <span class="var-chip" data-var="{due_date}">&#123;due_date&#125;</span>
                                                    <span class="var-chip" data-var="{period}">&#123;period&#125;</span>
                                                    <span class="var-chip" data-var="{fee_heads}">&#123;fee_heads&#125;</span>
                                                    <span class="var-chip" data-var="{school}">&#123;school&#125;</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ┌─ ACTIONS ────────────────────────────── --}}
                                <div class="bp-card">
                                    <div class="bp-card-body">
                                        <div class="bp-actions" style="position:static; margin:0; padding:0; border:0; background:transparent">
                                            <button type="submit" class="btn btn-success btn-lg" style="padding: 9px 32px; font-size:14px; font-weight:700">
                                                <i class="fa fa-save"></i>&nbsp; Save Billing Profile
                                            </button>
                                            <a href="{{ route('account.fees.billing-profile') }}" class="btn btn-default btn-lg" style="padding: 9px 24px">
                                                <i class="fa fa-times"></i>&nbsp; Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>{{-- /col-md-8 --}}

                            {{-- ═══════════════════════════════════════════════
                                 RIGHT COLUMN  (4/12)  — Summary sidebar
                            ════════════════════════════════════════════════ --}}
                            <div class="col-md-4 col-xs-12 hidden-sm hidden-xs">
                                <div class="bp-summary" id="profileSummary">
                                    <h6><i class="fa fa-eye"></i>&nbsp; Profile Summary</h6>

                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Profile Name</span>
                                        <span class="bp-sum-val" id="sum_name" style="color:#888">—</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Scope</span>
                                        <span class="bp-sum-val" id="sum_scope">All Students</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Cycle</span>
                                        <span class="bp-sum-val" id="sum_cycle">Monthly</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Billing Day</span>
                                        <span class="bp-sum-val" id="sum_day">Day 1</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Due After</span>
                                        <span class="bp-sum-val" id="sum_due">15 days</span>
                                    </div>

                                    <hr class="bp-sum-divider">

                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Fee Heads</span>
                                        <span class="bp-sum-val" id="sum_fee_count">0 added</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Total Bill Amount</span>
                                        <span class="bp-sum-val bp-sum-total" id="sum_total">৳ 0</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Installments</span>
                                        <span class="bp-sum-val" id="sum_installments">1 (full payment)</span>
                                    </div>

                                    <hr class="bp-sum-divider">

                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Fine Type</span>
                                        <span class="bp-sum-val" id="sum_fine">None</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">SMS on Generate</span>
                                        <span class="bp-sum-val" id="sum_sms"><span class="label label-default">Off</span></span>
                                    </div>

                                    <hr class="bp-sum-divider">

                                    {{-- Quick guide --}}
                                    <div style="font-size:11px; color:#7a8c9e; line-height:1.6">
                                        <strong style="color:#4a5568">Next Steps after saving:</strong><br>
                                        ① View profile in Billing Profiles list<br>
                                        ② Run manually or wait for auto-schedule<br>
                                        ③ Monitor results in Billing Runs log
                                    </div>
                                </div>
                            </div>{{-- /col-md-4 --}}

                        </div>{{-- /row --}}
                        </div>{{-- /bp-form-wrap --}}
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(function () {

    /* ─── Label maps (must be defined before any updateSummary() calls) ─── */
    var scopeLabels = { all: 'All Students', faculty: 'By Faculty', semester: 'By Class', batch: 'By Batch' };
    var cycleLabels = { monthly: 'Monthly', quarterly: 'Quarterly', half_yearly: 'Half-Yearly', yearly: 'Yearly', one_time: 'One-Time' };
    var fineLabels  = { none: 'None', flat: 'Flat Amount', per_day: 'Per Day' };

    /* ─────────────────────────────────────────────
       SCOPE TILES
    ───────────────────────────────────────────── */
    function updateScope() {
        var v = $('input[name="scope_type"]:checked').val();
        $('#scopeFaculty, #scopeSemester, #scopeBatch').hide();
        if (v === 'faculty')  $('#scopeFaculty').slideDown(180);
        if (v === 'semester') $('#scopeSemester').slideDown(180);
        if (v === 'batch')    $('#scopeBatch').slideDown(180);
        updateSummary();
    }
    $('input[name="scope_type"]').on('change', updateScope);
    updateScope();

    /* ══════════════════════════════════════════════
       FULL CALENDAR WIDGET
    ══════════════════════════════════════════════ */
    var MN  = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var MNA = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    function fmtDate(d) { return d.getDate() + ' ' + MNA[d.getMonth()] + ' ' + d.getFullYear(); }
    function fmtYMD(d)  { return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0'); }

    /* ── Generic calendar builder ── */
    function BpCal(cfg) {
        /* cfg: gridId, titleId, prevId, nextId, footerLabelId, todayBtnId,
                ymPickerId, ymGridId, yearLabelId, yearDownId, yearUpId,
                calBodyId, onSelect, selectedDate, minDate */
        var c = cfg;
        var today = new Date(); today.setHours(0,0,0,0);
        var vYear  = today.getFullYear();
        var vMonth = today.getMonth();
        var sel    = c.selectedDate ? new Date(c.selectedDate) : null;
        var ymOpen = false;
        var ymYear = vYear;

        function renderGrid() {
            var first = new Date(vYear, vMonth, 1);
            var last  = new Date(vYear, vMonth+1, 0);
            var dow   = first.getDay(); /* 0=Sun */
            var html  = '';

            /* leading empties */
            for (var i=0; i<dow; i++) {
                html += '<div class="bpcal-day bpcal-empty"></div>';
            }
            /* days of this month */
            for (var d=1; d<=last.getDate(); d++) {
                var dt   = new Date(vYear, vMonth, d);
                var cls  = 'bpcal-day';
                if (dt.getTime() === today.getTime()) cls += ' bpcal-today';
                if (sel && dt.getTime() === sel.getTime()) cls += ' bpcal-selected';
                if (c.minDate && dt < c.minDate) cls += ' bpcal-other';
                else cls += ' bpcal-pick';
                html += '<div class="'+cls+'" data-y="'+vYear+'" data-m="'+vMonth+'" data-d="'+d+'">'+d+'</div>';
            }
            /* trailing empties */
            var rem = (dow + last.getDate()) % 7;
            if (rem) { for (var j=0; j<(7-rem); j++) html += '<div class="bpcal-day bpcal-other"></div>'; }

            $('#'+c.gridId).html(html);
            $('#'+c.titleId).text(MN[vMonth] + ' ' + vYear);
        }

        function renderYmPicker() {
            var mHtml = '';
            for (var mi=0; mi<12; mi++) {
                var cls = 'bpcal-ym-cell' + (mi === vMonth && ymYear === vYear ? ' active' : '');
                mHtml += '<div class="'+cls+'" data-mi="'+mi+'">'+MNA[mi]+'</div>';
            }
            $('#'+c.ymGridId).html(mHtml);
            $('#'+c.yearLabelId).text(ymYear);
        }

        function openYm() {
            ymOpen = true; ymYear = vYear;
            renderYmPicker();
            $('#'+c.ymPickerId).addClass('show');
            $('#'+c.calBodyId).hide();
        }
        function closeYm() {
            ymOpen = false;
            $('#'+c.ymPickerId).removeClass('show');
            $('#'+c.calBodyId).show();
        }

        /* ── Events ── */
        $('#'+c.prevId).on('click', function() {
            if (vMonth===0) { vMonth=11; vYear--; } else vMonth--;
            renderGrid();
        });
        $('#'+c.nextId).on('click', function() {
            if (vMonth===11) { vMonth=0; vYear++; } else vMonth++;
            renderGrid();
        });
        $('#'+c.titleId).on('click', function() {
            ymOpen ? closeYm() : openYm();
        });
        $('#'+c.yearDownId).on('click', function() { ymYear--; renderYmPicker(); });
        $('#'+c.yearUpId).on('click', function()   { ymYear++; renderYmPicker(); });

        /* Month cell click in picker */
        $('#'+c.ymGridId).on('click', '.bpcal-ym-cell', function() {
            vMonth = parseInt($(this).data('mi'));
            vYear  = ymYear;
            closeYm(); renderGrid();
        });

        /* Day cell click */
        $('#'+c.gridId).on('click', '.bpcal-pick', function() {
            var y = parseInt($(this).data('y'));
            var m = parseInt($(this).data('m'));
            var d = parseInt($(this).data('d'));
            sel = new Date(y, m, d);
            renderGrid();
            if (c.footerLabelId) $('#'+c.footerLabelId).text(fmtDate(sel));
            if (c.onSelect) c.onSelect(sel);
        });

        /* Today button */
        $('#'+c.todayBtnId).on('click', function() {
            vYear = today.getFullYear(); vMonth = today.getMonth();
            sel = new Date(today);
            renderGrid();
            if (c.footerLabelId) $('#'+c.footerLabelId).text(fmtDate(sel));
            if (c.onSelect) c.onSelect(sel);
        });

        /* Initial render */
        renderGrid();

        return { getSelected: function() { return sel; } };
    }

    /* ─────────────────────────────────────────────
       BILLING CYCLE show/hide
    ───────────────────────────────────────────── */
    function updateCycle() {
        var v = $('input[name="billing_cycle"]:checked').val();
        if (v === 'one_time') {
            $('#billingDayGroup').slideUp(180);
            $('#one_time_date_group').slideDown(180);
            $('#billing_months_group').slideUp(180);
        } else {
            $('#billingDayGroup').slideDown(180);
            $('#one_time_date_group').slideUp(180);
            $('#billing_months_group').toggle(v === 'half_yearly' || v === 'yearly');
        }
        refreshSchedulePreview();
        updateSummary();
    }
    $('input[name="billing_cycle"]').on('change', updateCycle);
    updateCycle();

    /* ── Recurring calendar instance ── */
    var initDay = parseInt('{{ old("billing_day",1) }}') || 1;
    var initRecurDate = new Date(); initRecurDate.setDate(initDay);
    var recurCal = BpCal({
        gridId:'recurGrid', titleId:'recurTitle',
        prevId:'recurPrev', nextId:'recurNext',
        footerLabelId: null,
        todayBtnId:'recurTodayBtn',
        ymPickerId:'recurYmPicker', ymGridId:'recurMonthGrid',
        yearLabelId:'recurYearLabel', yearDownId:'recurYearDown', yearUpId:'recurYearUp',
        calBodyId:'recurCalBody',
        selectedDate: initRecurDate,
        onSelect: function(d) {
            var day = d.getDate();
            $('#billingDayHidden').val(day);
            $('#selectedDayLabel').text(day);
            refreshSchedulePreview();
            updateSummary();
        }
    });
    $('#billingDayHidden').val(initDay);
    $('#selectedDayLabel').text(initDay);

    /* ── One-time calendar instance ── */
    var initOtVal = '{{ old("one_time_date","") }}';
    var initOtDate = initOtVal ? new Date(initOtVal) : null;
    var otCal = BpCal({
        gridId:'otGrid', titleId:'otTitle',
        prevId:'otPrev', nextId:'otNext',
        footerLabelId:'otFooterLabel',
        todayBtnId:'otTodayBtn',
        ymPickerId:'otYmPicker', ymGridId:'otMonthGrid',
        yearLabelId:'otYearLabel', yearDownId:'otYearDown', yearUpId:'otYearUp',
        calBodyId:'otCalBody',
        selectedDate: initOtDate,
        minDate: new Date(new Date().setHours(0,0,0,0)),
        onSelect: function(d) {
            var val = fmtYMD(d);
            $('#onetimeDateHidden').val(val);
            updateOnetimePreview();
            updateSummary();
        }
    });
    if (initOtDate) { $('#otFooterLabel').text(fmtDate(initOtDate)); }

    /* ── Due-days stepper (recurring) ── */
    $('#dueDaysDown').on('click', function() {
        $('#dueDaysInput').val(Math.max(0, parseInt($('#dueDaysInput').val()||0)-1));
        refreshSchedulePreview(); updateSummary();
    });
    $('#dueDaysUp').on('click', function() {
        $('#dueDaysInput').val(Math.min(365, parseInt($('#dueDaysInput').val()||0)+1));
        refreshSchedulePreview(); updateSummary();
    });
    $('#dueDaysInput').on('input', function() { refreshSchedulePreview(); updateSummary(); });

    /* ── Due-days stepper (one-time) ── */
    $('#dueDaysDownOt').on('click', function() {
        $('#dueDaysOnetime').val(Math.max(0, parseInt($('#dueDaysOnetime').val()||0)-1));
        updateOnetimePreview();
    });
    $('#dueDaysUpOt').on('click', function() {
        $('#dueDaysOnetime').val(Math.min(365, parseInt($('#dueDaysOnetime').val()||0)+1));
        updateOnetimePreview();
    });
    $('#dueDaysOnetime, #tHour, #tMin, #tAmPm').on('change input', updateOnetimePreview);

    /* ── One-time preview ── */
    function updateOnetimePreview() {
        var dateStr = $('#onetimeDateHidden').val();
        var dueDays = parseInt($('#dueDaysOnetime').val()) || 0;
        var h = $('#tHour').val(), mn = $('#tMin').val(), ap = $('#tAmPm').val();
        if (!dateStr) { $('#onetimePreview').hide(); return; }
        var billing = new Date(dateStr);
        var due = new Date(billing); due.setDate(due.getDate() + dueDays);
        $('#onetimeDueDate').html(
            '<div class="sched-date-row"><span class="sched-date-pill">&#9889; ' + fmtDate(billing) + ' ' + h+':'+mn+' '+ap + '</span></div>' +
            '<div class="sched-date-row"><span class="sched-arrow">&#8594;</span><span class="sched-due-pill">&#10003; Due ' + fmtDate(due) + '</span></div>'
        );
        $('#onetimePreview').show();
    }
    if (initOtVal) updateOnetimePreview();

    /* ── Schedule preview (recurring) ── */
    function refreshSchedulePreview() {
        var cycle = $('input[name="billing_cycle"]:checked').val() || 'monthly';
        var day   = parseInt($('#billingDayHidden').val()) || 1;
        var due   = parseInt($('#dueDaysInput').val()) || 0;
        if (cycle === 'one_time') return;

        var selMonths = [];
        $('input[name="billing_months[]"]:checked').each(function() {
            selMonths.push(parseInt($(this).val()) - 1);
        });

        var now    = new Date(); now.setHours(0,0,0,0);
        var y = now.getFullYear(), m = now.getMonth();
        var found  = [], iter = 0;
        while (found.length < 3 && iter < 60) {
            iter++;
            var maxDay = new Date(y, m+1, 0).getDate();
            var d = Math.min(day, maxDay);
            var candidate = new Date(y, m, d);
            var valid = false;
            if (cycle === 'monthly')    valid = true;
            else if (cycle === 'quarterly') valid = [0,3,6,9].indexOf(m) !== -1;
            else if (cycle === 'half_yearly' || cycle === 'yearly') valid = selMonths.indexOf(m) !== -1;
            if (valid && candidate > now) found.push(candidate);
            if (++m > 11) { m=0; y++; }
        }

        var html = '';
        found.forEach(function(bd) {
            var dd = new Date(bd); dd.setDate(dd.getDate()+due);
            html += '<div class="sched-date-row">' +
                    '<span class="sched-date-pill">&#9889; '+fmtDate(bd)+'</span>' +
                    '<span class="sched-arrow">&#8594;</span>' +
                    '<span class="sched-due-pill">&#10003; Due '+fmtDate(dd)+'</span>' +
                    '</div>';
        });
        if (!html) html = '<div style="font-size:11px;color:#9aabbc">Select months to see preview</div>';
        $('#schedDates').html(html);
    }

    $(document).on('change', 'input[name="billing_months[]"]', function() {
        refreshSchedulePreview(); updateSummary();
    });
    refreshSchedulePreview();

    /* ─────────────────────────────────────────────
       FINE TYPE
    ───────────────────────────────────────────── */
    function updateFine() {
        var v = $('input[name="fine_type"]:checked').val();
        if (v === 'none') {
            $('#fine_settings_group').slideUp(180);
        } else {
            $('#fine_settings_group').slideDown(180);
            $('#fineHelpText').text(v === 'per_day' ? 'Amount charged per day after grace period' : 'Fixed amount charged once after grace period');
        }
        updateSummary();
    }
    $('input[name="fine_type"]').on('change', updateFine);
    updateFine();

    /* ─────────────────────────────────────────────
       SMS TOGGLE
    ───────────────────────────────────────────── */
    $('#smsToggle').on('change', function () {
        if ($(this).is(':checked')) {
            $('#smsSettingsBox').slideDown(200);
        } else {
            $('#smsSettingsBox').slideUp(200);
        }
        updateSummary();
    });
    if ($('#smsToggle').is(':checked')) $('#smsSettingsBox').show();

    /* ─────────────────────────────────────────────
       SMS CHAR COUNTER
    ───────────────────────────────────────────── */
    $('#smsTemplate').on('input', function () {
        $('#smsCharCount').text($(this).val().length);
    });

    /* ─────────────────────────────────────────────
       VARIABLE CHIP INSERTION
    ───────────────────────────────────────────── */
    $(document).on('click', '.var-chip', function () {
        var v    = $(this).data('var');
        var $ta  = $('#smsTemplate');
        var pos  = $ta[0].selectionStart;
        var cur  = $ta.val();
        $ta.val(cur.substring(0, pos) + v + cur.substring(pos));
        $ta.focus();
        var newPos = pos + v.length;
        $ta[0].setSelectionRange(newPos, newPos);
        $('#smsCharCount').text($ta.val().length);
    });

    /* ─────────────────────────────────────────────
       INSTALLMENT SPLITS
    ───────────────────────────────────────────── */
    function updateInstallments() {
        var count = parseInt($('#installmentCount').val()) || 1;
        var $grp  = $('#installment_group');
        var $rows = $('#installmentSplitRows');
        $rows.empty();
        if (count > 1) {
            $grp.slideDown(180);
            var def = Math.floor(100 / count);
            for (var i = 1; i <= count; i++) {
                var pct = (i === count) ? (100 - def * (count - 1)) : def;
                $rows.append(
                    '<div class="split-row">' +
                    '<span class="split-label"><i class="fa fa-circle-o" style="color:#7b5ea7;margin-right:5px"></i> Installment ' + i + '</span>' +
                    '<input type="number" name="installment_splits[]" class="form-control split-pct" style="width:90px" min="0" max="100" value="' + pct + '" placeholder="' + pct + '">' +
                    '<span class="split-total-indicator">%</span>' +
                    '</div>'
                );
            }
            checkSplitSum();
        } else {
            $grp.slideUp(180);
        }
        updateSummary();
    }
    $('#installmentCount').on('input change', updateInstallments);
    updateInstallments();

    function checkSplitSum() {
        var total = 0;
        $('.split-pct').each(function () { total += parseFloat($(this).val()) || 0; });
        if ($('.split-pct').length === 0) return;
        if (Math.round(total) === 100) {
            $('#splitSumAlert').hide();
            $('#splitSumOk').show();
        } else {
            $('#splitSumAlert').show();
            $('#splitSumOk').hide();
        }
    }
    $(document).on('input', '.split-pct', checkSplitSum);

    /* ─────────────────────────────────────────────
       FEE HEADS — auto-fill default amount
    ───────────────────────────────────────────── */
    $(document).on('change', '.fee-head-select', function () {
        var $opt    = $(this).find('option:selected');
        var amount  = parseFloat($opt.data('amount')) || 0;
        var $row    = $(this).closest('tr');
        var $over   = $row.find('.amount-override');
        var $def    = $row.find('.default-amount');
        if (amount > 0) {
            $over.val(amount.toFixed(2));
            $over.attr('placeholder', '৳ ' + amount.toFixed(2));
            $def.text('৳ ' + amount.toFixed(2)).css('color', '#399743');
        } else {
            $over.val('');
            $over.attr('placeholder', 'Override');
            $def.text('—').css('color', '#aaa');
        }
        recalcFeeTotal();
    });

    $(document).on('input', '.amount-override', recalcFeeTotal);

    function getRowAmount($row) {
        var overrideVal = parseFloat($row.find('.amount-override').val());
        if (!isNaN(overrideVal) && overrideVal > 0) return overrideVal;
        var $opt = $row.find('.fee-head-select option:selected');
        return parseFloat($opt.data('amount')) || 0;
    }

    function recalcFeeTotal() {
        var total = 0;
        $('#feeHeadRows tr.fee-head-row').each(function () {
            total += getRowAmount($(this));
        });
        var fmt = '৳ ' + total.toFixed(2);
        $('#feeGrandTotal').text(fmt);
        $('#totalFeeBadge').text(fmt);
        updateSummary();
    }

    /* ─────────────────────────────────────────────
       FEE HEAD ROW — add / remove
    ───────────────────────────────────────────── */
    function buildFeeRow() {
        var opts = $('#feeHeadTable').find('.fee-head-select:first').html();
        return $(
            '<tr class="fee-head-row">' +
            '<td><select name="fee_head_id[]" class="form-control fee-head-select" required>' + opts + '</select></td>' +
            '<td><input type="number" name="amount_override[]" class="form-control amount-override" min="0" step="0.01" placeholder="Override"></td>' +
            '<td><span class="default-amount text-muted" style="font-size:12px">—</span></td>' +
            '<td class="text-center"><input type="checkbox" name="is_optional[]" value="1" title="Mark as optional"></td>' +
            '<td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-fee-row" title="Remove row"><i class="fa fa-trash"></i></button></td>' +
            '</tr>'
        );
    }

    $('#addFeeHead').on('click', function () {
        $('#feeHeadRows').append(buildFeeRow());
        updateSummary();
    });

    $(document).on('click', '.remove-fee-row', function () {
        if ($('#feeHeadRows tr.fee-head-row').length > 1) {
            $(this).closest('tr').remove();
            recalcFeeTotal();
        } else {
            alert('At least one fee head is required.');
        }
    });

    /* ─────────────────────────────────────────────
       SUMMARY SIDEBAR
    ───────────────────────────────────────────── */
    function updateSummary() {
        var name  = $('#profileName').val() || '—';
        var scope = $('input[name="scope_type"]:checked').val() || 'all';
        var cycle = $('input[name="billing_cycle"]:checked').val() || 'monthly';
        var day   = $('#billingDayHidden').val() || $('input[name="billing_day"]').val();
        var due   = $('#dueDaysInput').val() || $('input[name="due_days"]').first().val();
        var fine  = $('input[name="fine_type"]:checked').val() || 'none';
        var count = parseInt($('#installmentCount').val()) || 1;
        var feeCount = $('#feeHeadRows tr.fee-head-row').length;
        var sms   = $('#smsToggle').is(':checked');

        $('#sum_name').text(name).css('color', name === '—' ? '#aaa' : '#3a4557');
        $('#sum_scope').text(scopeLabels[scope] || scope);
        $('#sum_cycle').text(cycleLabels[cycle] || cycle);
        $('#sum_day').text(cycle === 'one_time' ? 'One-time date' : 'Day ' + (day || '—'));
        $('#sum_due').text((due || '15') + ' days after billing');
        $('#sum_fine').text(fineLabels[fine] || fine);
        $('#sum_fee_count').text(feeCount + ' head' + (feeCount !== 1 ? 's' : ''));
        $('#sum_installments').text(count > 1 ? count + ' installments' : '1 (full payment)');
        $('#sum_total').text($('#feeGrandTotal').text());
        $('#sum_sms').html(sms
            ? '<span class="label label-success"><i class="fa fa-check"></i> On</span>'
            : '<span class="label label-default">Off</span>');
    }

    /* Live updates for summary */
    $('#profileName').on('input', updateSummary);
    updateSummary();

    /* ─────────────────────────────────────────────
       SYNC one_time due_days → main due_days field
    ───────────────────────────────────────────── */
    function syncDueDays() {
        /* billing_day hidden input is always up-to-date via day cell clicks */
        if ($('input[name="billing_cycle"]:checked').val() === 'one_time') {
            $('input[name="due_days"]').val($('#dueDaysOnetime').val() || 15);
        } else {
            $('input[name="due_days"]').val($('#dueDaysInput').val() || 15);
        }
    }

    /* ─────────────────────────────────────────────
       FORM SUBMIT VALIDATION
    ───────────────────────────────────────────── */
    $('#billingProfileForm').on('submit', function (e) {
        syncDueDays();
        var ok = true;

        /* Profile name */
        if (!$('#profileName').val().trim()) {
            alert('Profile name is required.'); ok = false;
        }

        /* At least one fee head selected */
        var hasHead = false;
        $('.fee-head-select').each(function () { if ($(this).val()) hasHead = true; });
        if (!hasHead) {
            alert('Please select at least one fee head.'); ok = false;
        }

        /* Installment sum */
        if (parseInt($('#installmentCount').val()) > 1) {
            var sum = 0;
            $('.split-pct').each(function () { sum += parseFloat($(this).val()) || 0; });
            if (Math.round(sum) !== 100) {
                alert('Installment percentages must sum to exactly 100%.'); ok = false;
            }
        }

        if (!ok) e.preventDefault();
    });

    /* Initial recalc */
    recalcFeeTotal();

});
</script>
@endsection
