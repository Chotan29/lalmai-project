{{--<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input are required. </span>
<hr class="hr-16">--}}
<div class="form-group">
@if (!isset($data['row']))
        <label class="col-sm-2 control-label">Group</label>
        <div class="col-sm-4">
            {{-- {!! Form::select('programme', $data['faculties'], null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);', "required"]) !!}--}}
            <select name="contact_directory_groups_id" class="form-control" id="form-field-select-3" data-placeholder="Choose a Group..." >
                <option value="" disabled="disabled"> Choose Group </option>
                @foreach( $data['group'] as $key => $group)
                    <option value="{{ $key }}">{{ $group }}</option>
                @endforeach
            </select>
        </div>
    @else
        <label class="col-sm-2 control-label">Group</label>
        <div class="col-sm-4">
            <select name="contact_directory_groups_id" class="form-control"  data-placeholder="Choose a Group..." >
                <option value="" disabled="disabled"> Choose Group </option>
                @foreach( $data['group'] as $key => $group)
                    <option value="{{ $key }}" {{($data['row']->contact_directory_groups_id == $key)?"selected":""}}>{{ $group }}</option>
                @endforeach
            </select>
            @include('includes.form_fields_validation_message', ['name' => 'programme'])
        </div>
    @endif

    {!! Form::label('name', 'Name', ['class' => 'col-sm-1 control-label',]) !!}
    <div class="col-sm-5">
        {!! Form::text('name', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'name'])
    </div>

</div>

<div class="form-group">
    {!! Form::label('sex', 'Sex', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('sex', __('common.gender'), null, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'sex'])
    </div>

    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_birth', null, ["data-date-format" => "dd-mm-yyyy", "class" => "form-control border-form date-picker input-mask-date"]) !!}
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
        {!! Form::text('nationality', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'nationality'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('mother_tongue', __('form_fields.student.fields.mother_tongue'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_tongue', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_tongue'])
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
        {!! Form::text('cell_1', null, ["class" => "form-control border-form input-mask-cell"]) !!}
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

<h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-picture"></i>Image</h4>
<div class="form-group">
    {!! Form::label('main_image', 'Image', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'main_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Minimum Image Size 300px X 300px</span>
    </div>

    @if (isset($data['row']))
        @if ($data['row']->image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'contact'.DIRECTORY_SEPARATOR.$data['row']->image) }}" class="img-responsive" width="100px">
        @endif
    @endif
</div>