@extends('user-student.layouts.master')

@section('css')
    @include('print.includes.print-layout')
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
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="widget-box transparent">
                                            @include('print.includes.institution-detail')
                                            <div class=row">

                                                <table>
                                                    <tr>

                                                        <td>
                                                            <div class="text-center">
                                                                <h4 class="text-uppercase no-margin-top">University Grade<!--Department of Examination--></h4>
                                                                <div class="space-4"></div>
                                                                <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 25px">
                                                                    <strong><u>{{ ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) }}</u></strong>
                                                                </h3>
                                                                <div class="space-10"></div>
                                                                <h2 class="no-margin text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 22px">
                                                                    <strong><u>GRADE - SHEET</u></strong>
                                                                </h2>
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
                                                                    <td class="text-left"><b>{{ ViewHelper::getFacultyTitle($data['faculty']) }}</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-right">Sem./Sec. : </td>
                                                                    <td class="text-left"><b>{{ ViewHelper::getSemesterTitle($data['semester']) }}</b></td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-right">Date of Birth : </td>
                                                                    <td class="text-left"><b>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y') }}</b></td>
                                                                    {{-- <td class="text-right">Son/Daughter of : </td>
                                                                     <td>{{$student->faculty}}</td>--}}
                                                                </tr>
                                                            </table>

                                                        </td>
                                                        <td class="text-center">
                                                            <table id="table2" class="table-bordered">

                                                                <!--<table id="table2" class="table table-bordered table-grading-scale">-->
                                                                <thead>
                                                                <tr>
                                                                    <th colspan="4"> <h4 class="text-uppercase no-margin-top" style="font-family: 'Black Ops One';font-size: 15px"> GRADING SYSTEM</h4></th>
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
                                                <div class="space-6"></div>
                                            </div>
                                            <div class="table-responsive">
                                                <table width="100%" class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <!--                                                            <th class="text-center">SN</th>-->
                                                        <th class="text-center" width="18%">Code No.</th>
                                                        <th class="text-center">COURSE TITLE</th>
                                                        <!--                                                            <th class="text-center">MARK</th>-->
                                                        <th class="text-center">CREDIT</th>
                                                        <th class="text-center">GRADE POINT</th>
                                                        <th class="text-center">Letter Grade</th>
                                                        {{--                                                            <th class="text-center">C x GPA</th>--}}
                                                        <!--                                                            <th class="text-center">GPA</th>-->
                                                        {{--<th rowspan="2"  class="text-center">REMARK</th>--}}
                                                    </tr>

                                                    </thead>
                                                    <tbody>
                                                    @if($student->subjects && $student->subjects->count() > 0)
                                                        @php($i=1)
                                                        @foreach($student->subjects as $subject)
                                                            <tr>
                                                                <!--                                                                <td>{{ $i++ }}</td>-->
                                                                <td>{{ViewHelper::getSubjectCodeById($subject->subjects_id)}}</td>
                                                                <td>{{ViewHelper::getSubjectById($subject->subjects_id)}}</td>
                                                                {{--                                                                <td class="text-center">{{$subject->obtainedMark?$subject->obtainedMark:'-'}}</td>--}}
                                                                <td class="text-center">{{$subject->creditHour}}</td>
                                                                <!--                                                                <td class="text-center">{{ViewHelper::getSubCreditById($subject->subjects_id)}}</td>-->
                                                                <td class="text-center">{{$subject->grade_point?$subject->grade_point:'-'}}</td>
                                                                <td class="text-center">{{$subject->final_grade?$subject->final_grade:'-'}}</td>
                                                                {{--                                                                <td class="text-center">{{$subject->gradeWithCredit?$subject->gradeWithCredit:'-'}}</td>--}}
                                                                {{--<td>{{$subject->remark?$subject->remark:'-'}}</td>--}}
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                    <tfoot class="bold-text">
                                                    <th colspan="" class="text-right text-uppercase bold-text"></th>
                                                    <th colspan="" class="text-right text-uppercase bold-text">Total</th>
                                                    <th colspan="" class="text-right text-uppercase bold-text">{{isset($student->creditHourSum )?$student->creditHourSum :''}}</th>
                                                    <th colspan="" class="text-right text-uppercase bold-text"></th>
                                                    <th colspan="2" class="text-right text-uppercase bold-text">
                                                        GPA : {{ isset($student->gpa_grade)?$student->gpa_grade:'' }}
                                                        ({{ isset($student->gpa_gradeletter)?$student->gpa_gradeletter:'' }})
                                                    </th>
                                                    {{--                                                        <td colspan="" class="text-right text-uppercase bold-text">GRADE POINT AVERAGE : {{isset($student->gpa_average)?$student->gpa_average:''}}</td>--}}
                                                    {{--                                                        <td colspan="" class="text-right text-uppercase bold-text">REMARK : {{isset($student->gpa_remark)?$student->gpa_remark:''}}</td>--}}
                                                    {{--                                                        <td colspan="" class="text-right text-uppercase bold-text">AVERAGE GRADE : {{ isset($student->gpa_grade)?$student->gpa_grade:'' }}</td>--}}
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="smaller-80">
                                                <strong>Abbreviations | </strong><strong>TH</strong>:Theory,<strong>PR</strong>:Practical,<strong>*AB</strong>:Absent,<strong>*NG</strong>:No Grade,<strong>*MG</strong>:Missing Grade, <strong>*MP</strong>:Missing Point
                                            </div>
                                            <div class="space-32"></div>
                                            <div class="space-32"></div>
                                            <div class="row text-uppercase">
                                                <table width="100%">
                                                    <tr>
                                                        <td class="text-left">
                                                            <strong>Date of Result Publication : {{ \Carbon\Carbon::parse(now())->format('Y-m-d')}}</strong>
                                                            {{--<br>
                                                            <strong>Date of Issue : {{ \Carbon\Carbon::parse(now())->format('Y-m-d')}}</strong>--}}
                                                        </td>
                                                        <td class="text-center"><strong style="border-top:1px black solid;"></strong></td>
                                                        <td class="text-center"><strong style="border-top:1px black solid;"></strong></td>
                                                        <td class="text-right"><strong style="border-top:1px black solid;">Controller of Examination</strong></td>
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