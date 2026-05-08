<div class="btn btn-block btn-primary">Admin Setting</div>
<hr class="hr-16" />

<div class="form-group">
    {!! Form::label('admin_title', 'Admin Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('admin_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'admin_title'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('copyright', 'Copyright©', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('copyright', null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'copyright'])
    </div>
</div>
