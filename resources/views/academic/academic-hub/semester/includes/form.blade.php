<div class="form-group">
    {!! Form::label('semester', 'Sem./Sec.', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('semester', null, ["placeholder" => "", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'semester'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('slug', 'Slug', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('slug', null, ["placeholder" => "", "class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'slug'])
    </div>
</div>


<div class="form-group">
    {!! Form::label('semester_fee', 'Sem/Sec Fee', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::number('semester_fee', 0, ["class" => "form-control border-form",'min'=>'0','step'=>'any']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'semester_fee'])
    </div>
</div>


<div class="form-group">
    {!! Form::label('gradingType_id', 'Grading Type', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('gradingType_id',$data['gradingScales'], null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'gradingType_id'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('staff_id', 'Teacher/Staff', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('staff_id',$data['staffs'], null, ["class" => "form-control border-form chosen-select","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'staffs'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('semester', 'Course Find and Add', ['class' => 'col-sm-12 control-label align-center']) !!}
    <div class="col-sm-12">
        {!! Form::select('subject_id', [], null, ["placeholder" => "Type Course Name...", "class" => "col-xs-12 col-sm-12", "style" => "width: 100%;"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'subject_id'])
        <hr>
        <div class="align-right">
            <button type="button" class="btn btn-sm btn-primary" id="load-html-btn">
                <i class="fa fa-plus bigger-120"></i> Add Course
            </button>
        </div>
    </div>
</div>
<div class="space-4"></div>
<!-- Option Values -->
@include($view_path.'.includes.subject')

<div class="space-4"></div>
<hr class="hr-2">
<div class="form-group">
    {!! Form::label('major_subject_count', 'Total Major Subjects', ['class' => 'col-sm-6 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::number('major_subject_count', null, ["placeholder" => "", "class" => "input-sm form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'major_subject_count'])
    </div>
</div>
<div class="space-4"></div>
<hr class="hr-2">
@if(isset($data['academicInfoLevels']) && $data['academicInfoLevels']->count() > 0)
    <div class="form-group">
        <label class="col-sm-12 control-label align-left" for="status"> Check Required Academic Info While Register &nbsp;&nbsp;&nbsp;</label>
        @foreach($data['academicInfoLevels'] as $level)
            <div class="row">
                <div class="control-group">
                    <div class="checkbox">
                        <label>
                            @if (!isset($data['row']))
                                {!! Form::checkbox('academic_level[]', $level->id, false, ['class' => 'ace']) !!}
                            @else
                                {!! Form::checkbox('academic_level[]', $level->id, array_key_exists($level->id, $data['active_academic_level']), ['class' => 'ace']) !!}
                            @endif
                            <span class="lbl"> {{ $level->title }} </span>
                        </label>
                    </div>
                </div>
            </div>
        @endforeach
        @include('includes.form_fields_validation_message', ['name' => 'semester[]'])
    </div>
@endif