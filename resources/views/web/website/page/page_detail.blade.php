@extends('web.website.layouts.master')

@section('title_and_meta')
    @include('web.website.includes.seo.pageDetail')
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
                            <h2>{{isset($data['row']->title)?$data['row']->title:''}}</h2>
                            <span>{{isset($data['row']->short_desc)?$data['row']->short_desc:''}}</span>
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
                                        @if (isset($data['row']))
                                            @if ($data['row']->image !=='')
                                                <img src="{{ asset('web/page/'.$data['row']->image) }}" width="100%" alt="{{ $data['row']->title }}">
                                            @endif
                                            {!! $data['row']->detail_desc !!}
                                        @endif
                                    </p>
                                    @if (isset($generalSetting->post_detail_foot))
                                        {!! $generalSetting->post_detail_foot !!}
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>
                <!--Sidebar Side-->


                <div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    @include('web.website.includes.widgets.related-page')
                    @include('web.website.includes.widgets.other-page')
                 </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    @include('web.website.includes.scripts.floating-social-share')
@endsection
