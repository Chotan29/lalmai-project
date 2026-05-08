@extends('web.website.layouts.master')
@section('css')
    <!-- Add icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            max-width: 300px;
            margin: auto;
            text-align: center;
        }

        .title {
            color: grey;
            font-size: 18px;
        }

        button {
            border: none;
            outline: 0;
            display: inline-block;
            padding: 8px;
            color: white;
            background-color: #2460B9;
            text-align: center;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
        }
        button:hover {
            background-color: #ff9a04;
        }

        a {
            text-decoration: none;
            font-size: 22px;
            color: black;
        }

        .social-icon-three a{
            padding: 10px;
        }

        button:hover, a:hover {
            opacity: 0.7;
        }
    </style>
@endsection
@section('content')

    @if (isset($data['staffs']) && $data['staffs']->count() > 0)
        <section class="services-section">
            <div class="auto-container">
                <div class="row clearfix">
                    <!--Content Side / Our Blog-->
                    <div class="content-side col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="services-single">
                            <div class="inner-box">
                                <div class="lower-content">
                                    <div class="text">
                                        <div class="sec-title light centered">
                                            <h2>{{isset($data['pageDetail']->title)?$data['pageDetail']->title:'Staffs'}}</h2>
                                            {{--<h2>{{isset($homeSetting->staff_title)?$homeSetting->staff_title:'Staffs'}}</h2>--}}
                                            {{--'title', 'slug', 'page_type', 'link', 'image', 'detail_desc', 'view_count','status'];--}}
                                            <div class="sub-title">{{isset($data['pageDetail']->short_desc)?$data['pageDetail']->short_desc:''}}</div>
                                            <div class="separator">
                                                <span class="dott"></span>
                                                <span class="dott"></span>
                                                <span class="dott"></span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            @foreach($data['staffs'] as $staff)
                                                <div class="team-member col-lg-4 col-md-4 col-sm-6 col-xs-12skew">
                                                    <div class="card hvr-float">
                                                        @if ($staff->profile_image)
                                                            <img src="{{ asset('web/staff/'.$staff->profile_image) }}"  alt="{{ $staff->name }}">
                                                        @else
                                                            <img style="width:100%" src="{{ asset('website/img/course/1.jpg') }}" alt="{{ $staff->name }}">
                                                        @endif
                                                        <h1>{{$staff->name}}</h1>
                                                        <p class="title">{{$staff->position}}</p>
                                                        <p><a href="tel:{{$staff->cell_1}}">{{$staff->cell_1}}</a> </p>
                                                            <div class="social-icon-three">
                                                        <a href="{{ isset($staff->twitter_url)?$staff->twitter_url:"#" }}"><i class="fa fa-twitter"></i></a>
                                                        <a href="{{ isset($staff->linkedIn_url)?$staff->linkedIn_url:"#" }}"><i class="fa fa-linkedin"></i></a>
                                                        <a href="{{ isset($staff->facebook_url)?$staff->facebook_url:"#" }}"><i class="fa fa-facebook"></i></a>
                                                        <a href="{{ isset($staff->whatsapp_url)?$staff->whatsapp_url:"#" }}"><i class="fa fa-whatsapp"></i></a>
                                                            </div>
                                                        <p>
                                                            <a href="{{ route('web.staffs.detail', ['id' => encrypt($staff->id)]) }}">
                                                                <button>Contact</button>
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

@endsection
@section('js')
@endsection







