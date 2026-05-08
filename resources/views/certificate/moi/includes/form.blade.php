{{--'date_of_issue', 'course', 'period', 'character', 'ref_text','status'--}}
<div class="form-group">

    {!! Form::label('moic_num', 'MOI C.No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        @if(isset($data['row']) && !isset($data['moic_num']))
            {!! Form::text('moic_num', $data['row']->moic_num, ["class" => "form-control border-form","required","readonly"]) !!}
        @else
            {!! Form::text('moic_num', $data['moic_num'], ["class" => "form-control border-form","required","readonly"]) !!}
        @endif
        @include('includes.form_fields_validation_message', ['name' => 'moic_num'])
    </div>

    {!! Form::label('date_of_issue', 'Issue Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_issue', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_issue'])
    </div>

    {!! Form::label('ref_num', 'Ref.No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('ref_num', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'ref_num'])
    </div>
</div>
<hr class="hr-2">
@include('certificate.includes.student-detail')