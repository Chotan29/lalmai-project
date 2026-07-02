<div class="btn btn-block btn-primary">Counter Setting</div>
<div class="space-8"></div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="status"> Counter Status </label>
    <div class="col-sm-10">
        {!! Form::select('counter_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'counter_status'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('counter_title', 'Counter Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('counter_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'counter_title'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('counter_slogan', 'Counter Slogan', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('counter_slogan', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'counter_slogan'])
    </div>
</div>
