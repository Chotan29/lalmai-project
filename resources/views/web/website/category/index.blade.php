@extends('web.website.layouts.master')

@section('content')
    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="inner-column">
                        <div class="sec-title light centered">
                            <h2>Category</h2>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            @if (isset($data['rows']) && $data['rows']->count() > 0)
                                <ul class="list-style-one">
                                    @foreach($data['rows'] as $row)
                                        <li>
                                            {{--<a href="{{ 'notice/'.$row->slug }}">{{ $row->title }}</a>--}}
                                            <a href="{{route('web.category.detail', ['slug' => $row->slug])}}">{{ $row->title }}</a>
                                            {{--<a href="{{route('web.category.detail').'?cat='.$row->slug}}">{{ $row->title }}</a>--}}
                                            {{--<div class="post-info">{{ \Carbon\Carbon::parse($row->publish_date )->format('d-M-Y')}}</div>--}}
                                        </li>
                                @endforeach
                                <!-- news item -->
                                </ul>
                                @include('web.website.includes.pagination')
                            @else
                                <p>No post found.</p>
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
