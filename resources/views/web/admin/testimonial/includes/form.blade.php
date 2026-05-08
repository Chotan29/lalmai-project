<div class="form-group">
    {!! Form::label('name', 'Full Name', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('name', null, ["placeholder" => "", "class" => "form-control border-form","required"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'Name'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('comment', 'Testimonial', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('comment', null, ["placeholder" => "Comment", "class" => "form-control border-form form-control","rows"=>'3',"required"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'comment'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('email', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
    </div>
    {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        <!-- input field attributes-->
        {!! Form::text('address', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>
</div>

<div class="space-4"></div>
<!-- label-->
<div class="form-group">
    {!! Form::label('office', 'Office', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('office', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'office'])
    </div>

    {!! Form::label('position', 'Designation', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('position', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'position'])
    </div>
</div>

<div class="space-4"></div>
<!-- label-->
<div class="form-group">
    {!! Form::label('link', 'Link', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('link', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'link'])
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))

    <div class="form-group">
        <label class="col-sm-2 control-label">Existing Image</label>
        <div class="col-sm-10">
            @if ($data['row']->profile_image)
                <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->profile_image) }}" width="250">
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
        @include('includes.form_fields_validation_message', ['name' => 'main_image', "required"])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recomended Minimum Image Size 81px X 81px(72dpi)</span>
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