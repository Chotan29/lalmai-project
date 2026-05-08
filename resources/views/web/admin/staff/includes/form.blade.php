<div class="form-group">
    {!! Form::label('name', 'Full Name', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-5">
        {!! Form::text('name', null, ["placeholder" => "", "class" => "form-control border-form", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'Name'])
    </div>

    {!! Form::label('position', 'Designation', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-3">
        <!-- input field attributes-->
        {!! Form::text('position', null, ["placeholder" => "", "class" => "form-control border-form", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'position'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('address', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>
</div>
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('tel', 'Telephone', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('tel', null, ["class" => "form-control border-form input-mask-phone"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'tel'])
    </div>

    {!! Form::label('cell_1', 'Mobile', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('cell_1', null, ["class" => "form-control border-form input-mask-cell","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'cell_1'])
    </div>
</div>
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('cell_2', 'Alternative Contact', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('cell_2', null, ["class" => "form-control border-form input-mask-cell"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'cell_2'])
    </div>

    {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('email', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', null, ["placeholder" => "Description", "class" => "form-control border-form summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'description'])
    </div>
</div>


<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('main_image', 'Image', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::file('main_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'main_image'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recomended Minimum Image Size 175px X 196px(72dpi)</span>
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
    {!! Form::label('twitter_url', 'Twitter URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('twitter_url', null, ["placeholder" => "Twitter URL", "class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'twitter_url'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('linkedIn_url', 'LinkedIn URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('linkedIn_url', null, ["placeholder" => "LinkedIn URL", "class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'linkedIn_url'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('facebook_url', 'Facebook URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('facebook_url', null, ["placeholder" => "Facebook URL", "class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'facebook_url'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('instagram_url', 'Instagram URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('instagram_url', null, ["placeholder" => "Instagram URL", "class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'instagram_url'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('whatsapp_url', 'WhatsApp URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('whatsapp_url', null, ["placeholder" => "WhatsApp URL", "class" => "form-control border-form "]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'whatsapp_url'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('skype_url', 'Skype URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('skype_url', null, ["placeholder" => "Skype URL", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'skype_url'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('pinterest_url', 'Pinterest URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('pinterest_url', null, ["placeholder" => "Pinterest URL", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'pinterest_url'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('rank', 'Rank', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
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