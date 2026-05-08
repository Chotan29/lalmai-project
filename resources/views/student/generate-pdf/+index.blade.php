<!DOCTYPE html>
<html lang="en">
{{--@include('layouts.includes.header')
@section('top-script')
@endsection--}}
<body class="no-skin">
<div class="main-container ace-save-state" id="main-container">
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">

                <div class="page-header hidden-print">
                    <h1>
                        Student
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="space-2"></div>

                        <div id="user-profile-2" class="user-profile">
                            <div class="tabbable tabs-left">
                                <ul class="nav nav-tabs  padding-18 hidden-print ">
                                    <li class="active">
                                        <a data-toggle="tab" href="#profile">
                                            <i class="green ace-icon fa fa-user bigger-140"></i>
                                            Profile
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content no-border padding-24">
                                    <div id="profile" class="tab-pane in active">
                                        <div class="row text-center">
                                            <div class="row" >
                                                <div class="col-md-1 align-left" style="height: 100px;">
                                                    @if(isset($generalSetting->logo))
                                                        <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" width="100px">
                                                    @endif
                                                </div>
                                                <div class="col-md-11 text-center">
                                                    {{--Anton|Archivo+Black|Josefin+Sans|Poppins|Russo+One|--}}
                                                    <h6 class="no-margin-top" style="font-size: 14px">
                                                        {{isset($generalSetting->salogan)?$generalSetting->salogan:""}}
                                                    </h6>
                                                    <h2 class="no-margin-top text-uppercase" style="font-family: 'Bowlby+One+SC'; font-size: 30px; font-weight: 600">
                                                        <strong>{{isset($generalSetting->institute)?$generalSetting->institute:""}}</strong>
                                                    </h2>
                                                    <h5 class="no-margin-top" style="font-size: 16px;">
                                                        {{isset($generalSetting->address)?$generalSetting->address:""}}, {{isset($generalSetting->phone)?$generalSetting->phone:""}}
                                                    </h5>
                                                    <h5 class="no-margin-top" style="font-size: 16px;">
                                                        {{isset($generalSetting->email)?$generalSetting->email:""}}, {{isset($generalSetting->website)?$generalSetting->website:""}}
                                                    </h5>

                                                </div>
                                            </div>
                                            <div class="space-4"></div>
                                            <h2 class="no-margin-top text-uppercase" style="font-family: 'Bowlby+One+SC'; font-size: 30px; font-weight: 600">
                                                <strong>REGISTRATION DETAIL</strong>
                                            </h2>
                                        </div>
                                        <hr class="hr-double hr-8">


                                        <div class="row">

                                            <div class="col-xs-12 col-sm-3 col-print-3">
                                                <div>
                                                    <span class="profile-picture">
                                                       @if($data['student']->student_image != '')
                                                            <img id="avatar" class="editable img-responsive" alt="{{ $data['student']->title }}" src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$data['student']->student_image) }}" width="250px" />
                                                        @else
                                                            <img id="avatar" class="editable img-responsive" alt="{{ $data['student']->title }}" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" />
                                                        @endif
                                                    </span>

                                                    @if($data['student']->student_signature != '')
                                                        <span class="profile-picture align-center">
                                                                <img class="editable img-responsive" alt="{{ $data['student']->title }}" src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$data['student']->student_signature) }}" width="150px" />
                                                        </span>
                                                    @else

                                                    @endif

                                                    <div class="space-4"></div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-9 col-print-9">

                                                <div class="space-3"></div>
                                                <div class="label label-info label-xlg arrowed-in arrowed-right arrowed">{{ $data['student']->first_name.' '.
                    $data['student']->middle_name.' '.$data['student']->last_name }}</div>
                                                <div class="space-6"></div>
                                                <div class="profile-user-info profile-user-info-striped">
                                                    <div class="profile-info-row">
                                                        @if($data['student']->faculty !="")
                                                            <div class="profile-info-name"> Faculty/Program/Class: </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="faculty">{{  ViewHelper::getFacultyTitle( $data['student']->faculty ) }}</span>
                                                            </div>
                                                        @endif
                                                        @if($data['student']->semester != "")
                                                            <div class="profile-info-name"> Sem./Sec. :</div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="semester">{{  ViewHelper::getSemesterTitle( $data['student']->semester ) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="profile-info-row">
                                                        @if($data['student']->batch !="")
                                                            <div class="profile-info-name"> Session/Batch: </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="faculty">{{  ViewHelper::getStudentBatchId( $data['student']->batch ) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="profile-info-row">
                                                        @if($data['student']->reg_no != "")
                                                            <div class="profile-info-name"> Reg. No.: </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="reg_no">{{ $data['student']->reg_no }}</span>
                                                            </div>
                                                        @endif
                                                        @if($data['student']->reg_date !="")
                                                            <div class="profile-info-name"> Reg. Date :</div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="reg_date">{{ \Carbon\Carbon::parse($data['student']->reg_date)->format('d/m/Y')}}</span>
                                                            </div>
                                                        @endif
                                                    </div>


                                                    <div class="profile-info-row">
                                                        @if($data['student']->university_reg != "")
                                                            <div class="profile-info-name"> Univ.Reg.: </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="university_reg">{{ $data['student']->university_reg }}</span>
                                                            </div>
                                                        @endif
                                                        @if($data['student']->date_of_birth != "")
                                                            <div class="profile-info-name"> DOB : </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="date_of_birth">{{ \Carbon\Carbon::parse($data['student']->date_of_birth)->format('d/m/Y')}}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="profile-info-row">
                                                        @if($data['student']->gender != "")
                                                            <div class="profile-info-name"> Gender : </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="gender">{{ $data['student']->gender }}</span>
                                                            </div>
                                                        @endif
                                                        @if($data['student']->blood_group != "")
                                                            <div class="profile-info-name"> Blood Group : </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="blood_group">{{ $data['student']->blood_group }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="profile-info-row">
                                                        @if($data['student']->religion !="")
                                                            <div class="profile-info-name"> Religion:</div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="religion">{{ $data['student']->religion }}</span>
                                                            </div>
                                                        @endif
                                                        @if($data['student']->caste !="")
                                                            <div class="profile-info-name"> Caste : </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="caste">{{ $data['student']->caste }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="profile-info-row">
                                                        @if($data['student']->nationality)
                                                            <div class="profile-info-name"> Nationality : </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="nationality">{{ $data['student']->nationality }}</span>
                                                            </div>
                                                        @endif
                                                        @if($data['student']->mother_tongue !="")
                                                            <div class="profile-info-name"> Mother Tongue: </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="mother_tongue">{{ $data['student']->mother_tongue }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="profile-info-row">
                                                        @if($data['student']->email !="")
                                                            <div class="profile-info-name"> E-mail : </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="email">{{ $data['student']->email }}</span>
                                                            </div>
                                                        @endif
                                                        @if($data['student']->mobile_1 !="")
                                                            <div class="profile-info-name"> Mobile No : </div>
                                                            <div class="profile-info-value">
                                                                <span class="editable" id="email">{{ $data['student']->mobile_1.','.$data['student']->mobile_2 }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div><!-- /.row -->
                                        <div class="row">
                                            <div class="space-6"></div>
                                            <div class="label label-info label-xlg arrowed-in arrowed-right arrowed">Permanent Address</div>
                                            <div class="space-6"></div>
                                            <div class="profile-user-info profile-user-info-striped">
                                                <div class="profile-info-row">
                                                    @if($data['student']->address !="")
                                                        <div class="profile-info-name"> Address : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="permanent_place">{{ $data['student']->address }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="profile-user-info profile-user-info-striped">
                                                <div class="profile-info-row">
                                                    @if($data['student']->state !="")
                                                        <div class="profile-info-name"> State :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="permanent_district">{{ $data['student']->state }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->country !="")
                                                        <div class="profile-info-name"> Country : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="permanent_zone">{{ $data['student']->country }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="space-6"></div>
                                            <div class="label label-info label-xlg arrowed-in arrowed-right arrowed">Temporary Address</div>
                                            <div class="space-6"></div>
                                            <div class="profile-user-info profile-user-info-striped">
                                                <div class="profile-info-row">
                                                    @if($data['student']->temp_address !="")
                                                        <div class="profile-info-name"> Address : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="permanent_place">{{ $data['student']->temp_address }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="profile-user-info profile-user-info-striped">
                                                <div class="profile-info-row">
                                                    @if($data['student']->temp_state !="")
                                                        <div class="profile-info-name"> State :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="permanent_district">{{ $data['student']->temp_state }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->temp_country !="")
                                                        <div class="profile-info-name"> Country : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="permanent_zone">{{ $data['student']->temp_country }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="space-6"></div>
                                            <div class="label label-info label-xlg arrowed-in arrowed-right arrowed">Parential Info</div>
                                            @if($data['student']->grandfather_first_name !="")
                                                <div class="space-6"></div>
                                                <div class="profile-user-info profile-user-info-striped">
                                                    <div class="profile-info-row">
                                                        <div class="profile-info-name"> Grand Father :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="temporary_place">{{ $data['student']->grandfather_first_name.' '.$data['student']->grandfather_middle_name.' '.$data['student']->grandfather_last_name }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="space-6"></div>
                                            <div class="profile-user-info profile-user-info-striped">
                                                <div class="profile-info-row">
                                                    @if($data['student']->father_first_name !="")
                                                        <div class="profile-info-name"> Father Name :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="temporary_place">{{ $data['student']->father_first_name.' '.$data['student']->father_middle_name.' '.$data['student']->father_last_name }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->father_eligibility !="")
                                                        <div class="profile-info-name"> Eligibility :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="father_eligibility">{{ $data['student']->father_eligibility }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->father_occupation !="")
                                                        <div class="profile-info-name"> Occupation :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="father_occupation">{{ $data['student']->father_occupation }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="profile-info-row">
                                                    @if($data['student']->father_office !="")
                                                        <div class="profile-info-name"> Office :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="father_office">{{ $data['student']->father_office }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->father_office_number !="")
                                                        <div class="profile-info-name"> Office Num. :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="father_office_number">{{ $data['student']->father_office_number }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->father_residence_number !="")
                                                        <div class="profile-info-name"> Residence : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="father_residence_number">{{ $data['student']->father_residence_number }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="profile-info-row">
                                                    @if($data['student']->father_mobile_1 !="")
                                                        <div class="profile-info-name"> Mobile 1 :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="father_mobile_1">{{ $data['student']->father_mobile_1 }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->father_mobile_2 !="")
                                                        <div class="profile-info-name"> Mobile 2 :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="father_mobile_2">{{ $data['student']->father_mobile_2 }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->father_email !="")
                                                        <div class="profile-info-name"> E-mail : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="father_email">{{ $data['student']->father_email }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="space-6"></div>
                                            <div class="profile-user-info profile-user-info-striped">
                                                <div class="profile-info-row">
                                                    @if($data['student']->mother_first_name !="")
                                                        <div class="profile-info-name"> Mother Name :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="temporary_place">{{ $data['student']->mother_first_name.' '.$data['student']->mother_middle_name.' '.$data['student']->mother_last_name }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->mother_eligibility !="")
                                                        <div class="profile-info-name"> Eligibility :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_eligibility">{{ $data['student']->mother_eligibility }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->mother_occupation !="")
                                                        <div class="profile-info-name"> Occupation :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_occupation">{{ $data['student']->mother_occupation }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="profile-info-row">
                                                    @if($data['student']->mother_office !="")
                                                        <div class="profile-info-name"> Office :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_office">{{ $data['student']->mother_office }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->mother_office_number !="")
                                                        <div class="profile-info-name"> Office Num. :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_office_number">{{ $data['student']->mother_office_number }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->mother_residence_number !="")
                                                        <div class="profile-info-name"> Residence : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_residence_number">{{ $data['student']->mother_residence_number }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="profile-info-row">
                                                    @if($data['student']->mother_mobile_1 !="")
                                                        <div class="profile-info-name"> Mobile 1 :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_mobile_1">{{ $data['student']->mother_mobile_1 }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->mother_mobile_2 !="")
                                                        <div class="profile-info-name"> Mobile 2 :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_mobile_2">{{ $data['student']->mother_mobile_2 }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->mother_email !="")
                                                        <div class="profile-info-name"> E-mail : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_email">{{ $data['student']->mother_email }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="space-6"></div>
                                            <div class="label label-info label-xlg arrowed-in arrowed-right arrowed">Guardian Info</div>
                                            <div class="space-6"></div>
                                            <div class="profile-user-info profile-user-info-striped">
                                                <div class="profile-info-row">
                                                    @if($data['student']->guardian_first_name !="")
                                                        <div class="profile-info-name"> Guardian Name :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="temporary_place">{{ $data['student']->guardian_first_name.' '.$data['student']->guardian_middle_name.' '.$data['student']->guardian_last_name }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->guardian_eligibility !="")
                                                        <div class="profile-info-name"> Eligibility :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_eligibility">{{ $data['student']->guardian_eligibility }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->guardian_occupation !="")
                                                        <div class="profile-info-name"> Occupation :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_occupation">{{ $data['student']->guardian_occupation }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="profile-info-row">
                                                    @if($data['student']->guardian_office !="")
                                                        <div class="profile-info-name"> Office :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_office">{{ $data['student']->guardian_office }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->guardian_office_number !="")
                                                        <div class="profile-info-name"> Office Num. :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_office_number">{{ $data['student']->guardian_office_number }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->guardian_residence_number !="")
                                                        <div class="profile-info-name"> Residence : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_residence_number">{{ $data['student']->guardian_residence_number }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="profile-info-row">
                                                    @if($data['student']->guardian_mobile_1 !="")
                                                        <div class="profile-info-name"> Mobile 1 :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_mobile_1">{{ $data['student']->guardian_mobile_1 }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->guardian_mobile_2 !="")
                                                        <div class="profile-info-name"> Mobile 2 :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_mobile_2">{{ $data['student']->guardian_mobile_2 }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->guardian_email !="")
                                                        <div class="profile-info-name"> E-mail : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_email">{{ $data['student']->guardian_email }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="profile-info-row">
                                                    @if($data['student']->guardian_relation !="")
                                                        <div class="profile-info-name"> Relation :  </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="guardian_relation">{{ $data['student']->guardian_relation }}</span>
                                                        </div>
                                                    @endif
                                                    @if($data['student']->guardian_address !="")
                                                        <div class="profile-info-name"> Address :</div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="mother_mobile_2">{{ $data['student']->guardian_address }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /#home -->
                                </div>
                            </div>

                        </div>
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
</div>

</body>
</html>
