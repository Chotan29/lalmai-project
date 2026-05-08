<div class="btn btn-block btn-primary">Services Settings</div>
<div class="space-8"></div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Status </label>
    <div class="col-sm-9">
        {!! Form::select('services_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'services_status'])
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Title </label>
    <div class="col-sm-9">
        {!! Form::text('services_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'services_title'])
    </div>
</div>
