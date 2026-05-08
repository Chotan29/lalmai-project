@extends('web.website.layouts.master')

@section('title_and_meta')
    @include('web.website.includes.seo.categoryDetail')
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
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            @if (isset($data['category_post']) && $data['category_post']->count() > 0)
                                <ul class="list-style-one">
                                    @foreach($data['category_post'] as $row)
                                        <li>
                                            <a href="{{route('web.blog').'/'.$row->slug}}">{{ $row->title }}</a>
                                        </li>
                                @endforeach
                                <!-- news item -->
                                </ul>
                            @endif
                        </div>
                        <div style="clear:both"><br></div>
                    </div>
                </div>
                <!--Sidebar Side-->
                <div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    @include('web.website.blog.partial.category-widget')
                    @include('web.website.blog.partial.recent-blog-widget')
                </div>

            </div>
        </div>
    </div>

@endsection
