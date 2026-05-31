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

/* ── Edit-mode banner ─────────────────────────────────────── */
.edit-banner {
    background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%);
    border: 1px solid #ffc107;
    border-radius: 6px;
    padding: 10px 16px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #856404;
}
.edit-banner i { font-size: 16px; color: #f0ad4e; }

/* ── Misc helpers ─────────────────────────────────────────── */
.required-star { color: #d9534f; }
.field-label { font-size: 12px; font-weight: 700; color: #4a5568; margin-bottom: 5px; display: block; }
.help-note { font-size: 11px; color: #8a9bb0; margin-top: 3px; }
.section-divider { border: 0; border-top: 1px solid #eef1f5; margin: 14px 0; }
.input-icon-wrap { position: relative; }
.input-icon-wrap .fa { position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #b0bdc9; pointer-events: none; }
.sms-char-count { font-size: 11px; color: #8a9bb0; text-align: right; margin-top: 3px; }
.toggle-sms-wrap { display: none; margin-top: 12px; }

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
                    <small><i class="ace-icon fa fa-angle-double-right"></i> Edit Profile</small>
                </h1>
            </div>

            <div class="row">
                @include('account.includes.buttons')
                <div class="col-xs-12">
                    @include('account.fees.includes.buttons')
                    @include('includes.flash_messages')
                    @include('includes.validation_error_messages')

                    {{-- Edit mode banner --}}
                    <div class="edit-banner">
                        <i class="fa fa-pencil-square-o"></i>
                        <span>Editing: <strong>{{ $profile->profile_name }}</strong> &mdash; ID #{{ $profile->id }}</span>
                        <span class="label {{ $profile->status ? 'label-success' : 'label-danger' }}" style="margin-left:auto">
                            {{ $profile->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('account.fees.billing-profile.update', $profile->id) }}" id="billingProfileForm" novalidate>
                        @csrf
                        @method('POST')

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
                                                        value="{{ old('profile_name', $profile->profile_name) }}"
                                                        placeholder="e.g., Class 10 Monthly Tuition Fee"
                                                        required maxlength="200"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="field-label">Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" {{ old('status', $profile->status) == 1 ? 'selected' : '' }}>Active</option>
                                                        <option value="0" {{ old('status', $profile->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label">Description <small class="text-muted" style="font-weight:400">(optional)</small></label>
                                            <textarea name="description" class="form-control" rows="2"
                                                placeholder="Add notes about this billing profile…">{{ old('description', $profile->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- ┌─ STEP 2: Student Scope ──────────────── --}}
                                @php $currentScope = old('scope_type', $profile->scope_type); @endphp
                                <div class="bp-card step-teal">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">2</span>
                                        <i class="fa fa-users card-icon" style="color:#25a89b"></i>
                                        <h5>Student Scope — Who will be billed?</h5>
                                    </div>
                                    <div class="bp-card-body">

                                        <label class="field-label">Apply To <span class="required-star">*</span></label>
                                        <div class="scope-tiles">
                                            <div class="scope-tile">
                                                <input type="radio" name="scope_type" id="scope_all" value="all" {{ $currentScope === 'all' ? 'checked' : '' }}>
                                                <label for="scope_all"><i class="fa fa-globe" style="color:#25a89b"></i>All Students</label>
                                            </div>
                                            <div class="scope-tile">
                                                <input type="radio" name="scope_type" id="scope_faculty" value="faculty" {{ $currentScope === 'faculty' ? 'checked' : '' }}>
                                                <label for="scope_faculty"><i class="fa fa-building" style="color:#4a8dc8"></i>By Faculty</label>
                                            </div>
                                            <div class="scope-tile">
                                                <input type="radio" name="scope_type" id="scope_semester" value="semester" {{ $currentScope === 'semester' ? 'checked' : '' }}>
                                                <label for="scope_semester"><i class="fa fa-graduation-cap" style="color:#e87d2a"></i>By Class</label>
                                            </div>
                                            <div class="scope-tile">
                                                <input type="radio" name="scope_type" id="scope_batch" value="batch" {{ $currentScope === 'batch' ? 'checked' : '' }}>
                                                <label for="scope_batch"><i class="fa fa-th-large" style="color:#7b5ea7"></i>By Batch</label>
                                            </div>
                                        </div>

                                        <div id="scopeFaculty" class="scope-sub" style="display:{{ $currentScope === 'faculty' ? 'block' : 'none' }}">
                                            <label class="field-label">Select Faculty <span class="required-star">*</span></label>
                                            <select name="faculty_id" class="form-control">
                                                <option value="">— Select Faculty —</option>
                                                @foreach($faculties as $f)
                                                <option value="{{ $f->id }}" {{ old('faculty_id', $profile->faculty_id) == $f->id ? 'selected' : '' }}>{{ $f->faculty }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="scopeSemester" class="scope-sub" style="display:{{ $currentScope === 'semester' ? 'block' : 'none' }}">
                                            <label class="field-label">Select Class / Semester <span class="required-star">*</span></label>
                                            <select name="semester_id" class="form-control">
                                                <option value="">— Select Semester —</option>
                                                @foreach($semesters as $s)
                                                <option value="{{ $s->id }}" {{ old('semester_id', $profile->semester_id) == $s->id ? 'selected' : '' }}>{{ $s->semester }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="scopeBatch" class="scope-sub" style="display:{{ $currentScope === 'batch' ? 'block' : 'none' }}">
                                            <label class="field-label">Select Batch <span class="required-star">*</span></label>
                                            <select name="batch_id" class="form-control">
                                                <option value="">— Select Batch —</option>
                                                @foreach($batches as $b)
                                                <option value="{{ $b->id }}" {{ old('batch_id', $profile->batch_id) == $b->id ? 'selected' : '' }}>{{ $b->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <hr class="section-divider">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="field-label" style="font-weight:400">
                                                    <input type="checkbox" name="only_active_students" value="1" {{ old('only_active_students', $profile->only_active_students) ? 'checked' : '' }}>
                                                    &nbsp;Only Active Students
                                                </label>
                                                <p class="help-note">Skip inactive / withdrawn students</p>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="field-label" style="font-weight:400">
                                                    <input type="checkbox" name="only_regular_status" value="1" {{ old('only_regular_status', $profile->only_regular_status) ? 'checked' : '' }}>
                                                    &nbsp;Only Regular Status
                                                </label>
                                                <p class="help-note">Skip suspended / on-hold students</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ┌─ STEP 3: Billing Cycle ──────────────── --}}
                                @php
                                    $currentCycle  = old('billing_cycle', $profile->billing_cycle);
                                    $currentMonths = old('billing_months', $profile->billing_months ?? []);
                                @endphp
                                <div class="bp-card step-orange">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">3</span>
                                        <i class="fa fa-calendar card-icon" style="color:#e87d2a"></i>
                                        <h5>Billing Cycle &amp; Schedule</h5>
                                    </div>
                                    <div class="bp-card-body">

                                        <label class="field-label">Cycle Type <span class="required-star">*</span></label>
                                        <div class="cycle-tiles">
                                            @foreach($cycles as $key => $label)
                                            <div class="cycle-tile">
                                                <input type="radio" name="billing_cycle" id="cycle_{{ $key }}" value="{{ $key }}"
                                                    {{ $currentCycle === $key ? 'checked' : '' }}>
                                                <label for="cycle_{{ $key }}">{{ $label }}</label>
                                            </div>
                                            @endforeach
                                        </div>

                                        {{-- billing day (hidden for one_time) --}}
                                        <div id="billingDayGroup" style="display:{{ $currentCycle === 'one_time' ? 'none' : 'block' }}">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Generate on Day of Month <span class="required-star">*</span></label>
                                                        <div class="input-icon-wrap">
                                                            <input type="number" name="billing_day" class="form-control"
                                                                min="1" max="28" value="{{ old('billing_day', $profile->billing_day) }}" placeholder="1 – 28">
                                                            <i class="fa fa-calendar-o"></i>
                                                        </div>
                                                        <p class="help-note">Bills auto-generated on this day each cycle</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Due After (days) <span class="required-star">*</span></label>
                                                        <div class="input-icon-wrap">
                                                            <input type="number" name="due_days" class="form-control"
                                                                min="0" value="{{ old('due_days', $profile->due_days) }}">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>
                                                        <p class="help-note">Days after billing date before due</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Month selector for half_yearly / yearly --}}
                                        <div id="billing_months_group" style="display:{{ in_array($currentCycle, ['half_yearly','yearly']) ? 'block' : 'none' }}">
                                            <label class="field-label">Generate in Which Months? <span class="required-star">*</span></label>
                                            <div class="month-grid">
                                                @foreach($months as $num => $name)
                                                <div class="month-chip">
                                                    <input type="checkbox" name="billing_months[]" id="month_{{ $num }}" value="{{ $num }}"
                                                        {{ in_array($num, $currentMonths) ? 'checked' : '' }}>
                                                    <label for="month_{{ $num }}">{{ substr($name, 0, 3) }}</label>
                                                </div>
                                                @endforeach
                                            </div>
                                            <p class="help-note" style="margin-top:6px">Select 2 months for Half-Yearly, 1 for Yearly</p>
                                        </div>

                                        {{-- One-time date --}}
                                        <div id="one_time_date_group" style="display:{{ $currentCycle === 'one_time' ? 'block' : 'none' }}">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Billing Date <span class="required-star">*</span></label>
                                                        <input type="date" name="one_time_date" class="form-control"
                                                            value="{{ old('one_time_date', $profile->one_time_date ? \Carbon\Carbon::parse($profile->one_time_date)->format('Y-m-d') : '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Due After (days)</label>
                                                        <div class="input-icon-wrap">
                                                            <input type="number" name="due_days_onetime" id="dueDaysOnetime" class="form-control"
                                                                min="0" value="{{ old('due_days', $profile->due_days) }}">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>
                                                        <p class="help-note">Days after billing date before due</p>
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

                                                @forelse($profile->profileItems as $item)
                                                <tr class="fee-head-row">
                                                    <td>
                                                        <select name="fee_head_id[]" class="form-control fee-head-select" required>
                                                            <option value="">— Select Fee Head —</option>
                                                            @foreach($fee_heads as $h)
                                                            <option value="{{ $h->id }}"
                                                                data-amount="{{ $h->fee_head_amount ?? 0 }}"
                                                                {{ $h->id == $item->fee_head_id ? 'selected' : '' }}>
                                                                {{ $h->fee_head_title }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="amount_override[]"
                                                            class="form-control amount-override"
                                                            min="0" step="0.01"
                                                            value="{{ $item->amount_override }}"
                                                            placeholder="Override">
                                                    </td>
                                                    <td>
                                                        @php $defAmt = $item->feeHead->fee_head_amount ?? 0; @endphp
                                                        <span class="default-amount" style="font-size:12px; color:{{ $defAmt > 0 ? '#399743' : '#aaa' }}">
                                                            {{ $defAmt > 0 ? '৳ '.number_format($defAmt, 2) : '—' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="is_optional[]" value="1" {{ $item->is_optional ? 'checked' : '' }} title="Mark as optional">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-danger remove-fee-row" title="Remove row">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr class="fee-head-row">
                                                    <td>
                                                        <select name="fee_head_id[]" class="form-control fee-head-select" required>
                                                            <option value="">— Select Fee Head —</option>
                                                            @foreach($fee_heads as $h)
                                                            <option value="{{ $h->id }}" data-amount="{{ $h->fee_head_amount ?? 0 }}">{{ $h->fee_head_title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="amount_override[]" class="form-control amount-override" min="0" step="0.01" placeholder="Override"></td>
                                                    <td><span class="default-amount text-muted" style="font-size:12px">—</span></td>
                                                    <td class="text-center"><input type="checkbox" name="is_optional[]" value="1"></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-danger remove-fee-row" title="Remove row"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                @endforelse

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
                                @php $currentFineType = old('fine_type', $profile->fine_type ?? 'none'); @endphp
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
                                                <input type="radio" name="fine_type" id="fine_none" value="none" {{ $currentFineType === 'none' ? 'checked' : '' }}>
                                                <label for="fine_none"><i class="fa fa-ban"></i><br>No Fine</label>
                                            </div>
                                            <div class="fine-type-btn">
                                                <input type="radio" name="fine_type" id="fine_flat" value="flat" {{ $currentFineType === 'flat' ? 'checked' : '' }}>
                                                <label for="fine_flat"><i class="fa fa-money"></i><br>Flat Amount</label>
                                            </div>
                                            <div class="fine-type-btn">
                                                <input type="radio" name="fine_type" id="fine_per_day" value="per_day" {{ $currentFineType === 'per_day' ? 'checked' : '' }}>
                                                <label for="fine_per_day"><i class="fa fa-clock-o"></i><br>Per Day</label>
                                            </div>
                                        </div>

                                        <div id="fine_settings_group" style="display:{{ $currentFineType !== 'none' ? 'block' : 'none' }}">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Fine Amount (৳)</label>
                                                        <input type="number" name="fine_amount" class="form-control" min="0" step="0.01" value="{{ old('fine_amount', $profile->fine_amount) }}">
                                                        <p class="help-note" id="fineHelpText">
                                                            {{ $currentFineType === 'per_day' ? 'Amount charged per day after grace period' : 'Fixed amount charged once after grace period' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Grace Period (days)</label>
                                                        <input type="number" name="fine_grace_days" class="form-control" min="0" value="{{ old('fine_grace_days', $profile->fine_grace_days) }}">
                                                        <p class="help-note">Fine starts after this many days</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="field-label">Max Fine Cap (৳) <small class="text-muted">optional</small></label>
                                                        <input type="number" name="max_fine" class="form-control" min="0" step="0.01" value="{{ old('max_fine', $profile->max_fine) }}" placeholder="No cap">
                                                        <p class="help-note">Fine will not exceed this amount</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ┌─ STEP 6: Installments ───────────────── --}}
                                @php
                                    $currentInstallCount  = old('installment_count', $profile->installment_count ?? 1);
                                    $currentInstallSplits = old('installment_splits', $profile->installment_splits ?? []);
                                @endphp
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
                                                    <input type="number" name="installment_count" class="form-control"
                                                        min="1" max="12" id="installmentCount"
                                                        value="{{ $currentInstallCount }}">
                                                    <p class="help-note">Set to 1 for full payment at once</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="installment_group" style="display:{{ $currentInstallCount > 1 ? 'block' : 'none' }}">
                                            <label class="field-label">Split Percentages <small class="text-muted" style="font-weight:400">(must total 100%)</small></label>
                                            <div id="installmentSplitRows">
                                                @if($currentInstallCount > 1 && count($currentInstallSplits))
                                                    @foreach($currentInstallSplits as $i => $pct)
                                                    <div class="split-row">
                                                        <span class="split-label"><i class="fa fa-circle-o" style="color:#7b5ea7;margin-right:5px"></i> Installment {{ $i + 1 }}</span>
                                                        <input type="number" name="installment_splits[]" class="form-control split-pct" style="width:90px" min="0" max="100" value="{{ $pct }}">
                                                        <span style="font-size:11px; color:#6c8ebf">%</span>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
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
                                @php $smsOn = old('sms_on_generation', $profile->sms_on_generation ?? false); @endphp
                                <div class="bp-card step-indigo">
                                    <div class="bp-card-header">
                                        <span class="bp-step-badge">7</span>
                                        <i class="fa fa-comment card-icon" style="color:#3d6bb8"></i>
                                        <h5>SMS Notification on Bill Generation</h5>
                                    </div>
                                    <div class="bp-card-body">

                                        <div class="form-group">
                                            <label class="field-label" style="font-weight:400; font-size:13px">
                                                <input type="checkbox" name="sms_on_generation" id="smsToggle" value="1" {{ $smsOn ? 'checked' : '' }}>
                                                &nbsp;<strong>Send SMS automatically when bills are generated</strong>
                                            </label>
                                            <p class="help-note">An SMS will be sent to each student's mobile immediately after bills are created</p>
                                        </div>

                                        <div class="toggle-sms-wrap" id="smsSettingsBox" style="display:{{ $smsOn ? 'block' : 'none' }}">
                                            <hr class="section-divider">
                                            <div class="form-group">
                                                <label class="field-label">Alert Event Key</label>
                                                <input type="text" name="alert_event_key" class="form-control" style="max-width:260px"
                                                    value="{{ old('alert_event_key', $profile->alert_event_key) }}" placeholder="BillingGenerated">
                                                <p class="help-note">Must match an Alert Setting event configured in the system</p>
                                            </div>

                                            <div class="form-group">
                                                <label class="field-label">SMS Message Template</label>
                                                <textarea name="sms_template" id="smsTemplate" class="form-control sms-template-area" rows="4"
                                                    placeholder="Dear {name}, your {period} bill of BDT {amount} has been generated. Due date: {due_date}. Reg: {reg_no} - Lalmai Institute">{{ old('sms_template', $profile->sms_template ?? '') }}</textarea>
                                                <div class="sms-char-count"><span id="smsCharCount">{{ strlen(old('sms_template', $profile->sms_template ?? '')) }}</span> characters</div>
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
                                        <div style="display:flex; gap:10px; align-items:center">
                                            <button type="submit" class="btn btn-warning btn-lg" style="padding: 9px 32px; font-size:14px; font-weight:700">
                                                <i class="fa fa-save"></i>&nbsp; Update Billing Profile
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
                                <div class="bp-summary">
                                    <h6><i class="fa fa-eye"></i>&nbsp; Profile Summary</h6>

                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Profile Name</span>
                                        <span class="bp-sum-val" id="sum_name">{{ $profile->profile_name }}</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Scope</span>
                                        <span class="bp-sum-val" id="sum_scope">—</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Cycle</span>
                                        <span class="bp-sum-val" id="sum_cycle">—</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Billing Day</span>
                                        <span class="bp-sum-val" id="sum_day">—</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Due After</span>
                                        <span class="bp-sum-val" id="sum_due">—</span>
                                    </div>

                                    <hr class="bp-sum-divider">

                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Fee Heads</span>
                                        <span class="bp-sum-val" id="sum_fee_count">{{ $profile->profileItems->count() }} added</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Total Bill Amount</span>
                                        <span class="bp-sum-val bp-sum-total" id="sum_total">৳ 0</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Installments</span>
                                        <span class="bp-sum-val" id="sum_installments">—</span>
                                    </div>

                                    <hr class="bp-sum-divider">

                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">Fine Type</span>
                                        <span class="bp-sum-val" id="sum_fine">—</span>
                                    </div>
                                    <div class="bp-sum-row">
                                        <span class="bp-sum-label">SMS on Generate</span>
                                        <span class="bp-sum-val" id="sum_sms">—</span>
                                    </div>

                                    <hr class="bp-sum-divider">

                                    <div style="font-size:11px; color:#7a8c9e; line-height:1.6">
                                        <strong style="color:#4a5568">Profile created:</strong><br>
                                        {{ $profile->created_at ? $profile->created_at->format('d M Y, h:i A') : '—' }}<br><br>
                                        <strong style="color:#4a5568">Last updated:</strong><br>
                                        {{ $profile->updated_at ? $profile->updated_at->format('d M Y, h:i A') : '—' }}
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
    /* No initial call — server already rendered correct visibility */

    /* ─────────────────────────────────────────────
       BILLING CYCLE
    ───────────────────────────────────────────── */
    function updateCycle() {
        var v = $('input[name="billing_cycle"]:checked').val();
        $('#billingDayGroup').toggle(v !== 'one_time');
        $('#one_time_date_group').toggle(v === 'one_time');
        $('#billing_months_group').toggle(v === 'half_yearly' || v === 'yearly');
        updateSummary();
    }
    $('input[name="billing_cycle"]').on('change', updateCycle);
    /* No initial call — server already rendered correct visibility */

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
       On edit: server pre-renders rows. Only rebuild on user change.
    ───────────────────────────────────────────── */
    function rebuildInstallmentRows(count) {
        var $rows = $('#installmentSplitRows');
        $rows.empty();
        var def = Math.floor(100 / count);
        for (var i = 1; i <= count; i++) {
            var pct = (i === count) ? (100 - def * (count - 1)) : def;
            $rows.append(
                '<div class="split-row">' +
                '<span class="split-label"><i class="fa fa-circle-o" style="color:#7b5ea7;margin-right:5px"></i> Installment ' + i + '</span>' +
                '<input type="number" name="installment_splits[]" class="form-control split-pct" style="width:90px" min="0" max="100" value="' + pct + '">' +
                '<span style="font-size:11px; color:#6c8ebf">%</span>' +
                '</div>'
            );
        }
        checkSplitSum();
    }

    $('#installmentCount').on('input change', function () {
        var count = parseInt($(this).val()) || 1;
        if (count > 1) {
            $('#installment_group').slideDown(180);
            rebuildInstallmentRows(count);
        } else {
            $('#installment_group').slideUp(180);
        }
        updateSummary();
    });

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
    /* Run once on load to validate pre-existing splits */
    if ($('.split-pct').length > 0) checkSplitSum();

    /* ─────────────────────────────────────────────
       FEE HEADS — auto-fill default amount on select
    ───────────────────────────────────────────── */
    $(document).on('change', '.fee-head-select', function () {
        var $opt   = $(this).find('option:selected');
        var amount = parseFloat($opt.data('amount')) || 0;
        var $row   = $(this).closest('tr');
        var $over  = $row.find('.amount-override');
        var $def   = $row.find('.default-amount');
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
        recalcFeeTotal();
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
    var scopeLabels = { all: 'All Students', faculty: 'By Faculty', semester: 'By Class', batch: 'By Batch' };
    var cycleLabels = { monthly: 'Monthly', quarterly: 'Quarterly', half_yearly: 'Half-Yearly', yearly: 'Yearly', one_time: 'One-Time' };
    var fineLabels  = { none: 'None', flat: 'Flat Amount', per_day: 'Per Day' };

    function updateSummary() {
        var name  = $('#profileName').val() || '—';
        var scope = $('input[name="scope_type"]:checked').val() || 'all';
        var cycle = $('input[name="billing_cycle"]:checked').val() || 'monthly';
        var day   = $('input[name="billing_day"]').val();
        var due   = $('input[name="due_days"]').first().val();
        var fine  = $('input[name="fine_type"]:checked').val() || 'none';
        var count = parseInt($('#installmentCount').val()) || 1;
        var feeCount = $('#feeHeadRows tr.fee-head-row').length;
        var sms   = $('#smsToggle').is(':checked');

        $('#sum_name').text(name);
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

    $('#profileName').on('input', updateSummary);
    $('input[name="billing_day"], input[name="due_days"]').on('input', updateSummary);

    /* ─────────────────────────────────────────────
       SYNC one_time due_days → main due_days field
    ───────────────────────────────────────────── */
    function syncDueDays() {
        if ($('input[name="billing_cycle"]:checked').val() === 'one_time') {
            $('input[name="due_days"]').val($('#dueDaysOnetime').val() || 15);
        }
    }

    /* ─────────────────────────────────────────────
       FORM SUBMIT VALIDATION
    ───────────────────────────────────────────── */
    $('#billingProfileForm').on('submit', function (e) {
        syncDueDays();
        var ok = true;

        if (!$('#profileName').val().trim()) {
            alert('Profile name is required.'); ok = false;
        }

        var hasHead = false;
        $('.fee-head-select').each(function () { if ($(this).val()) hasHead = true; });
        if (!hasHead) {
            alert('Please select at least one fee head.'); ok = false;
        }

        if (parseInt($('#installmentCount').val()) > 1) {
            var sum = 0;
            $('.split-pct').each(function () { sum += parseFloat($(this).val()) || 0; });
            if (Math.round(sum) !== 100) {
                alert('Installment percentages must sum to exactly 100%.'); ok = false;
            }
        }

        if (!ok) e.preventDefault();
    });

    /* ─────────────────────────────────────────────
       INITIALISE summary & fee total on page load
    ───────────────────────────────────────────── */
    recalcFeeTotal();
    updateSummary();

});
</script>
@endsection
