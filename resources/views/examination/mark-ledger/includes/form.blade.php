<div class="form-group">
    {!! Form::label('years_id', 'Year', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('years_id', $data['years'], old('years_id'), ["class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'years_id'])
    </div>

    {!! Form::label('months_id', 'Month', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('months_id', $data['months'], old('months_id'), ["class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'months_id'])
    </div>

    {!! Form::label('exams_id', 'Exam', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-5">
        {!! Form::select('exams_id', $data['exams'], old('exams_id'), ["class" => "form-control border-form chosen-select","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'exams_id'])
    </div>

</div>
<div class="form-group">

    <label class="col-sm-1 control-label">{{ __('form_fields.student.fields.faculty')}}</label>
    <div class="col-sm-4">
        {!! Form::select('faculty', $data['faculties'], old('faculty'), ['class' => 'form-control chosen-select', 'onChange' => 'loadSemesters(this);']) !!}

    </div>

    <label class="col-sm-1 control-label">{{__('form_fields.student.fields.semester')}}</label>
    <div class="col-sm-2">
        <select name="semester_select" class="form-control semester_select" onChange ="loadSubject(this)">
            <option> Select {{__('form_fields.student.fields.semester')}} </option>
        </select>
    </div>

    <label class="col-sm-1 control-label">Subject</label>
    <div class="col-sm-3">
        <select name="schedule_subject" class="form-control schedule_subject" onChange ="loadStudent(this)">
            <option> Select Subject </option>
        </select>
    </div>

</div>

<div class="space-4"></div>
<!-- Option Values -->
@include($view_path.'.includes.student')
