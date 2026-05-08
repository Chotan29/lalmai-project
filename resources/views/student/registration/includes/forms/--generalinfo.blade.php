@if(Config::get('edufirmconfig.student.registration.tabs.general_info.general_info') == 1)
<fieldset>
    <legend>{{ __('form_fields.student.section_label.general_info')}}</legend>
    <div class="form-group">
        {!! Form::label('reg_no', __('form_fields.student.fields.reg_no'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
        </div>

        {!! Form::label('reg_date', __('form_fields.student.fields.reg_date'), ['class' => 'col-sm-2 control-label']) !!}
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
            <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.faculty')}}</label>
            <div class="col-sm-5">
                <select name="faculty" class="form-control  chosen-select"  data-placeholder="Choose a Faculty..."  onChange ="loadSemesters(this)" required="required">
                    @foreach( $data['faculties'] as $key => $faculty)
                        <option value="{{ $key }}">{{ $faculty }}</option>
                    @endforeach
                </select>
            </div>

            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.semester')}}</label>
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
            @if(isset($data['subjects']) && $data['subjects']->count() >0)
                <input type="hidden" name="max_subjects_count" value="{{ $data['max_subjects_count'] }}">
                <div class="row">
                    <div class="col-md-12 padding-5">
                        <div class="label label-warning arrowed-in arrowed-right arrowed">Applied For Following Subjects</div>
                        <hr class="hr-2">
                        @foreach($data['subjects'] as $subjects)
                            <div class="col-md-4">
                                <label>
                                    @if (!isset($data['row']))
                                        {!! Form::checkbox('subject[]', $subjects->id, false, ['class' => 'ace subject']) !!}
                                    @else
                                        {!! Form::checkbox('subject[]', $subjects->id, array_key_exists($subjects->id, $data['existing_subjects']), ['class' => 'ace subject']) !!}
                                    @endif
                                    <span class="lbl"> {{ $subjects->title}} </span>
                                </label>
                                <hr class="hr-2">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="form-group">
        @if (!isset($data['row']))
            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.batch')}}</label>
            <div class="col-sm-5">
                {!! Form::select('batch', $data['batch'], 1, ['class' => 'form-control chosen-select','required'=>"required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'batch'])
            </div>

            <label class="col-sm-2 control-label">{{__('form_fields.student.fields.academic_status')}}</label>
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
        {!! Form::label('first_name', __('form_fields.student.fields.name_of_student'), ['class' => 'col-sm-3 control-label',]) !!}
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
        {!! Form::label('date_of_birth', __('form_fields.student.fields.date_of_birth'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('date_of_birth', null, ["class" => "form-control border-form date-picker input-mask-date","required"]) !!}
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
                {!! Form::text('nationality', __('form_fields.student.fields.religion_default'), ["placeholder" => "", "class" => "form-control border-form upper","required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'nationality'])
            </div>

            {!! Form::label('national_id_1', __('form_fields.student.fields.national_id_1'), ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-2">
                {!! Form::text('national_id_1', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'national_id_1'])
            </div>

            {!! Form::label('national_id_2', __('form_fields.student.fields.national_id_2'), ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-2">
                {!! Form::text('national_id_2', null, ["class" => "form-control border-form upper"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'national_id_2'])
            </div>
    </div>

    <div class="form-group">
        {!! Form::label('national_id_3', __('form_fields.student.fields.national_id_3'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('national_id_3', null, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'national_id_3'])
        </div>

        {!! Form::label('national_id_4', __('form_fields.student.fields.national_id_4'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('national_id_4', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'national_id_4'])
        </div>
        {!! Form::label('mother_tongue', __('form_fields.student.fields.mother_tongue'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('mother_tongue', null, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'mother_tongue'])
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('religion', __('form_fields.student.fields.religion'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::text('religion', null, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'religion'])
        </div>

        {!! Form::label('caste', __('form_fields.student.fields.caste'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-5">
            {!! Form::text('caste', null, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'caste'])
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::text('email', null, ["class" => "form-control border-form "]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'email'])
        </div>
        {!! Form::label('extra_info', __('form_fields.student.fields.extra_info'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-5">
            {!! Form::textarea('extra_info', null, ["class" => "form-control border-form", "rows"=>"1"]) !!}
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

        {!! Form::label('mobile_1', __('form_fields.student.fields.mobile_1'), ['class' => 'col-sm-1 control-label']) !!}
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
            {!! Form::label('address', __('form_fields.student.fields.address'), ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::text('address', null, ["class" => "form-control border-form upper","required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'address'])
            </div>

    {{--        {!! Form::label('state', __('form_fields.student.fields.state'), ['class' => 'col-sm-1 control-label']) !!}--}}
    {{--        <div class="col-sm-3">--}}
    {{--            {!! Form::select('state', $data['state'],null, ['class' => 'form-control',"required"]) !!}--}}
    {{--            @include('includes.form_fields_validation_message', ['name' => 'state'])--}}
    {{--        </div>--}}

            {!! Form::label('state', __('form_fields.student.fields.state'), ['class' => 'col-sm-1 control-label']) !!}
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


