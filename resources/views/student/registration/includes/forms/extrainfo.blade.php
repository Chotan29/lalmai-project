<fieldset>
    <legend>Fee & Bank Info:</legend>
    <div class="form-group">
        {!! Form::label('total_fee_per_year', 'Total Fee Per Year', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('total_fee_per_year', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'total_fee_per_year'])
        </div>
        {!! Form::label('bank_name', 'Bank Name', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-6">
            {!! Form::text('bank_name', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'bank_name'])
        </div>
    </div>

    <div class="form-group">

        {!! Form::label('bank_code', 'Bank Code (IFSC)', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('bank_code', null, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'bank_code'])
        </div>

        {!! Form::label('bank_account_number', 'ACCOUNT NO.', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('bank_account_number', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'bank_account_number'])
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Admission Portal Info:</legend>
    <div class="form-group">
        {!! Form::label('admission_portal_login_id', 'Login Id', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('admission_portal_login_id', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'admission_portal_login_id'])
        </div>

        {!! Form::label('admission_portal_login_password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('admission_portal_login_password', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'admission_portal_login_password'])
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Scholarship Info:</legend>
    <div class="form-group">
        {!! Form::label('scholarship', 'Scholarship', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::select('scholarship', $data['scholarship'], isset($data['row']->scholarships_id)?$data['row']->scholarships_id:null, ['class' => 'form-control']) !!}
        </div>

        {!! Form::label('scholarship_application_id', 'Application ID', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('scholarship_application_id', null, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'scholarship_application_id'])
        </div>

        {!! Form::label('scholarship_user_name', 'User Name', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('scholarship_user_name', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'scholarship_user_name'])
        </div>
        {!! Form::label('scholarship_password', 'Password', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('scholarship_password', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'scholarship_password'])
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Placement Details:</legend>
    <div class="form-group">
        {!! Form::label('placement_company', 'Company', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('placement_company', $data['placement'], isset($data['row']->scholarships_id)?$data['row']->scholarships_id:null, ['class' => 'form-control']) !!}
        </div>

        {!! Form::label('placement_salary', 'Salary', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::number('placement_salary', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'placement_salary'])
        </div>
        {!! Form::label('placement_date', 'Year', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('placement_date', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'placement_date'])
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('placement_location', 'Location', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('placement_location', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'placement_location'])
        </div>
        {!! Form::label('placement_designation', 'Designation', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('placement_designation', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'placement_designation'])
        </div>

    </div>
</fieldset>

<fieldset>
    <legend>DMC's Info:</legend>
    @include('student.registration.includes.forms.degree_tr')
</fieldset>