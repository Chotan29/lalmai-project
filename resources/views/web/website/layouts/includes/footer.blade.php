<?php
/*<div class="footer-widget-area">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="single-footer-widget">
                    <div class="footer-logo">
                        <a href="{{ route('web.home') }}">
@if (isset($generalSetting->footer_logo))
    <img src="{{ asset('web/general_settings/'. $generalSetting->footer_logo) }}" alt="{{ $generalSetting->site_title }}" >
    @endif
    </a>
    </div>
    <p>{!! isset($generalSetting->site_desc)?$generalSetting->site_desc:"" !!} </p>
    <div class="social-icons">
        <a href="{{ isset($contact_info->facebook_link)?$contact_info->facebook_link:"#" }}"><i class="zmdi zmdi-facebook"></i></a>
        <a href="{{ isset($contact_info->googlePlus_link)?$contact_info->googlePlus_link:"#" }}"><i class="zmdi zmdi-google-plus"></i></a>
        <a href="{{ isset($contact_info->twitter_link)?$contact_info->twitter_link:"#" }}"><i class="zmdi zmdi-twitter"></i></a>
        <a href="{{ isset($contact_info->pinterest_link)?$contact_info->pinterest_link:"#" }}"><i class="zmdi zmdi-pinterest"></i></a>
        <a href="{{ isset($contact_info->instagram_link)?$contact_info->instagram_link:"#" }}"><i class="zmdi zmdi-instagram"></i></a>
    </div>
    </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="single-footer-widget">
            <h3>GET IN TOUCH</h3>
            <span><i class="fa fa-phone"></i>{{ isset($contact_info->phone)?$contact_info->phone:"" }}</span>
            <span><i class="fa fa-envelope"></i>{{ isset($contact_info->email)?$contact_info->email:"" }}</span>
            <span><i class="fa fa-globe"></i><a href="{{isset($contact_info->webURL)?$contact_info->webURL:""}}" target="_blank" style="color:white">{{ isset($contact_info->webURL)?$contact_info->webURL:"" }}</a></span>
            <span><i class="fa fa-map-marker"></i>{{ isset($contact_info->address)?$contact_info->address:"" }}</span>
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="single-footer-widget">
            <h3>Useful Links</h3>
            <ul class="footer-list">
                @if (isset($useful_links) && $useful_links->count() > 0)
                    @foreach($useful_links as $usefulLinks)
                        <li>
                            <a href="{{  $usefulLinks->page_type == "link-page" ? "$usefulLinks->link" : ($usefulLinks->page_type == "predefine-link" ? route('web.home').'/'.$usefulLinks->link : 'page/'.$usefulLinks->slug) }}">
                                {{ $usefulLinks->title  }}
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
    </div>
    </div>
    </div>
    <!--End of Footer Widget Area-->

    <!--Footer Area Start-->
    <footer class="footer-area">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-7">
                <span>
                    @if(isset($generalSetting->copyright))
                        {{$generalSetting->copyright}}
                    @else
                        Copyright &copy; <a href="http://businesswithtechnology.com">Business with Technology Pvt. Ltd.</a>
                    @endif
                </span>
                </div>
                <div class="col-md-6 col-sm-5">
                    <div class="column-right">
                    <span>
                        @if (isset($footer) && $footer->count() > 0)
                            @foreach($footer as $footer)
                                &nbsp;|&nbsp;<a href="{{  $footer->page_type == "link-page" ? "$footer->link" : ($footer->page_type == "predefine-link" ? route('web.home').'/'.$footer->link : 'page/'.$footer->slug) }}">
                                    {{ $footer->title  }}
                                </a>
                            @endforeach
                        @endif

                    </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>*/
?>
@if (isset($homeSetting) && $homeSetting->email_call_to_action_status == 1)
    <section class="call-to-action">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="column col-md-8 col-sm-12 col-xs-12">
                    <div class="text">{{$homeSetting->email_call_to_action_title}}</div>
                </div>
                <div class="btn-column col-md-4 col-sm-12 col-xs-12">
                    <a href="{{$homeSetting->email_call_to_action_link}}" target="_blank" class="theme-btn btn-style-six">{{$homeSetting->email_call_to_action_button_text}}</a>
                </div>

            </div>
        </div>
    </section>
