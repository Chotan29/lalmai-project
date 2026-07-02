<div class="btn btn-block btn-primary">Logo & Default Banner Setting</div>
<hr class="hr-16" />

@if (isset($data['row']))
    <div class="form-group">
        <label class="col-sm-3 control-label">Existing Favicon</label>
        <div class="col-sm-9">
            @if ($data['row']->favicon)
                <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->favicon) }}">
            @else
                <p>No image.</p>
            @endif
        </div>
    </div>
@endif
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('favicon_image', 'Favicon', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::file('favicon_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'favicon_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recommended Minimum Image Size 16px X 16px(72dpi)</span>
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))
    <div class="form-group">
        <label class="col-sm-3 control-label">Existing Site Logo</label>
        <div class="col-sm-9">
            @if ($data['row']->site_logo)
                <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->site_logo) }}" >
            @else
                <p>No image.</p>
            @endif
        </div>
    </div>
@endif
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('site_logo_image', 'Site Logo', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::file('site_logo_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'site_logo_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recommended Image Size 404*px X 84px(72dpi)</span>
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))
    <div class="form-group">
        <label class="col-sm-3 control-label">Existing Default Banner</label>
        <div class="col-sm-9">
            @if ($data['row']->page_banner)
                <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->page_banner) }}" width="250">
            @else
                <p>No image.</p>
            @endif
        </div>
    </div>
@endif
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('page_banner_image', 'Default Banner', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::file('page_banner_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'page_banner_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recommended Minimum Image Size 770px X 513px(72dpi)</span>
    </div>
</div>
<div class="space-4"></div>