
<div class="tabbable">
    <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4">
        <li class="active" id="generalInfoTab">
            <a data-toggle="tab" href="#generalInfo"><i class="fa fa-user bigger-110"></i> {{__('form_fields.student.tabs.general_info')}}</a>
        </li>
        @if(Config::get('edufirmconfig.student.registration.tabs.academic_info') == 1)
            <li id="academicInfoTab">
                <a data-toggle="tab" href="#academicInfo" onclick="activeAcademicInfo()"><i class="fa fa-certificate bigger-110"></i> {{__('form_fields.student.tabs.academic_info')}}</a>
            </li>
        @endif
        @if(Config::get('edufirmconfig.student.registration.tabs.profile_image') == 1)
        <li id="profileImageTab">
            <a data-toggle="tab" href="#profileImage" onclick="activeProfileImage()"><i class="fa fa-image bigger-110"></i> {{__('form_fields.student.tabs.profile_image')}}</a>
        </li>
        @endif
        @if(Config::get('edufirmconfig.student.registration.tabs.annexure') == 1)
        <li id="ruleAgreementTab">
            <a data-toggle="tab" href="#ruleAgreement"  onclick="activeRuleAgreement()"><i class="fa fa-certificate bigger-110"></i> {{__('form_fields.student.tabs.annexure')}}</a>
        </li>
        @endif
        @if(Config::get('edufirmconfig.student.registration.tabs.extra_info') == 1)
        <li id="extraInfoTab">
            <a data-toggle="tab" href="#extraInfo" onclick="activeExtraInfo()"><i class="fa fa-list-alt bigger-110"></i> {{__('form_fields.student.tabs.extra_info')}}</a>
        </li>
        @endif
    </ul>

    <div class="tab-content">
        <div id="generalInfo" class="tab-pane active">
            @include($view_path.'.registration.includes.forms.generalinfo')
            @if(Config::get('edufirmconfig.student.registration.tabs.general_info.parent_info') == 1)
                @include($view_path.'.registration.includes.forms.parent')
            @endif
            <hr>
            <div class="text-center">
                <a class=" btn btn-info" data-toggle="tab" href="#academicInfo" onclick="activeAcademicInfo()">
                    Next <i class="fa fa-arrow-circle-right bigger-110"></i>
                </a>
            </div>
        </div>

        @if(Config::get('edufirmconfig.student.registration.tabs.academic_info') == 1)
            <div id="academicInfo" class="tab-pane">
                @include($view_path.'.registration.includes.forms.academicinfo')
                <hr>
                <div class="text-center">
                    <a class="btn btn-primary" data-toggle="tab" href="#generalInfo" onclick="activeGeneralInfo()">
                        <i class="fa fa-arrow-circle-left bigger-110"></i> Previous
                    </a>
                    <a class="btn btn-info" data-toggle="tab" href="#profileImage" onclick="activeProfileImage()">
                        Next <i class="fa fa-arrow-circle-right bigger-110"></i>
                    </a>
                </div>
            </div>
        @endif

        @if(Config::get('edufirmconfig.student.registration.tabs.profile_image') == 1)
            <div id="profileImage" class="tab-pane">
                @include($view_path.'.registration.includes.forms.profileimage')
                <hr>
                <div class="text-center">
                    <a class="btn btn-primary" data-toggle="tab" href="#academicInfo" onclick="activeAcademicInfo()">
                        <i class="fa fa-arrow-circle-left bigger-110"></i> Previous
                    </a>

                    <a class="btn btn-info" data-toggle="tab" href="#ruleAgreement" onclick="activeRuleAgreement()">
                        Next <i class="fa fa-arrow-circle-right bigger-110"></i>
                    </a>

                </div>
            </div>
        @endif

        @if(Config::get('edufirmconfig.student.registration.tabs.annexure') == 1)
            <div id="ruleAgreement" class="tab-pane">
            @if(isset($data['annexures']))
                <div class="row">
                    <div class="col-md-12 padding-5">
                        <div class="label label-warning arrowed-in arrowed-right arrowed">
                            Details of Document & photo copy :
                        </div>
                        <hr class="hr-8">
                        @foreach($data['annexures'] as $annexure)
                            <div class="col-md-6">

                                <label>
                                    @if (!isset($data['row']))
                                        {!! Form::checkbox('annexure[]', $annexure->id, false, ['class' => 'ace']) !!}
                                    @else
                                        {!! Form::checkbox('annexure[]', $annexure->id, array_key_exists($annexure->id, $data['existing_annexure']), ['class' => 'ace']) !!}
                                    @endif
                                        <span class="lbl"> {{ $annexure->title}} </span>
                                </label>
                                <hr class="hr-2">
                            </div>
                        @endforeach


                    </div>

                </div>
            @endif
            <hr>
            <div class="text-center">
                <a class="btn btn-primary" data-toggle="tab" href="#profileImage" onclick="activeProfileImage()">
                    <i class="fa fa-arrow-circle-left bigger-110"></i> Previous
                </a>

                <a class="btn btn-info" data-toggle="tab" href="#extraInfo" onclick="activeExtraInfo()">
                    Next <i class="fa fa-arrow-circle-right bigger-110"></i>
                </a>

            </div>
        </div>
        @endif

        @if(Config::get('edufirmconfig.student.registration.tabs.extra_info') == 1)
            <div id="extraInfo" class="tab-pane">
            @include($view_path.'.registration.includes.forms.extrainfo')
            <hr>
            <div class="text-center">
                <a class="btn btn-primary" data-toggle="tab" href="#ruleAgreement" onclick="activeRuleAgreement()">
                    <i class="fa fa-arrow-circle-left bigger-110"></i> Previous
                </a>
{{--                @if(request()->is('student/registration*'))--}}
{{--                    <div class="clearfix form-actions">--}}
{{--                        <div class="col-md-12 align-center">--}}
{{--                            <button class="btn" type="reset">--}}
{{--                                <i class="fa fa-undo bigger-110"></i>--}}
{{--                                Reset--}}
{{--                            </button>--}}

{{--                            <button class="btn btn-primary" type="submit" value="Save" name="add_student" id="add-student">--}}
{{--                                <i class="fa fa-save bigger-110"></i>--}}
{{--                                Register--}}
{{--                            </button>--}}

{{--                            <button class="btn btn-success" type="submit" value="Save" name="add_student_another" id="add-student-another">--}}
{{--                                <i class="fa fa-save bigger-110"></i>--}}
{{--                                <i class="fa fa-plus bigger-110"></i>--}}
{{--                                Register & Add Another--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endif--}}

            </div>
        </div>
        @endif

    </div>


    <div class="space-4"></div>

    <div class="hr hr-24"></div>
</div>