@else
    <section class="call-to-action">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="column col-md-12 col-sm-12 col-xs-12">
                    {!! Form::open(['route' => 'web.subscribers.store', 'method' => 'POST', 'class' => 'form-inline',
                                'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                    <input type="text" name="name" class="form-control mb-2 mr-sm-2 col-5" id="inlineFormInputName2" placeholder="Your Name" required>
                    <input type="email" name="email" class="form-control mb-2 mr-sm-2 col-5" id="inlineFormInputName2" placeholder="youremail@domain.com" required>
                    <button type="submit" class="btn btn-warning" >Subscribe</button>
                    {!! Form::close() !!}
                </div>

            </div>
        </div>
    </section>
@endif
@if (isset($generalSetting) && $generalSetting->footer_status == 1)
    <!--Main Footer-->
    <footer class="main-footer">
        <!--Widgets Section-->
        <div class="widgets-section">
            <div class="auto-container">
                <div class="row clearfix">
                    <!--Big Column-->
                    <div class="row clearfix">
                        <!--Footer Column-->
                        <div class="footer-column col-md-5 col-sm-5 col-xs-12">
                            <div class="footer-widget newsletter-widget">
                                <div class="footer-title">
                                    <h2>Contact Us</h2>
                                    <div class="separator">
                                        <span class="dott"></span>
                                        <span class="dott"></span>
                                        <span class="dott"></span>
                                    </div>
                                </div>
                                <div class="widget-content">
                                    <ul>
                                        <li style="font-size: 21px">
                                            {{-- <span class="fa fa-home"></span>--}}{{ isset($contact_info->company)?$contact_info->company:$generalSetting->site_title }}
                                        </li>
                                        <li>
                                            <span class="fa fa-map-marker"></span>{{ isset($contact_info->address)?$contact_info->address:"" }}
                                        </li>
                                        <li>
                                            <span class="fa fa-phone"></span>{{ isset($contact_info->phone)?$contact_info->phone:"" }}
                                        </li>
                                        @if(isset($contact_info->fax))
                                            <li>
                                                <span class="fa fa-fax"></span>{{ isset($contact_info->fax)?$contact_info->fax:"" }}

                                            </li>
                                        @endif

                                        <li>
                                            <span class="fa fa-envelope"></span>{{ isset($contact_info->email)?$contact_info->email:"" }}
                                        </li>
                                    </ul>
                                </div>
                                <ul class="social-icon-two">
                                    <li>
                                        <a href="{{ isset($contact_info->twitter_link)?$contact_info->twitter_link:"#" }}" target="_blank">
                                            <span class="fa fa-twitter"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ isset($contact_info->facebook_link)?$contact_info->facebook_link:"#" }}" target="_blank">
                                            <span class="fa fa-facebook"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ isset($contact_info->linkedIn_link)?$contact_info->linkedIn_link:"#" }}" target="_blank">
                                            <span class="fa fa-linkedin"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ isset($contact_info->googlePlus_link)?$contact_info->googlePlus_link:"#" }}" target="_blank">
                                            <span class="fa fa-google-plus"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ isset($contact_info->skype_link)?$contact_info->skype_link:"#" }}" target="_blank">
                                            <span class="fa fa-skype"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ isset($contact_info->whatsApp_link)?$contact_info->whatsApp_link:"#" }}" target="_blank">
                                            <span class="fa fa-whatsapp"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ isset($contact_info->pinterest_link)?$contact_info->pinterest_link:"#" }}" target="_blank">
                                            <span class="fa fa-pinterest"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ isset($contact_info->instagram_link)?$contact_info->instagram_link:"#" }}" target="_blank">
                                            <span class="fa fa-instagram"></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="footer-column col-md-3 col-sm-4 col-xs-12">
                            <div class="footer-widget newsletter-widget">
                                {!! isset($generalSetting->facebook_widget)?$generalSetting->facebook_widget:'' !!}

                            </div>
                        </div>
                        <!--Footer Column-->
                        @if (isset($generalSetting) && $generalSetting->quick_nav_status == 1)
                            <div class="footer-column col-md-4 col-sm-3 col-xs-12">
                                <div class="footer-widget link-widget">
                                    <div class="footer-title">
                                        <h2>Useful Links</h2>
                                        <div class="separator">
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                        </div>
                                    </div>
                                    <div class="widget-content">
                                        <ul>
                                            @if (isset($useful_links) && $useful_links->count() > 0)
                                                @foreach($useful_links as $usefulLinks)
                                                    <li>
                                                        <a href="{{  $usefulLinks->page_type == "link-page" ? "$usefulLinks->link" : ($usefulLinks->page_type == "predefine-link" ? route('web.home').'/'.$usefulLinks->link : 'page/'.$usefulLinks->slug) }}" target="_blank">
                                                            {{ $usefulLinks->title  }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>

                </div>
            </div>
        </div>
        <!--Footer Bottom-->
        <div class="footer-bottom">
            <div class="auto-container">
                <div class="row clearfix">
                    <div class="column col-md-6 col-sm-12 col-xs-12">
                        <div class="copyright">Copyrights &copy;
                            @if(isset($generalSetting->copyright))
                                {{$generalSetting->copyright}}
                            @else
                                <a href="#">Freelancer Umesh</a>
                            @endif
                        </div>
                    </div>

                    @if (isset($generalSetting) && $generalSetting->footer_nav_status == 1)
                        <div class="column col-md-6 col-sm-12 col-xs-12">
                            <div class="footer-right clearfix">
                                <ul class="clearfix">
                                    @if (isset($footer_nav) && $footer_nav->count() > 0)
                                        @foreach($footer_nav as $footerMenu)
                                            <li class="">
                                                @if($footerMenu->page_type == 'content-page')
                                                    <a href="{{route('web.page').'/'.$footerMenu->slug}}">{{ $footerMenu->title }}</a>
                                                @elseif($footerMenu->page_type =="predefine-link")
                                                    <a href="{{route('web.home').'/'.$footerMenu->slug}}">{{ $footerMenu->title }}</a>
                                                @else
                                                    <a href="{{$footerMenu->slug}}" target="_blank">{{ $footerMenu->title }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </footer>
    <!--End pagewrapper-->
@endif


<!--Scroll to top-->
<div class="scroll-to-top scroll-to-target" data-target="html">
    <span class="fa fa-arrow-up"></span>
</div>

