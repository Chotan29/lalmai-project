{{--
<style>
.page-break {
    page-break-after: always;
}
</style>
<h1>Page 1</h1>
<div class="page-break"></div>
<h1>Page 2</h1>
--}}

<!DOCTYPE html>
<html lang="en">
@include('layouts.includes.header')

<link href="https://fonts.googleapis.com/css?family=Fugaz+One|Lobster|Merienda|Righteous|Black+Ops+One|Gilda+Display" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
<style>
    .main-content {
        margin-left: 10px;
    }

    .profile-info-name {
        width: 300px;
    }
    .profile-info-value {
        margin-left: 310px;
    }

    @page {
        size: A4 portrait;
        /*margin: 2cm 3cm;*/
    }
    @media print {
        /*body {margin-top: 50mm; margin-bottom: 50mm;
            margin-left: 0mm; margin-right: 0mm}
        */
        body
        {
            margin: 1mm 1mm 1mm 1mm;
        }
        @page {
            size: A4 portrait;
            /*margin: 2cm 3cm;*/
        }

        .page-break {
            page-break-before: always;
            margin-top: 1.5in;
        }
        .avoid-break {
            page-break-inside: avoid;
        }
    }

</style>

<body>
    <div class="main-container" id="main-container">
    <div class="main-container-inner">
        <div class="main-content">
            <div class="main-content-inner">
                <div class="page-content">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            @include('includes.flash_messages')
                            @include('includes.validation_error_messages')
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-sm-12 align-right hidden-print">
                                    <a href="#" class="btn btn-xs btn-primary" onclick="window.print();">
                                        <i class="ace-icon fa fa-print"></i> Print
                                    </a>
                                </div>
                                <div class="space-12"></div>
                                <div class="row">
                                    <div class="col-md-2 col-print-2 align-left">
                                        @if(isset($reg_setting->logo))
                                            <img src="{{ asset('web/registration_settings/'. $reg_setting->logo) }}" width="150px">
                                        @else
                                            <img src="{{ asset('images/setting/general/'. $general_setting->logo) }}" width="150px">
                                        @endif
                                    </div>
                                    <div class="col-md-10 col-print-10 align-right">
                                        <div class="text-center">
                                            <h2 class="no-margin-top text-uppercase" style="font-family: 'Raleway', cursive; font-size: 22px;font-weight:600;">
                                                <strong>{{isset($reg_setting->title)?$reg_setting->title:$general_setting->institution}}</strong>
                                            </h2>
                                            <h5 class="no-margin-top">
                                                {{isset($general_setting->address)?$general_setting->address:""}}, {{isset($general_setting->phone)?$general_setting->phone:""}}, {{isset($general_setting->email)?$general_setting->email:""}}
                                            </h5>
                                            <h3 class="no-margin-top" style="font-family: 'Righteous', cursive; font-size: 24px;font-weight:600;text-transform: uppercase">{{isset($reg_setting->sub_title)?$reg_setting->sub_title:'ONLINE APPLICATION'}}  </h3>
                                            <h3 class="text-uppercase no-margin-top" style="font-family: 'Righteous', cursive; font-size: 22px;font-weight:600;">{{  isset($row->registration_programmes_id)?WebsiteViewHelper::getProgrammeById( $row->registration_programmes_id ):'' }} </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-9 col-print-9">
                                        {{--<div class="space-3"></div>
                                        <div class="label label-info label-xlg arrowed-in arrowed-right arrowed">{{ $row->name  }}</div>--}}

                                        <div class="space-6"></div>
                                        <div class="profile-user-info profile-user-info-striped">
                                            @if($row->registration_programmes_id !="")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Application For : </div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="registration_programmes_id">{{  ViewHelper::getProgrammeById( $row->registration_programmes_id ) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($row->reg_no != "")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Register No :</div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="reg_no">{{  $row->reg_no }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->reg_date != "")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Register Date :</div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="reg_date">{{  \Carbon\Carbon::parse($row->reg_date)->format('d-m-Y') }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->name != "")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Name :</div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="name">{{  $row->name }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->sex != "")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Sex : </div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="sex">{{ $row->sex }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->date_of_birth != "")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> DOB : </div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="date_of_birth">{{ \Carbon\Carbon::parse($row->date_of_birth)->format('d-m-Y')}}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->religion !="")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Religion:</div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="religion">{{ $row->religion }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->caste !="")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Cast : </div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="caste">{{ $row->caste }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->nationality)
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Nationality : </div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="nationality">{{ $row->nationality }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->state !="")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> State of Permanent Resident : </div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="state">{{ $row->state }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->mother_tongue !="")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Mother Tongue : </div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="mother_tongue">{{ $row->mother_tongue }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($row->blood_group != "")
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> Blood Group : </div>
                                                    <div class="profile-info-value">
                                                        <span class="editable" id="blood_group">{{ $row->blood_group }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if(isset($reg_setting->medical_info_status) && $reg_setting->medical_info_status==1)
                                                @if($row->medicine_info != "")
                                                    <div class="profile-info-row">
                                                        <div class="profile-info-name"> Allergic to any Medicine. If yes, Detail : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="medicine_info">{{ $row->medicine_info }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($row->disease_info != "")
                                                    <div class="profile-info-row">
                                                        <div class="profile-info-name"> Affected by any recurring disease. If yes, Detail : </div>
                                                        <div class="profile-info-value">
                                                            <span class="editable" id="disease_info">{{ $row->disease_info }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>

                                    </div>
                                    <div class="col-xs-12 col-sm-3 col-print-3">
                                        @if(isset($reg_setting->photo_status) && $reg_setting->photo_status==1)
                                            <div>
                                                <span class="profile-picture">
                                                   @if($row->student_image != '')
                                                        <img id="avatar" class="editable img-responsive" alt="" src="{{ asset('images'.DIRECTORY_SEPARATOR.'registration'.DIRECTORY_SEPARATOR.$row->student_image) }}" width="250px" />
                                                    @else
                                                        <img id="avatar" class="editable img-responsive" alt="" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" />
                                                    @endif
                                                </span>

                                                @if($row->student_signature != '')
                                                    <span class="profile-picture align-center">
                                                    <img class="editable img-responsive" alt="" src="{{ asset('images'.DIRECTORY_SEPARATOR.'registration'.DIRECTORY_SEPARATOR.$row->student_signature) }}" width="150px" />
                                                </span>
                                                @else
                                                @endif
                                                @if($row->guardian_signature != '')
                                                    <span class="profile-picture align-center">
                                                    <img class="editable img-responsive" alt="" src="{{ asset('images'.DIRECTORY_SEPARATOR.'registration'.DIRECTORY_SEPARATOR.$row->guardian_signature) }}" width="150px" />
                                                </span>
                                                @else
                                                @endif
                                                <div class="space-4"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-print-12">
                                        @if(isset($reg_setting->guardian_detail_status) && $reg_setting->guardian_detail_status==1)
                                            <div class="space-12"></div>
                                            <h3 class="text-uppercase no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">GUARDIAN/PARENT</h3>
                                            <table class="table-bordered" width="100%">
                                                <tr class="text-center">
                                                    <th>Name of Parent/Guardian</th>
                                                    <th>Relation</th>
                                                    <th>Occupation</th>
                                                    <th>Annual Income</th>
                                                </tr>
                                                <tr class="text-center">
                                                    <td>
                                                        {{ $row->guardian_name }}
                                                    </td>
                                                    <td>
                                                        {{ $row->guardian_relation }}
                                                    </td>
                                                    <td>
                                                        {{ $row->guardian_occupation }}
                                                    </td>
                                                    <td>
                                                        {{ $row->guardian_annual_income }}
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif

                                        @if(isset($reg_setting->permanent_address_status) && $reg_setting->permanent_address_status==1)
                                        <div class="space-12"></div>
                                        <h3 class="text-uppercase no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">PERMANENT ADDRESS</h3>
                                        <table class="table-bordered text-center" width="100%">
                                            <tr>
                                                <th>Address:</th>
                                                <th>Telephone</th>
                                                <th>Mobile</th>
                                                <th>Alternative Contact</th>
                                                <th>Email</th>
                                            </tr>
                                            <tr>
                                                <td>{{  $row->address }}</td>
                                                <td>{{  $row->tel }}</td>
                                                <td>{{  $row->cell_1 }}</td>
                                                <td>{{  $row->cell_2 }}</td>
                                                <td>{{  $row->email }}</td>
                                            </tr>
                                        </table>
                                        @endif

                                        @if(isset($reg_setting->mailing_address_status) && $reg_setting->mailing_address_status==1)
                                        <div class="space-12"></div>
                                        <h3 class="text-uppercase no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">MAILING ADDRESS</h3>
                                        <table class="table-bordered text-center" width="100%">
                                            <tr>
                                                <th>Address:</th>
                                                <th>Telephone</th>
                                                <th>Mobile</th>
                                                <th>Alternative Contact</th>
                                                <th>Email</th>
                                            </tr>
                                            <tr>
                                                <td>{{  $row->mailing_address }}</td>
                                                <td>{{  $row->mailing_tel }}</td>
                                                <td>{{  $row->mailing_cell_1 }}</td>
                                                <td>{{  $row->mailing_cell_2 }}</td>
                                                <td>{{  $row->mailing_email }}</td>
                                            </tr>
                                        </table>

                                        @endif
                                    </div>
                                </div>
                            </div><!-- /.row -->

                            <div class="row">
                                @if (isset($academicInfos) && $academicInfos->count() > 0)
                                    <div class="space-12"></div>
                                    <h3 class="text-uppercase text-center no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">ACADEMIC QUALIFICATIONS</h3>
                                    <div class="space-6"></div>
                                <table class="table-bordered" width="100%">
                                    <tr class="text-center">
                                        <th width="5%">S.N.</th>
                                        <th>EXAMINATION PASSED</th>
                                        <th>NAME OF COLLEGE/SCHOOL</th>
                                        <th>NAME OF BOARD/UNIVERSITY</th>
                                        <th>YEAR OF PASS</th>
                                        <th>PERCENTAGE/GRADE OF MARK</th>
                                    </tr>
                                    @php($i=1)
                                    @foreach($academicInfos as $academicInfo)
                                    <tr class="text-center">
                                        <td>{{$i}}</td>
                                        <td>
                                            {{ $academicInfo->examination }}
                                        </td>
                                        <td>
                                            {{ $academicInfo->institution }}
                                        </td>
                                        <td>
                                            {{ $academicInfo->board_university }}
                                        </td>
                                        <td>
                                            {{ $academicInfo->year_of_pass }}
                                        </td>
                                        <td>
                                            {{ $academicInfo->percentage_grade }}
                                        </td>
                                    </tr>
                                        @php($i++)
                                    @endforeach
                                </table>
                                @endif
                             @if (isset($workExperiences) && $workExperiences->count() > 0)
                                        <div class="space-12"></div>
                                        <h3 class="text-uppercase text-center no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">PREVIOUS WORK EXPERIENCES</h3>
                                        <div class="space-6"></div>
                                        <table class="table-bordered" width="100%">
                                            <tr class="text-center">
                                                <th width="5%">S.N.</th>
                                                <th>Experiences Detail</th>
                                            </tr>
                                            @php($i=1)
                                            @foreach($workExperiences as $workExperience)
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>
                                                        {{ $workExperience->experience_info }}
                                                    </td>
                                                </tr>
                                                @php($i++)
                                            @endforeach
                                        </table>
                                @endif
                            </div>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->
    </div><!-- /.main-container-inner -->
    </div>
</body>

</html>

