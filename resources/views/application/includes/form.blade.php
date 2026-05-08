<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ $panel }}</h4>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-sm-2 control-label">Application Type</label>
            <div class="col-sm-3">
                {!! Form::select('application_type_id', $data['applicationType'], null, ['class' => 'form-control chosen-select','onChange'=>'checkApplicationType()']) !!}
            </div>

            <span id="leave_application">
                {!! Form::label('date', 'Date', ['class' => 'col-sm-1 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('date', null, ["id" => "", "class" => "form-control border-form date-picker"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'date'])
                </div>
                {!! Form::label('end_date', 'End Date', ['class' => 'col-sm-1 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('end_date', null, ["placeholder" => "", "class" => "form-control border-form date-picker"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'end_date'])
                </div>
            </span>
        </div>
    </div>
    <div class="col-md-12 email">
        <div class="form-group">
            {!! Form::label('subject', 'Subject', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
                {!! Form::text('subject', null, ["class" => "form-control border-form"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'subject'])
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('message', 'Message/Reason', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
                {!! Form::textarea('message', null, ["class" => "form-control border-form", "rows"=>"4"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'message'])
            </div>
        </div>
    </div>


    <div class="form-group">
        {!! Form::label('attach_file', 'Attachment File', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-8">
            {!! Form::file('attach_file', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'attach_file'])
        </div>
    </div>

    @if (isset($data['row']))

        <div class="space-4"></div>

        <div class="form-group">
            {!! Form::label('old_file', 'Old File', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-8 ace-file-input">
                @if ($data['row']->file)
                    <a href="{{ asset('application'.DIRECTORY_SEPARATOR.$data['row']->file) }}" target="_blank">
                        <i class="ace-icon fa fa-download bigger-120"></i> &nbsp;{{ $data['row']->title }} Attached File
                    </a>
                @else
                    <p>No File.</p>
                @endif
            </div>
        </div>

    @endif

</div>
<div class="hr hr-24"></div>