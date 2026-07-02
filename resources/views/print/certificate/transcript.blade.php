@extends('layouts.master')

@section('css')
  {{-- @include('print.includes.print-layout')--}}
@include('print.certificate.common.css')
    <style>
        .main-content{
            font-family: 'Open Sans' !important;
            font-size:12px ;
        }
        .institute-detail{
            height: 150px;
        }
        .page-content {
            padding: 20px 5px !important;
            /*border: 20px #438eb9 solid;*/
        }
        .widget-box.transparent {
            border-width: 0;
            padding: 25px;
        }

        /*.widget-box.transparent.padding-class {*/
        /*    padding: 0px 25px;*/
        /*}*/
        span.position {
            border: 1px black solid;
            padding: 10px;
            float: right;
            margin-right:50px ;
            font-weight: bold;
        }

        .table {
            width: 98%;
            /* max-width: 100%; */
            margin-bottom: 12px;
            margin: 5px auto;
            font-size: 12px;
            font-weight: 600;
        }
        .footer {
            height: auto;
            width: auto;
        }

        @media print {
           /* .main-content-inner{
                padding: 10px;
            }*/
            /*.main-content {
                width: 210mm;
                height: 297mm;
                adding: 15px 15px 5px 15px !important;
                border: 10px #3C80A6 solid;
            }*/
            .page-content {
                position: relative;
                margin: 0;
                padding: 0px 20px;
                /*border: 10px #3C80A6 solid;*/
                width: 210mm;
                height: 297mm;
            }

           /* table#table2 {
                width:70%;
                margin:0 auto !important;
            }
            .table>tbody>tr>td{
                padding: 2px;
                line-height: 1.5;
            }
            .table>thead>tr>th {
                padding: 3px;
            }*/
            .table {
                width: 98%;
                /* max-width: 100%; */
                margin-bottom: 10px;
                margin: 5px auto;
                font-size: 8px;
                /*font-weight: 600;*/
            }
            .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
                line-height: 1.05;
                padding: 2px !important;
            }
            .table>tbody>tr>td{
                
            }

            // HTML
            // CSS
            .header {
            position: fixed;
            top: 0;
            }
            .footer {
                position: fixed;
                bottom: 20px;
                width: 90% !important;
            }
        }
    </style>
@endsection

