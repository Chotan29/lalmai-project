<div class="alert-settings-container">
    <div class="alert-header">
        <h4 class="blue">
            <i class="fa fa-{{ isset($data['row']) ? 'edit' : 'plus' }}"></i>
            {{ isset($data['row']) ? 'Edit' : 'Add' }} Alert Setting
        </h4>
    </div>

    <div class="form-horizontal">
        <div class="form-group">
            {!! Form::label('event', 'Event', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::text('event', null, [
                    "class" => "form-control border-form", 
                    "required",
                    isset($data['row']) ? "disabled" : ""
                ]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'event'])
            </div>

            <div class="col-sm-6 alert-type-toggle">
                <div class="alert-toggle-item">
                    {!! Form::checkbox('sms', 1, isset($data['row']) ? $data['row']->sms : false, [
                        'class' => 'ace ace-switch ace-switch-6',
                        'id' => 'sms-toggle'
                    ]) !!}
                    <label class="lbl" for="sms-toggle">
                        <i class="fa fa-comment bigger-120"></i> SMS Alerts
                    </label>
                </div>

                <div class="alert-toggle-item">
                    {!! Form::checkbox('email', 1, isset($data['row']) ? $data['row']->email : false, [
                        'class' => 'ace ace-switch ace-switch-6',
                        'id' => 'email-toggle'
                    ]) !!}
                    <label class="lbl" for="email-toggle">
                        <i class="fa fa-envelope bigger-120"></i> Email Alerts
                    </label>
                </div>
            </div>
        </div>

        <div class="template-editor">
            <div class="form-group">
                {!! Form::label('template', 'SMS Template', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::textarea('template', null, [
                        "class" => "form-control border-form",
                        "rows" => "4",
                        "placeholder" => "Enter SMS template content...",
                        "required"
                    ]) !!}
                    <small class="help-block">Maximum 160 characters for SMS</small>
                    @include('includes.form_fields_validation_message', ['name' => 'template'])
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('subject', 'Email Subject', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::text('subject', null, [
                        "class" => "form-control border-form",
                        "placeholder" => "Email subject line...",
                        "required"
                    ]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'subject'])
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('email_template', 'Email Template', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::textarea('email_template', null, [
                        "class" => "form-control border-form summernote",
                        "placeholder" => "Enter email template content...",
                        "required"
                    ]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'email_template'])
                </div>
            </div>
        </div>

        @include('setting.alert.includes.template')

        <div class="clearfix form-actions">
            <div class="col-md-12 align-center">
                <button class="btn btn-default" type="reset">
                    <i class="fa fa-undo bigger-110"></i> Reset
                </button>
                &nbsp; &nbsp; &nbsp;
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-save bigger-110"></i> Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Insert variable on click
        $('.variable-badge').click(function() {
            const variable = $(this).text().trim();
            const textarea = $('#template');
            const currentPos = textarea[0].selectionStart;
            const currentVal = textarea.val();
            
            textarea.val(currentVal.substring(0, currentPos) + variable + currentVal.substring(currentPos));
            textarea.focus();
        });
        
        // Character counter for SMS
        $('#template').on('input', function() {
            const count = $(this).val().length;
            const counter = $(this).next('.help-block');
            counter.text(`${count}/160 characters`);
            
            if (count > 160) {
                counter.addClass('text-danger');
            } else {
                counter.removeClass('text-danger');
            }
        });
    });
</script>