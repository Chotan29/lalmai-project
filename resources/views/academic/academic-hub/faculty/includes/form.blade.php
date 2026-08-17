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

{{-- ID card validity. The card used to work the expiry out one way for every department -
     31 December of the year after the session - which is right for HSC and wrong for a three or
     four year course. These two boxes let each department say how long its course runs and which
     month it ends in; the card prints the last day of that month. --}}
<div class="form-group">
    {!! Form::label('id_card_valid_years', 'ID Card Valid (Years)', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::number('id_card_valid_years', null, ["class" => "form-control border-form", "min" => 1, "max" => 10, "placeholder" => "e.g. 2 for HSC, 3 for Degree, 4 for Hon's"]) !!}
        <span class="help-block small">
            Course length in years. The ID card expires this many years after the session begins,
            so a 2025-2026 four year course expires in 2029.
        </span>
        @include('includes.form_fields_validation_message', ['name' => 'id_card_valid_years'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('id_card_expiry_month', 'ID Card Expiry Month', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('id_card_expiry_month', [
                '' => '-- not set --',
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ], null, ["class" => "form-control border-form"]) !!}
        <span class="help-block small">
            The card prints the last day of this month - June gives 30 June, December gives
            31 December. Leave both boxes empty and the card falls back to the college-wide
            setting, then to its old rule, so it is never printed without a date.
        </span>
        @include('includes.form_fields_validation_message', ['name' => 'id_card_expiry_month'])
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

