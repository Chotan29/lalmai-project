<div class="form-group">
    {!! Form::label('title', 'Attendance Status', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('label', null, ["placeholder" => "", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'label'])
    </div>
</div>

<div class="form-group">

    {!! Form::label('display_class', 'DisplayColor', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::radio('display_class', false, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        <span class="btn btn-sm btn-danger">RED</span>
        <hr class="hr-2">
        {!! Form::radio('display_class', false, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        {!! Form::label('display_class', '', ['class' => 'btn btn-sm btn-info p-5'])  !!}
        <hr class="hr-2">
        {!! Form::radio('display_class', false, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        {!! Form::label('display_class', '', ['class' => 'btn btn-sm btn-warning']) !!}
        <hr class="hr-2">
        {!! Form::radio('display_class', false, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        {!! Form::label('display_class', '', ['class' => 'btn btn-sm btn-success']) !!}
        <hr class="hr-2">
        {!! Form::radio('display_class', false, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        {!! Form::label('display_class', '', ['class' => 'btn btn-sm btn-primary p-5']) !!}

        @include('includes.form_fields_validation_message', ['name' => 'display_class'])
    </div>
</div>

