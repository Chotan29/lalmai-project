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
                            <h2>{{isset($homeSetting->download_title)?$homeSetting->download_title:'Download'}}</h2>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            {!! Form::open(['route' => 'web.download', 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @include('web.website.includes.search-form')
                            {!! Form::close() !!}
                            @if (isset($data['rows']) && $data['rows']->count() > 0)
                                <ul class="list-style-one">
                                    @foreach($data['rows'] as $row)
                                        <li>
                                            <a href="{{ route('web.download').'/'.$row->file }}" target="_blank">{{ $row->title }}</a>
                                            {{--<div class="post-info">{!! $row->description !!}</div>--}}
                                        </li>
                                    @endforeach
                                </ul>
                                <!-- news item -->
                                @include('web.website.includes.pagination')
                            @else

                            @endif

                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>
                <!--Sidebar Side-->
                <div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    @include('web.website.blog.partial.recent-blog-widget')
                </div>

            </div>
        </div>
    </div>

@endsection
