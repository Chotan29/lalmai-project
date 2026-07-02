<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('new_price', 'New Price', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::number('new_price', null, ["placeholder" => "", "class" => "form-control border-form", "step" => "any"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'new_price'])
    </div>
    {!! Form::label('old_price', 'Old Price', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::number('old_price', null, ["placeholder" => "", "class" => "form-control border-form",  "step" => "any"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'old_price'])
    </div>
    {!! Form::label('per_term', 'Term', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('per_term', null, ["placeholder" => "Monthly/Fixed", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'per_term'])
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
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('tag', 'Tag', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('tag', null, ["placeholder" => "Best", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'tag'])
    </div>
    <div class="col-sm-3">
        {!! Form::color('tag_color', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'tag_color'])
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
    {!! Form::label('button_text', 'Button Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('button_text', null, ["placeholder" => "Button Text", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'button_text'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('open_in', 'Target Page', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::select('open_in', ['_self' => 'Same Page', '_blank' => 'New Tab'], null, ["placeholder" => "Choose Target Page", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'open_in'])
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