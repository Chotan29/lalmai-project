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

                <div class="page-header hidden-print">
                    <h1>
                        Student
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="space-2"></div>

                        <div id="user-profile-2" class="user-profile">
                            <div class="tabbable tabs-left">
                                <ul class="nav nav-tabs  padding-18 hidden-print ">
                                    <li class="active">
                                        <a data-toggle="tab" href="#profile">
                                            <i class="green ace-icon fa fa-user bigger-140"></i>
                                            Profile
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content no-border padding-24">
                                    <div id="profile" class="tab-pane in active">
                                        <div class="row text-center">
                                            @include('print.includes.institution-detail')
                                            <h2 class="no-margin-top text-uppercase" style="font-family: 'Bowlby+One+SC'; font-size: 30px; font-weight: 600">
                                                <strong>REGISTRATION DETAIL</strong>
                                            </h2>
                                        </div>
                                        <hr class="hr-double hr-8">
{{--                                       @include('student.generate-pdf.includes.profile')--}}
                                    </div><!-- /#home -->
                                </div>
                            </div>

                        </div>
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    </div>
@endsection

@section('js')

@endsection