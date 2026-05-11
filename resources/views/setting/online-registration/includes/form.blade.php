<div class="easy-link-menu align-right">
    <a class="btn-primary btn-sm " href="{{route('online-registration.registration')}}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;Show Online Registration Form</a>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="status"> Registration Status </label>
    <div class="col-sm-10">
        {!! Form::select('status', ['active'=>'Open','in-active'=>'Close'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'status'])
    </div>
</div>
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('range', 'Open & Close Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class=" col-sm-3">
        <div class="input-group ">
            {!! Form::text('start_date', null, ["placeholder" => "", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
            <span class="input-group-addon">
                <i class="fa fa-exchange"></i>
            </span>
            {!! Form::text('end_date', null, ["placeholder" => "", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
        </div>
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))
    <div class="form-group">
        <label class="col-sm-2 control-label">Existing Logo</label>
        <div class="col-sm-9">
            @if ($data['row']->logo)
                <img src="{{ asset('images/setting/'.$folder_name.'/'.$data['row']->logo) }}" >
            @else
                <p>No image.</p>
            @endif
        </div>
    </div>
@endif
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('logo_image', 'Logo', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::file('logo_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'logo_image'])
    </div>
</div>


<div class="space-4"></div>

<!-- Student Type Settings -->
<div class="form-group">
    <label class="col-sm-2 control-label">Student Type Settings</label>
    <div class="col-sm-10">
        <div class="checkbox">
            <label>
                {!! Form::checkbox('new_student_enabled', true, null) !!}
                Enable New Student Registration
            </label>
        </div>
        <div class="checkbox">
            <label>
                {!! Form::checkbox('old_student_enabled', true, null) !!}
                Enable Old/Returning Student Registration
            </label>
        </div>
    </div>
</div>

<div class="space-4"></div>

<!-- Registration Fee Settings -->
<div class="form-group">
    {!! Form::label('new_student_registration_fee', 'New Student Registration Fee (৳)', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::number('new_student_registration_fee', null, ["placeholder" => "0.00", "class" => "form-control border-form", "step" => "0.01", "min" => "0"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'new_student_registration_fee'])
    </div>

    {!! Form::label('old_student_registration_fee', 'Old Student Registration Fee (৳)', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::number('old_student_registration_fee', null, ["placeholder" => "0.00", "class" => "form-control border-form", "step" => "0.01", "min" => "0"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'old_student_registration_fee'])
    </div>
</div>

<div class="space-4"></div>

<!-- Payment Requirement -->
<div class="form-group">
    <label class="col-sm-2 control-label">Payment Settings</label>
    <div class="col-sm-10">
        <div class="checkbox">
            <label>
                {!! Form::checkbox('payment_required', true, null) !!}
                Require Payment for Registration (if fees are set above)
            </label>
        </div>
        <small class="text-muted">If checked, students must make payment before completing registration. The payment will be recorded as their admission/registration fee.</small>
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="rules_status"> Rules Status </label>
    <div class="col-sm-10">
        {!! Form::select('rules_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form ']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'rules_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('rules', 'Rules Info', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('rules', null, ["placeholder" => "", "class" => "form-control border-form summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'rules'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="agreement_status"> Agreement Status </label>
    <div class="col-sm-10">
        {!! Form::select('agreement_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'agreement_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('agreement', 'Agreement Info', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('agreement', null, ["placeholder" => "", "class" => "form-control border-form summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'agreement'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('registration_guidelines', 'Registration Guideline Info', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('registration_guidelines', null, ["placeholder" => "", "class" => "form-control border-form summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'registration_guidelines'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('registration_close_message', 'Registration Close Message', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('registration_close_message', null, ["placeholder" => "", "class" => "form-control border-form summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'registration_close_message'])
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="status"> Registration Enable Base </label>
    <div class="col-sm-10">
        {!! Form::select('base', ['faculty'=>'Faculty/Program/Class','semester'=>'Semester/Section'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'base'])
    </div>
</div>


<div class="form-group">
    <label class="col-sm-2 control-label"> Program Management </label>
    <div class="col-sm-10">
        <div class="clearfix" style="margin-bottom: 10px;">
            <button type="button" class="btn btn-primary btn-sm pull-right" id="add-program-html">
                <i class="fa fa-plus"></i> Add Program
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
            <thead class="thead-light">
            <tr>
                <th>{{__('form_fields.student.fields.faculty')}}</th>
                <th width="25%">Semester/Sec</th>
                <th width="30%">Start & End Date</th>
                <th width="10%">Status</th>
                <th width="10%">Action</th>
            </tr>
            </thead>

            <tbody id="program_wrapper">
            @if (isset($data['exist_program']) && $data['exist_program']->count() > 0)
                @foreach($data['exist_program'] as $program)
                    @include($view_path.'.includes.program_tr_edit')
                @endforeach
            @endif
            </tbody>
            </table>
        </div>
    </div>
</div>
