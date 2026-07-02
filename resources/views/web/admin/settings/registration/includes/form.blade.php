<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Registration Status </label>
    <div class="col-sm-9">
        {!! Form::select('status', ['active'=>'Show','in-active'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'status'])
    </div>
</div>


<div class="form-group">
    {!! Form::label('range', 'Open & Close Date', ['class' => 'col-sm-3 control-label']) !!}
    <div class=" col-sm-3">
        <div class="input-group ">
            {!! Form::text('start_date', null, ["placeholder" => "", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
            <span class="input-group-addon">
                                        <i class="fa fa-exchange"></i>
                                    </span>
            {!! Form::text('end_date', null, ["placeholder" => "", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
        </div>
    </div>
</div>


<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('sub_title', 'Subtitle', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('sub_title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'sub_title'])
    </div>
</div>

<div class="space-4"></div>

@if (isset($data['row']))
    <div class="form-group">
        <label class="col-sm-3 control-label">Existing Logo</label>
        <div class="col-sm-9">
            @if ($data['row']->logo)
                <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->logo) }}" >
            @else
                <p>No image.</p>
            @endif
        </div>
    </div>
@endif
<div class="space-4"></div>
<div class="form-group">
    {!! Form::label('logo_image', 'Logo', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::file('logo_image') !!}
        @include('includes.form_fields_validation_message', ['name' => 'logo_image'])
    </div>
</div>

<div class="space-8"></div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Medical Info </label>
    <div class="col-sm-9">
        {!! Form::select('medical_info_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'medical_info_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Guardian Detail </label>
    <div class="col-sm-9">
        {!! Form::select('guardian_detail_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_detail_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="status"> Permanent Address </label>
    <div class="col-sm-9">
        {!! Form::select('permanent_address_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'permanent_address_status'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="mailing_address_status"> Mailing Address </label>
    <div class="col-sm-9">
        {!! Form::select('mailing_address_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mailing_address_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="photo_status"> Photo Status </label>
    <div class="col-sm-9">
        {!! Form::select('photo_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'photo_status'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="applicant_photo_status"> Applicant Photo </label>
    <div class="col-sm-9">
        {!! Form::select('applicant_photo_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'applicant_photo_status'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="applicant_signature_status"> Applicant Signature </label>
    <div class="col-sm-9">
        {!! Form::select('applicant_signature_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'applicant_signature_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="guardian_photo_status"> Guardian Photo </label>
    <div class="col-sm-9">
        {!! Form::select('guardian_photo_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_photo_status'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="qualification"> Qualification </label>
    <div class="col-sm-9">
        {!! Form::select('qualification', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'qualification'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="experience"> Experience </label>
    <div class="col-sm-9">
        {!! Form::select('experience', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'experience'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="rules_status"> Rules Status </label>
    <div class="col-sm-9">
        {!! Form::select('rules_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form ']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'rules_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('rules', 'Rules Info', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::textarea('rules', null, ["placeholder" => "", "class" => "form-control border-form summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'rules'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="agreement_status"> Agreement Status </label>
    <div class="col-sm-9">
        {!! Form::select('agreement_status', ['1'=>'Show','0'=>'Hide'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'agreement_status'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('agreement', 'Agreement Info', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::textarea('agreement', null, ["placeholder" => "", "class" => "form-control border-form summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'agreement'])
    </div>
</div>
