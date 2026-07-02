@extends('layouts.master')

@section('css')
    <style type="text/css" media="screen">
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
            /*font: 12pt "Tahoma";*/
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 296mm;
            height: 210mm;
            margin: 10mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .page-content {
            background-color: transparent !important;
        }

        .subpage {
            width: 286mm;
            height: 200mm;
            margin: 10px auto;
            padding: 10px;
            /*height: 257mm;*/
        }

        .col-md-4:not(:last-child) {
            border-right: 1px #eeeded solid;
        }

        .table {
            margin-bottom: 0px;
        }

        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
            padding: 4px !important;
            font-size: 0.8em;
            font-weight: 600;
            text-align: left;
        }

        @page {
            size: A4;
            margin: 0;
        }

        /*@media print {*/
        /*    html, body {*/
        /*        width: 296mm;*/
        /*        height: 210mm;*/
        /*    }*/

        /*    .page {*/
        /*        margin: 0;*/
        /*        border: initial;*/
        /*        border-radius: initial;*/
        /*        width: initial;*/
        /*        min-height: initial;*/
        /*        box-shadow: initial;*/
        /*        background: initial;*/
        /*        !*page-break-after: always;*!*/
        /*    }*/
        /*}*/

    </style>
    <style type="text/css" media="print">


        /* @page {size:landscape}  */
        @media print {

            @page {size: A4 landscape;max-height:100%; max-width:100%;}

            .subpage {
                /*width: 286mm;*/
                /*height: 200mm;*/
                margin: 10px auto;
                padding: 10px;
                /*height: 257mm;*/
            }

            .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
                padding: 2px 4px !important;
                font-size: 0.8em;
                font-weight: 600;
                text-align: left;
            }

            /*.table,table,.table-bordered{*/
            /*    width: inherit*/
            /*    }*/

            .table {
                margin-bottom: 0px;
            }
        }

            /* use width if in portrait (use the smaller size to try
               and prevent image from overflowing page... */
            /*img { height: 90%; margin: 0; padding: 0; }*/

            /*body{width:100%;*/
            /*    height:100%;*/
            /*    -webkit-transform: rotate(-90deg) scale(.68,.68);*/
            /*    -moz-transform:rotate(-90deg) scale(.58,.58) }    }*/

    </style>
@endsection

@section('content')
    @if($data['student'] && $data['student']->count() > 0 )
        @foreach($data['student'] as $key => $student)
            <div class="main-content" style="page-break-after:always;">
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                <div class="space-6"></div>
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="widget-box transparent">
                                            @include('print.student-fee.includes.print-header')
                                        </div>
                                    </div>
                                    <!-- PAGE CONTENT ENDS -->
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div><!-- /.page-content -->
                    </div>
                </div><!-- /.main-content -->
            </div>

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
                                                <table>
                                                    <tr>
                                                        <td>
                                                            @include('print.student-fee.includes.fee-voucher-common-content')
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <td style="font-size: 12px;font-weight: 600; text-align: center;">BANK COPY:</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        Bank STAMP
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                        <td width="25px;"></td>
                                                        <td width="25px;" style="border-left: 1px #c9c4c4 solid"></td>
                                                        <td>
                                                            @include('print.student-fee.includes.fee-voucher-common-content')
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <td style="font-size: 12px;font-weight: 600; text-align: center;">COLLEGE COPY:</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        Bank STAMP
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td width="25px;"></td>
                                                        <td width="25px;" style="border-left: 1px #c9c4c4 solid"></td>
                                                        <td>
                                                            @include('print.student-fee.includes.fee-voucher-common-content')
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <td style="font-size: 12px;font-weight: 600; text-align: center;">STUDENT COPY:</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        Bank STAMP
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
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
   @include('includes.scripts.print_script')
@endsection
