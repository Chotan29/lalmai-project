@extends('layouts.master')

@section('css')
    @include('print.certificate.common.css')
    <style>
        .tab-content {
            border: none;
        }

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
            width: 210mm;
            height: 296mm;
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
            width: 200mm;
            height: 286mm;
            margin: 10px auto;
            padding: 10px;
            /*height: 257mm;*/
        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {

        }
        .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
            border: 1px solid #ddd0;
            font-size: 12px;
            line-height: 1.1;
            padding: 2px !important;
        }

        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
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
        }

    </style>
@endsection

@section('content')
    @include('print.student-fee.includes.print-header')

    @if($data['student'] && $data['student']->count() > 0 )
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
                                                        @include('print.student-fee.includes.institution-detail')
                                                        <div class="row align-center">
{{--                                                            <h4 class="receipt-copy"> DUE DETAIL SLIP </h4>--}}
                                                            <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Black Ops One', cursive;font-size: 22px">
                                                                <strong>DUE REMINDER SLIP </strong>
                                                            </h3>
                                                        </div>
                                                        <hr class="hr hr-2">
                                                        @include('print.student-fee.includes.studentinfo-due')
                                                        <div>
                                                            <table class="table table-striped table-bordered no-margin-bottom">
                                                                <tr style="border-top: 3px #346da5 solid;border-bottom: 1px #346da5 solid !important;">
                                                                    <th class="center">SN</th>
                                                                    <th>SEM/SEC</th>
                                                                    <th>Head</th>
                                                                    <th>Due Date</th>
                                                                    <th>Amount</th>
                                                                    <th>Di</th>
                                                                    <th>Fi</th>
                                                                    <th>Paid</th>
                                                                    <th>Due</th>
                                                                </tr>

                                                                @php($i=1)
                                                                @foreach($student->master as $feeMaster)
                                                                    @if(isset($feeMaster->due) && $feeMaster->due >0)
                                                                        <tr>
                                                                            <td class="center">{{ $i }}</td>
                                                                            <td>
                                                                                {{ ViewHelper::getSemesterById($feeMaster->semester) }}
                                                                            </td>
                                                                            <td>
                                                                                {{ ViewHelper::getFeeHeadById($feeMaster->fee_head) }}
                                                                            </td>
                                                                            <td>
                                                                                {{ \Carbon\Carbon::parse($feeMaster->fee_due_date)->format('Y-m-d') }}
                                                                            </td>
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
                                                                            <td class="text-right">
                                                                                {{ $feeMaster->due?$feeMaster->due:'-'  }}
                                                                            </td>
                                                                        </tr>
                                                                        @php($i++)
                                                                    @endif
                                                                @endforeach
                                                                <tfoot>
                                                                <tr class="text-uppercase" style="border-top: 2px #346da5 solid;border-bottom: 3px #346da5 solid;font-weight: 600;">
                                                                    <td colspan="5" class="text-left ">
                                                                        Balance In Word:<strong> {{ ViewHelper::convertNumberToWord($student->balance) }}only.</strong>

                                                                    </td>
                                                                    <td colspan="4">
                                                                        Total Balance :<strong>{{$student->balance}}/-</strong>

                                                                    </td>

                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>

                                                        <div class="hr hr8 hr-dotted"></div>


                                                        <div class="row text-uppercase">
                                                            <div class="col-sm-4 pull-right align-right">

                                                            </div>
                                                            <div class="col-sm-8 pull-left align-left">

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
        @endforeach
    @endif
@endsection


@section('js')
    <!-- inline scripts related to this page -->
   @include('includes.scripts.print_script')
@endsection