{{--'date_of_issue', 'course', 'period', 'character', 'ref_text','status'--}}
<div class="form-group">

    {!! Form::label('trc_num', 'TRC.No.', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        @if(isset($data['row']) && !isset($data['trc_num']))
            {!! Form::text('trc_num', $data['row']->trc_num, ["class" => "form-control border-form","required","readonly"]) !!}
        @else
            {!! Form::text('trc_num', $data['trc_num'], ["class" => "form-control border-form","required","readonly"]) !!}
        @endif
        @include('includes.form_fields_validation_message', ['name' => 'trc_num'])
    </div>

    {!! Form::label('date_of_issue', 'Issue Date', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('date_of_issue', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_issue'])
    </div>

    {!! Form::label('year', 'Pass Year', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('year', null, [ "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'year'])
    </div>

    {!! Form::label('provisional_certificate_num', 'Provisional Certificate Number', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('provisional_certificate_num', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'provisional_certificate_num'])
    </div>

    {!! Form::label('mark_sheet_sn', 'Mark Sheet SerialNo.', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mark_sheet_sn', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mark_sheet_sn'])
    </div>

</div>
<div class="form-group">
    {!! Form::label('verification_code', 'Verification Code', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('verification_code', isset($data['row']->reg_no)?$data['row']->reg_no:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'verification_code'])
    </div>

    {!! Form::label('duration', 'Duration', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('duration', isset($data['TranscriptData']->duration)?$data['TranscriptData']->duration:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'duration'])
    </div>

    {!! Form::label('credit_required', 'Credit Required', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('credit_required', isset($data['TranscriptData']->credit_required)?$data['TranscriptData']->credit_required:null, ["class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'credit_required'])
    </div>

    {!! Form::label('gpa', 'GPA', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('gpa', isset($data['TranscriptData']->transcriptGPA)?$data['TranscriptData']->transcriptGPA:null, [ "class" => "form-control border-form upper","required","disabled"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'gpa'])
    </div>
</div>

<hr class="hr-2">
@include('certificate.includes.student-detail')