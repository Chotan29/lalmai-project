{{--'date_of_issue', 'course', 'period', 'character', 'ref_text','status'--}}
<div class="form-group">
    {!! Form::label('tmc_num', 'TMC. No.', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        @if(isset($data['row']) && !isset($data['tmc_num']))
            {!! Form::text('tmc_num', $data['row']->tmc_num, ["class" => "form-control border-form","required","readonly"]) !!}
        @else
            {!! Form::text('tmc_num', $data['tmc_num'], ["class" => "form-control border-form","required","readonly"]) !!}
        @endif
        @include('includes.form_fields_validation_message', ['name' => 'tmc_num'])
    </div>

    {!! Form::label('date_of_issue', 'Issue Date', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_issue', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_issue'])
    </div>

    {!! Form::label('ref_num', 'Ref.No.', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('ref_num', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'ref_num'])
    </div>

    {!! Form::label('year', 'Pass Year', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('year', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'year'])
    </div>


</div>
<div class="form-group">
    {!! Form::label('program_duration', 'Course Duration', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('program_duration', isset($data['TranscriptData']->duration)?$data['TranscriptData']->duration:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'program_duration'])
    </div>

    {!! Form::label('scale', 'Scale', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('scale', isset($data['TranscriptData']->scale)?$data['TranscriptData']->scale:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'scale'])
    </div>

    {!! Form::label('gpa', 'GPA', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('gpa', isset($data['TranscriptData']->transcriptGPA)?$data['TranscriptData']->transcriptGPA:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'gpa'])
    </div>




    {!! Form::label('average_grade', 'Average Grade', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('average_grade', isset($data['TranscriptData']->transcriptGL)?$data['TranscriptData']->transcriptGL:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'average_grade'])
    </div>
</div>
{{--'
        '', 'year', 'gpa', 'scale', 'average_grade', 'ref_text','status'--}}
<hr class="hr-2">
@include('certificate.includes.student-detail')