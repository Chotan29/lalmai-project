@extends('web.website.layouts.master')

@section('title_and_meta')
    @include('web.website.includes.seo.blogDetail')
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
                                <img src="{{ asset('web/blog/'.$data['row']->image) }}" width="100%" alt="{{ $data['row']->title }}">
                            @endif
                            <p class="text">
                                {!! $data['row']->detail_desc !!}
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
                    @include('web.website.blog.partial.category-widget')
                    @include('web.website.blog.partial.recent-blog-widget')
                    @include('web.website.includes.widgets.subscriber-form')
                </div>

            </div>
        </div>
    </div>

@endsection

@section('js')
    @include('web.website.includes.scripts.floating-social-share')
@endsection
