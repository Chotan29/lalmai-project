@extends('web.website.layouts.master')

@section('title_and_meta')
    @include('web.website.includes.seo.galleryDetail')
@endsection
@section('css')
    @include('web.website.includes.css.floating-social-share')
    <style>
        .row > .column {
            padding: 8px 8px;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Create four equal columns that floats next to eachother */
        .column {
            float: left;
            width: 25%;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: black;
        }

        /* Modal Content */
        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            width: 90%;
            max-width: 1200px;
        }

        /* The Close Button */
        .close {
            color: white;
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 35px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #999;
            text-decoration: none;
            cursor: pointer;
        }

        /* Hide the slides by default */
        .mySlides {
            display: none;
        }

        /* Next & previous buttons */
        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            -webkit-user-select: none;
        }

        /* Position the "next button" to the right */
        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        /* On hover, add a black background color with a little bit see-through */
        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        /* Number text (1/3 etc) */
        .numbertext {
            color: #f2f2f2;
            font-size: 12px;
            padding: 8px 12px;
            position: absolute;
            top: 0;
        }

        /* Caption text */
        .caption-container {
            text-align: center;
            background-color: black;
            padding: 2px 16px;
            color: white;
        }

        .caption-container p{
            background: #2460B9;
            color: #ffffff !important;
        }

        img.demo {
            opacity: 0.6;
        }

        .active,
        .demo:hover {
            opacity: 1;
        }

        img.hover-shadow {
            transition: 0.3s;
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        a:not([href]):not([tabindex]) {
            color: white;
            text-decoration: none;
            background: #2460B9;
        }
        .sidebar-page-container .content-column .inner-column .text p {
            font-size: 16px;
            line-height: 1.8em;
            margin-bottom: 18px;
        }

        .close:not(:disabled):not(.disabled) {
            cursor: pointer;
            color: white;
            background: #2460b9;
            padding: 26px;
        }
    </style>
@endsection

@section('content')

    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
                            <!--Gallery Area Start-->
                            <div class="gallery-area pt-110 pb-130">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="course-details-content">
                                                <div class="single-course-details">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="overlay-effect">
                                                                <a href="#">
                                                                    @if ($data['row']-> image_name)
                                                                        <img src="{{ asset('web/gallery/'.$data['row']-> image_name) }}" alt="{{ $data['row']->title }}">
                                                                    @else
                                                                        <img alt="" src="{{ asset('website/img/details/1.jpg') }}">
                                                                    @endif
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="single-item-text">
                                                                {{--<h3>{{ $data['row']->title }}</h3>--}}
                                                                {{--<div class="single-item-text-info">
                                                                   <!--  <span>By: <span>Salim Rana</span></span> -->
                                                                    <span>Date: <span>{{ Carbon\Carbon::parse($data['row']->event_date)->format('d M Y') }}</span>|
                                                                    <span><i class="zmdi zmdi-eye"></i>&nbsp;{{ $data['row']->view_count }}  </span>
                                                                </div>--}}
                                                                <div class="course-text-content">
                                                                    <p>
                                                                        {!! $data['row']->description  !!}
                                                                    </p>
                                                                </div>
                                                                @if (isset($generalSetting->post_detail_foot))
                                                                    {!! $generalSetting->post_detail_foot !!}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if (isset($data['images']) && $data['images']->count() > 0)
                                            <div class="gallery" id="gallery">
                                                <!-- Grid column -->
                                                    <!-- Images used to open the lightbox -->
                                                    <div class="row">
                                                        @foreach($data['images'] as $key => $image)
                                                            <div class="column col-4">
                                                                <img src="{{ asset('web/gallery/'.$image->image) }}" onclick="openModal();currentSlide({{$key+1}})" class="hover-shadow">
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <!-- The Modal/Lightbox -->
                                                    <div id="myModal" class="modal">
                                                        {{--<span class="close cursor" onclick="closeModal()">&times;</span>--}}
                                                        <div class="modal-content">
                                                            @foreach($data['images'] as $key => $image)
                                                                <div class="mySlides">
                                                                    <div class="numbertext"> {{$key+1}} / {{$data['images']->count()}}</div>
                                                                    <img src="{{ asset('web/gallery/'.$image->image) }}" style="width:100%">
                                                                </div>
                                                            @endforeach

                                                            <!-- Next/previous controls -->
                                                            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                                                            <a class="next" onclick="plusSlides(1)">&#10095;</a>

                                                            <!-- Caption text -->
                                                            <div class="caption-container">
                                                                <p id="caption"></p>
                                                                <span class="close cursor" onclick="closeModal()">&times;</span>
                                                            </div>
                                                            <div class="row">

                                                                <!-- Thumbnail image controls -->
                                                                @foreach($data['images'] as $key => $image)
                                                                    <div class="column">
                                                                        <img class="demo" src="{{ asset('web/gallery/'.$image->image) }}" onclick="currentSlide({{$key+1}})" alt="{{$image->alt_text}}">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!--End of Gallery Area-->
                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('js')
    <script>

        $(document).ready(function () {
            $('.sticky-header').hidden;
        });

        // Open the Modal
        function openModal() {
            document.getElementById("myModal").style.display = "block";
        }

        // Close the Modal
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        var slideIndex = 1;
        showSlides(slideIndex);

        // Next/previous controls
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        // Thumbnail image controls
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            var dots = document.getElementsByClassName("demo");
            var captionText = document.getElementById("caption");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
            captionText.innerHTML = dots[slideIndex-1].alt;
        }
    </script>

    @include('web.website.includes.scripts.floating-social-share')
@endsection
