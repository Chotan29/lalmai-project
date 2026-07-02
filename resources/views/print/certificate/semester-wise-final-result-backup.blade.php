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
           /* width: 95%;*/
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
                /*width: 98%;*/
                /* max-width: 100%; */
                margin-bottom: 10px;
                margin: 5px auto;
                font-size: 12px;
                font-weight: 600;
            }
            .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
                line-height: 1.1;
                padding: 2px !important;
            }
            .table>tbody>tr>td{
                
            }

            // HTML
          
            // CSS
            /*.header {*/
            /*position: fixed;*/
            /*top: 0;*/
            /*}*/
            /*.footer {*/
            /*    position: fixed;*/
            /*    bottom: 0;*/
            /*    width: 90% !important;*/
            /*}*/

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
                                                                                {!! $student->certificate_template !!}

                                                                                <div class="text-center">
                                                                                    <h3 class="no-margin text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 18px">
                                                                                        <strong><u>{{$student->certificate}}</u></strong>
                                                                                    </h3>
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
                                                                        @php($i=1)

                                                                            <table class="table table-bordered" style="width: 30%;margin:0 auto;">
                                                                                <tr class="text-center">
                                                                                    <td>SEMESTER</td>
                                                                                    <td>GPA</td>
                                                                                    <td>GRADE</td>
                                                                                </tr>
                                                                                @foreach($student->semestersList as $semesterKey => $semester)
                                                                                <tr>
                                                                                    <td>{{$semester}}</td>
                                                                                    <td>{{ isset($student->gpaGrade[$semesterKey])?$student->gpaGrade[$semesterKey]:'' }}</td>
                                                                                    <td>{{ isset($student->GradeLetter[$semesterKey])?$student->GradeLetter[$semesterKey]:'' }}</td>
                                                                                </tr>
                                                                                    @php($i++)
                                                                                @endforeach

                                                                            </table>
                                                                        <table class="table table-bordered" style="width: 20%;margin:0 auto;">
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
                                                                    @endif
                                                                @endif
                                                                <table class="table-bordered float-right" style="width: 21%;font-weight: 600;float: right !important;">

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
                                                                    <strong style="border-top:1px black solid;">Section Officer (Exam Section)</strong>
                                                                    <div class="space-32"></div>
                                                                    <div class="space-32"></div>
                                                                    <strong style="border-top:1px black solid;">&nbsp;&nbsp;&nbsp;Registrar&nbsp;&nbsp;&nbsp;</strong>

                                                                    <br>
                                                                </span>
                                                                <span style="float:right;">
                                                                     <strong style="border-top:1px black solid;">Controller of Examination</strong>
                                                                    <div class="space-32"></div>
                                                                    <strong style="border-top:1px black solid;">Approved</strong>
                                                                    <div class="space-32"></div>
                                                                    <strong style="border-top:1px black solid;">Vice-Chancellor</strong>
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