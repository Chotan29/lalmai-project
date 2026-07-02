@extends('web.website.layouts.master')

@section('css')
    @include('web.website.includes.css.search-box')
@endsection

@section('content')
<div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="inner-column">
                        <div class="sec-title light centered">
                            <h2>{{isset($homeSetting->event_title)?$homeSetting->event_title:'Event'}}</h2>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            {!! Form::open(['route' => 'web.events', 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @include('web.website.includes.search-form')
                            {!! Form::close() !!}
                            @if (isset($data['rows']) && $data['rows']->count() > 0)
                                <ul class="list-style-one">
                                    @foreach($data['rows'] as $row)
                                        <li>
                                            <a href="{{ 'events/'.$row->slug }}">{{ $row->title }}</a>
                                            <div class="post-info">
                                                <span>
                                                    <span>{{ Carbon\Carbon::parse($row->event_date)->format('d-M-Y') }}</span>
                                                </span> |
                                                <span><i class="zmdi zmdi-time"></i>{{ $row->event_time }}</span> |
                                                <span><i class="zmdi zmdi-pin"></i>{{ $row->event_place }}</span>
                                            </div>
                                        </li>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="single-event-item">
                                                <div class="single-event-image">
                                                    <a href="{{ 'events/'.$row->slug }}">

                                                    </a>
                                                </div>
                                            </div>
                                        </div>


                                    @endforeach
                                </ul>
                                <!-- news item -->
                                @include('web.website.includes.pagination')
                            @endif

                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>
                <!--Sidebar Side-->
                <div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    @include('web.website.blog.partial.recent-blog-widget')
                    @include('web.website.blog.partial.category-widget')
                </div>

            </div>
        </div>
    </div>

@endsection
