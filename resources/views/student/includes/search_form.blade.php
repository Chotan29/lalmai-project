<div class="form-group">
    {!! Form::label('reg_no', __('form_fields.student.fields.reg_no'), ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration", "autofocus"]) !!}
    </div>

    {!! Form::label('reg_date', __('form_fields.student.fields.reg_date'), ['class' => 'col-sm-1 control-label']) !!}
    <div class=" col-sm-3">
        <div class="input-group ">
            {!! Form::text('reg_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
            <span class="input-group-addon">
                <i class="fa fa-exchange"></i>
            </span>
            {!! Form::text('reg_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
        </div>
    </div>

    <label class="col-sm-1 control-label">{{ __('common.status')}}</label>
    <div class="col-sm-2">
        {!! Form::select('academic_status', $data['academic_status'], null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}
    </div>
    <div class="col-sm-2">
        <select class="form-control border-form" name="status" id="cat_id">
            <option value="all"> {{ __('common.status')}} </option>
            <option value="active" >{{ __('common.active')}}</option>
            <option value="in-active" >{{ __('common.in_active')}}</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.faculty')}}</label>
    <div class="col-sm-4">
        {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control chosen-select', 'onChange' => 'loadSemesters(this);']) !!}
    </div>

    <label class="col-sm-1 control-label">{{__('form_fields.student.fields.semester')}}</label>
    <div class="col-sm-2">
        <select name="semester_select" class="form-control semester_select" >
            <option value="0"> Select {{__('form_fields.student.fields.semester')}} </option>
        </select>
    </div>

    <label class="col-sm-1 control-label">{{__('form_fields.student.fields.batch')}}</label>
    <div class="col-sm-2">
        {!! Form::select('batch', $data['batch'], null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">

    {!! Form::label('first_name', __('form_fields.student.fields.name_of_student'), ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-2">
        {!! Form::text('first_name', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>
    <div class="col-sm-2">
        {!! Form::text('middle_name', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'middle_name'])
    </div>
    <div class="col-sm-2">
        {!! Form::text('last_name', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'last_name'])
    </div>

    {!! Form::label('gender', __('form_fields.student.fields.gender'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('gender', __('common.gender'), null, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
</div>

<div class="form-group">

    {!! Form::label('blood_group', __('form_fields.student.fields.blood_group'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('blood_group', __('common.blood_group'), null,
        [ 'class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'blood_group'])
    </div>



    {!! Form::label('date_of_birth', __('form_fields.student.fields.date_of_birth'), ['class' => 'col-sm-1 control-label']) !!}
    <div class=" col-sm-3">
        <div class="input-group ">
            {!! Form::text('date_of_birth_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
            <span class="input-group-addon">
                <i class="fa fa-exchange"></i>
            </span>
            {!! Form::text('date_of_birth_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
        </div>
    </div>

    {!! Form::label('university_reg', __('form_fields.student.fields.university_reg'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('university_reg', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
    </div>


</div>

<div class="form-group">
    {!! Form::label('nationality', __('form_fields.student.fields.nationality'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('nationality', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
    </div>

    {!! Form::label('national_id_1', __('form_fields.student.fields.national_id_1'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('national_id_1', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
    </div>

    {!! Form::label('national_id_2', __('form_fields.student.fields.national_id_2'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('national_id_2', null, ["class" => "form-control border-form"]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('national_id_3', __('form_fields.student.fields.national_id_3'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('national_id_3', null, ["class" => "form-control border-form"]) !!}
    </div>

    {!! Form::label('national_id_4', __('form_fields.student.fields.national_id_4'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('national_id_4', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
    </div>
    {!! Form::label('mother_tongue', __('form_fields.student.fields.mother_tongue'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_tongue', null, ["class" => "form-control border-form"]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('religion', __('form_fields.student.fields.religion'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('religion', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
    </div>

    {!! Form::label('caste', __('form_fields.student.fields.caste'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('caste', null, ["class" => "form-control border-form"]) !!}
    </div>
    {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('email', null, ["class" => "form-control border-form"]) !!}
    </div>
</div>