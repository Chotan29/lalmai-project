<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ $panel }}</h4>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('type', 'TYPE', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
                <label>{!! Form::radio('type[]','sms' ,true, ["class" => "ace form-control border-form", "id"=>"typeSms","onclick" => "messageTypeCondition()"]) !!}<span class="lbl"> SMS </span></label>
                <label>{!! Form::radio('type[]','email' ,false, ["class" => "ace form-control border-form", "id"=>"typeEmail","onclick" => "messageTypeCondition()"]) !!}<span class="lbl"> E-mail</span></label>
                @include('includes.form_fields_validation_message', ['name' => 'type'])
            </div>
        </div>
        <hr class="hr-4">
        <div class="col-md-12 sms">
            <div class="form-group">
                {!! Form::label('number', 'Number', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::text('number', null, ["class" => "form-control border-form"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'number'])
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('message', 'Message', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    <div class="sms-composer-panel">
                        <div class="sms-composer-toolbar">
                            <button type="button" class="btn btn-xs btn-default sms-token-btn" data-sms-token="{name}">Name</button>
                            <button type="button" class="btn btn-xs btn-default sms-token-btn" data-sms-token="{number}">Number</button>
                            <button type="button" class="btn btn-xs btn-default sms-token-btn" data-sms-token="{date}">Date</button>
                            <button type="button" class="btn btn-xs btn-default sms-token-btn" data-sms-token="{time}">Time</button>
                            <button type="button" class="btn btn-xs btn-warning" id="sms-clear-message">Clear</button>
                        </div>
                        {!! Form::textarea('message', null, ["class" => "form-control border-form sms-composer-input","id"=>"smsmessage", "rows"=>"10", "placeholder" => "Write your SMS here..."]) !!}
                        <div class="sms-composer-footer">
                            <span class="sms-counter" id="count">0 characters</span>
                            <span class="sms-counter" id="smsSegments">1 SMS</span>
                            <span class="sms-hint">Plain text is sent as SMS. Use tokens to personalize fast.</span>
                        </div>
                    </div>
                    @include('includes.form_fields_validation_message', ['name' => 'message'])
                </div>
            </div>
        </div>
        <div class="col-md-12 email">
            <div class="form-group">
                {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::text('email', null, ["class" => "form-control border-form"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'email'])
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('subject', 'Subject', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::text('subject', null, ["class" => "form-control border-form"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'subject'])
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('emailMessage', 'Message', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::textarea('emailMessage', null, ["class" => "form-control border-form", "id"=>"summernote","rows"=>"5"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'emailMessage'])
                </div>
            </div>
        </div>
    </div>
</div>

<div class="clearfix form-actions">
    <div class="col-md-12 align-center">
        <button class="btn" type="reset">
            <i class="fa fa-undo bigger-110"></i>
            Reset
        </button>

        <button class="btn btn-info" type="submit" id="individual-message-send-btn">
            <i class="fa fa-save bigger-110"></i>
            Send
        </button>
    </div>
</div>

<div class="hr hr-24"></div>
