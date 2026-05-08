<div class="form-group">
    {!! Form::label('analytics_view_id', 'Analytics View Id', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('analytics_view_id', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'analytics_view_id'])
    </div>
</div>
<span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Setup Google Analytics For Guide <a href="https://github.com/spatie/laravel-analytics#how-to-obtain-the-credentials-to-communicate-with-google-analytics" target="_blank">Visit Official Package Guide</a></span>


<div class="space-4"></div>

@if (isset($data['row']))
    @if ($data['row']->analytics_json_file)
        <div class="form-group">
            <label class="col-sm-2 control-label">Existing JsonFile</label>
            <div class="col-sm-10">
                <a href="{{ asset('app/analytics/'.$data['row']->analytics_json_file) }}" target="_blank"><i class="icon-download"></i> {{$data['row']->analytics_json_file}}</a>
            </div>
        </div>
    @endif
@endif

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('json_file', 'Config JsonFile', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::file('json_file') !!}
        @include('includes.form_fields_validation_message', ['name' => 'json_file'])
    </div>
</div>

<div class="space-4"></div>


{{--'analytics_view_id','analytics_json_file','recaptcha_secret_key'--}}
<div class="form-group">
    {!! Form::label('recaptcha_site_key', 'Google Captcha Site Key', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('recaptcha_site_key', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'recaptcha_site_key'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('recaptcha_secret_key', 'Google Captcha Secret', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('recaptcha_secret_key', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'recaptcha_secret_key'])
    </div>
</div>
<span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Setup Google Captcha For Secure the Contact Form. <a href="https://www.google.com/recaptcha/intro/v3.html" target="_blank">Visit Official Page To Generate Captcha Secret</a></span>
<div class="space-4"></div>

