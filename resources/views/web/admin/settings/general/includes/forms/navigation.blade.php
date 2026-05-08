<div class="btn btn-block btn-primary">Top Navigation Setting</div>
<div class="space-8"></div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Status </label>
    <div class="col-sm-9">
        {!! Form::select('top_nav_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'top_nav_status'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('top_nav_text', 'info Text', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('top_nav_text', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'top_nav_text'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('top_nav_text_link', 'info Link', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('top_nav_text_link', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'top_nav_text_link'])
    </div>
</div>

<div class="btn btn-block btn-primary">Main Navigation Button Setting</div>
<div class="space-8"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Main Nav Status </label>
    <div class="col-sm-9">
        {!! Form::select('main_nav_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'main_nav_status'])
    </div>
</div>
<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Button Status </label>
    <div class="col-sm-9">
        {!! Form::select('main_nav_button_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'main_nav_button_status'])
    </div>
</div>
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('main_nav_button_button_text', 'Button Text', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('main_nav_button_button_text', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'main_nav_button_button_text'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('main_nav_button_link', 'Link', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('main_nav_button_link', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'main_nav_button_link'])
    </div>
</div>
<div class="space-4"></div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Sticky Menu </label>
    <div class="col-sm-9">
        {!! Form::select('sticky_nav_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'sticky_nav_status'])
    </div>
</div>

<div class="btn btn-block btn-primary">Footer Setting</div>
<div class="space-8"></div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Status </label>
    <div class="col-sm-9">
        {!! Form::select('footer_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'footer_status'])
    </div>
</div>


<div class="btn btn-block btn-primary">Quick Links Menu Setting</div>
<div class="space-8"></div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Status </label>
    <div class="col-sm-9">
        {!! Form::select('quick_nav_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'quick_nav_status'])
    </div>
</div>


<div class="btn btn-block btn-primary">Footer Menu Setting</div>
<div class="space-8"></div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Status </label>
    <div class="col-sm-9">
        {!! Form::select('footer_nav_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'footer_nav_status'])
    </div>
</div>

