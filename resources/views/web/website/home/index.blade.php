@extends('web.website.layouts.master')

@section('title_and_meta')
    @include('web.website.includes.seo.home')
@endsection

@section('css')
    @include('web.website.includes.css.slider')
@endsection


@section('content')
    @include($view_path.'.partial.slider')


    @include($view_path.'.partial.welcome')

    <!--Notice/Blog/Events Area Start-->
    @include($view_path.'.partial.notice-blog-event')
    <!--End of Notice Area-->
    <!--About Area -->
    @include($view_path.'.partial.aboutArea')
    <!--About Area End-->



    <!--Slider Section-->
    {{--@include($view_path.'.partial.slider')--}}
    <!--End of Slider Area-->

    <!--product/Services Area -->
    @include($view_path.'.partial.services')
    <!--About Area End-->

    <!--Staff Area-->
    @include($view_path.'.partial.staffs')
    <!--Staff Area End-->
    <!--Counter Area Start-->
    @include($view_path.'.partial.counter')
    <!--End of Counter Area-->
    <!--Testimonial Start-->
    @include($view_path.'.partial.testimonial')
    <!--Testimonial Area End -->
    <!--Client Start-->
    @include($view_path.'.partial.client')
    <!--Client Area End -->
@endsection

@section('js')
    @include('web.website.includes.scripts.slider')
@endsection
