@extends('layouts.master')

@section('css')
    <style>
        body {
            width: 100%;
            /*height: 100%;*/
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
            width: 101.6mm;
            /*height: 296mm;*/
            margin: 10mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .page-content{
            background-color: transparent !important;
        }
        .subpage {
            width: 90mm;
            /* height: 286mm;*/
            margin: 10px auto;
            padding: 10px;
            /*height: 257mm;*/
        }
        .receipt-copy{
            font-width: 600;
            font-size:22px;
        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            padding: 4px !important;
            font-width: 600;
            font-size:14px;
        }

        @page {
        /
        }
        @media print {
            html, body {
                width: 101.6mm;
                /*height: 297mm;*/
            }
            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                /*page-break-after: always;*/
            }
            .slip-title{
                margin-top:5px;
                text-align:center !important;
            }
            .slip-date{
                text-align:right !important;
            }
        }

    </style>
@endsection

@section('content')
    @if($data['student'] && $data['student']->count() > 0 )
        @foreach($data['student'] as $student)
            <div class="main-content" >
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                <div class="space-6"></div>
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="widget-box transparent">

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
                                                <div class="col-sm-12 col-md-12 col-print-12 align-center text-center">
                                                    <div class="widget-box transparent">
                                                        @include('print.student-fee.includes.print-header')

                                                        <div class="row" >

                                                            <div class="col-md-12 text-center">
                                                                {{--Anton|Archivo+Black|Josefin+Sans|Poppins|Russo+One|--}}
                                                                <h6 class="no-margin-top" style="font-size: 12px">
                                                                    {{isset($generalSetting->salogan)?$generalSetting->salogan:""}}
                                                                </h6>
                                                                <h2 class="no-margin-top text-uppercase" style="font-family: 'algerian'; font-size: 18px; font-weight: 600">
                                                                    <strong>{{isset($generalSetting->institute)?$generalSetting->institute:""}}</strong>
                                                                </h2>
                                                                <h5 class="no-margin-top" style="font-size: 12px;">
                                                                    {{isset($generalSetting->address)?$generalSetting->address:""}}, {{isset($generalSetting->phone)?$generalSetting->phone:""}}
                                                                </h5>
                                                                <h5 class="no-margin-top" style="font-size: 12px;">
                                                                    {{isset($generalSetting->email)?$generalSetting->email:""}}, {{isset($generalSetting->website)?$generalSetting->website:""}}
                                                                </h5>

                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">

                                                                <span class="float-right"></span>
                                                            </div>

                                                            <hr class="hr hr-2">
                                                            <table class="tab-content">
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td class="text-right">Date: {{ViewHelper::convertDateWithMonth(\Carbon\Carbon::parse(now())->format('Y-m-d'))}}{{--ViewHelper::convertAdToBs(\Carbon\Carbon::parse(now())->format('Y-m-d'))--}}</td>

                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3" class="text-center" style="font-size:18px;font-weight600;">INFORMATION SLIP</th>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-right" >Name</td>
                                                                    <td> : </td>
                                                                    <th class="text-left">{{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</th>
                                                                </tr>
                                                                <tr>

                                                                    <td class="text-right">Reg.No.</td>
                                                                    <td> : </td>
                                                                    <th class="text-left">{{ $student->reg_no }}</th>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6">
                                                                        <hr class="hr hr-2">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-right">Class/Sec</td>
                                                                    <td> : </td>
                                                                    <th class="text-left">{{ ViewHelper::getFacultyTitle($student->faculty) }}/{{ ViewHelper::getSemesterTitle($student->semester) }}</th>
                                                                </tr>

                                                            </table>
                                                            <div>
                                                                <table class="table table-striped table-bordered no-margin-bottom">
                                                                    <tr class="text-center">
                                                                        <th class="center"></th>
                                                                        <!--                                                                    <th></th>-->
                                                                        <th>Head</th>
                                                                        <th>Due Date</th>
                                                                        <?php /*
                                                                    <th>Amount</th>
                                                                    <th>Di</th>
                                                                    <th>Fi</th>
                                                                    <th>Paid</th>
                                                                    */ ?>
                                                                        <th>Due</th>
                                                                    </tr>

                                                                    @php($i=1)
                                                                    @foreach($student->master as $feeMaster)
                                                                        @if(isset($feeMaster->due) && $feeMaster->due >0)
                                                                            <tr>
                                                                                <td class="center">{{ $i }}</td>
                                                                                <?php /*
                                                                            <td>
                                                                                {{ ViewHelper::getSemesterById($feeMaster->semester) }}
                                                                            </td>
                                                                            */?>
                                                                                <td class="text-left">
                                                                                    {{ ViewHelper::getFeeHeadById($feeMaster->fee_head) }}
                                                                                </td>
                                                                                <td>
                                                                                    {{ \Carbon\Carbon::parse($feeMaster->fee_due_date)->format('Y-m-d') }}
                                                                                </td>
                                                                                <?php /*
                                                                            <td class="text-right">
                                                                                {{ $feeMaster->fee_amount?$feeMaster->fee_amount:'-' }}
                                                                            </td>
                                                                            <td class="text-right">
                                                                                {{ $feeMaster->discount?$feeMaster->discount:'-' }}
                                                                            </td>
                                                                            <td class="text-right">
                                                                                {{ $feeMaster->fine?$feeMaster->fine:'-' }}
                                                                            </td>
                                                                            <td class="text-right">
                                                                                {{ $feeMaster->paid_amount?$feeMaster->paid_amount:'-' }}
                                                                            </td>
                                                                            */?>
                                                                                <td class="text-right">
                                                                                    {{ $feeMaster->due?$feeMaster->due:'-'  }}
                                                                                </td>
                                                                            </tr>
                                                                            @php($i++)
                                                                        @endif
                                                                    @endforeach
                                                                </table>
                                                            </div>

                                                            <div class="hr hr8 hr-dotted"></div>


                                                            <div class="row text-uppercase">
                                                                <div class="col-sm-12">
                                                                    Total Amount :<strong>{{$student->balance}}/-</strong><br>
                                                                    Amount In Word:<strong> {{ ViewHelper::convertNumberToWord($student->balance) }}only.</strong><br>
                                                                    हरेक महिनाको १५ गते भित्र उक्त  महिनाको शुल्क (fee) बुझाउनुपर्नेछ। १५ गतेपछि जरिवाना लागने छ ।
                                                                </div>
                                                            </div>

                                                            <div class="hr hr8 hr-dotted"></div>
                                                            <div class="space-6"></div>
                                                            @include('print.student-fee.includes.print-footer')
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
                <div style="page-break-after:always;"></div>
            @endforeach
            @endif
            @endsection


            @section('js')
                <!-- inline scripts related to this page -->
            @include('includes.scripts.print_script')
@endsection