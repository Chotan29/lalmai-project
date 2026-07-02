<div class="form-group">
    {!! Form::label('title', 'Company', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

{{--'company','address','phone','email','logo',--}}
<div class="form-group">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('address', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('phone', 'Contact No.', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('phone', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'phone'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('email', 'Email', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::email('email', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('description', 'Description', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('description', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'description'])
    </div>
</div>