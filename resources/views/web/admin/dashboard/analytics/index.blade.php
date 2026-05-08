@extends('web.admin.layouts.master')
@section('css')
    <link href="{{ asset('admin-panel/assets/css/dashboard.css') }}" rel="stylesheet" />
    @endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
            <script type="text/javascript">
                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
            </script>

            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home"></i>
                    <a href="#">Home</a>
                </li>
                <li class="active">Analytics</li>
            </ul><!-- .breadcrumb -->

            <!-- div class="nav-search" id="nav-search">
                <form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="icon-search nav-search-icon"></i>
								</span>
                </form>
            </div> --><!-- #nav-search -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Google Analytics
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        overview
                    </small>
                </h1>
            </div><!-- /.page-header -->


            <div class="row">
                    <!-- PAGE CONTENT BEGINS -->
                <div class="col-xs-12">
                    {!! Form::open(['route' => 'web.admin.analytics', 'method' => 'GET', 'class' => 'form-horizontal',
                                           'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        <div class="form-group">
                            {!! Form::label('range', 'Date Range', ['class' => 'col-sm-2 control-label']) !!}
                            <div class=" col-sm-3">
                                <div class="input-group ">
                                    {!! Form::text('start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                    <span class="input-group-addon">
                                        <i class="fa fa-exchange"></i>
                                    </span>
                                    {!! Form::text('end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                </div>
                            </div>

                            {!! Form::label('period', 'Period', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-2">
                                {{--{!! Form::number('max_result', 10, ["placeholder" => "", "class" => "form-control border-form"]) !!}--}}
                                {!! Form::select('period', ['day'=>'Days','month'=>'Months','year'=>'Year'], null, ['class' => 'form-control']) !!}
                            </div>

                            <div class="col-sm-2">
                                <button class="btn-primary btn-sm" type="submit" value="filter">
                                    <i class="fa fa-filter bigger-110"></i>
                                    Filter
                                </button>
                            </div>
                        </div>

                    {!! Form::close() !!}
                </div>

                @if(isset($data['datePageVisitorChart']))
                    <div class="col-xs-12">
                        @include('admin.dashboard.analytics.includes.dateviews')
                        <div class="hr hr-32 hr-double"></div>
                    </div>
                @endif
                @if(isset($data['userTypeCompare']))
                    <div class="col-xs-6">
                        @include('admin.dashboard.analytics.includes.usertypecompare')
                        <div class="hr hr-32 hr-double"></div>
                    </div>
                @endif
                @if(isset($data['datePageVisitorChart']))
                    <div class="col-xs-6">
                        @include('admin.dashboard.analytics.includes.browsercompare')
                        <div class="hr hr-32 hr-double"></div>
                    </div>
                @endif
                @if(isset($data['page_views']))
                    <div class="col-xs-12">
                        @include('admin.dashboard.analytics.includes.pageviews')
                        <div class="hr hr-32 hr-double"></div>
                    </div>
                @endif
                @if(isset($data['top_referrals']))
                    <div class="col-xs-12">
                        @include('admin.dashboard.analytics.includes.topreferals')
                        <div class="hr hr-32 hr-double"></div>
                    </div>
                @endif
                @if(isset($data['visitors_page']))
                    <div class="col-xs-12">
                        @include('admin.dashboard.analytics.includes.visitorpageviews')
                        <div class="hr hr-32 hr-double"></div>
                    </div>
                @endif

                <!-- PAGE CONTENT ENDS -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div><!-- /.main-content -->
@endsection

@section('js')
<!-- page specific plugin scripts -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" charset="utf-8"></script>
@if(isset($data['datePageVisitorChart']))
    {!! $data['datePageVisitorChart']->script() !!}
@endif
@if(isset($data['userTypeCompare']))
    {!! $data['userTypeCompare']->script() !!}
@endif
@if(isset($data['browserCompare']))
    {!! $data['browserCompare']->script() !!}
 @endif

@include('includes.scripts.inputMask_script')
@include('includes.scripts.datepicker_script')
 @endsection
