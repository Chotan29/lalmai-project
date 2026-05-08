@extends('web.admin.layouts.master')
@section('content')
    <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
            <ul class="breadcrumb">
                @include($view_path.'.includes.breadcrumb-primary')
            </ul><!-- .breadcrumb -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    {{ $panel }} Manager
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        View
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12 col-md-12">
                @include('includes.flash_messages')
                @include('includes.validation_error_messages')
                <!-- PAGE CONTENT BEGINS -->
                    <div class="row">
                        <div class="col-sm-12 align-right hidden-print">
                            @include('web.admin.includes.buttons.view-page-button')
                            <a href="#" class="btn-primary btn-sm" onclick="window.print();">
                                <i class="ace-icon fa fa-print"></i> Print
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-9 col-print-9">
                                {{--<div class="space-3"></div>
                                <div class="label label-info label-xlg arrowed-in arrowed-right arrowed">{{ $data['row']->name  }}</div>--}}

                                <div class="space-6"></div>
                                <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                        @if($data['row']->contact_groups_id != "")
                                            <div class="profile-info-name"> Group :</div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="contact_groups_id">{{ WebsiteViewHelper::getContactGroupById($data['row']->contact_groups_id) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->reg_no != "")
                                            <div class="profile-info-name"> Register No :</div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="reg_no">{{  $data['row']->reg_no }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->reg_date != "")
                                            <div class="profile-info-name"> Register Date :</div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="reg_date">{{  \Carbon\Carbon::parse($data['row']->reg_date)->format('d-m-Y') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->name != "")
                                            <div class="profile-info-name"> Name :</div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="name">{{  $data['row']->name }}</span>
                                            </div>

                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->sex != "")
                                            <div class="profile-info-name"> Sex : </div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="sex">{{ $data['row']->sex }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->date_of_birth != "")
                                            <div class="profile-info-name"> DOB : </div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="date_of_birth">{{ \Carbon\Carbon::parse($data['row']->date_of_birth)->format('d-m-Y')}}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->religion !="")
                                            <div class="profile-info-name"> Religion:</div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="religion">{{ $data['row']->religion }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->caste !="")
                                            <div class="profile-info-name"> Cast : </div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="caste">{{ $data['row']->caste }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->nationality)
                                            <div class="profile-info-name"> Nationality : </div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="nationality">{{ $data['row']->nationality }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->state !="")
                                            <div class="profile-info-name"> State of Permanent Resident : </div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="state">{{ $data['row']->state }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->mother_tongue !="")
                                            <div class="profile-info-name"> Mother Tongue : </div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="mother_tongue">{{ $data['row']->mother_tongue }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->blood_group != "")
                                            <div class="profile-info-name"> Blood Group : </div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="blood_group">{{ $data['row']->blood_group }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->medicine_info != "")
                                            <div class="profile-info-name"> Allergic to any Medicine. If yes, Detail :</div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="medicine_info">{{ $data['row']->medicine_info }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-info-row">
                                        @if($data['row']->disease_info != "")
                                            <div class="profile-info-name"> Affected by any recurring disease. If yes, Detail : </div>
                                            <div class="profile-info-value">
                                                <span class="editable" id="disease_info">{{ $data['row']->disease_info }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div class="col-xs-12 col-sm-3 col-print-3">
                                <div>
                                        <span class="profile-picture">
                                           @if($data['row']->image != '')
                                                <img id="avatar" class="editable img-responsive" alt="" src="{{ asset($folder_name.DIRECTORY_SEPARATOR.$data['row']->image) }}" width="250px" />
                                            @endif
                                        </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-print-12">
                                <div class="space-12"></div>
                                <div class="profile-user-info profile-user-info-striped">

                                </div>

                                <div class="space-12"></div>
                                <h3 class="text-uppercase no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">PERMANENT ADDRESS</h3>
                                <table class="table-bordered text-center" WIDTH="100%">
                                    <tr>
                                        <th>Address:</th>
                                        <th>Telephone</th>
                                        <th>Mobile</th>
                                        <th>Alternative Contact</th>
                                        <th>Email</th>
                                    </tr>
                                    <tr>
                                        <td>{{  $data['row']->address }}</td>
                                        <td>{{  $data['row']->tel }}</td>
                                        <td>{{  $data['row']->cell_1 }}</td>
                                        <td>{{  $data['row']->cell_2 }}</td>
                                        <td>{{  $data['row']->email }}</td>
                                    </tr>
                                </table>

                                <div class="space-12"></div>
                                <h3 class="text-uppercase no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">MAILING ADDRESS</h3>
                                <table class="table-bordered text-center" WIDTH="100%">
                                    <tr>
                                        <th>Address:</th>
                                        <th>Telephone</th>
                                        <th>Mobile</th>
                                        <th>Alternative Contact</th>
                                        <th>Email</th>
                                    </tr>
                                    <tr>
                                        <td>{{  $data['row']->mailing_address }}</td>
                                        <td>{{  $data['row']->mailing_tel }}</td>
                                        <td>{{  $data['row']->mailing_cell_1 }}</td>
                                        <td>{{  $data['row']->mailing_cell_2 }}</td>
                                        <td>{{  $data['row']->mailing_email }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div><!-- /.row -->


                    <div class="row">
                        @if (isset($data['academicInfos']) && $data['academicInfos']->count() > 0)
                            <div class="space-12"></div>
                            <h3 class="text-uppercase text-center no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">ACADEMIC QUALIFICATIONS</h3>
                            <div class="space-6"></div>
                            <table class="table-bordered" WIDTH="100%">
                                <tr class="text-center">
                                    <th width="5%">S.N.</th>
                                    <th>EXAMINATION PASSED</th>
                                    <th>NAME OF COLLEGE/SCHOOL</th>
                                    <th>NAME OF BOARD/UNIVERSITY</th>
                                    <th>YEAR OF PASS</th>
                                    <th>PERCENTAGE/GRADE OF MARK</th>
                                </tr>
                                @php($i=1)
                                @foreach($data['academicInfos'] as $academicInfo)
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


                        @if (isset($data['workExperiences']) && $data['workExperiences']->count() > 0)
                            <div class="space-12"></div>
                            <h3 class="text-uppercase text-center no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">PREVIOUS WORK EXPERIENCES</h3>
                            <div class="space-6"></div>
                            <table class="table-bordered" WIDTH="100%">
                                <tr class="text-center">
                                    <th width="5%">S.N.</th>
                                    <th>Experiences Detail</th>
                                </tr>
                                @php($i=1)
                                @foreach($data['workExperiences'] as $workExperience)
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
    </div><!-- /.main-content -->
@endsection

