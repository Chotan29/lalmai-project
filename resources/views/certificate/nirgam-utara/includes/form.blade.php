<div class="form-group">
    {!! Form::label('nu_num', 'NU. No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        @if(isset($data['row']) && !isset($data['nu_num']))
            {!! Form::text('nu_num', $data['row']->nu_num, ["class" => "form-control border-form","required","readonly"]) !!}
        @else
            {!! Form::text('nu_num', $data['nu_num'], ["class" => "form-control border-form","required","readonly"]) !!}
        @endif
        @include('includes.form_fields_validation_message', ['name' => 'nu_num'])
    </div>

    {!! Form::label('date_of_issue', 'Issue Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_issue', null, ["class" => "form-control date-picker border-form input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_issue'])
    </div>

    {!! Form::label('date_of_leaving', 'Leave Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_leaving', null, ["class" => "form-control date-picker border-form input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_leaving'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('previous_school_name', 'Previous School Name', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('previous_school_name', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'previous_school_name'])
    </div>

    {!! Form::label('reason_to_leave', 'Reason To Leave', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('reason_to_leave', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reason_to_leave'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('join_time_class', 'Join Time Level/Class', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('join_time_class', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'join_time_class'])
    </div>

    {!! Form::label('leaving_time_class', 'Leaving Time Level/Class', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('leaving_time_class', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'leaving_time_class'])
    </div>

    {!! Form::label('mention_body_mark', 'Body Mark', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mention_body_mark', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mention_body_mark'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('any_other_remark', 'Any Other Remarks', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('any_other_remark', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'any_other_remark'])
    </div>

</div>

<hr class="hr-2">
@include('certificate.includes.student-detail')