<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))

    <div class="space-4"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Existing Image</label>
        <div class="col-sm-10">
            @if ($data['row']->image_name)
                <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->image_name) }}" class="img-preview" class="img-responsive">
            @else
                <p>No image.</p>
            @endif
        </div>
    </div>
@endif

<div class="form-group">
    {!! Form::label('main_image', 'Image', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::file('main_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'main_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recomended Image Size 224px X 124px(72dpi)</span>
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('link', 'Target Link', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('link', null, ["placeholder" => "URL", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'link'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('rank', 'Rank', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::number('rank', null, ["placeholder" => "Enter Rank", "class" => "form-control border-form", 'min' => 0, 'required']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'rank'])
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