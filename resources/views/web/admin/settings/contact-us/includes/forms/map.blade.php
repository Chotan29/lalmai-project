
<div class="form-group">
    {!! Form::label('google_map', 'Google Map', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('google_map', null, ["placeholder" => "", "class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'google_map'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Google Maps Iframe Width="100%" Height="600px"</span>
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('latitude', 'Latitude', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('latitude', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'latitude'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('longitude', 'Longitude', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('longitude', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'longitude'])
    </div>
</div>

<div class="space-4"></div>