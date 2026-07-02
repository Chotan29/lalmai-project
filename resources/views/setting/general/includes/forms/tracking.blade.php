<div class="form-group">
    <label class="col-sm-2 control-label">
        <i class="fa fa-chart-bar bigger-120 white" aria-hidden="true"></i> Tracking Code's Like:Analytics, Facebook Pixel/ChatWidget
    </label>
    <div class="col-sm-8">
        {!! Form::textarea('tracking_code', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'tracking_code'])
    </div>
</div>