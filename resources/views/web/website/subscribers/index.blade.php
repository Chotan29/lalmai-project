@extends('web.website.layouts.master')

@section('content')

    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="inner-column">


                        <div class="row text">
                            <div class="content-column col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                <div class="inner-column">
                                    <div class="sec-title light centered">
                                        <h2>Subscriber Info</h2>
                                        <div class="separator">
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                        </div>
                                    </div>
                                    @include('includes.flash_messages')

                                    <div style="clear:both"><br></div>
                                </div>
                            </div>

                            <!--Sidebar Side-->
                            <div class="sidebar-side col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                @include('web.website.includes.widgets.subscriber-form')
                                @include('web.website.blog.partial.recent-blog-widget')
                                @include('web.website.blog.partial.category-widget')
                            </div>

                        </div>

                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>
                <!--Sidebar Side-->

            </div>
        </div>
    </div>

@endsection
