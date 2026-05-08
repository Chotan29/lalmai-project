@extends('web.website.layouts.master')

@section('css')
    @include('web.website.includes.css.search-box')
@endsection

@section('content')
    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="inner-column">
                        <div class="sec-title light centered">
                            <h2>404 : Page Not Found</h2>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            <h3 class="lighter smaller">We looked everywhere but we couldn't find it!</h3>

                            <div>
                                <div class="space"></div>
                                <h4 class="smaller">Try one of the following:</h4>

                                <ul class="list-unstyled spaced inline bigger-110 margin-15">
                                    <li>
                                        <i class="ace-icon fa fa-hand-o-right blue"></i>
                                        Re-check the url for type
                                    </li>

                                    <li>
                                        <i class="ace-icon fa fa-hand-o-right blue"></i>
                                        Read the faq
                                    </li>

                                    <li>
                                        <i class="ace-icon fa fa-hand-o-right blue"></i>
                                        Tell us about it
                                    </li>

                                    <li>
                                        <i class="ace-icon fa fa-hand-o-right blue"></i>
                                        Please, search what you want to looking for .
                                    </li>
                                </ul>
                            </div>
                            @if(isset($data['url']))
                                {!! Form::open(['url' => $data['url'], 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @else
                                {!! Form::open(['url' => url()->current(), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @endif
                                @include('web.website.includes.search-form')
                                {!! Form::close() !!}
                                <hr />

                            @include('web.website.home.partial.notice')

                            <div class="space"></div>

                            <div class="center">
                                <a href="javascript:history.back()" class="btn btn-grey">
                                    <i class="ace-icon fa fa-arrow-left"></i>
                                    Go Back
                                </a>

                                <a href="{{ url('/web') }}" class="btn btn-primary">
                                    <i class="ace-icon fa fa-tachometer"></i>
                                    Home
                                </a>
                            </div>

                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>
                <!--Sidebar Side-->
                {{--<div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    @include('website.blog.partial.category-widget')
                </div>--}}

            </div>
        </div>
    </div>
@endsection

