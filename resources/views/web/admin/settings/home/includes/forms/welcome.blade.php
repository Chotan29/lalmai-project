<div class="btn btn-block btn-primary">Welcome Area Setting</div>
<div class="space-8"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Welcome Status </label>
    <div class="col-sm-9">
        {!! Form::select('welcome_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'welcome_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('welcome_title', 'Title', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('welcome_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'welcome_title'])
    </div>
</div>
<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('welcome_sub_title', 'Sub Title', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('welcome_sub_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'welcome_sub_title'])
    </div>
</div>
<div class="space-4"></div>


@if (isset($data['row']) && $data['row']->welcome_image)
    <div class="form-group">
        <label class="col-sm-3 control-label">Existing Image</label>
        <div class="col-sm-9">
            <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->welcome_image) }}" width="200">
        </div>
    </div>
@endif


<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('main_image', 'Image', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::file('main_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'main_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recommended Minimum Image Size 585px X 385px(72dpi)</span>
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('welcome_description', 'Description', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::textarea('welcome_description', null, ["placeholder" => "", "class" => "form-control border-form summernote","rows"=>"5"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'welcome_description'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('welcome_button_text', 'Button Text', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('welcome_button_text', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'welcome_button_text'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('welcome_link', 'Button Link', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('welcome_link', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'welcome_link'])
    </div>
</div>