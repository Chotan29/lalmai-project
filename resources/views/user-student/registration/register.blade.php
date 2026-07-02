@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ── Base overrides ───────────────────────────────────────── */
body { font-family: 'Inter', sans-serif !important; background: #f0f4f9 !important; }
.page-header { display: none; }
.page-content { padding-top: 20px !important; background: #f0f4f9 !important; }

/* ── Form page wrapper ────────────────────────────────────── */
.reg-page { max-width: 1080px; margin: 0 auto; }

/* ── Header bar ───────────────────────────────────────────── */
.reg-topbar {
    background: linear-gradient(135deg, #0f3e6a 0%, #1a6fa3 100%);
    border-radius: 14px;
    padding: 22px 28px;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #fff;
}
.reg-topbar-title { font-size: 20px; font-weight: 800; letter-spacing: -.2px; }
.reg-topbar-sub   { font-size: 12px; opacity: .7; margin-top: 2px; }
.reg-topbar-badge {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 12px; font-weight: 600;
    display: flex; align-items: center; gap: 6px;
}

/* ── Steps indicator ──────────────────────────────────────── */
.reg-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    padding: 6px;
    margin-bottom: 22px;
}
.reg-step-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 11px 14px;
    border: none;
    border-radius: 9px;
    background: none;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    transition: all .2s;
}
.reg-step-btn:hover { background: #f3f4f6; color: #374151; }
.reg-step-btn.active {
    background: linear-gradient(135deg, #0f3e6a, #1a6fa3);
    color: #fff;
    box-shadow: 0 4px 12px rgba(15,62,106,.25);
}
.reg-step-btn.done { background: #dcfce7; color: #15803d; }
.reg-step-num {
    width: 24px; height: 24px;
    border-radius: 50%;
    background: rgba(0,0,0,.08);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800;
    flex-shrink: 0;
}
.reg-step-btn.active .reg-step-num { background: rgba(255,255,255,.25); }
.reg-step-btn.done   .reg-step-num { background: #bbf7d0; color: #15803d; }
.reg-step-divider { width: 1px; height: 28px; background: #e5e7eb; margin: 0 2px; }

/* ── Form card ────────────────────────────────────────────── */
.reg-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    margin-bottom: 18px;
    overflow: hidden;
}
.reg-card-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 22px;
    border-bottom: 1px solid #f3f4f6;
    background: #fafbfc;
}
.reg-card-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.rci-blue   { background: #dbeafe; color: #1d4ed8; }
.rci-green  { background: #dcfce7; color: #15803d; }
.rci-purple { background: #ede9fe; color: #7c3aed; }
.rci-orange { background: #ffedd5; color: #c2410c; }
.rci-teal   { background: #ccfbf1; color: #0f766e; }
.rci-rose   { background: #ffe4e6; color: #be123c; }
.reg-card-head h5 {
    margin: 0;
    font-size: 14px; font-weight: 700;
    color: #111827;
}
.reg-card-body { padding: 20px 22px; }

/* ── Form field overrides ─────────────────────────────────── */
.form-horizontal .form-group { margin-bottom: 16px; }
.form-horizontal .control-label {
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: #374151 !important;
    text-transform: uppercase;
    letter-spacing: .4px;
    padding-top: 10px !important;
}
.form-horizontal .form-control,
.form-horizontal input.form-control,
.form-horizontal select.form-control,
.form-horizontal textarea.form-control {
    height: 38px !important;
    border-radius: 8px !important;
    border: 1.5px solid #e5e7eb !important;
    font-size: 13px !important;
    font-family: 'Inter', sans-serif !important;
    color: #111827 !important;
    padding: 0 12px !important;
    box-shadow: none !important;
    transition: border-color .18s, box-shadow .18s !important;
    background: #fafbfc !important;
}
.form-horizontal textarea.form-control { height: auto !important; padding: 8px 12px !important; }
.form-horizontal .form-control:focus {
    border-color: #1a6fa3 !important;
    background: #fff !important;
    box-shadow: 0 0 0 3px rgba(26,111,163,.12) !important;
}
.form-horizontal select.form-control { padding-right: 32px !important; }

/* ── Required fields ──────────────────────────────────────── */
.form-horizontal .border-form { }

/* ── Section sub-headers ──────────────────────────────────── */
.reg-section-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    padding: 5px 12px;
    border-radius: 6px;
    margin-bottom: 12px;
    margin-top: 8px;
}
/* Override existing label-warning arrowed */
.label.label-warning.arrowed-in,
.label.label-info.arrowed-in {
    border-radius: 6px !important;
    padding: 5px 14px !important;
    font-size: 11px !important;
    letter-spacing: .5px !important;
    font-weight: 700 !important;
}
.label.label-warning.arrowed-in { background: #fef3c7 !important; color: #92400e !important; border: none !important; }
.label.label-info.arrowed-in    { background: #dbeafe !important; color: #1e40af !important; border: none !important; }

/* Hide arrowed pseudo-elements */
.label.arrowed-in::before,
.label.arrowed-in::after,
.label.arrowed-right::before,
.label.arrowed-right::after { display: none !important; }

/* ── Existing h4 headers inside forms ───────────────────── */
.form-horizontal h4.header,
h4.header.blue { color: #0f3e6a !important; font-size: 15px !important; font-weight: 700 !important; }

/* ── Tab content area (hidden by step buttons) ────────────── */
.reg-tab-pane { display: none; }
.reg-tab-pane.active { display: block; }

/* ── Guardian radio styling ────────────────────────────────── */
.form-horizontal .radio label { font-size: 13px; font-weight: 500; color: #374151; }
.ace-radio-container { display: flex; gap: 16px; flex-wrap: wrap; padding: 6px 0; }

/* ── Action bar ───────────────────────────────────────────── */
.reg-action-bar {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    padding: 16px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    gap: 12px;
}
.reg-action-left { display: flex; gap: 10px; }
.reg-action-right { display: flex; gap: 10px; }
.btn-reg-reset {
    background: #f3f4f6; color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 8px; padding: 9px 20px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: 'Inter', sans-serif;
    transition: background .15s;
}
.btn-reg-reset:hover { background: #e5e7eb; }
.btn-reg-save {
    background: linear-gradient(135deg, #0f3e6a, #1a6fa3);
    color: #fff; border: none;
    border-radius: 8px; padding: 9px 24px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: 'Inter', sans-serif;
    transition: opacity .15s;
    display: flex; align-items: center; gap: 7px;
}
.btn-reg-save:hover { opacity: .88; color: #fff; }
.btn-reg-save-another {
    background: #15803d; color: #fff; border: none;
    border-radius: 8px; padding: 9px 22px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: 'Inter', sans-serif;
    transition: background .15s;
    display: flex; align-items: center; gap: 7px;
}
.btn-reg-save-another:hover { background: #166534; color: #fff; }
.btn-reg-next, .btn-reg-prev {
    background: #f3f4f6; color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 8px; padding: 9px 20px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: 'Inter', sans-serif;
    display: flex; align-items: center; gap: 7px;
    transition: background .15s;
}
.btn-reg-next:hover, .btn-reg-prev:hover { background: #e5e7eb; }

/* ── Profile image upload area ──────────────────────────────── */
.photo-upload-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
.photo-upload-card {
    border: 1.5px dashed #d1d5db;
    border-radius: 12px;
    padding: 18px;
    text-align: center;
    background: #fafbfc;
    transition: border-color .2s;
}
.photo-upload-card:hover { border-color: #1a6fa3; }
.photo-upload-card img { border-radius: 8px; margin-bottom: 10px; }
.photo-upload-label { font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 8px; display: block; text-transform: uppercase; letter-spacing: .4px; }
.photo-upload-card input[type="file"] { width: 100%; font-size: 12px; }

/* ── Academic info table ────────────────────────────────────── */
#responsive.table { border-radius: 8px; overflow: hidden; }
#responsive.table thead th { background: #f3f4f6; color: #374151; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; border: none; }
#responsive.table td, #responsive.table th { vertical-align: middle; border-color: #f3f4f6; }

/* ── Validation message ──────────────────────────────────────── */
.help-block { font-size: 11px !important; color: #dc3545 !important; }
.has-error .form-control { border-color: #dc3545 !important; }
.has-error .control-label { color: #dc3545 !important; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 767px) {
    .reg-topbar { flex-direction: column; gap: 12px; text-align: center; }
    .reg-steps  { flex-direction: column; gap: 4px; }
    .reg-step-divider { display: none; }
    .reg-action-bar { flex-direction: column; align-items: stretch; }
    .reg-action-left, .reg-action-right { justify-content: center; }
    .photo-upload-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
<div class="main-content">
<div class="main-content-inner">
<div class="page-content" style="padding-top:20px">
@include('layouts.includes.template_setting')

<div class="reg-page">

    @include('includes.validation_error_messages')

    {{-- Top bar --}}
    <div class="reg-topbar">
        <div>
            <div class="reg-topbar-title">
                <i class="fa fa-user-plus" style="margin-right:8px"></i>Student Registration
            </div>
            <div class="reg-topbar-sub">Fill in all required fields to register a new student</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center">
            <a href="{{ route('student.import') }}" class="reg-topbar-badge" style="color:#fff;text-decoration:none">
                <i class="fa fa-upload"></i> Bulk Import
            </a>
        </div>
    </div>

    {!! Form::open(['route' => $base_route.'.register', 'method' => 'POST', 'id' => 'validation-form', 'enctype' => 'multipart/form-data']) !!}

    {{-- Step indicator --}}
    <div class="reg-steps" id="regSteps">
        <button type="button" class="reg-step-btn active" data-step="1" onclick="showStep(1)">
            <span class="reg-step-num">1</span>
            <i class="fa fa-user"></i> General Information
        </button>
        <div class="reg-step-divider"></div>
        <button type="button" class="reg-step-btn" data-step="2" onclick="showStep(2)">
            <span class="reg-step-num">2</span>
            <i class="fa fa-users"></i> Parent Details
        </button>
        <div class="reg-step-divider"></div>
        <button type="button" class="reg-step-btn" data-step="3" onclick="showStep(3)">
            <span class="reg-step-num">3</span>
            <i class="fa fa-graduation-cap"></i> Academic Info
        </button>
        <div class="reg-step-divider"></div>
        <button type="button" class="reg-step-btn" data-step="4" onclick="showStep(4)">
            <span class="reg-step-num">4</span>
            <i class="fa fa-image"></i> Photos
        </button>
    </div>

    {{-- Step 1: General Information --}}
    <div class="reg-tab-pane active" id="step-1">

        {{-- Academic placement --}}
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-blue"><i class="fa fa-university"></i></div>
                <h5>Academic Placement</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('reg_no', 'REG. NO.', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::text('reg_no', null, ['class' => 'form-control border-form input-mask-registration', 'disabled', 'placeholder' => 'Auto']) !!}
                        </div>
                        {!! Form::label('reg_date', 'Admission Date', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::text('reg_date', null, ['class' => 'form-control date-picker border-form input-mask-date', 'disabled']) !!}
                        </div>
                        {!! Form::label('university_reg', __('form_fields.student.fields.university_reg'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::text('university_reg', null, ['class' => 'form-control border-form', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.faculty') }}</label>
                        <div class="col-sm-4">
                            {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                        <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.semester') }}</label>
                        <div class="col-sm-4">
                            {!! Form::select('semester', $data['semester'], null, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.batch') }}</label>
                        <div class="col-sm-4">
                            {!! Form::select('batch', $data['batch'], null, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-4">
                            {!! Form::select('academic_status', $data['academic_status'], null, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal details --}}
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-purple"><i class="fa fa-id-card"></i></div>
                <h5>Personal Details</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('first_name', __('form_fields.student.fields.name_of_student'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            <div class="row" style="margin:0;gap:6px;display:flex">
                                <div class="col-xs-4" style="padding:0 3px">
                                    {!! Form::text('first_name', null, ['class' => 'form-control border-form upper', 'required', 'placeholder' => 'First Name']) !!}
                                    @include('includes.form_fields_validation_message', ['name' => 'first_name'])
                                </div>
                                <div class="col-xs-4" style="padding:0 3px">
                                    {!! Form::text('middle_name', null, ['class' => 'form-control border-form upper', 'placeholder' => 'Middle Name']) !!}
                                </div>
                                <div class="col-xs-4" style="padding:0 3px">
                                    {!! Form::text('last_name', null, ['class' => 'form-control border-form upper', 'required', 'placeholder' => 'Last Name']) !!}
                                    @include('includes.form_fields_validation_message', ['name' => 'last_name'])
                                </div>
                            </div>
                        </div>
                        {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('date_of_birth', null, ['class' => 'form-control border-form date-picker input-mask-date', 'required', 'placeholder' => 'YYYY-MM-DD']) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('gender', __('form_fields.student.fields.gender'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::select('gender', __('common.gender'), null, ['class' => 'form-control border-form', 'required']) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'gender'])
                        </div>
                        {!! Form::label('blood_group', __('form_fields.student.fields.blood_group'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::select('blood_group', __('common.blood_group'), null, ['class' => 'form-control border-form']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('religion', __('form_fields.student.fields.religion'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('religion', null, ['class' => 'form-control border-form upper']) !!}
                        </div>
                        {!! Form::label('nationality', __('form_fields.student.fields.nationality'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('nationality', null, ['class' => 'form-control border-form upper', 'required']) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'nationality'])
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('caste', __('form_fields.student.fields.caste'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('caste', null, ['class' => 'form-control border-form upper']) !!}
                        </div>
                        {!! Form::label('mother_tongue', __('form_fields.student.fields.mother_tongue'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('mother_tongue', null, ['class' => 'form-control border-form upper']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('email', null, ['class' => 'form-control border-form', 'placeholder' => 'student@email.com']) !!}
                        </div>
                        {!! Form::label('extra_info', 'Extra Info', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('extra_info', null, ['class' => 'form-control border-form']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-teal"><i class="fa fa-phone"></i></div>
                <h5>Contact Numbers</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('home_phone', 'Phone', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('home_phone', null, ['class' => 'form-control border-form input-mask-phone', 'placeholder' => 'Home phone']) !!}
                        </div>
                        {!! Form::label('mobile_1', 'Mobile 1', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::text('mobile_1', null, ['class' => 'form-control border-form input-mask-mobile', 'required', 'placeholder' => '01XXXXXXXXX']) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'mobile_1'])
                        </div>
                        {!! Form::label('mobile_2', 'Mobile 2', ['class' => 'col-sm-1 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::text('mobile_2', null, ['class' => 'form-control border-form input-mask-mobile', 'placeholder' => 'Optional']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Addresses --}}
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-orange"><i class="fa fa-map-marker"></i></div>
                <h5>Address</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <p style="font-size:12px;font-weight:700;color:#0f3e6a;margin-bottom:12px;text-transform:uppercase;letter-spacing:.5px">
                        <i class="fa fa-home"></i> Permanent Address
                    </p>
                    <div class="form-group">
                        {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('address', null, ['class' => 'form-control border-form upper', 'required']) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'address'])
                        </div>
                        {!! Form::label('state', 'District / State', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('state', null, ['class' => 'form-control border-form upper', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('country', 'Country', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('country', null, ['class' => 'form-control border-form upper', 'required']) !!}
                        </div>
                    </div>

                    <hr style="border-color:#f3f4f6;margin:16px 0">
                    <p style="font-size:12px;font-weight:700;color:#0f3e6a;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">
                        <i class="fa fa-map-pin"></i> Temporary Address
                    </p>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label style="font-size:13px;font-weight:500;color:#374151">
                                {!! Form::checkbox('permanent_address_copier', '', false, ['class' => 'ace', 'onclick' => 'CopyAddress(this.form)']) !!}
                                <span class="lbl" style="margin-left:6px"> Same as Permanent Address</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('temp_address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('temp_address', null, ['class' => 'form-control border-form upper']) !!}
                        </div>
                        {!! Form::label('temp_state', 'District / State', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('temp_state', null, ['class' => 'form-control border-form upper']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('temp_country', 'Country', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('temp_country', null, ['class' => 'form-control border-form upper']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="reg-action-bar">
            <div class="reg-action-left">
                <button type="reset" class="btn-reg-reset"><i class="fa fa-undo"></i> Reset</button>
            </div>
            <div class="reg-action-right">
                <button type="button" class="btn-reg-next" onclick="showStep(2)">
                    Parent Details <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>{{-- /step-1 --}}

    {{-- Step 2: Parent Details --}}
    <div class="reg-tab-pane" id="step-2">

        @include($view_path.'.registration.includes.forms.pull-guardian-info')

        {{-- Grandfather --}}
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-blue"><i class="fa fa-male"></i></div>
                <h5>Grandfather's Details</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('grandfather_name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10" style="display:flex;gap:8px">
                            <div style="flex:1">{!! Form::text('grandfather_first_name', null, ['class' => 'form-control border-form upper', 'placeholder' => 'First Name']) !!}</div>
                            <div style="flex:1">{!! Form::text('grandfather_middle_name', null, ['class' => 'form-control border-form upper', 'placeholder' => 'Middle Name']) !!}</div>
                            <div style="flex:1">{!! Form::text('grandfather_last_name', null, ['class' => 'form-control border-form upper', 'placeholder' => 'Last Name']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Father --}}
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-green"><i class="fa fa-male"></i></div>
                <h5>Father's Details</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('father_name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10" style="display:flex;gap:8px">
                            <div style="flex:1">{!! Form::text('father_first_name', null, ['class' => 'form-control border-form upper', 'required', 'placeholder' => 'First Name']) !!}</div>
                            <div style="flex:1">{!! Form::text('father_middle_name', null, ['class' => 'form-control border-form upper', 'placeholder' => 'Middle Name']) !!}</div>
                            <div style="flex:1">{!! Form::text('father_last_name', null, ['class' => 'form-control border-form upper', 'required', 'placeholder' => 'Last Name']) !!}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('father_eligibility', 'Eligibility', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('father_eligibility', null, ['class' => 'form-control border-form upper']) !!}</div>
                        {!! Form::label('father_occupation', 'Occupation', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('father_occupation', null, ['class' => 'form-control border-form upper']) !!}</div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('father_office', 'Office', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('father_office', null, ['class' => 'form-control border-form upper']) !!}</div>
                        {!! Form::label('father_office_number', 'Office No.', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('father_office_number', null, ['class' => 'form-control border-form upper']) !!}</div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('father_mobile_1', 'Mobile 1', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('father_mobile_1', null, ['class' => 'form-control border-form input-mask-mobile']) !!}</div>
                        {!! Form::label('father_mobile_2', 'Mobile 2', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('father_mobile_2', null, ['class' => 'form-control border-form input-mask-mobile']) !!}</div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('father_email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('father_email', null, ['class' => 'form-control border-form']) !!}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mother --}}
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-rose"><i class="fa fa-female"></i></div>
                <h5>Mother's Details</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('mother_name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10" style="display:flex;gap:8px">
                            <div style="flex:1">{!! Form::text('mother_first_name', null, ['class' => 'form-control border-form upper', 'required', 'placeholder' => 'First Name']) !!}</div>
                            <div style="flex:1">{!! Form::text('mother_middle_name', null, ['class' => 'form-control border-form upper', 'placeholder' => 'Middle Name']) !!}</div>
                            <div style="flex:1">{!! Form::text('mother_last_name', null, ['class' => 'form-control border-form upper', 'required', 'placeholder' => 'Last Name']) !!}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('mother_eligibility', 'Eligibility', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('mother_eligibility', null, ['class' => 'form-control border-form upper']) !!}</div>
                        {!! Form::label('mother_occupation', 'Occupation', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('mother_occupation', null, ['class' => 'form-control border-form upper']) !!}</div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('mother_mobile_1', 'Mobile 1', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('mother_mobile_1', null, ['class' => 'form-control border-form input-mask-mobile']) !!}</div>
                        {!! Form::label('mother_email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">{!! Form::text('mother_email', null, ['class' => 'form-control border-form']) !!}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Guardian --}}
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-purple"><i class="fa fa-shield"></i></div>
                <h5>Guardian's Details</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div style="display:flex;gap:18px;flex-wrap:wrap;margin-bottom:8px">
                                <label style="font-size:13px;font-weight:500;color:#374151;display:flex;align-items:center;gap:7px">
                                    {!! Form::radio('guardian_is', 'father_as_guardian', false, ['class' => 'ace', 'onclick' => 'FatherAsGuardian(this.form)']) !!}
                                    <span class="lbl">Father is Guardian</span>
                                </label>
                                <label style="font-size:13px;font-weight:500;color:#374151;display:flex;align-items:center;gap:7px">
                                    {!! Form::radio('guardian_is', 'mother_as_guardian', false, ['class' => 'ace', 'onclick' => 'MotherAsGuardian(this.form)']) !!}
                                    <span class="lbl">Mother is Guardian</span>
                                </label>
                                <label style="font-size:13px;font-weight:500;color:#374151;display:flex;align-items:center;gap:7px">
                                    {!! Form::radio('guardian_is', 'other_guardian', true, ['class' => 'ace', 'onclick' => 'OtherGuardian(this.form)']) !!}
                                    <span class="lbl">Other Guardian</span>
                                </label>
                                <label style="font-size:13px;font-weight:500;color:#374151;display:flex;align-items:center;gap:7px">
                                    {!! Form::radio('guardian_is', 'link_guardian', false, ['class' => 'ace', 'onclick' => 'linkGuardian(this.form)']) !!}
                                    <span class="lbl">Link Guardian</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="guardian-detail">
                        <div class="form-group">
                            {!! Form::label('guardian_name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-10" style="display:flex;gap:8px">
                                <div style="flex:1">{!! Form::text('guardian_first_name', null, ['class' => 'form-control border-form upper', 'required', 'placeholder' => 'First Name']) !!}</div>
                                <div style="flex:1">{!! Form::text('guardian_middle_name', null, ['class' => 'form-control border-form upper', 'placeholder' => 'Middle Name']) !!}</div>
                                <div style="flex:1">{!! Form::text('guardian_last_name', null, ['class' => 'form-control border-form upper', 'required', 'placeholder' => 'Last Name']) !!}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('guardian_eligibility', 'Eligibility', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">{!! Form::text('guardian_eligibility', null, ['class' => 'form-control border-form upper']) !!}</div>
                            {!! Form::label('guardian_occupation', 'Occupation', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">{!! Form::text('guardian_occupation', null, ['class' => 'form-control border-form upper']) !!}</div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('guardian_mobile_1', 'Mobile 1', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">{!! Form::text('guardian_mobile_1', null, ['class' => 'form-control border-form input-mask-mobile', 'required']) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'guardian_mobile_1'])</div>
                            {!! Form::label('guardian_email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">{!! Form::text('guardian_email', null, ['class' => 'form-control border-form']) !!}</div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('guardian_relation', 'Relation', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">{!! Form::text('guardian_relation', null, ['class' => 'form-control border-form upper', 'required']) !!}</div>
                            {!! Form::label('guardian_address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">{!! Form::text('guardian_address', null, ['class' => 'form-control border-form upper', 'required']) !!}</div>
                        </div>
                    </div>
                    <div id="link-guardian-detail" style="display:none">
                        <div class="form-group">
                            {!! Form::label('guardian_info', 'Search Guardian', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-10">
                                {!! Form::select('guardian_link_id', [], null, ['placeholder' => 'Type Reg.No., Name or Mobile...', 'class' => 'col-xs-12', 'style' => 'width:100%']) !!}
                                <div style="margin-top:10px;text-align:right">
                                    <button type="button" class="btn-reg-save" id="load-guardian-html-btn" style="display:inline-flex">
                                        <i class="fa fa-link"></i> Link Guardian
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="guardian_wrapper"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="reg-action-bar">
            <div class="reg-action-left">
                <button type="button" class="btn-reg-prev" onclick="showStep(1)"><i class="fa fa-arrow-left"></i> Back</button>
            </div>
            <div class="reg-action-right">
                <button type="button" class="btn-reg-next" onclick="showStep(3)">Academic Info <i class="fa fa-arrow-right"></i></button>
            </div>
        </div>
    </div>{{-- /step-2 --}}

    {{-- Step 3: Academic Info --}}
    <div class="reg-tab-pane" id="step-3">
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-blue"><i class="fa fa-graduation-cap"></i></div>
                <h5>Academic Background</h5>
            </div>
            <div class="reg-card-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <table id="responsive" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">Sort</th>
                                    <th width="80%">Detail</th>
                                    <th width="10%">
                                        <button type="button" class="btn btn-xs btn-primary pull-right" id="load-academicinfo-html">
                                            <i class="fa fa-plus"></i> Add
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="academicInfo_wrapper">
                                @if(isset($data['academicInfo-html']))
                                    {!! $data['academicInfo-html'] !!}
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="reg-action-bar">
            <div class="reg-action-left">
                <button type="button" class="btn-reg-prev" onclick="showStep(2)"><i class="fa fa-arrow-left"></i> Back</button>
            </div>
            <div class="reg-action-right">
                <button type="button" class="btn-reg-next" onclick="showStep(4)">Photos <i class="fa fa-arrow-right"></i></button>
            </div>
        </div>
    </div>{{-- /step-3 --}}

    {{-- Step 4: Photos --}}
    <div class="reg-tab-pane" id="step-4">
        <div class="reg-card">
            <div class="reg-card-head">
                <div class="reg-card-icon rci-teal"><i class="fa fa-camera"></i></div>
                <h5>Profile Pictures & Signatures</h5>
            </div>
            <div class="reg-card-body">
                <div class="photo-upload-grid">

                    {{-- Student Photo --}}
                    <div class="photo-upload-card">
                        <span class="photo-upload-label"><i class="fa fa-user"></i> Student Photo</span>
                        @if(isset($data['row']) && $data['row']->student_image)
                            <img src="{{ asset('images/studentProfile/'.$data['row']->student_image) }}" width="80" style="margin-bottom:10px;border-radius:8px">
                        @else
                            <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="80" style="margin-bottom:10px;border-radius:8px;opacity:.4">
                        @endif
                        {!! Form::file('student_main_image', ['class' => 'form-control border-form']) !!}
                    </div>

                    {{-- Signature --}}
                    <div class="photo-upload-card">
                        <span class="photo-upload-label"><i class="fa fa-pencil"></i> Student Signature</span>
                        @if(isset($data['row']) && $data['row']->student_signature)
                            <img src="{{ asset('images/studentProfile/'.$data['row']->student_signature) }}" width="80" style="margin-bottom:10px;border-radius:8px">
                        @else
                            <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="80" style="margin-bottom:10px;border-radius:8px;opacity:.4">
                        @endif
                        {!! Form::file('student_signature_main_image', ['class' => 'form-control border-form']) !!}
                    </div>

                    {{-- Father --}}
                    <div class="photo-upload-card">
                        <span class="photo-upload-label"><i class="fa fa-male"></i> Father's Photo</span>
                        @if(isset($data['row']) && $data['row']->father_image)
                            <img src="{{ asset('images/parents/'.$data['row']->father_image) }}" width="80" style="margin-bottom:10px;border-radius:8px">
                        @else
                            <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="80" style="margin-bottom:10px;border-radius:8px;opacity:.4">
                        @endif
                        {!! Form::file('father_main_image', ['class' => 'form-control border-form']) !!}
                    </div>

                    {{-- Mother --}}
                    <div class="photo-upload-card">
                        <span class="photo-upload-label"><i class="fa fa-female"></i> Mother's Photo</span>
                        @if(isset($data['row']) && $data['row']->mother_image)
                            <img src="{{ asset('images/parents/'.$data['row']->mother_image) }}" width="80" style="margin-bottom:10px;border-radius:8px">
                        @else
                            <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="80" style="margin-bottom:10px;border-radius:8px;opacity:.4">
                        @endif
                        {!! Form::file('mother_main_image', ['class' => 'form-control border-form']) !!}
                    </div>

                    {{-- Guardian --}}
                    <div class="photo-upload-card">
                        <span class="photo-upload-label"><i class="fa fa-shield"></i> Guardian's Photo</span>
                        @if(isset($data['row']) && $data['row']->guardian_image)
                            <img src="{{ asset('images/parents/'.$data['row']->guardian_image) }}" width="80" style="margin-bottom:10px;border-radius:8px">
                        @else
                            <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="80" style="margin-bottom:10px;border-radius:8px;opacity:.4">
                        @endif
                        {!! Form::file('guardian_main_image', ['class' => 'form-control border-form']) !!}
                    </div>

                </div>
            </div>
        </div>

        {{-- Final submit --}}
        <div class="reg-action-bar">
            <div class="reg-action-left">
                <button type="button" class="btn-reg-prev" onclick="showStep(3)"><i class="fa fa-arrow-left"></i> Back</button>
                <button type="reset" class="btn-reg-reset"><i class="fa fa-undo"></i> Reset All</button>
            </div>
            <div class="reg-action-right">
                <button class="btn-reg-save" type="submit" name="add_student" id="add-student">
                    <i class="fa fa-save"></i> Save Student
                </button>
                <button class="btn-reg-save-another" type="submit" name="add_student_another" id="add-student-another">
                    <i class="fa fa-save"></i><i class="fa fa-plus"></i> Save &amp; Add Another
                </button>
            </div>
        </div>
    </div>{{-- /step-4 --}}

    {!! Form::close() !!}

</div>{{-- /reg-page --}}
</div>
</div>
</div>
@endsection

@section('js')
@include('includes.scripts.jquery_validation_scripts')
@include('student.registration.includes.student-common-script')
@include('includes.scripts.inputMask_script')
@include('includes.scripts.datepicker_script')

<script>
function showStep(n) {
    // Hide all steps
    document.querySelectorAll('.reg-tab-pane').forEach(function(el) {
        el.classList.remove('active');
    });
    // Show target step
    document.getElementById('step-' + n).classList.add('active');
    // Update step buttons
    document.querySelectorAll('.reg-step-btn').forEach(function(btn) {
        var s = parseInt(btn.getAttribute('data-step'));
        btn.classList.remove('active', 'done');
        if (s === n) btn.classList.add('active');
        else if (s < n) btn.classList.add('done');
    });
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// If validation fails, show the first step that has errors
$(document).ready(function() {
    @if($errors->any())
    showStep(1);
    @endif
});
</script>
@endsection
