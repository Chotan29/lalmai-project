@extends('layouts.master')

@section('css')
    @include('print.certificate.common.css')
    <style>
        .page {
            width: 210mm;
            height: 296mm;
            padding: 2mm;
            margin: 0 auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .subpage {
            width: 200mm;
            height: 286mm;
        }
        .table {
            width: 95%;
            /* max-width: 100%; */
            margin-bottom: 12px;
            margin: 5px auto;
            font-size: 12px;
            font-weight: 600;
        }

        .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
            font-size: 12px;
            text-align:left;
            border: none;
            line-height: 1;
        }
        p {
            margin: 0 0 0px;
        }
        @media print {
            .page-content {
                position: relative;
                margin: 0;
                padding: 20px;
            }
            .table {
                border: none;
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
    @if($data['students'] && $data['students']->count() > 0)
        @foreach($data['students'] as $student)
            <div class="book">
                <div class="page">
                    <div class="subpage">
                        <div class="main-content" {{--style="page-break-after:always;"--}}>
                            <div class="main-content-inner">
                                <div class="page-content">
                                    <div class="row">
                                        <div class="col-sm-12 align-center text-center">
                                            <div class="widget-box transparent">
                                                @include('print.includes.institution-detail')
                                                <div class="row">
                                                    <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One',cursive;font-size: 22px;width: 100%;">
                                                        <strong><u>{{$student->certificate}} </u></strong>
                                                    </h3>
                                                </div>
                                                <div class=row">
                                                    <div class="text-center" style="font-size: 12px;">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    {!! $student->certificate_template !!}
                                                                </td>
                                                                <td>
                                                                    @if($student->student_image != '')
                                                                        <img class="img-thumbnail"  alt="Image" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_image) }}" width="120px" />
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    <strong style="border-bottom: 2px black solid"  >{{\Carbon\Carbon::now()}}</strong ><br>
                                                                    Print Date/Time
                                                                </td>
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
                                                    <div class="row text-uppercase">

                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-12 align-center text-center">
                                            <div class="widget-box transparent">
                                                @include('print.includes.institution-detail')
                                                <div class="row">
                                                    <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One',cursive;font-size: 22px;width: 100%;">
                                                        <strong><u>{{$student->certificate}} </u></strong>
                                                    </h3>
                                                </div>
                                                <div class=row">
                                                    <div class="text-center" style="font-size: 12px;">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    {!! $student->certificate_template !!}
                                                                </td>
                                                                <td>
                                                                    @if($student->student_image != '')
                                                                        <img class="img-thumbnail"  alt="Image" src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$student->student_image) }}" width="120px" />
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    <strong style="border-bottom: 2px black solid"  >{{\Carbon\Carbon::now()}}</strong ><br>
                                                                    Print Date/Time
                                                                </td>
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
                                                    <div class="row text-uppercase">

                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->
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
    {{--@include('includes.scripts.print_script')--}}
@endsection