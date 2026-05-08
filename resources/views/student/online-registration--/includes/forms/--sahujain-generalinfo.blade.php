<?php
/*
  *
  * <div class="label label-warning arrowed-in arrowed-right arrowed">Payment</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('reg_fee', 'Registration Fee', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-4">
        {!! Form::text('reg_fee', '200', ['class'=>'form-control border-form',"readonly"]); !!}
        @include('includes.form_fields_validation_message', ['name' => 'reg_fee'])
    </div>

    {!! Form::label('sbi_collect_no', 'SBI Collect No.', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-4">
        {!! Form::text('sbi_collect_no', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'sbi_collect_no'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('bank_ref_no', 'Bank Ref. No.', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-4">
        {!! Form::text('bank_ref_no', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'bank_ref_no'])
    </div>
    {!! Form::label('payment_date', 'Payment Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('payment_date', null, ["class" => "form-control border-form date-picker input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'payment_date'])
    </div>
</div>
  * */
?>

<div class="label label-warning arrowed-in arrowed-right arrowed">Course</div>
<hr class="hr-8">

<div class="form-group">
    {!! Form::label('batch', 'Session', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('batch', $data['batch'], null, [ 'class'=>'form-control border-form',"required",'readonly']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'batch'])
    </div>

    <label class="col-sm-2 control-label">Level</label>
    <div class="col-sm-4">
        <select name="faculty" class="form-control"  data-placeholder="Choose a Faculty..."  onChange ="loadSemesters(this)" required="required">
            @foreach( $data['faculties'] as $key => $faculty)
                <option value="{{ $key }}">{{ $faculty }}</option>
            @endforeach
        </select>
    </div>

    <label class="col-sm-1 control-label">Sem/Sec</label>
    <div class="col-sm-2">
        <select id="semester" name="semester" required onChange ="loadSubject(this)" class="form-control border-form semester"  >
        </select>
        @include('includes.form_fields_validation_message', ['name' => 'semester'])
    </div>
</div>

<div class="form-group">
    <div id="subjects_wrapper">
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">University</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('university_enrollment_no', 'University Enrollment No.', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-3">
        {!! Form::text('university_enrollment_no', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_enrollment_no'])
    </div>

    {!! Form::label('university_reg', 'Unique Student ID (URN)', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('university_reg', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Student</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('first_name', __('form_fields.student.fields.name_of_student'), ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-3">
        {!! Form::text('first_name', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>

    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_birth', null, ["class" => "form-control border-form date-picker input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>

    {!! Form::label('gender', __('form_fields.student.fields.gender'), ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('gender', __('common.gender'), null, ['class'=>'form-control border-form',"required"]); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('caste', __('form_fields.student.fields.caste'), ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('caste', ['' => '','GENERAL' => 'GENERAL','OBC' => 'OBC','SC' => 'SC','ST' => 'ST','EWS' => 'EWS', ], null,
        [ 'class'=>'form-control border-form',"required"]); !!}
        @include('includes.form_fields_validation_message', ['name' => 'caste'])
    </div>

    {!! Form::label('religion', 'Nationality/Religion', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('religion', ['' => '','Indian/Hindu' => 'Indian/Hindu','Indian/Sikh' => 'Indian/Sikh','Indian/Muslim' => 'Indian/Muslim','Indian/Christian' => 'Indian/Christian','Indian/Others' => 'Indian/Others', ], null,
        [ 'class'=>'form-control border-form',"required"]); !!}
        @include('includes.form_fields_validation_message', ['name' => 'religion'])
    </div>

    {!! Form::label('national_id_1', 'Aadhaar Card No', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('national_id_1', null, ["placeholder" => "", "class" => "form-control border-form input-mask-aadhaar-id",'maxlength' => 12,"required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'national_id_1'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('national_id_2', 'Voter Id Card No', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('national_id_2', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'national_id_2'])
    </div>

    {!! Form::label('special_category', 'Special Category', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('special_category', ['' => '','Handicapped' => 'Handicapped','Freedom Fighter Dependent' => 'Freedom Fighter Dependent','Defence Ward/Police Ward/Ex-Defence Person' => 'Defence Ward/Police Ward/Ex-Defence Person','OTHERS' => 'OTHERS','NONE' => 'NONE', ], null,
        [ 'class'=>'form-control border-form',"required"]); !!}
        @include('includes.form_fields_validation_message', ['name' => 'special_category'])
    </div>

    {!! Form::label('weightage_claim', 'Weightage Claimed', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::select('weightage_claim', ['' => '','NCC' => 'NCC','NSS' => 'NSS','SCOUT' => 'SCOUT','GAMES' => 'GAMES','STAFFWARD' => 'STAFFWARD','AWARD' => 'AWARD','OTHERS' => 'OTHERS','NONE' => 'NONE', ], null,
        [ 'class'=>'form-control border-form',"required"]); !!}
        @include('includes.form_fields_validation_message', ['name' => 'weightage_claim'])
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Contact of Student</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('email', null, ["class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
    </div>

    {!! Form::label('mobile_1', 'Mobile', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mobile_1', null, ["class" => "form-control border-form input-mask-mobile","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mobile_1'])
    </div>

    {!! Form::label('mobile_2', 'WhatsApp Mobile No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mobile_2', null, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mobile_2'])
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Permanent Address</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('address', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>

    {!! Form::label('state', 'State', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('state', $data['state'], 1, ['class' => 'form-control',"required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'state'])
    </div>

    {!! Form::label('postal_code', 'Postal Code', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('postal_code', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'postal_code'])
    </div>
</div>


<div class="label label-warning arrowed-in arrowed-right arrowed">Temporary Address</div>

<div class="control-group col-sm-12">
    <div class="radio">
        <label>
            {!! Form::checkbox('permanent_address_copier', '', false, ['class' => 'ace', "onclick"=>"CopyAddress(this.form)"]) !!}
            <span class="lbl"> Temporary Address Same As Permanent Address</span>
        </label>
    </div>
</div>
<hr>
<hr class="hr-8">

<div class="form-group">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('temp_address', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_address'])
    </div>

    {!! Form::label('state', 'State', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('temp_state', $data['state'], 1, ['class' => 'form-control']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_state'])
    </div>

    {!! Form::label('temp_postal_code', 'Postal Code', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('temp_postal_code', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_postal_code'])
    </div>
</div>