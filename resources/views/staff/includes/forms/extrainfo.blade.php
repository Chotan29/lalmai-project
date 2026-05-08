<fieldset>
    <legend>Experience & More:</legend>

    <div class="form-group">
        {!! Form::label('experience', 'Experience', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::text('experience', null, ["class" => "form-control border-form upper",]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'experience'])
        </div>

        {!! Form::label('experience_info', 'Experience Info', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-5">
            {!! Form::textarea('experience_info', null, ["class" => "form-control border-form ", "rows"=>"1"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'experience_info'])
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('other_info', 'Other Info', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::textarea('other_info', null, ["class" => "form-control border-form", "rows"=>"1"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'other_info'])
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Info:</legend>

    <div class="form-group">
        {!! Form::label('basic_salary', 'Basic Salary', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::number('basic_salary', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'basic_salary'])
        </div>
        {!! Form::label('date_of_relieving', 'Date of Re-leaving', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
        {!! Form::text('date_of_relieving', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        </div>
        {!! Form::label('date_of_rejoin', 'Date of Re-join', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
        {!! Form::text('date_of_rejoin', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        </div>
    </div>
</fieldset>
<fieldset>
    <legend>Bank Detail:</legend>
    <div class="form-group">
        {!! Form::label('bank_name', 'Bank Name', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::text('bank_name', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'bank_name'])
        </div>


        {!! Form::label('bank_code', 'Bank Code (IFSC)', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('bank_code', null, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'bank_code'])
        </div>

        {!! Form::label('bank_account_number', 'ACCOUNT NO.', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('bank_account_number', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'bank_account_number'])
        </div>
    </div>
</fieldset>
