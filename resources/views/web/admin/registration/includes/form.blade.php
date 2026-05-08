{{--<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input are required. </span>
<hr class="hr-16">--}}
<div class="form-group">
@if (!isset($data['row']))
        <label class="col-sm-2 control-label">Application For</label>
        <div class="col-sm-6">
            {{-- {!! Form::select('programme', $data['faculties'], null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);', "required"]) !!}--}}
            <select name="web_registration_programmes_id" class="form-control" id="form-field-select-3" data-placeholder="Choose a Application For..." >
                <option value="" disabled="disabled"> Choose Application For </option>
                @foreach( $data['programmes'] as $key => $programme)
                    <option value="{{ $key }}">{{ $programme }}</option>
                @endforeach
            </select>
        </div>
    @else
        <label class="col-sm-2 control-label">Application For</label>
        <div class="col-sm-6">
            <select name="web_registration_programmes_id" class="form-control"  data-placeholder="Choose a Application For..." >
                <option value="" disabled="disabled"> Choose Application For </option>
                @foreach( $data['programmes'] as $key => $programme)
                    <option value="{{ $key }}" {{($data['row']->web_registration_programmes_id == $key)?"selected":""}}>{{ $programme }}</option>
                @endforeach
            </select>
            @include('includes.form_fields_validation_message', ['name' => 'programme'])
        </div>
    @endif

    {!! Form::label('reg_date', 'Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('reg_date', null, ["data-date-format" => "dd-mm-YYYY", "class" => "form-control date-picker border-form input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reg_date'])
    </div>

</div>


<div class="form-group">
    {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-10">
        {!! Form::text('name', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'name'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('sex', 'Sex', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('sex', __('common.gender'), null, ['class'=>'form-control border-form',"required"]); !!}
        @include('includes.form_fields_validation_message', ['name' => 'sex'])
    </div>

    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_birth', null, ["data-date-format" => "dd-mm-yyyy", "class" => "form-control border-form date-picker input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>

    {!! Form::label('blood_group', __('form_fields.student.fields.blood_group'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('blood_group', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'blood_group'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('religion', __('form_fields.student.fields.religion'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('religion', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'religion'])
    </div>

    {!! Form::label('caste', __('form_fields.student.fields.caste'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('caste', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'caste'])
    </div>

    {!! Form::label('nationality', __('form_fields.student.fields.nationality'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('nationality', null, ["placeholder" => "", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'nationality'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('mother_tongue', __('form_fields.student.fields.mother_tongue'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_tongue', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_tongue'])
    </div>

    {!! Form::label('state', 'State of Permanent Residence', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('state', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'state'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('medicine_info', 'Allergic to any medicine. If yes, Detail', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::textarea('medicine_info', null, ["class" => "form-control border-form", "rows"=>"1"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'medicine_info'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('disease_info', 'Affected by any recurring disease. If yes, Detail', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::textarea('disease_info', null, ["class" => "form-control border-form", "rows"=>"1"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'disease_info'])
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Guardian Detail</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('guardian_name', 'Name of Father/Guardian', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-5">
        {!! Form::text('guardian_name', null, [ "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_name'])
    </div>

    {!! Form::label('guardian_relation', 'Guardian Relation', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('guardian_relation', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_relation'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('guardian_occupation', 'Occupation', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-5">
        {!! Form::text('guardian_occupation', null, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_occupation'])
    </div>

    {!! Form::label('guardian_annual_income', 'Annual Income', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('guardian_annual_income', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_annual_income'])
    </div>
</div>


<div class="label label-warning arrowed-in arrowed-right arrowed">Permanent Address</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('address', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('tel', 'Telephone', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('tel', null, ["class" => "form-control border-form input-mask-phone"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'tel'])
    </div>

    {!! Form::label('cell_1', 'Mobile', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('cell_1', null, ["class" => "form-control border-form input-mask-cell","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'cell_1'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('cell_2', 'Alternative Contact', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('cell_2', null, ["class" => "form-control border-form input-mask-cell"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'cell_2'])
    </div>

    {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('email', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Mailing Address</div>

<div class="control-group col-sm-12">
    <div class="radio">
        <label>
            {!! Form::checkbox('mailing_address_copier', '', false, ['class' => 'ace', "onclick"=>"CopyAddress(this.form)"]) !!}
            <span class="lbl"> Mailing Address Same As Permanent Address</span>
        </label>
    </div>
</div>

<hr>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('mailing_address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('mailing_address', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mailing_address'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('mailing_tel', 'Telephone', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mailing_tel', null, ["class" => "form-control border-form input-mask-phone"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mailing_tel'])
    </div>

    {!! Form::label('mailing_cell_1', 'Mobile', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mailing_cell_1', null, ["class" => "form-control border-form input-mask-cell"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mailing_cell_1'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('mailing_cell_2', 'Alternative Contact', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mailing_cell_2', null, ["class" => "form-control border-form input-mask-cell"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mailing_cell_2'])
    </div>

    {!! Form::label('mailing_email', 'E-mail', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mailing_email', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mailing_email'])
    </div>
</div>

<h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-picture"></i>Photo & Signature</h4>
<div class="form-group">
    {!! Form::label('student_main_image', 'Student Profile Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('student_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'student_main_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recomended Image Size 300px X 300px</span>
    </div>

    @if (isset($data['row']))
        @if ($data['row']->student_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'registration'.DIRECTORY_SEPARATOR.$data['row']->student_image) }}" class="img-responsive" width="100px">
        @endif
    @endif
</div>


<div class="form-group">
    {!! Form::label('student_signature_main_image', 'Student Signature', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('student_signature_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'student_signature_main_image'])
    </div>

    @if (isset($data['row']))
        @if ($data['row']->student_signature)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'registration'.DIRECTORY_SEPARATOR.$data['row']->student_signature) }}" class="img-responsive" width="100px">
        @endif
    @endif
</div>

<div class="form-group">
    {!! Form::label('guardian_signature_main_image', 'Guardian Signature', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('guardian_signature_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_signature_main_image'])
    </div>
    @if (isset($data['row']))
        @if ($data['row']->guardian_signature)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'registration'.DIRECTORY_SEPARATOR.$data['row']->guardian_signature) }}" class="img-responsive" width="100px">
        @endif
    @endif
</div>

@include($view_path.'.includes.academicinfo')
@include($view_path.'.includes.workexperience')