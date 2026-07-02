<div class="form-group">
    {!! Form::label('contact_title', 'Contact Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('contact_title', null, ["placeholder" => "", "class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'contact_title'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('company', 'Company', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('company', null, ["placeholder" => "", "class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'company'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('address', null, ["placeholder" => "", "class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('phone', 'Phone', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('phone', null, ["placeholder" => "", "class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'phone'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('fax', 'Fax', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('fax', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'fax'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('email', null, ["placeholder" => "", "class" => "form-control border-form","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('webURL', 'webURL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('webURL', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'webURL'])
    </div>
</div>

<div class="space-4"></div>