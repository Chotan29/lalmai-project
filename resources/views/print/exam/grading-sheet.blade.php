@extends('layouts.master')

@section('css')
   @include('print.includes.print-layout')
    <style>
        .page-content {
            padding: 20px 22px !important;
            border: 20px #438eb9 solid;
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
                border: 10px #3C80A6 solid;
                width: 210mm;
                height: 297mm;
            }

            table#table2 {
                width:70%;
                margin:0 auto !important;
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
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                <div class="space-6"></div>
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1 padding-class">
                                        <div class="widget-box transparent ">
                                            @include('print.includes.institution-detail')
                                            <div class="row">
                                                <div class="col-md-2 col-print-2 align-left">

                                                </div>
                                                <div class="col-md-10 col-print-10 align-right">
                                                    <div class="text-center">
                                                        <h4 class="text-uppercase no-margin-top">Department of Examination</h4>
                                                        <div class="space-4"></div>
                                                        <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 25px">
                                                            <strong><u>{{ ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) }}</u></strong>
                                                        </h3>
                                                        <div class="space-10"></div>
                                                        <h2 class="no-margin text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 30px">
                                                            <strong><u>GRADE - SHEET</u></strong>
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class=row">
                                                <div class="space-6"></div>
                                                @include('print.includes.studentinfo')
                                                <div class="space-6"></div>
                                            </div>
                                            <div class="table-responsive">
                                                <table width="100%" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2" class="text-center">SN</th>
                                                            <th colspan="2" class="text-center">SUBJECT / COURSE</th>
                                                            <th rowspan="2" class="text-center">CREDIT</th>
                                                            <th colspan="3" class="text-center">OBTAINED GRADE</th>
                                                            <th rowspan="2"  class="text-center">GRADE POINT</th>
                                                            {{--<th rowspan="2"  class="text-center">REMARK</th>--}}
                                                        </tr>
                                                        <tr>
                                                            <th>CODE</th>
                                                            <th>TITLE</th>
                                                            <th>THEORY</th>
                                                            <th>PRACTICAL</th>
                                                            <th>FINAL GRADE</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if($student->subjects && $student->subjects->count() > 0)
                                                        @php($i=1)
                                                        @foreach($student->subjects as $subject)
                                                            <tr>
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ViewHelper::getSubjectCodeById($subject->subjects_id)}}</td>
                                                                <td>{{ViewHelper::getSubjectById($subject->subjects_id)}}</td>
                                                                <td class="text-center">{{ViewHelper::getSubCreditById($subject->subjects_id)}}</td>
                                                                <td class="text-center">{{$subject->obtain_score_theory?$subject->obtain_score_theory:'-'}}</td>
                                                                <td class="text-center">{{$subject->obtain_score_practical?$subject->obtain_score_practical:'-'}}</td>
                                                                <td class="text-center">{{$subject->final_grade?$subject->final_grade:'-'}}</td>
                                                                <td class="text-center">{{$subject->grade_point?$subject->grade_point:'-'}}</td>
                                                                {{--<td>{{$subject->remark?$subject->remark:'-'}}</td>--}}
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                    <tfoot class="">
                                                        <td colspan="3" class="text-right text-uppercase bold-text">AVERAGE GRADE : {{ isset($student->gpa_grade)?$student->gpa_grade:'' }}</td>
                                                        <td colspan="3" class="text-right text-uppercase bold-text">GRADE POINT AVERAGE : {{isset($student->gpa_average)?$student->gpa_average:''}}</td>
                                                        <td colspan="3" class="text-right text-uppercase bold-text">REMARK : {{isset($student->gpa_remark)?$student->gpa_remark:''}}</td>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="smaller-80">
                                                <span>
                                                    <strong>Abbreviations | </strong><strong>TH</strong>:Theory,<strong>PR</strong>:Practical,<strong>*AB</strong>:Absent,<strong>*NG</strong>:No Grade,<strong>*MG</strong>:Missing Grade, <strong>*MP</strong>:Missing Point
                                                </span>
                                                <span class="position text-uppercase" >
                                                    <?php /*
                                                    AVERAGE GRADE : {{ isset($student->gpa_grade)?$student->gpa_grade:'' }}<br>
                                                    GRADE POINT AVERAGE : {{isset($student->gpa_average)?$student->gpa_average:''}}<br>
                                                    REMARK : {{isset($student->remark)?$student->remark:''}}<br>
                                                    */?>
                                                    Rank : {{$student->rank}}
                                                        <?php /*
                                                        <br>
                                                    Position : {{$student->position}}*/ ?>
                                                </span>
                                            </div>
                                            <div class="space-24"></div>
                                            <div class="row">
                                                <div class="table-responsive">
                                                    <div class="text-center">
                                                        <h4 class="text-uppercase no-margin-top" style="font-family: 'Black Ops One';font-size: 15px"> DETAILS OF GRADE SHEET</h4>

                                                    </div>
                                                    <table id="table2" width="100%" class="table table-bordered">

<!--                                                    <table id="table2" class="table table-bordered table-grading-scale">-->
                                                            <thead>
                                                                <tr>
                                                                    <th>SN</th>
                                                                    <th>Interval in Percent</th>
                                                                    <th>Grade</th>
                                                                    <th>Description</th>
                                                                    <th>Grade Point</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            @if($data['grade-scale-range'] && $data['grade-scale-range']->count() > 0)
                                                                @php($sn=1)
                                                                @foreach($data['grade-scale-range'] as $gradingScale)
                                                                    <tr>
                                                                        <td>{{ $sn++ }}</td>
                                                                        <td>{{ $gradingScale->percentage_from.' to '.$gradingScale->percentage_to }}</td>
                                                                        <td>{{ $gradingScale->name }}</td>
                                                                        <td>{{ $gradingScale->description }}</td>
                                                                        <td>{{ $gradingScale->grade_point }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="space-32"></div>
                                            <div class="space-8"></div>
                                            <div class="row text-uppercase">
                                                <table width="100%">
                                                    <tr>
                                                        <td class="text-left"><strong style="border-top:1px black solid;">Class Teacher</strong></td>
                                                        <td class="text-center"><strong style="border-top:1px black solid;"></strong></td>
                                                        <td class="text-center"><strong style="border-top:1px black solid;"></strong></td>
                                                        <td class="text-right"><strong style="border-top:1px black solid;">Controller of Examination</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-center" style="padding-top:30px">
                                                           <?php /* <strong>Date of Result Publication : {{ \Carbon\Carbon::parse(now())->format('Y-m-d')}}</strong> |*/?>
                                                            <strong>Date of Issue : {{ \Carbon\Carbon::parse(now())->format('Y-m-d')}}</strong>
                                                        </td>
                                                    </tr>
                                                </table>
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
            <div style="page-break-after:always;"></div>
        @endforeach
    @endif
@endsection

@section('js')
    <!-- inline scripts related to this page -->
   @include('includes.scripts.print_script')
@endsection