@section('content')
    @include('print.certificate.common.print-button')
    @if($data['student'] && $data['student']->count() > 0)
        @foreach($data['student'] as $student)

            <div class="main-content" >
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row" style="width:98%; margin: 0 auto;">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1 padding-class">
                                        <div class="widget-box transparent ">                                            
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <td>
                                                            <div class="header">
                                                                <div class=row">
                                                                    <table>
                                                                        <tr>
                                                                            <td >
                                                                                @include('print.includes.print-head')
                                                                                <div class="text-center hidden-print">
                                                                                    <h3 class="no-margin text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 18px">
                                                                                        <strong><u>ACADEMIC TRANSCRIPT</u></strong>
                                                                                    </h3>
                                                                                </div>
                                                                                {!! $student->certificate_template !!}

                                                                                <?php
                                                                                    /*
                                                                                     * <table style="font-size:12px;line-height: 1.3;">
                                                                                    <tr>
                                                                                        <td>Unique Student ID# </td>
                                                                                        <td class="text-center"> : </td>
                                                                                        <td><b>{{ $student->reg_no }}</b></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Program</td>
                                                                                        <td class="text-center"> : </td>
                                                                                        <td><b>{{ ViewHelper::getFacultyTitle($student->faculty) }}</b></td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td> Name of Student </td>
                                                                                        <td class="text-center"> : </td>
                                                                                        <td><b>{{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</b></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Passing Year</td>
                                                                                        <td class="text-center"> : </td>
                                                                                        <td><b>{{ ViewHelper::getStudentBatchId($student->batch) }}</b></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Credit Required</td>
                                                                                        <td class="text-center"> : </td>
                                                                                        <td><b>{{ ViewHelper::getStudentBatchId($student->batch) }}</b></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Duration</td>
                                                                                        <td class="text-center"> : </td>
                                                                                        <td><b>{{ ViewHelper::getStudentBatchId($student->batch) }}</b></td>
                                                                                    </tr>
{{--                                                                                    <tr>--}}
{{--                                                                                        <td>Date of Birth </td>--}}
{{--                                                                                        <td class="text-center"> : </td>--}}
{{--                                                                                        <td><b>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y') }}</b></td>--}}
{{--                                                                                    </tr>--}}
                                                                                </table>
                                                                                     * */
                                                                                    ?>
                                                                            </td>
                                                                            <td class="text-center" width="20%">
                                                                                <div class="">
                                                                                    <table class="table table-bordered" style="font-size: 8px">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th colspan="4"><b>GRADING SYSTEM</b></th>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th>Marks Range</th>
                                                                                            <th>Letter Grade</th>
                                                                                            <th>Grade Point</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @if($student->gradeScaleRange && $student->gradeScaleRange->count() > 0)
                                                                                            @php($sn=1)
                                                                                            @foreach($student->gradeScaleRange as $gradingScale)
                                                                                                <tr>
                                                                                                    <td>{{ $gradingScale->percentage_from.' to '.$gradingScale->percentage_to }}</td>
                                                                                                    <td>{{ $gradingScale->name }}</td>
{{--                                                                                                    <td>{{ $gradingScale->grade_point }}</td>--}}
                                                                                                    <td>{{ number_format((float)$gradingScale->grade_point,2) }}</td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        @endif
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="content">
                                                                @if($student->semesterLedger)
                                                                @if(isset($student->semestersList) && $student->semestersList->count() > 0)
                                                                        @php($year = [1,1,2,2,3,3,4,4,0,0])
                                                                    @php($i=1)
                                                                    @foreach($student->semestersList as $semesterKey => $semester)
                                                                            <table>
                                                                                <tr>
                                                                                    <td width="30%">
                                                                                        <h3 class="no-margin text-uppercase left" style="font-size: 13px;">
                                                                                            <strong>Year-{{$year[$i-1]}}</strong>
                                                                                        </h3>
                                                                                    </td>
                                                                                    <td width="30%">
                                                                                        <h3 class="no-margin text-uppercase center" style="font-size: 13px;">
                                                                                            <strong>{{$semester}}</strong>
                                                                                        </h3>
                                                                                    </td>
                                                                                    <td width="30%">

                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                            <div class="table-responsive">
                                                                                <table width="90%" class="table table-bordered">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th class="text-center" style="width: 80px;">Code No.</th>
                                                                                            <th class="text-center" style="width: 350px;">COURSE TITLE</th>
                                                                                            {{--<th class="text-center">MARK</th>--}}
                                                                                            <th class="text-center" style="width: 60px;">CREDIT</th>
                                                                                            <th class="text-center" style="width: 70px;">GRADE POINT</th>
{{--                                                                                            <th class="text-center">Letter Grade</th>--}}
                                                                                            <th class="text-center" style="width: 70px;">Letter Grade</th>
                                                                                            <th class="text-center" style="width: 50px;">GPA</th>
                                                                                        </tr>

                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @if($student->semesterLedger[$semesterKey])
                                                                                        @php($j=1)
                                                                                        @foreach($student->semesterLedger[$semesterKey] as $subject)
                                                                                            <tr>
                                                                                                <td>{{$subject['code']}}</td>
                                                                                                <td>{{$subject['SubjectTitle']}}</td>
                                                                                                {{--<td class="text-center">{{$subject['obtainedMark']?$subject['obtainedMark']:'-'}}</td>--}}
                                                                                                <td class="text-center">{{$subject['creditHour']?$subject['creditHour']:'-'}}</td>
                                                                                                <td class="text-center">{{$subject['grade_point']?$subject['grade_point']:'-'}}</td>
{{--                                                                                                <td class="text-center">{{$subject['gradeWithCredit']?$subject['gradeWithCredit']:'-'}}</td>--}}
                                                                                                <td class="text-center">{{$subject['final_grade']?$subject['final_grade']:'-'}}</td>
                                                                                                @if($j==1)
                                                                                                <td rowspan="{{count($student->semesterLedger[$semesterKey])+1}}" style="vertical-align: middle;text-align:center;">
                                                                                                    {{ isset($student->gpaGrade[$semesterKey])?$student->gpaGrade[$semesterKey]:'' }}<br>
                                                                                                    ( {{ isset( $student->GradeLetter[$semesterKey])?$student->GradeLetter[$semesterKey]:'' }} )
                                                                                                </td>
                                                                                                @endif
                                                                                            </tr>
                                                                                            @php($j++)
                                                                                        @endforeach
                                                                                            <tr>
                                                                                            <td colspan="" class="text-center text-uppercase bold-text"></td>
                                                                                            <td colspan="" class="text-right text-uppercase bold-text">Total&nbsp;</td>
                                                                                            <td colspan="" class="text-center text-uppercase bold-text">{{isset($student->creditHourSum[$semesterKey])?$student->creditHourSum[$semesterKey] :''}}</td>
                                                                                            <td colspan="" class="text-center text-uppercase bold-text"></td>
                                                                                            <td colspan="" class="text-center text-uppercase bold-text">
{{--                                                                                                {{isset($student->gradeWithCredit[$semesterKey])?$student->gradeWithCredit[$semesterKey] :''}}--}}
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endif
                                                                                    </tbody>
                                                                                    <!-- <tfoot class="bold-text">
                                                                                    
                                                                                    </tfoot> -->
                                                                                </table>
                                                                            </div>

                                                                            @if($i==4)
                                                                                <div style="page-break-after:always;"></div>
                                                                            @endif
                                                                        @php($i++)
                                                                        @endforeach
                                                                @endif
                                                                @endif
                                                                <table class="table-bordered float-right" style="width: 20%;font-weight: 600;float: right !important;">
                                                                    <tr>
                                                                        <td>Credit Earned</td>
                                                                        <td class="text-center">{{$student['transcriptCHS']}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Total CGPA</td>
                                                                        <td class="text-center">{{$student['transcriptGPA']}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Letter Grade</td>
                                                                        <td class="text-center">{{$student['transcriptGL']}}</td>
                                                                    </tr>
                                                                </table>                                                               
                                                                
                                                            </div>
                                                            <div class="space-8"></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>
                                                            <div class="footer">
                                                                <span style="float:left; width: 30%">
                                                                    <strong>Prepared By:</strong>
                                                                    <br>


                                                                    <strong>Verified By:</strong>
                                                                    <br>
                                                                    <br>
                                                                    <br>
                                                                </span>
                                                                <span style="float:right;">
                                                                    <div class="space-8"></div>
<!--                                                                     <strong style="border-top:1px black solid;">Controller of Examination</strong>-->
                                                                     <strong style="font-size:16px;">Controller of Examination</strong>
                                                                </span>

                                                                <div class="space-8"></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tfoot>

                                            </table>
                                        </div>
                                        <!-- PAGE CONTENT ENDS -->
                                    </div><!-- /.col -->
                                </div><!-- /.row -->
                            </div><!-- /.page-content -->
                        </div>
                    </div>
                </div>
            </div><!-- /.main-content -->

        @endforeach
    @endif
@endsection

@section('js')
    <!-- inline scripts related to this page -->
   @include('includes.scripts.print_script')
@endsection