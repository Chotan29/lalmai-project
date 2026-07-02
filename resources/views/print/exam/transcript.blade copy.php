@extends('layouts.master')

@section('css')
{{--   @include('print.includes.print-layout')--}}
@include('print.certificate.common.css')
    <style>
        .page-content {
            padding: 20px 5px !important;
            /*border: 20px #438eb9 solid;*/
        }

        .widget-box.transparent.padding-class {
            padding: 0px 25px;
        }
        span.position {
            border: 1px black solid;
            padding: 10px;
            float: right;
            margin-right:50px ;
            font-weight: bold;
        }
        .table {
            width: 95%;
            /* max-width: 100%; */
            margin-bottom: 12px;
            margin: 5px auto;
            font-size: 12px;
            font-weight: 600;
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
                padding: 20px;
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
                width: 95%;
                /* max-width: 100%; */
                margin-bottom: 12px;
                margin: 5px auto;
                font-size: 8px;
                font-weight: 600;
            }
            .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
                padding: 2px !important;
            }

            // HTML
          
            // CSS
            .header {
            position: fixed;
            top: 0;
            }
            .footer {
            position: fixed;
            bottom: 0;
            }

        }


    </style>
@endsection

@section('content')

    @if($data['student'] && $data['student']->count() > 0)
        @foreach($data['student'] as $student)

            <div class="main-content " >
                <div class="col-sm-12 align-right hidden-print">
                    <a href="#" class="btn btn-primary" onclick="window.print();">
                        <i class="ace-icon fa fa-print bigger-200"></i> Print
                    </a>
                </div>
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row" style="width:98%; margin: 0 auto;">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1 padding-class">
                                        <div class="widget-box transparent ">
                                            <div class="header">
                                                <div class=row">
                                                    <table>
                                                        <tr>
                                                            <td width="80%">
                                                                @include('print.includes.institution-detail')
                                                                <div class="text-center">
                                                                    <h2 class="no-margin text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 18px">
                                                                        <strong><u>ACADEMIC TRANSCRIPT</u></strong>
                                                                    </h2>
                                                                    
                                                                    <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 25px">
                                                                        <strong><u>{{-- ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) --}}</u></strong>
                                                                    </h3>
                                                                </div>

                                                                {{--@include('print.includes.studentinfo')--}}
                                                                <table width="100%" class="table table-bordered">
                                                                    <tr>
                                                                        <td class="text-right">Reg No. : </td>
                                                                        <td class="text-left"><b>{{ $student->reg_no }}</b></td>
                                                                    </tr>
                                                                    <tr>

                                                                        <td class="text-right">Name : </td>
                                                                        <td class="text-left"><b>{{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</b></td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="text-right">Faculty/Program/Class : </td>
                                                                        <td class="text-left"><b>{{ ViewHelper::getFacultyTitle($student->faculty) }}</b></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-right">Batch : </td>
                                                                        <td><b>{{ ViewHelper::getStudentBatchId($student->batch) }}</b></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-right">Date of Birth : </td>
                                                                        <td class="text-left"><b>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y') }}</b></td>
                                                                    </tr>
                                                                </table>

                                                            </td>
                                                            <td class="text-center" width="30%">
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
                                                                    @if($data['grade-scale-range'] && $data['grade-scale-range']->count() > 0)
                                                                        @php($sn=1)
                                                                        @foreach($data['grade-scale-range'] as $gradingScale)
                                                                            <tr>
                                                                                <td>{{ $gradingScale->percentage_from.' to '.$gradingScale->percentage_to }}</td>
                                                                                <td>{{ $gradingScale->name }}</td>
                                                                                <td>{{ $gradingScale->grade_point }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="space-6"></div>
                                            </div>
                                            <div class="content">
                                                @if($student->semesterLedger)
                                                @if(isset($student->semestersList) && $student->semestersList->count() > 0)
                                                    @php($i=1)
                                                    @foreach($student->semestersList as $semesterKey => $semester)
                                                            <h2 class="no-margin text-uppercase center" style="font-family: 'Black Ops One', cursive;font-size: 12px">
                                                                <strong><u>{{$semester}}</u></strong>
                                                            </h2>
                                                            <div class="table-responsive">
                                                                <table width="80%" class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center" width="18%">Code No.</th>
                                                                            <th class="text-center">COURSE TITLE</th>
                                                                            {{--<th class="text-center">MARK</th>--}}
                                                                            <th class="text-center">CREDIT</th>
                                                                            <th class="text-center">GRADE POINT</th>
                                                                            <th class="text-center">Letter Grade</th>
                                                                            <th class="text-center">GPA</th>
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
                                                                                <td class="text-center">{{$subject['creditHour']}}</td>
                                                                                <td class="text-center">{{$subject['grade_point']?$subject['grade_point']:'-'}}</td>
                                                                                <td class="text-center">{{$subject['final_grade']?$subject['final_grade']:'-'}}</td>
                                                                                @if($j==1)
                                                                                <td rowspan="{{count($student->semesterLedger[$semesterKey])+1}}">
                                                                                GPA : {{ isset($student->gpaGrade[$semesterKey])?$student->gpaGrade[$semesterKey]:'' }}<br>
                                                                                    ( {{ isset($student->GradeLetter[$semesterKey])?$student->GradeLetter[$semesterKey]:'' }} )
                                                                                </td>
                                                                                @endif
                                                                            </tr>
                                                                            @php($j++)
                                                                        @endforeach
                                                                        <tr>
                                                                        <td colspan="" class="text-center text-uppercase bold-text"></td>
                                                                        <td colspan="" class="text-center text-uppercase bold-text">Total</td>
                                                                        <td colspan="" class="text-center text-uppercase bold-text">{{isset($student->creditHourSum[$semesterKey])?$student->creditHourSum[$semesterKey] :''}}</td>
                                                                        <td colspan="" class="text-center text-uppercase bold-text"></td>
                                                                        <td colspan="" class="text-center text-uppercase bold-text">
                                                                        
                                                                        </td>
                                                                        </tr>
                                                                    @endif
                                                                    </tbody>
                                                                    <tfoot class="bold-text">
                                                                    
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                                @if($i==4)
                                                                <div style="page-break-after:always;"></div>
                                                                <div class="space-16"></div>
                                                                @endif
                                                        @php($i++)
                                                        @endforeach
                                                @endif
                                                @endif
                                                <table class="table-bordered float-right" style="width: 21%;font-weight: 600;float: right !important;">
                                                    <tr>
                                                        <td>Credit Earned</td>
                                                        <td class="text-center">{{$student['transcriptCHS']}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Grade Point</td>
                                                        <td class="text-center">{{$student['transcriptGPA']}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total CGPA</td>
                                                        <td class="text-center">{{$student['transcriptGL']}}</td>
                                                    </tr>
                                                </table>
                                                <div class="space-32"></div>
                                                
                                            </div>
                                            <div class="footer">
                                            <div class="space-32"></div>
                                                <div class="row text-uppercase">
                                                    <table style="width: 88%; margin: 0 auto;">
                                                        <tr>
                                                            <td class="text-left"><strong>Date of Print : {{ \Carbon\Carbon::parse(now())->format('Y-m-d')}}</strong></td>
                                                            <td class="text-left"><strong style="border-top:1px black solid;">Prepared By:</strong></td>
                                                            <td class="text-center"><strong style="border-top:1px black solid;"></strong></td>
                                                            <td class="text-left"><strong style="border-top:1px black solid;">Verified By:</strong></td>
                                                            <td class="text-center"><strong style="border-top:1px black solid;"></strong></td>
                                                            <td class="text-center"><strong style="border-top:1px black solid;"></strong></td>
                                                            <td class="text-right"><strong style="border-top:1px black solid;">Controller of Examination</strong></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="space-32"></div>
                                            </div>
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