{{-- The Receive History as a report on paper.

     Its own view rather than print rules bolted onto the screen: the list at /account/fees is
     used all day and is paged, and a printed sheet that stopped after one page would quietly
     leave money out. This one is handed every row the filter matched.

     It wears the same clothes as the reports under Account > Reports - report-print-page,
     report-letterhead, report-sheet - so the college's paperwork looks like one college's
     paperwork. See assets/css/paper.css. --}}
@extends('layouts.master')

@section('css')
    @include('print.includes.print-layout')
@endsection

@section('content')
    {{-- landscape: thirteen columns will not sit on an upright A4. paper.css already carries a
         landscape page box for the balance statement; this asks for the same one by name. --}}
    <div class="main-content report-print-page landscape">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header hidden-print">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Print
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row hidden-print">
                    <div class="col-xs-12">
                        @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                    </div>
                </div>

                <div class="col-sm-12 align-right hidden-print">
                    <a href="{{ route($base_route) }}?{{ http_build_query(request()->except('print')) }}" class="btn btn-default btn-lg">
                        <i class="ace-icon fa fa-arrow-left"></i> Back to the list
                    </a>
                    <a href="#" class="btn-primary btn-lg" onclick="window.print(); return false;">
                        <i class="ace-icon fa fa-print"></i> Print
                    </a>
                </div>
                <div class="space-32 hidden-print"></div>

                <div class="report-letterhead">
                    @include('print.includes.institution-detail')
                </div>

                <div class="report-sheet">
                    <h2>Fees Receive History</h2>

                    {{-- What this sheet covers, said in words. A page of figures that does not
                         name its own filter is a page somebody has to take on trust. --}}
                    <div class="report-meta text-center">
                        <span class="report-meta-item">
                            <span class="report-meta-l">Covering</span>
                            <span class="report-meta-v">{{ $data['filter_summary'] }}</span>
                        </span>
                        <span class="report-meta-item">
                            <span class="report-meta-l">Receipts</span>
                            <span class="report-meta-v">{{ number_format($data['totals']['row_count']) }}</span>
                        </span>
                        <span class="report-meta-item">
                            <span class="report-meta-l">Printed</span>
                            <span class="report-meta-v">{{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</span>
                        </span>
                    </div>

                    @include($view_path.'.includes.print-table')

                    <div class="report-sign">
                        <div class="report-sign-box">Prepared By</div>
                        <div class="report-sign-box">Accounts Officer</div>
                        <div class="report-sign-box">Principal</div>
                    </div>

                    <div class="report-print-foot">
                        <span>{{ isset($generalSetting->institute) ? $generalSetting->institute : '' }} &mdash; Fees Receive History</span>
                        <span>Printed by {{ auth()->check() ? auth()->user()->name : '' }} on {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection
