<div class="btn btn-block btn-primary">Website Basic Setting</div>
<hr class="hr-16" />
<div class="form-group">
    {!! Form::label('site_title', 'Site Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('site_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'site_title'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('site_slogan', 'Site Slogan', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('site_slogan', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'site_slogan'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('site_desc', 'Site Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('site_desc', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'site_desc'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('site_keyword', 'Site Keywords', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('site_keyword', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'site_keyword'])
    </div>
</div>

<span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Site Title, Description & Keyword Pull on Home Page SEO</span>
