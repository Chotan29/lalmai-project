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
        {!! Form::textarea('description', null, ["placeholder" => "Description", "class" => "form-control border-form form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'description'])
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))

    <div class="space-4"></div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Existing File</label>
        <div class="col-sm-10">
            @if ($data['row']->file)
                <a href="{{ asset($folder_name.'/'.$data['row']->file) }}" target="_blank"><i class="icon-download"></i>  {{ $data['row']->title }}</a>
            @else
                <p>No File.</p>
            @endif
        </div>
    </div>


@endif

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('download_file', 'Download File', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::file('download_file') !!}
        @include('includes.form_fields_validation_message', ['name' => 'download_file'])
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