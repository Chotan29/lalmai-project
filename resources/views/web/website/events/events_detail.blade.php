@extends('web.website.layouts.master')

@section('title_and_meta')
    @include('web.website.includes.seo.eventsDetail')
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
                            <span><i class="fa fa-calendar"></i> {{ Carbon\Carbon::parse($data['row']->event_date)->format('d M Y') }}</span>
                            <span><i class="fa fa-clock-o"></i> {{ $data['row']->event_time }}</span>
                            <span><i class="fa fa-map-marker"></i> {{ $data['row']->event_place }}</span>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            @if ($data['row']->image)
                                <img src="{{ asset('web/events/'.$data['row']->image) }}" width="100%" alt="{{ $data['row']->title }}">
                            @endif
                            <p>
                                {!! $data['row']->description !!}
                            </p>
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
                                <h2>Latest {{isset($data['home_setting']->event_title)?$data['home_setting']->event_title:'Notice'}}</h2>
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
                                                <a href="{{ route('web.notice').'/'.$row->slug }}">{{ $row->title }}</a>
                                            </li>
                                        @endforeach
                                        <a class="pull-right" href="{{route('web.notice')}}">More {{isset($data['home_setting']->event_title)?$data['home_setting']->event_title:'Notice'}}...</a>
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
