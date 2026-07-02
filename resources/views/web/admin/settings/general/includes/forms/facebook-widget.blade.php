<div class="btn btn-block btn-primary">Facebook Footer Widget Code</div>
<hr class="hr-16" />

<div class="form-group">
    <div class="col-sm-12">
        {!! Form::textarea('facebook_widget', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'facebook_widget'])
    </div>
    <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Generate Widget Code From <a href="https://developers.facebook.com/docs/plugins/page-plugin" target="_blank">Facebook Developer Page</a></span>
</div>

<div class="space-4"></div>
