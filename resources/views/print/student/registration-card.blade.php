@extends('layouts.master')

@section('css')
    @include('print.certificate.common.css')
    <style>
        .page {
            width: 8.5in;
            height: 14.2in;
        }
        .page {
            width: 8.5in;
            height: 14.2in;
            padding: 2mm;
            margin: 0px auto;
            /*border: 1px #D3D3D3 solid;*/
            border-radius: 5px;
            /*background: white;
            border:1px red solid;*/
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .subpage {
            width: 8.4in;
            /*height: 3.8in;*/
            padding: 0 50px !important;
            /*background: rgba(255, 0, 0, 0.28);
            border:1px red solid;*/
            margin: 0 auto;
        }

        .subpage.top-layout {
            height: 8.1in;
            /*background: rgba(0, 128, 0, 0.29);
            border:1px red solid;*/
            overflow: hidden;
        }

        .subpage.bottom-layout {
            height: 5.8in;
            /*background: rgba(143, 113, 250, 0.29);
            border:1px red solid;*/
            overflow: hidden;
        }

        .top-print-head {
            height: 2.1in;
            /*background: rgba(255, 255, 84, 0.39);
            border:1px red solid;*/
        }

        .top-print-content {
            height: 6in;
            /*background: rgba(11, 117, 148, 0.42);
            border:1px red solid;*/
        }

        .top-bottom-content-gap {
            height: 0.5in;
            /*background: red;
            border:1px red solid;*/
        }

        .bottom-content-header {
            height: 1.7in;
            /*background: yellow;
            border:1px red solid;*/
        }

        .bottom-content-body {
            height: 3.6in;
            /*background: #e35cec;
            border:1px red solid;*/
        }


        .table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {
            font-size: 12px;
            text-align: left;
            border: 1px solid #dddddd1a;
        }

        td.float-right {
            vertical-align: baseline;
        }


        .top-print-content .table-bordered > tbody > tr > td{
            line-height: 1.2;
        }
        .bottom-content-body .table-bordered > tbody > tr > td{
            line-height: 0.8;
            font-size: 10px;
        }
        table.signature-bottom {
            /*background: red;
            border:1px red solid;*/
            float: right;
            width: 58%;
            margin-top: -14px;
            margin-right: 6px;
        }

        p {
            margin: 0 0 0px;
        }

        .img-thumbnail {
            border: none;
        }

        .real-content {
           /* font-size: 12px;*/
            margin: 0 auto !important;
        }

        @media print {
            .real-content {
                width: 100%;
            }

            .table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {
                border: 1px solid #dddddd1a !important;
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
                    <div class="subpage top-layout">
                        <div class="top-print-head">
                            @if($data['certificate_template']->print_institute_head == 1)
                                <div class="institute-detail">
                                    @include('print.includes.institution-detail')
                                </div>
                                <div class="row">
                                    <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One',cursive;font-size: 22px;width: 100%; text-align: center;">
                                        <strong><u>{{$student->certificate}} </u></strong>
                                    </h3>
                                </div>
                            @else
                            @endif

                        </div>
                        <div class="top-content-body">
                            <div class=row">
                                <div class="space-16"></div>
                                <div class="text-center top-print-content">
                                    <table style="font-size:12px;line-height: 1.1;">
                                        <tr>
                                            <td>
                                                {!! $student->certificate_template !!}
                                            </td>
                                            <td class="float-right">
                                                @if($student->student_image != '')
                                                    <img class="img-thumbnail"  alt="Image" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_image) }}" width="120px" />
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="space-32"></div>
                                    <table>
                                        <tr>
                                            <td>
                                                @if($student->student_signature != '')
                                                    <img class="img-thumbnail"  alt="Image" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_signature) }}" width="120px" /><br>
                                                    Student Signature
                                                @else
                                                    <span style="border-top: 2px black solid; padding: 0px 50px;">&nbsp;Student Signature&nbsp;</span >

                                                @endif


                                            </td>
                                            <td>
                                                @if($student->student_signature != '')
                                                    <img class="img-thumbnail"  alt="Image" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_signature) }}" width="120px" /><br>
                                                    Registrar
                                                @else
                                                    <span style="border-top: 2px black solid; padding: 0px 50px;">&nbsp;Registrar&nbsp;</span >
                                                @endif

                                            </td>
                                            {{--                                                                <td>--}}
                                            {{--                                                                    <span style="border-top: 2px black solid; padding: 0px 50px;">&nbsp;Registrar&nbsp;</span >--}}
                                            {{--                                                                </td>--}}
                                        </tr>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="subpage bottom-layout">
                        <div class="top-bottom-content-gap">
<!--                            <hr style="border-bottom: 1px dashed black;">-->
                        </div>
                        <div class="bottom-content-header">
                            @if($data['certificate_template']->print_institute_head == 1)
                                <div class="institute-detail">
                                    @include('print.includes.institution-detail')
                                </div>
                                <div class="row">
                                    <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One',cursive;font-size: 22px;width: 100%; text-align: center;">
                                        <strong><u>{{$student->certificate}} </u></strong>
                                    </h3>
                                </div>
                            @endif

                        </div>
                        <div class="bottom-content-body">
                            <div class=row">
                                <div class="text-center top-print-content">
                                    <table>
                                        <tr>
                                            <td>
                                                {!! $student->certificate_template !!}
                                            </td>
                                            <td class="float-right">
                                                @if($student->student_image != '')
                                                    <img class="img-thumbnail"  alt="Image" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_image) }}" width="120px" />
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="signature-bottom">
                                        <tr>
                                            <td>
                                                @if($student->student_signature != '')
                                                    <img class="img-thumbnail"  alt="Image" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_signature) }}" width="120px" /><br>
                                                    Student Signature
                                                @else
                                                    <span style="border-top: 2px black solid; padding: 0px 50px;">&nbsp;Student Signature&nbsp;</span >

                                                @endif


                                            </td>
                                            <td>
                                                @if($student->student_signature != '')
                                                    <img class="img-thumbnail"  alt="Image" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_signature) }}" width="120px" /><br>
                                                    Registrar
                                                @else
                                                    <span style="border-top: 2px black solid; padding: 0px 50px;">&nbsp;Registrar&nbsp;</span >
                                                @endif

                                            </td>
                                            {{--                                                                <td>--}}
                                            {{--                                                                    <span style="border-top: 2px black solid; padding: 0px 50px;">&nbsp;Registrar&nbsp;</span >--}}
                                            {{--                                                                </td>--}}
                                        </tr>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection

@section('js')
    <!-- inline scripts related to this page -->
    {{--@include('includes.scripts.print_script')--}}
@endsection