{{--'date_of_issue', 'course', 'period', 'character', 'ref_text','status'--}}
<div class="form-group">

    {!! Form::label('pc_num', 'CC.No.', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        @if(isset($data['row']) && !isset($data['pc_num']))
            {!! Form::text('pc_num', $data['row']->pc_num, ["class" => "form-control border-form","required","readonly"]) !!}
        @else
            {!! Form::text('pc_num', $data['pc_num'], ["class" => "form-control border-form","required","readonly"]) !!}
        @endif
        @include('includes.form_fields_validation_message', ['name' => 'pc_num'])
    </div>

    {!! Form::label('date_of_issue', 'Issue Date', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('date_of_issue', null, ["class" => "form-control date-picker border-form input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_issue'])
    </div>

    {!! Form::label('result_publish_date', 'Result Publish', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('result_publish_date', null, ["class" => "form-control date-picker border-form input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'result_publish_date'])
    </div>

    {!! Form::label('year', 'Pass Year', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('year', null, [ "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'year'])
    </div>


    {!! Form::label('gpa', 'GPA', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('gpa', isset($data['TranscriptData']->transcriptGPA)?$data['TranscriptData']->transcriptGPA:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'gpa'])
    </div>

    {!! Form::label('scale', 'Scale', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('scale', isset($data['TranscriptData']->scale)?$data['TranscriptData']->scale:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'scale'])
    </div>
</div>
<hr class="hr-2">
@include('certificate.includes.student-detail')