@extends('web.website.layouts.master')

@section('css')
    @include('web.website.includes.css.search-box')
    <style>
        img.float-right, img.float-left {
            width: 286px;
            margin: 15px;
        }
        a.index-title {
            font-size: 24px;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="inner-column">
                        <div class="sec-title light centered">
                            <h2>{{isset($homeSetting->blog_title)?$homeSetting->blog_title:'Blog'}}</h2>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            {!! Form::open(['route' => 'web.blog', 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @include('web.website.includes.search-form')
                            {!! Form::close() !!}
                            @if (isset($data['rows']) && $data['rows']->count() > 0)
                                <ul class="list-style-one">
                                    @foreach($data['rows'] as $row)
                                        <li>
                                            <a href="{{ 'blog/'.$row->slug }}" class="index-title">{{ $row->title }}</a>
                                            <div class="post-info">
                                                {{ \Carbon\Carbon::parse($row->publish_date )->format('d-M-Y')}} | {{$row->category_title}}
                                                {{-- <a href="{{ 'blog/'.$row->slug }}">
                                                    <img class="float-right" src="{{ asset(isset($data['row']->image)?asset('web/blog/'.$data['row']->image):'images/general_settings/'. $generalSetting->page_banner) }}" alt="{{$row->title}}">
                                                </a>--}}
                                                <p>
                                                    {{ substr((isset($row->seo_description)?$row->seo_description:strip_tags(htmlspecialchars_decode($row->detail_desc))),0,200).'...' }}
                                                    <a href="blog/{{isset($row->slug)?$row->slug:'#'}}" >Read Detail</a>
                                                </p>

                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                               @include('web.website.includes.pagination')

                            @endif

                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>
                <!--Sidebar Side-->
                <div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    @include('web.website.blog.partial.category-widget')
                    @include('web.website.includes.widgets.related-page')
                </div>

            </div>
        </div>
    </div>
@endsection
