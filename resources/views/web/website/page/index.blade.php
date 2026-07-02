@extends('web.website.layouts.master')

@section('content')
    <!--Breadcrumb Banner Area Start-->
    <div class="breadcrumb-banner-area" style="background-image:url('{{ asset('web/general_settings/'.$generalSetting->page_banner) }}')">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb-text">
                        <h1 class="text-center">Latest Blog</h1>
                        <div class="breadcrumb-bar">
                            <ul class="breadcrumb text-center">
                                <li><a href="{{ route('web.home') }}">Home</a></li>
                                <li>Latest Blog</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End of Breadcrumb Banner Area-->
    <!--Latest News Area Start-->
    <div class="latest-area section-padding latest-page">
        <div class="container">
            @if (isset($data['rows']) && $data['rows']->count() > 0)
                <div class="row">
                

                        @foreach($data['rows'] as $row)

                            @include('website.blog.partial.blog_li')

                        @endforeach

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pagination-content">
                            {!! $data['rows']->links() !!}
                        </div>
                    </div>
                </div>
            @else
                <p>No post found.</p>
            @endif
        </div>
    </div>
    <!--End of Latest News Area-->

@endsection
