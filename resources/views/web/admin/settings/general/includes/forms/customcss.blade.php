<div class="btn btn-block btn-primary">Custom CSS</div>
<hr class="hr-16" />

<div class="form-group">
    <div class="col-sm-12">
        {!! Form::textarea('custom_css', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'custom_css'])
    </div>
</div>

<div class="space-4"></div>
