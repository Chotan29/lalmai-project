@extends('web.admin.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
    @endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home"></i>
                    <a href="#">Home</a>
                </li>
                <li class="active">Dashboard</li>
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
            <div class="space-8"></div>
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    @include('includes.flash_messages')
                    @include($view_path.'.includes.indicator')
                    <div class="space-6"></div>
                    <div class="space-6"></div>
                    <div class="row">
                        <div class="col-sm-12">
                            @if(isset($data['datePageVisitorChart']))
                                @include($view_path.'.analytics.includes.dateviews')
                            @endif
                        </div>
                        <div class="col-sm-12">
                            <div class="widget-box transparent" id="recent-box">
                                <div class="widget-header">
                                    <h4 class="lighter smaller">
                                        <i class="icon-envelope orange"></i>
                                        Recent Contact Message
                                    </h4>

                                    <div class="widget-toolbar no-border">
                                        <ul class="nav nav-tabs" id="recent-tab">
                                            <li class="active">
                                                <a data-toggle="tab" href="#contact-tab">Contact Us</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-4">
                                        <div class="tab-content padding-8 overflow-visible">
                                            <div id="contact-tab" class="tab-pane active">
                                                <div class="comments">
                                                    @if(isset($data['contact_message']) && $data['contact_message']->count() > 0)
                                                        @foreach($data['contact_message'] as $contact_list)
                                                            <div class="itemdiv commentdiv">
                                                                <div class="user">
                                                                    <img alt="Bob Doe's Avatar" src="{{ asset('admin-panel/assets/avatars/user.jpg') }}" />
                                                                </div>

                                                                <div class="body">
                                                                    <div class="name">
                                                                        <a href="#">{{ $contact_list->name }}</a>
                                                                    </div>

                                                                    <div class="time">
                                                                        <i class="icon-time"></i>
                                                                        <span class="green">

                                                                    {{ $contact_list->created_at->diffForHumans() }}
                                                                    </span>
                                                                    </div>

                                                                    <div class="text">
                                                                        <i class="icon-quote-left"></i>
                                                                        {!! \Illuminate\Support\Str::words($contact_list->message, 15)  !!}

                                                                        @if($contact_list->view_status == 0 )
                                                                            <a href="{{ route($base_route.'.contact-us.view', ['id' => encrypt($contact_list->id)]) }}">
                                                                                <span class="label label-sm label-success">Read Message</span>
                                                                            </a>
                                                                        @else
                                                                            <a href="{{ route($base_route.'.contact-us.view', ['id' => encrypt($contact_list->id)]) }}">
                                                                                <span class="label label-sm label-warning">Alread Read</span>
                                                                            </a>
                                                                        @endif
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        {!! $data['contact_message']->links() !!}
                                                    @endif
                                                </div>

                                                <div class="hr hr-8"></div>

                                                <div class="center">
                                                    <i class="icon-comments-alt icon-2x green"></i>

                                                    &nbsp;
                                                    <a href="{{ route('web.admin.contact-us') }}">
                                                        See all Contact Message &nbsp;
                                                        <i class="icon-arrow-right"></i>
                                                    </a>
                                                </div>

                                                <div class="hr hr-double hr8"></div>
                                            </div>
                                        </div>
                                    </div><!-- /widget-main -->
                                </div><!-- /widget-body -->
                            </div><!-- /widget-box -->
                        </div><!-- /span -->
                        <div class="vspace-sm"></div>
                    </div>

                    <div class="space-6"></div>

                <!-- /row -->

                    <!-- PAGE CONTENT ENDS -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div><!-- /.main-content -->
@endsection
@section('js')
    <!-- page specific plugin scripts -->
    @if(isset($data['datePageVisitorChart']))
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" charset="utf-8"></script>
         {!! $data['datePageVisitorChart']->script() !!}
    @endif
@endsection


