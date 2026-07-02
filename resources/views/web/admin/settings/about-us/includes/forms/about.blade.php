<div class="btn btn-block btn-primary">About Us Detail Setting</div>
<div class="space-8"></div>

<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('slogan', 'Slogan', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('slogan', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'slogan'])
    </div>
</div>

<div class="space-4"></div>

<div id="content">
    @if (isset($data['row']))
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
            <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recommended Minimum Image Size 570px X 418px(72dpi)</span>
        </div>
    </div>

    <div class="space-4"></div>

    <div class="form-group">
        {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::textarea('description', null, ["placeholder" => "", "class" => "form-control border-form summernote"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'description'])
        </div>
    </div>

    <div class="space-4"></div>
</div>