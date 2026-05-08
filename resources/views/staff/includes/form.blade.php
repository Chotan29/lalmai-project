<div class="tabbable">
    <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4">
        <li class="active" id="generalInfoTab">
            <a data-toggle="tab" href="#generalInfo">
                <i class="fa fa-user bigger-110"></i> General Information
            </a>
        </li>
        <li class="" id="profileImageTab">
            <a data-toggle="tab" href="#profileImage"><i class="fa fa-image bigger-110"></i> Profile Images</a>
        </li>
        <li id="extraInfoTab">
            <a data-toggle="tab" href="#extraInfo"><i class="fa fa-list-alt bigger-110"></i> Extra Info</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="generalInfo" class="tab-pane active">
            <fieldset>
                <legend>General Info:</legend>

                <div class="form-group">
                    {!! Form::label('reg_no', __('form_fields.student.fields.reg_no'), ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration", "required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
                    </div>

                    {!! Form::label('join_date', 'Join Date', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('join_date', null, ["class" => "form-control date-picker border-form input-mask-date","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'join_date'])
                    </div>

                    {!! Form::label('designation', 'Designation', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::select('designation', $data['designations'], null, ['class' => 'form-control chosen-select', "required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'designation'])
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('first_name', 'NAME OF STAFF', ['class' => 'col-sm-3 control-label',]) !!}
                    <div class="col-sm-3">
                        {!! Form::text('first_name', null, ["placeholder" => "FIRST NAME", "class" => "form-control border-form upper","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
                    </div>
                    <div class="col-sm-3">
                        {!! Form::text('middle_name', null, ["placeholder" => "MIDDLE NAME", "class" => "form-control border-form upper"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'middle_name'])
                    </div>
                    <div class="col-sm-3">
                        {!! Form::text('last_name', null, ["placeholder" => "LAST NAME", "class" => "form-control border-form upper","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'last_name'])
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('father_name', 'Father Name', ['class' => 'col-sm-2 control-label',]) !!}
                    <div class="col-sm-4">
                        {!! Form::text('father_name', null, ["placeholder" => " ", "class" => "form-control border-form upper"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'father_name'])
                    </div>
                    {!! Form::label('mother_name', 'Mother Name', ['class' => 'col-sm-2 control-label',]) !!}
                    <div class="col-sm-4">
                        {!! Form::text('mother_name', null, ["placeholder" => " ", "class" => "form-control border-form upper"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'mother_name'])
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('date_of_birth', null, ["placeholder" => "", "class" => "form-control date-picker border-form input-mask-date","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
                    </div>

                    {!! Form::label('gender', __('form_fields.student.fields.gender'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::select('gender', __('common.gender'), null, ['class'=>'form-control border-form',"required"]); !!}
                        @include('includes.form_fields_validation_message', ['name' => 'gender'])
                    </div>

                    {!! Form::label('blood_group', __('form_fields.student.fields.blood_group'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::select('blood_group', __('common.blood_group'), null,
                        [ 'class'=>'form-control border-form']); !!}
                        @include('includes.form_fields_validation_message', ['name' => 'blood_group'])
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('nationality', __('form_fields.student.fields.nationality'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('nationality', null, ["placeholder" => "", "class" => "form-control border-form upper","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'nationality'])
                    </div>

                    {!! Form::label('national_id_1', 'NID', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('national_id_1', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'national_id_1'])
                    </div>

                    {{-- national_id_2, _3, _4 hidden --}}
                    {!! Form::hidden('national_id_2', null) !!}
                    {!! Form::hidden('national_id_3', null) !!}
                    {!! Form::hidden('national_id_4', null) !!}

                    {!! Form::label('mother_tongue', __('form_fields.student.fields.mother_tongue'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('mother_tongue', null, ["class" => "form-control border-form upper"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'mother_tongue'])
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('qualification', 'Qualification', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('qualification', null, ["class" => "form-control border-form upper","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'qualification'])
                    </div>

                    {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('email', null, ["class" => "form-control border-form","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'email'])
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Contact:</legend>
                <div class="form-group">
                    {!! Form::label('home_phone', 'Phone', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('home_phone', null, ["class" => "form-control border-form input-mask-phone"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'home_phone'])
                    </div>

                    {!! Form::label('mobile_1', 'Mobile 1', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('mobile_1', null, ["class" => "form-control border-form input-mask-mobile","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'mobile_1'])
                    </div>

                    {!! Form::label('mobile_2', 'Mobile 2', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('mobile_2', null, ["class" => "form-control border-form input-mask-mobile"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'mobile_2'])
                    </div>
                </div>
                <div class="label label-warning arrowed-in arrowed-right arrowed">Permanent Address</div>
                <hr class="hr-8">
                <div class="form-group">
                    {!! Form::label('address', 'Address', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('address', null, ["class" => "form-control border-form upper","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'address'])
                    </div>

                    {!! Form::label('state', 'Division', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::select('state', $data['state'],null, ['class' => 'form-control',"required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'state'])
                    </div>

                    {!! Form::label('postal_code', 'Postal Code', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-1">
                        {!! Form::text('postal_code', null, ["class" => "form-control border-form upper","required"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'postal_code'])
                    </div>
                </div>


                <div class="label label-warning arrowed-in arrowed-right arrowed">Temporary Address</div>

                <div class="control-group col-sm-12">
                    <div class="radio">
                        <label>
                            {!! Form::checkbox('permanent_address_copier', '', false, ['class' => 'ace', "onclick"=>"CopyAddress(this.form)"]) !!}
                            <span class="lbl"> Temporary Address Same As Permanent Address</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('address', 'Address', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('temp_address', null, ["class" => "form-control border-form upper"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'temp_address'])
                    </div>

                    {!! Form::label('state', 'Division', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::select('temp_state', $data['state'],null, ['class' => 'form-control']) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'temp_state'])
                    </div>

                    {!! Form::label('temp_postal_code', 'Postal Code', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-1">
                        {!! Form::text('temp_postal_code', null, ["class" => "form-control border-form upper"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'temp_postal_code'])
                    </div>
                </div>
            </fieldset>

            <hr>
            <div class="text-right">
                <a class="btn btn-info" data-toggle="tab" href="#profileImage" onclick="activeProfileImage()">
                    Next <i class="fa fa-arrow-circle-right bigger-110"></i>
                </a>
            </div>
        </div>

        <div id="profileImage" class="tab-pane">
            <h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-plus"></i>Profile Pictures</h4>
            <div class="form-group">
                {!! Form::label('main_image', 'Staff Profile Picture', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::file('main_image', ["class" => "form-control border-form"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'main_image'])
                </div>
            </div>

            @if (isset($data['row']))

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Existing Image</label>
                    <div class="col-sm-10">
                        @if ($data['row']->staff_image)
                            <img id="avatar"  src="{{ asset('images/'.$folder_name.'/'.$data['row']->staff_image) }}" class="img-responsive" width="100">
                        @else
                            <p>No image.</p>
                        @endif

                    </div>
                </div>
            @endif

            <hr>
            <div class="text-right">
                <a class="btn btn-primary" data-toggle="tab" href="#generalInfo" onclick="activeGeneralInfo()">
                    <i class="fa fa-arrow-circle-left bigger-110"></i> Previous
                </a>
                <a class="btn btn-info" data-toggle="tab" href="#extraInfoTab" onclick="activeExtraInfo()">
                    Next <i class="fa fa-arrow-circle-right bigger-110"></i>
                </a>
            </div>
        </div>

        <div id="extraInfo" class="tab-pane">
            @include($view_path.'.includes.forms.extrainfo')
            <hr>
            <div class="text-right">
                <a class="btn btn-info" data-toggle="tab" href="#profileImage" onclick="activeProfileImage()">
                    <i class="fa fa-arrow-circle-left bigger-110"></i> Previous
                </a>
            </div>
        </div>


    </div>
    <div class="hr hr-24"></div>
</div>