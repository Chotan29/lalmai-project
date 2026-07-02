<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', null, ["placeholder" => "Title", "class" => "form-control border-form", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', null, ["placeholder" => "Description", "class" => "form-control border-form form-control summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'description'])
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))

    <div class="space-4"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Existing Image</label>
        <div class="col-sm-10">
            @if ($data['row']->image)
                <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->image) }}" width="500">
            @else
                <p>No image.</p>
            @endif
        </div>
    </div>


@endif

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('main_image', 'Image', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::file('main_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'main_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recomended Image Size 801px X 439px(72dpi)</span>
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('event_date', 'Event Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {{--{!! Form::text('event_date', null, ["class" => "form-control border-form date-picker input-mask-date","required"]) !!}--}}
        {!! Form::text('event_date', null, ["data-date-format" => "yyyy-mm-dd", "class" => "form-control date-picker border-form input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'event_date'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('event_place', 'Event Place', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('event_place', null, ["placeholder" => "", "class" => "form-control border-form", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'event_place'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('event_time', 'Event Time', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('event_time', null, ["placeholder" => "4.00 PM - 8.00 PM", "class" => "form-control border-form", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'event_time'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('seo_title', 'SEO Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('seo_title', null, ["placeholder" => "SEO Title", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'seo_title'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('seo_keywords', 'SEO Keyword', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('seo_keywords', null, ["placeholder" => "SEO Keywords", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'seo_keywords'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('seo_description', 'SEO Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('seo_description', null, ["placeholder" => "Seo Description", "class" => "form-control border-form form-control","rows"=>"3"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'seo_description'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="status"> Status </label>

    <div class="col-sm-10">
        <div class="control-group">

            <div class="radio">
                <label>
                    {!! Form::radio('status', 'active', true, ['class' => 'ace']) !!}
                    <span class="lbl"> Active</span>
                </label>

                <label>
                    {!! Form::radio('status', 'in-active', false, ['class' => 'ace']) !!}
                    <span class="lbl"> Inactive</span>
                </label>
            </div>
            @include('includes.form_fields_validation_message', ['name' => 'status'])
        </div>
    </div>
</div>

<div class="space-4"></div>