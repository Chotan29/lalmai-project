@extends('web.website.layouts.master')

@section('title_and_meta')
    @include('web.website.includes.seo.noticeDetail')
@endsection

@section('css')
    @include('web.website.includes.css.floating-social-share')
@endsection

@section('content')
    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">

                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="inner-column">
                        <div class="sec-title light centered">
                            <h2>{{ $data['row']->title }}</h2>
                            <a href="#">
                                <span><i class="fa fa-calendar"></i> {{ Carbon\Carbon::parse($data['row']->publish_date)->format('d M Y') }}</span>
                            </a>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            @if ($data['row']->image)
                                <img src="{{ asset('web/notice/'.$data['row']->image) }}" width="100%" alt="{{ $data['row']->title }}">
                            @endif
                            <p class="text">
                                {!! $data['row']->description !!}
                            </p>
                            @if(isset($data['row']->file))
                                <div class="btns-box">
                                    <a href="{{ asset('web/notice/'.$data['row']->file)}}" target="_blank" class="theme-btn btn-style-four">
                                        Download Attachment
                                    </a>
                                </div>
                            @endif
                            @if (isset($generalSetting->post_detail_foot))
                                {!! $generalSetting->post_detail_foot !!}
                            @endif

                        </div>
                        <div style="clear:both"><br></div>
                    </div>
                </div>

                <!--Sidebar Side-->
                <div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <aside class="sidebar right-sidebar">
                        <!--Category Blog-->
                        <div class="sidebar-widget categories-blog">
                            <div class="sidebar-title">
                                <h2>Latest {{isset($data['home_setting']->notice_title)?$data['home_setting']->notice_title:'Notice'}}</h2>
                                <div class="separator">
                                    <span class="dott"></span>
                                    <span class="dott"></span>
                                    <span class="dott"></span>
                                </div>
                            </div>
                            <div class="inner-box">
                                <ul class="list">
                                    @if (isset($data['recent']) && $data['recent']->count() > 0)
                                        @foreach($data['recent'] as $row)
                                    <li>
                                        <a href="{{ route('notice').'/'.$row->slug }}">{{ $row->title }}</a>
                                    </li>
                                        @endforeach
                                            <a class="pull-right" href="{{route('notice')}}">More {{isset($data['home_setting']->notice_title)?$data['home_setting']->notice_title:'Notice'}}...</a>
                                    @else
                                        <li>
                                            <a href="#">....</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                    </aside>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('web.website.includes.scripts.floating-social-share')
@endsection
