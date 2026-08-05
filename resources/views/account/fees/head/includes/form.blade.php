<div class="form-group">
    {!! Form::label('fee_head_title', 'Head', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('fee_head_title', null, ["placeholder" => "e.g. Monthly Fee", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'fee_head_title'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('fee_head_amount', 'Amount', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('fee_head_amount', null, ["placeholder" => "e.g. 5000", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'fee_head_amount'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('collected_by', 'Collected By', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('collected_by', ['college' => 'College', 'department' => 'Department'], null, ["class" => "form-control border-form"]) !!}
        <span class="help-block small">Department money is collected by the college but handed over to the department.</span>
        @include('includes.form_fields_validation_message', ['name' => 'collected_by'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('is_treasury', 'Govt. Treasury', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        <label class="pos-rel">
            {!! Form::hidden('is_treasury', 0) !!}
            {!! Form::checkbox('is_treasury', 1, null, ['class' => 'ace']) !!}
            <span class="lbl">&nbsp;Deposited to the government treasury</span>
        </label>
        @include('includes.form_fields_validation_message', ['name' => 'is_treasury'])
    </div>
</div>

<div class="hr hr-24"></div>
