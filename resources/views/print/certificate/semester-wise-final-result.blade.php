@extends('layouts.master')

@section('css')
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
            .page-content {
               /* position: relative;
                margin: 0;
                padding: 20px;
                !*border: 10px #3C80A6 solid;*!
                width: 210mm;
                height: 297mm;*/
            }

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

        }


    </style>

    <style>
        @if($data['certificate_template']->background_status == 1)
            @if(@isset($data['certificate_template']->background_image))
                .subpage{
            background-image: url({{ asset('images/certificateBackground/'.$data['certificate_template']->background_image) }}) !important;
            background-repeat: round !important;
            background-size: cover !important;
        }
        @endif
        @endif

        {{$data['certificate_template']->custom_css}}
    </style>
@endsection

@section('content')
    @include('print.certificate.common.print-button')
    @if($data['student'] && $data['student']->count() > 0)
        @foreach($data['student'] as $student)
            <div class="book">
                <div class="page">
                    <div class="subpage">
                        <div class="main-content">
                            <div class="main-content-inner">
                                <div class="page-content">
                                    <div class="row">
                                        <div class="col-xs-12 align-center">
                                            <!-- PAGE CONTENT BEGINS -->
                                            <div class="row">
                                                <div class="col-sm-12 col-md-12 col-print-12 align-center text-center">
                                                    <div class="widget-box transparent">
                                                        @include('print.includes.print-head')
                                                        <div class="row" style="padding: 35px;">
                                                            {!! $student->certificate_template !!}
                                                        </div>

                                                        <div class="row">
                                                            <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 16px">
                                                                <strong>{{$student->certificate}} </strong>
                                                            </h3>
                                                            @if($student->semesterLedger)
                                                                @if(isset($student->semestersList) && $student->semestersList->count() > 0)
                                                                    @php($i=1)

                                                                    <table class="table table-bordered" style="width: 50%;margin:0 auto;">
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
                                                                    <table class="table table-bordered" style="width: 30%;margin:0 auto;">
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
                                                        </div>
                                                    </div>
                                                    <div class="space-16"></div>
                                                    <div class="row" style="padding-left: 30px;">
                                                        <table width="90%">
                                                            <tr>
                                                                <td class="text-left">
                                                                    {{--<strong>Print Date:{{\Carbon\Carbon::now()->format('d-m-Y') }}</strong>--}}
                                                                </td>

                                                                <td width="30%">
                                                                    <hr>
                                                                    Section Officer <br>Exam Section

                                                                </td>
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                                                                <td width="30%">
                                                                    <hr>
                                                                    Deputy Controller of Examination
                                                                    <div class="space-32"></div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-left">
                                                                    {{--<strong>Print Date:{{\Carbon\Carbon::now()->format('d-m-Y') }}</strong>--}}
                                                                </td>

                                                                <td width="30%">


                                                                </td>
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                                                                <td width="30%">
                                                                    <hr>
                                                                    Approved
                                                                    <div class="space-32"></div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-left">
                                                                    {{--<strong>Print Date:{{\Carbon\Carbon::now()->format('d-m-Y') }}</strong>--}}
                                                                </td>

                                                                <td width="30%">
                                                                    <hr>
                                                                    Registrar

                                                                </td>
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                                                                <td width="30%">
                                                                    <hr>
                                                                    Vice- Chancellor
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div><!-- /.col -->
                                            </div><!-- /.row -->
                                        </div><!-- /.page-content -->
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.main-content -->
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection

@section('js')
    <!-- inline scripts related to this page -->
    @include('includes.scripts.print_script')
@endsection