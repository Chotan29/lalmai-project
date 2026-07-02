<div class="form-group">
    {!! Form::label('title', 'Title & Description', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="form-group">
    <div class="col-sm-12">
        {!! Form::textarea('description', null, ["placeholder" => "Description text...", "class" => "form-control border-form","id"=>"summernote", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'description'])
    </div>
</div>

