<div class="form-group">
    {!! Form::label('type', 'Display Page Type', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('type', ['home' => 'Home Page', 'about_us' => 'About Us'], null, ["placeholder" => "Choose Target Page", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'type'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('icon', 'Icon', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('icon', null, ["placeholder" => "", "class" => "form-control border-form form-control icon-picker", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'icon'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', null, ["placeholder" => "Enter Title", "class" => "form-control border-form form-control", "required"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('counter', 'Counter Number', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::number('counter', null, ["placeholder" => "0", "class" => "form-control border-form form-control", "min"=>"0", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'counter'])
        {{--<span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Counter Number Enter on Percentage (max 100%) When Page Type is About Us</span>--}}
    </div>
</div>



<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('rank', 'Counter Rank', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::number('rank', null, ["placeholder" => "Enter Rank", "class" => "form-control border-form", 'min' => 0, 'required']) !!}

        @include('includes.form_fields_validation_message', ['name' => 'rank'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="status"> Status </label>

    <div class="col-sm-10">
        <div class="control-group">
            <div class="radio">
                <label>
                    {!! Form::radio('status', 'active', true, ['class' => 'ace']) !!}
                    <span class="lbl"> Active</span>
                </label>
                <label>
                    {!! Form::radio('status', 'in-active', false, ['class' => 'ace']) !!}
                    <span class="lbl"> Inactive</span>
                </label>
            </div>
            @include('includes.form_fields_validation_message', ['name' => 'status'])
        </div>
    </div>
</div>

<div class="space-4"></div>