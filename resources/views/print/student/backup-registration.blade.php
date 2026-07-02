@extends('layouts.master')

@section('css')
    <style>
        @page {
            /* margin-top: 5cm;
             margin-bottom: 5cm;*/
        }

        span.receipt-copy {
            font-family:'Alfa+Slab+One';
            font-size: 22px;
            font-weight: 600;
            /*background: #438EB9;
            color: white;*/
            padding: 3px 15px;
        }

        /* .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
             border: 1px solid #444 !important;
             padding: 0px 3px 0px 5px;
         }
        */

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            padding: 5px;
        }

        @media print {

            body {
                margin-top: 6mm; margin-bottom: 6mm;
                margin-left: 12.7mm; margin-right: 6mm
            }

            @page{
                /*margin-left: 100px !important;*/
                /* margin: 500px !important;*/
            }

            .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
                border: 0.5px solid #7e7d7d1c !important;

            }

            span.receipt-copy {
                font-size: 22px;
                font-weight: 600;
                /*background: black;
                color: white;*/
                padding: 3px 15px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="row text-center">
                    <div class="col-sm-12 align-right hidden-print mb-4">
                        <a href="#" class="btn btn-primary" onclick="window.print();">
                            <i class="ace-icon fa fa-print align-top bigger-125 icon-on-right"></i> Print
                        </a>
                    </div>
                    <div class="row" >
                        <table>
                            <tr>
                                <td>
                                    @if(isset($generalSetting->logo))
                                        <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" width="150px">
                                    @endif
                                </td>
                                <td width="75%">
                                    <h2 class="no-margin-top text-uppercase" style="font-family: 'Bowlby+One+SC'; font-size: 30px; font-weight: 600">
                                        <strong>{{isset($generalSetting->institute)?$generalSetting->institute:""}}</strong>
                                    </h2>
                                    <h5 class="no-margin-top" style="font-size: 16px;">
                                        {{isset($generalSetting->address)?$generalSetting->address:""}}, {{isset($generalSetting->phone)?$generalSetting->phone:""}}
                                    </h5>
                                    <h6 class="no-margin-top" style="font-size: 14px">
                                        <b>{{isset($generalSetting->salogan)?$generalSetting->salogan:""}}</b>
                                    </h6>
                                    <h5 class="no-margin-top" style="font-size: 16px;">
                                        {{isset($generalSetting->email)?$generalSetting->email:""}}, {{isset($generalSetting->website)?$generalSetting->website:""}}
                                    </h5>
                                    <hr class="hr-double hr-4">
                                    <h2 class="no-margin-top text-uppercase" style="font-family: 'Bowlby+One+SC'; font-size: 24px; font-weight: 600">
                                        <strong>Admission Application Form</strong>
                                    </h2>
                                    <hr class="hr-double hr-2">
                                </td>
                                <td>
                                    <span class="profile-picture">
                                           @if($data['student']->student_image != '')
                                                <img class="editable img-responsive" alt="{{ $data['student']->title }}" src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$data['student']->student_image) }}" width="150px" />
                                            @else
                                                <img class="editable img-responsive" alt="{{ $data['student']->title }}" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="150px"/>
                                            @endif
                                        </span>

                                        @if($data['student']->student_signature != '')
                                            <span class="profile-picture align-center">
                                        <img class="editable img-responsive" alt="{{ $data['student']->title }}" src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$data['student']->student_signature) }}" width="150px" />
                                    </span>
                                    @else

                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row" >
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Sr.No: </b> {{$data['student']->reg_no}}
                            </td>
                            <td>
                                <b>Date: </b>{{ \Carbon\Carbon::parse($data['student']->reg_date)->format('d/m/Y')}}
                            </td>
                            <td>
                                <b>Class:</b> {{  ViewHelper::getFacultyTitle( $data['student']->faculty ) }} -{{  ViewHelper::getSemesterTitle( $data['student']->semester ) }}
                            </td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td>
                                <b>Name of Student:</b> {{ $data['student']->first_name.' '.
                    $data['student']->middle_name.' '.$data['student']->last_name }}
                            </td>
                            <td>
                                <b>Subject Applied for:</b>&nbsp;
                                @if(isset($data['appliedSubjects']) && $data['appliedSubjects']->count() > 0)
                                    @foreach($data['appliedSubjects'] as $subject)
                                        {{ViewHelper::getSubjectById($subject->subjects_id)}} ,
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><b>Gender:</b> {{ $data['student']->gender }}</td>
                            <td><b>D.O.B.:</b> {{ \Carbon\Carbon::parse($data['student']->date_of_birth)->format('d/m/Y')}}</td>
                        </tr>
                        <tr>
                            <td><b>Aaadhaar: </b>{{ $data['student']->adhaar_id }}</td>
                            <td><b>SBI Collect No:</b> {{ $data['student']->sbi_collect_no}}</td>
                        </tr>
                        <tr>
                            <td><b>Father's Name:</b>
                                {{ $data['student']->father_first_name.' '.$data['student']->father_middle_name.' '.$data['student']->father_last_name }}
                            </td>
                            <td><b>Mother's Name:</b>
                                {{ $data['student']->mother_first_name.' '.$data['student']->mother_middle_name.' '.$data['student']->mother_last_name }}
                            </td>
                        </tr>
                        <tr>
                            <td><b>Guardian Name: </b>
                                {{ $data['student']->guardian_first_name.' '.$data['student']->guardian_middle_name.' '.$data['student']->guardian_last_name }}
                            </td>
                            <td><b>Nationality &amp; Religion: </b>{{ $data['student']->religion }}</td>
                        </tr>
                        <tr>
                            <td><b>Permanent Mobile No: </b>{{ $data['student']->sbi_collect_no}}</td>
                            <td><b>State:</b> {{ $data['student']->state}}</td>
                        </tr>
                        <tr>
                            <td><b>Caste: </b>{{ $data['student']->caste}}</td>
                            <td><b>Special Category:</b> {{ $data['student']->special_category}}</td>
                        </tr>
                        <tr>
                            <td><b>University URN: </b>{{ $data['student']->university_reg}}</td>
                            <td><b>Registration Fee Detail:</b>&nbsp; {{ $data['student']->reg_fee}}</td>
                        </tr>
                        <tr>
                            <td><b>Permanent Address: </b>{{ $data['student']->address}}</td>
                            <td><b>Weightage Clam for Admission:</b> {{ $data['student']->weightage_claim}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    @if (isset($data['academicInfos']) && $data['academicInfos']->count() > 0)
                        <h3 class="text-uppercase text-center no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">EDUCATIONAL QUALIFICATIONS</h3>
                        <div class="space-2"></div>
                        <table class="table table-bordered" style="font-size: 12px;">
                            <tbody>
                            <tr>
                                <td style="text-align: center; "><b>Exam</b></td>
                                <td style="text-align: center; "><b>Board/University</b></td>
                                <td style="text-align: center; "><b>Year</b></td>
                                <td style="text-align: center; "><b>Subjects</b></td>
                                <td style="text-align: center; "><b>Marks Obtained</b></td>
                                <td style="text-align: center; "><b>Marks Maximum</b></td>
                                <td style="text-align: center; "><b>Percentage</b></td>
                            </tr>
                            @foreach($data['academicInfos'] as $academicInfo)
                                <tr class="text-center">
                                    <td>
                                        {{ $academicInfo->board }}
                                    </td>
                                    <td>
                                        {{ $academicInfo->institution }}
                                    </td>
                                    <td>
                                        {{ $academicInfo->pass_year }}
                                    </td>
                                    <td>
                                        {{ $academicInfo->major_subjects }}
                                    </td>
                                    <td>
                                        {{ $academicInfo->mark_obtained }}
                                    </td>
                                    <td>
                                        {{ $academicInfo->maximum_mark }}
                                    </td>
                                    <td>
                                        {{ $academicInfo->percentage }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="row">
                    <h3 class="text-uppercase text-center no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">Declaration</h3>
                    <div class="space-6"></div>
                    I declare that I have gone through the rules of College and University and I have met the eligibility criteria for admission. The information given in this form is true and correct to the best of my knowledge. No disciplinary action or court case or use of unfair means in exams has been reported against me. My application is liable to get cancelled if my application is found to contain incorrect / false information. I, further declare that I shall abide by the rules & regulations of the college.
                </div>
                <div class="row">
                    <table>
                        <tbody>
                        <tr>
                            <td><b>Date:</b>.............................</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>Signature of Father/Mother/Guardian:</b>.............................</td>
                            <td><b>Signature of Candidate:</b>.............................</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <span class="text-uppercase text-left no-margin-top" style="font-family: 'Righteous', cursive; font-size: 16px">List of Annexures : </span>
                    @if(isset($data['annexure']) && $data['annexure']->count() >0)
                        @foreach($data['annexure'] as $annexure)
                            <i class="fa fa-check-square"></i> {{ ViewHelper::getAnnextureById($annexure->annexures_id)}}
                        @endforeach
                    @endif
                </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    </div>
@endsection

@section('js')
@endsection

