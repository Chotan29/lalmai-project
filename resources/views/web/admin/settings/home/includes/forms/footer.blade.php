<div class="btn btn-block btn-primary">Footer Email / Call to Action Setting</div>
<div class="space-8"></div>

<div class="space-4"></div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="status">Status </label>
    <div class="col-sm-9">
        {!! Form::select('email_call_to_action_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email_call_to_action_status'])
        <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> When call to action hide on footer, Subscriber form will appear.</span>
    </div>
</div>
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('email_call_to_action_title', 'Call To Action Title', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('email_call_to_action_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'email_call_to_action_title'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('email_call_to_action_link', 'Call To Action Link', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('email_call_to_action_link', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'email_call_to_action_link'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('email_call_to_action_button_text', 'Button Text', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('email_call_to_action_button_text', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'email_call_to_action_button_text'])
    </div>
</div>