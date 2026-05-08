<div class="form-group">
    {!! Form::label('faculty', __('academic.child.academic_level.child.faculty'), ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('faculty', null, ["placeholder" => "e.g. Class One/Bachelor of Business Admin..", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'faculty'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('faculty_code', 'Code', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('faculty_code', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'faculty_code'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('gradingType_id', 'Grading Type', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('gradingType_id',$data['gradingScales'], null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'gradingType_id'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('scale', 'Scale', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('scale', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'scale'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('duration', 'Duration', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('duration', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'duration'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('credit_required', 'Credit Required', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('credit_required', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'credit_required'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('registration_validate', 'Registration Valid Period', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('registration_validate', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'registration_validate'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('sorting', 'Sorting Order', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::number('sorting', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'sorting'])
    </div>
</div>

@if(isset($data['semester']) && $data['semester']->count() > 0)
    <div class="form-group">
        <label class="col-sm-12 control-label align-left" for="status"> Check Semester/Section &nbsp;&nbsp;&nbsp;</label>
        @foreach($data['semester'] as $semester)
            <div class="row">
                <div class="control-group">
                    <div class="checkbox">
                        <label>
                            @if (!isset($data['row']))
                                {!! Form::checkbox('semester[]', $semester->id, false, ['class' => 'ace']) !!}
                            @else
                                {!! Form::checkbox('semester[]', $semester->id, array_key_exists($semester->id, $data['active_semester']), ['class' => 'ace']) !!}
                            @endif
                            <span class="lbl"> {{ $semester->semester.' - '.$semester->slug }} </span>
                        </label>
                    </div>
                </div>
            </div>
        @endforeach
        @include('includes.form_fields_validation_message', ['name' => 'semester[]'])
    </div>
@endif

