<div class="btn btn-block btn-primary">Testimonial Setting</div>
<div class="space-8"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Status </label>
    <div class="col-sm-9">
        {!! Form::select('testimonial_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'testimonial_status'])
    </div>
</div>
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('testimonial_title', 'Title', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('testimonial_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'testimonial_title'])
    </div>
</div>