{{--<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input are required. </span>
<hr class="hr-16">--}}
<div class="form-group row">
    <label class="col-sm-2 control-label">Application For</label>
    <div class="col-sm-5">
        <select name="web_registration_programmes_id" class="form-control" id="form-field-select-3" data-placeholder="Choose a Application For..." required>
            <option value="" disabled="disabled"> Choose Application For </option>
            @foreach( $data['programmes'] as $key => $programme)
                <option value="{{ $key }}">{{ $programme }}</option>
            @endforeach
        </select>
    </div>
    {!! Form::label('reg_no', 'Reg.No.', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-3">
        {!! Form::text('reg_no', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
    </div>
</div>

<div class="form-group row">
    {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-5">
        {!! Form::text('name', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'name'])
    </div>

    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('date_of_birth', null, ["data-date-format" => "dd-mm-yyyy", "class" => "form-control border-form date-picker input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>
</div>


<div class="form-group">
    <div class="clearfix form-actions">
        <div class="align-right">
            <button class="btn" type="reset">
                <i class="icon-undo bigger-110"></i>
                Reset
            </button>
            <button class="btn btn-info" type="submit">
                <i class="icon-ok bigger-110"></i>
                Print My Application
            </button>
        </div>
    </div>
</div>