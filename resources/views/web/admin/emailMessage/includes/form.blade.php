<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', null, ["placeholder" => "Title", "class" => "form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', null, ["placeholder" => "Description", "class" => "form-control form-control summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'description'])
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))
    <div class="space-2"></div>
    <div class="form-group">
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
    {!! Form::label('link', 'Link', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('link', null, ["placeholder" => "Link", "class" => "form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'link'])
    </div>
</div>

<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ $panel }}</h4>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
                {!! Form::text('email', null, ["class" => "form-control border-form"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'email'])
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('subject', 'Subject', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
                {!! Form::text('subject', null, ["class" => "form-control border-form"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'subject'])
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('emailMessage', 'Message', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
                {!! Form::textarea('emailMessage', null, ["class" => "form-control border-form", "id"=>"summernote","rows"=>"5"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'emailMessage'])
            </div>
        </div>
    </div>
</div>

<div class="clearfix form-actions">
    <div class="col-md-12 align-center">
        <button class="btn" type="reset">
            <i class="ace-icon fa fa-undo bigger-110"></i>
            Reset
        </button>

        <button class="btn btn-info" type="submit" id="individual-message-send-btn">
            <i class="ace-icon fa fa-undo bigger-110"></i>
            Send
        </button>
    </div>
</div>

<div class="hr hr-24"></div>
