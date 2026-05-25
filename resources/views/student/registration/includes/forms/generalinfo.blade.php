@if(Config::get('edufirmconfig.student.registration.tabs.general_info.general_info') == 1)
<fieldset>
    <legend>{{ __('form_fields.student.section_label.general_info')}}</legend>
    <div class="form-group">
        {!! Form::label('reg_no', __('form_fields.student.fields.reg_no').' <span class="text-danger">*</span>', ['class' => 'col-sm-2 control-label'], false) !!}
        <div class="col-sm-2">
            {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration", "required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
        </div>

        {!! Form::label('reg_date', __('form_fields.student.fields.reg_date').' <span class="text-danger">*</span>', ['class' => 'col-sm-2 control-label'], false) !!}
        <div class="col-sm-2">
            {!! Form::text('reg_date', null, ["class" => "form-control date-picker border-form input-mask-date","required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'reg_date'])
        </div>

        {!! Form::label('university_reg', __('form_fields.student.fields.university_reg'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('university_reg', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
        </div>
    </div>

    @if (!isset($data['row']))
        <div class="form-group">
            <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.faculty')}} <span class="text-danger">*</span></label>
            <div class="col-sm-5">
                <select name="faculty" class="form-control  chosen-select"  data-placeholder="Choose a Faculty..."  onChange ="loadSemesters(this)" required="required">
                    @foreach( $data['faculties'] as $key => $faculty)
                        <option value="{{ $key }}">{{ $faculty }}</option>
                    @endforeach
                </select>
            </div>

            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.semester')}} <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select id="semester" name="semester" required onChange ="loadSubject(this)" class="form-control border-form semester"  > </select>
                @include('includes.form_fields_validation_message', ['name' => 'semester'])
            </div>

        </div>
        @if(Config::get('edufirmconfig.student.registration.tabs.general_info.subject_info') == 1)
            <div class="form-group">
                <div id="subjects_wrapper">
                </div>
            </div>
        @endif

    @else
        <div class="form-group">
            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.faculty')}}</label>
            <div class="col-sm-5">
                {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control',"disabled"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'faculty'])
            </div>

            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.semester')}}</label>
            <div class="col-sm-3">
                {!! Form::select('semester', $data['semester'], null, ['class' => 'form-control',"disabled"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'semester'])
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12 padding-5">
                <div id="subjects_wrapper">
                    {!! $data['subjects_html'] ?? '' !!}
                </div>
            </div>
        </div>
    @endif

    <div class="form-group">
        @if (!isset($data['row']))
            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.batch')}} <span class="text-danger">*</span></label>
            <div class="col-sm-5">
                {!! Form::select('batch', $data['batch'], 1, ['class' => 'form-control chosen-select','required'=>"required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'batch'])
            </div>

            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.academic_status')}} <span class="text-danger">*</span></label>
            <div class="col-sm-3">
                {!! Form::select('academic_status', $data['academic_status'], 1, ['class' => 'form-control']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'academic_status'])
            </div>
        @else
            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.batch')}}</label>
            <div class="col-sm-5">
                {!! Form::select('batch', $data['batch'], null, ['class' => 'form-control chosen-select']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'batch'])
            </div>

            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.academic_status')}}</label>
            <div class="col-sm-3">
                {!! Form::select('academic_status', $data['academic_status'], null, ['class' => 'form-control']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'academic_status'])
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('first_name', __('form_fields.student.fields.name_of_student').' <span class="text-danger">*</span>', ['class' => 'col-sm-3 control-label'], false) !!}
        <div class="col-sm-3">
            {!! Form::text('first_name', null, ["class" => "form-control border-form upper","required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'first_name'])
        </div>
        <div class="col-sm-3">
            {!! Form::text('middle_name', null, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'middle_name'])
        </div>
        <div class="col-sm-3">
            {!! Form::text('last_name', null, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'last_name'])
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('date_of_birth', __('form_fields.student.fields.date_of_birth').' <span class="text-danger">*</span>', ['class' => 'col-sm-2 control-label'], false) !!}
        <div class="col-sm-2">
            {!! Form::text('date_of_birth', null, ["class" => "form-control border-form date-picker input-mask-date","required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
        </div>

        {!! Form::label('gender', __('form_fields.student.fields.gender').' <span class="text-danger">*</span>', ['class' => 'col-sm-2 control-label'], false) !!}
        <div class="col-sm-2">
            @php
                $selectedGender = isset($data['row']) ? strtoupper(trim((string)$data['row']->gender)) : null;
            @endphp
            {!! Form::select('gender', __('common.gender'), $selectedGender, ['class'=>'form-control border-form',"required"]); !!}
            @include('includes.form_fields_validation_message', ['name' => 'gender'])
        </div>

        {!! Form::label('blood_group', __('form_fields.student.fields.blood_group'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            @php
                $selectedBloodGroup = isset($data['row']) ? strtoupper(trim((string)$data['row']->blood_group)) : null;
                $normalizedBloodGroup = $selectedBloodGroup ? str_replace(' ', '', $selectedBloodGroup) : null;
                $bloodGroupMap = [
                    'A+' => 'A+',
                    'A-' => 'A-',
                    'B+' => 'B+',
                    'B-' => 'B-',
                    'AB+' => 'AB+',
                    'AB-' => 'AB-',
                    'O+' => 'O+',
                    'O-' => 'O-',
                ];
                if ($normalizedBloodGroup && array_key_exists($normalizedBloodGroup, $bloodGroupMap)) {
                    $selectedBloodGroup = $bloodGroupMap[$normalizedBloodGroup];
                }
            @endphp
            {!! Form::select('blood_group', __('common.blood_group'), $selectedBloodGroup,
            [ 'class'=>'form-control border-form']); !!}
            @include('includes.form_fields_validation_message', ['name' => 'blood_group'])
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('nationality', __('form_fields.student.fields.nationality').' <span class="text-danger">*</span>', ['class' => 'col-sm-2 control-label'], false) !!}
        <div class="col-sm-4">
            {!! Form::text('nationality', 'Bangladeshi', ["class" => "form-control border-form","required", "readonly" => true]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'nationality'])
        </div>

        {!! Form::label('religion', __('form_fields.student.fields.religion').' <span class="text-danger">*</span>', ['class' => 'col-sm-2 control-label'], false) !!}
        <div class="col-sm-4">
            @php
                $selectedReligion = isset($data['row']) ? trim((string)$data['row']->religion) : null;
                $religionMap = [
                    'ISLAM' => 'Islam',
                    'MUSLIM' => 'Islam',
                    'ইসলাম' => 'Islam',
                    'HINDU' => 'Hinduism',
                    'HINDUISM' => 'Hinduism',
                    'হিন্দু' => 'Hinduism',
                    'BUDDHIST' => 'Buddhism',
                    'BUDDHISM' => 'Buddhism',
                    'বৌদ্ধ' => 'Buddhism',
                    'CHRISTIAN' => 'Christianity',
                    'CHRISTIANITY' => 'Christianity',
                    'খ্রিস্টান' => 'Christianity',
                    'OTHER' => 'Other',
                    'অন্যান্য' => 'Other',
                ];
                $selectedReligionUpper = strtoupper($selectedReligion);
                if (array_key_exists($selectedReligionUpper, $religionMap)) {
                    $selectedReligion = $religionMap[$selectedReligionUpper];
                } elseif (array_key_exists($selectedReligion, $religionMap)) {
                    $selectedReligion = $religionMap[$selectedReligion];
                }

                $religionOptions = [
                    '' => 'Select Religion',
                    'Islam' => 'Islam',
                    'Hinduism' => 'Hinduism',
                    'Buddhism' => 'Buddhism',
                    'Christianity' => 'Christianity',
                    'Other' => 'Other'
                ];

                // Keep legacy/custom stored values visible and selected instead of showing blank.
                if ($selectedReligion && !array_key_exists($selectedReligion, $religionOptions)) {
                    $religionOptions[$selectedReligion] = $selectedReligion;
                }
            @endphp
            {!! Form::select('religion', $religionOptions, $selectedReligion, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'religion'])
        </div>
    </div>

    <div class="form-group" style="display:none;">
        {!! Form::label('national_id_2', __('form_fields.student.fields.national_id_2'), ['class' => 'col-sm-2 control-label', 'style' => 'display:none;']) !!}
        <div class="col-sm-2" style="display:none;">
            {!! Form::text('national_id_2', null, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'national_id_2'])
        </div>

        {!! Form::label('national_id_3', __('form_fields.student.fields.national_id_3'), ['class' => 'col-sm-2 control-label', 'style' => 'display:none;']) !!}
        <div class="col-sm-2" style="display:none;">
            {!! Form::text('national_id_3', null, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'national_id_3'])
        </div>

        {!! Form::label('national_id_4', __('form_fields.student.fields.national_id_4'), ['class' => 'col-sm-2 control-label', 'style' => 'display:none;']) !!}
        <div class="col-sm-2" style="display:none;">
            {!! Form::text('national_id_4', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'national_id_4'])
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('email', __('form_fields.student.fields.email').' <span class="text-danger">*</span>', ['class' => 'col-sm-2 control-label'], false) !!}
        <div class="col-sm-4">
            {!! Form::email('email', null, ["class" => "form-control border-form", "required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'email'])
        </div>
        {!! Form::label('extra_info', __('form_fields.student.fields.extra_info'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::textarea('extra_info', null, ["class" => "form-control border-form", "rows"=>"2"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'extra_info'])
        </div>
    </div>
</fieldset>
@endif
<fieldset>
    @if(Config::get('edufirmconfig.student.registration.tabs.general_info.contact_info') == 1)
        <legend>{{ __('form_fields.student.section_label.contact_info')}}</legend>
        <div class="form-group">
        {!! Form::label('home_phone', __('form_fields.student.fields.home_phone'), ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::text('home_phone', null, ["class" => "form-control border-form input-mask-phone"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'home_phone'])
        </div>

        {!! Form::label('mobile_1', __('form_fields.student.fields.mobile_1').' <span class="text-danger">*</span>', ['class' => 'col-sm-1 control-label'], false) !!}
        <div class="col-sm-3">
            {!! Form::text('mobile_1', null, ["class" => "form-control border-form input-mask-mobile","required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'mobile_1'])
        </div>

        {!! Form::label('mobile_2', __('form_fields.student.fields.mobile_2'), ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::text('mobile_2', null, ["class" => "form-control border-form input-mask-mobile"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'mobile_2'])
        </div>
    </div>
    @endif
    @if(Config::get('edufirmconfig.student.registration.tabs.general_info.address') == 1)
        <div class="label label-warning arrowed-in arrowed-right arrowed">{{ __('form_fields.student.section_label.address')}}</div>
        <hr class="hr-8">
        <div class="form-group">
            {!! Form::label('address', __('form_fields.student.fields.address').' <span class="text-danger">*</span>', ['class' => 'col-sm-1 control-label'], false) !!}
            <div class="col-sm-4">
                {!! Form::text('address', null, ["class" => "form-control border-form upper","required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'address'])
            </div>

    {{--        {!! Form::label('state', __('form_fields.student.fields.state'), ['class' => 'col-sm-1 control-label']) !!}--}}
    {{--        <div class="col-sm-3">--}}
    {{--            {!! Form::select('state', $data['state'],null, ['class' => 'form-control',"required"]) !!}--}}
    {{--            @include('includes.form_fields_validation_message', ['name' => 'state'])--}}
    {{--        </div>--}}

            {!! Form::label('state', __('form_fields.student.fields.state').' <span class="text-danger">*</span>', ['class' => 'col-sm-1 control-label'], false) !!}
            <div class="col-sm-3">
                {!! Form::text('state', null, ["class" => "form-control border-form upper","required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'state'])
            </div>

            {!! Form::label('postal_code', __('form_fields.student.fields.postal_code'), ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-1">
                {!! Form::text('postal_code', null, ["class" => "form-control border-form upper","required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'postal_code'])
            </div>
        </div>
    @endif
    @if(Config::get('edufirmconfig.student.registration.tabs.general_info.temp_address') == 1)
        <div class="label label-warning arrowed-in arrowed-right arrowed">{{ __('form_fields.student.section_label.temp_address')}}</div>

        <div class="control-group col-sm-12">
            <div class="radio">
                <label>
                    {!! Form::checkbox('permanent_address_copier', '', false, ['class' => 'ace', "onclick"=>"CopyAddress(this.form)"]) !!}
                    <span class="lbl"> Temporary Address Same As Permanent Address</span>
                </label>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('temp_address', __('form_fields.student.fields.address'), ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::text('temp_address', null, ["class" => "form-control border-form upper"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'temp_address'])
            </div>

    {{--        {!! Form::label('state', 'State', ['class' => 'col-sm-1 control-label']) !!}--}}
    {{--        <div class="col-sm-3">--}}
    {{--            {!! Form::select('temp_state', $data['state'],null, ['class' => 'form-control']) !!}--}}
    {{--            @include('includes.form_fields_validation_message', ['name' => 'temp_state'])--}}
    {{--        </div>--}}

            {!! Form::label('temp_state', __('form_fields.student.fields.state'), ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-3">
                {!! Form::text('temp_state', null, ["class" => "form-control border-form upper"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'temp_state'])
            </div>

            {!! Form::label('temp_postal_code', __('form_fields.student.fields.postal_code'), ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-1">
                {!! Form::text('temp_postal_code', null, ["class" => "form-control border-form upper"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'temp_postal_code'])
            </div>
        </div>
    @endif
</fieldset>


