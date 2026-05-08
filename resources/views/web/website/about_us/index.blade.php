@extends('web.website.layouts.master')

@section('content')
    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="inner-column">
                        <div class="sec-title light centered">
                            {{--<h2>{{isset($data['aboutUs_setting']->title)?$data['aboutUs_setting']->title:''}}</h2>--}}
                            <h2>{{isset($data['aboutUs_setting']->title)?$data['aboutUs_setting']->title:''}}</h2>
                            <div class="sub-title">{{isset($data['aboutUs_setting']->slogan)?$data['aboutUs_setting']->slogan:''}}</div>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="row text">
                            <div class="col-lg-12 col-md-12">
                                <div class="about-text-container">
                                    <p>
                                        @if ($data['aboutUs_setting']->image)
                                            <img class="float-right" src="{{ asset('web/about_us_settings/'.$data['aboutUs_setting']->image) }}" width="50%" alt="{{ $data['aboutUs_setting']->title }}">
                                        @else
                                        @endif

                                        {!! $data['aboutUs_setting']->description !!}

                                       {{-- @include('web.website.includes.widgets.related-page')--}}
                                    </p>
                                </div>

                            </div>

                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>
                <!--Sidebar Side-->
               {{-- <div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    @include('web.website.blog.partial.category-widget')
                </div>--}}
            </div>
        </div>
    </div>

    @include('web.website.home.partial.counter')

    @include('web.website.home.partial.staffs')

@endsection