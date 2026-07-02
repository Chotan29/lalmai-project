<!-- Main Header-->
<header class="main-header">
    @if (isset($generalSetting) && $generalSetting->top_nav_status == 1)
        <!--Header Top-->
        <div class="header-top">
            <div class="auto-container">
                <div class="clearfix">
                    <div class="top-left">
                        <ul class="clearfix">
                            <li><a href="{{ isset($generalSetting->top_nav_text_link)?$generalSetting->top_nav_text_link:"#" }}" target="_blank">{{ isset($generalSetting->top_nav_text)?$generalSetting->top_nav_text:"" }}</a></li>

                            {{--@if(!isset($generalSetting->top_nav_text_link))
                                <li><a href="{{ isset($generalSetting->top_nav_text_link)?$generalSetting->top_nav_text_link:"#" }}" target="_blank">{{ isset($generalSetting->top_nav_text)?$generalSetting->top_nav_text:"" }}</a></li>
                            @else
                                <marquee>
                                    <li><a href="{{ isset($generalSetting->top_nav_text_link)?$generalSetting->top_nav_text_link:"#" }}" target="_blank">{{ isset($generalSetting->top_nav_text)?$generalSetting->top_nav_text:"" }}</a></li> |
                                    <li><a href="{{ isset($generalSetting->top_nav_text_link)?$generalSetting->top_nav_text_link:"#" }}" target="_blank">{{ isset($generalSetting->top_nav_text)?$generalSetting->top_nav_text:"" }}</a></li> |
                                    <li><a href="{{ isset($generalSetting->top_nav_text_link)?$generalSetting->top_nav_text_link:"#" }}" target="_blank">{{ isset($generalSetting->top_nav_text)?$generalSetting->top_nav_text:"" }}</a></li>
                                </marquee>
                            @endif--}}
                        </ul>
                    </div>

                    <div class="top-right clearfix">
                        <ul class="clearfix">
                            @if (isset($top_nav) && $top_nav->count() > 0)
                                @foreach($top_nav as $topMenu)
                                    <li class="">
                                        @if($topMenu->page_type == 'content-page')
                                            <a href="{{route('web.page').'/'.$topMenu->slug}}">{{ $topMenu->title }}</a>
                                        @elseif($topMenu->page_type =="predefine-link")
                                            <a href="{{route('web.home').'/'.$topMenu->slug}}">{{ $topMenu->title }}</a>
                                        @else
                                            <a href="{{$topMenu->link}}" target="_blank">{{ $topMenu->title }}</a>
                                        @endif
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!--Header-Upper-->
    <div class="header-upper">
        <div class="auto-container clearfix">
            <div class="pull-left logo-outer">
                <div class="logo">
                    <a href="{{ route('web.home') }}">
                        @if (isset($generalSetting->site_logo))
                            <img src="{{ asset('web/general_settings/'. $generalSetting->site_logo) }}" alt="{{ $generalSetting->site_title }}" alt="" title="">
                        @endif
                    </a>
                </div>
            </div>

            <div class="pull-right upper-right clearfix">
                <!--Info Box-->
                <div class="upper-column info-box">
                    {{--<div class="icon-box"><span class="flaticon-telephone-handle-silhouette"></span></div>--}}
                    <ul>
                        <li><strong>Call Us</strong>{{ isset($contact_info->phone)?$contact_info->phone:"" }}</li>
                    </ul>
                </div>

                <!--Info Box-->
                <div class="upper-column info-box">
                    {{--<div class="icon-box"><span class="flaticon-symbol"></span></div>--}}
                    <ul>
                        <li><strong>Email</strong>{{ isset($contact_info->email)?$contact_info->email:"" }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--End Header Upper-->

    <!--Header Lower-->
    @if (isset($generalSetting) && $generalSetting->main_nav_status == 1)
        <div class="header-lower">
            <div class="bg-box"></div>
            <div class="auto-container">
                <div class="nav-outer clearfix">
                    <!-- Main Menu -->
                    @if (isset($main_nav) && $main_nav->count() > 0)
                    <nav class="navbar-expand-lg main-menu">
                        <div class="navbar-header">
                            <!-- Toggle Button -->
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarSupportedContent">
                                <span class="fa fa-navicon"></span> MENU
                            </button>
                        </div>

                        <div class="collapse navbar-collapse clearfix" id="navbarSupportedContent" aria-expanded="false">
                            <ul class="navigation clearfix">
                                <li class="home"><a href="{{route('web.home')}}"><span class="fa fa-home"></span></a></li>
                                    @foreach($main_nav as $mainMenu)
                                        @if($mainMenu->children()->count() > 0)
                                            <li class="dropdown">
                                                @if($mainMenu->page_type == 'content-page')
                                                    <a href="{{route('web.page').'/'.$mainMenu->slug}}">{{ $mainMenu->title }}</a>
                                                @elseif($mainMenu->page_type =="predefine-link")
                                                    <a href="{{route('web.home').'/'.$mainMenu->slug}}">{{ $mainMenu->title }}</a>
                                                @else
                                                    <a href="{{$mainMenu->link}}" target="_blank">{{ $mainMenu->title }}</a>
                                                @endif
                                                <ul class="submenu">
                                                    @if($childMenues = $mainMenu->children()->get())
                                                        @foreach($childMenues as $childMenue)
                                                            <li>
                                                                @if($childMenue->page_type == 'content-page')
                                                                    <a href="{{route('web.page').'/'.$childMenue->slug}}">{{ $childMenue->title }}</a>
                                                                @elseif($childMenue->page_type =="predefine-link")
                                                                    <a href="{{route('web.home').'/'.$mainMenu->slug}}">{{ $mainMenu->title }}</a>
                                                                @else
                                                                    <a href="{{$childMenue->link}}" target="_blank">{{ $childMenue->title }}</a>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                                <div class="dropdown-btn"><span class="fa fa-angle-down"></span></div><div class="dropdown-btn"><span class="fa fa-angle-down"></span></div></li>
                                        @else
                                            <li class="">
                                                @if($mainMenu->page_type == 'content-page')
                                                    <a href="{{route('web.page').'/'.$mainMenu->slug}}">{{ $mainMenu->title }}</a>
                                                @elseif($mainMenu->page_type =="predefine-link")
                                                    @if(!isset($mainMenu->slug))
                                                        <a href="{{route('web.home')}}">{{ $mainMenu->title }}</a>
                                                    @else
                                                        <a href="{{route('web.home').'/'.$mainMenu->slug}}">{{ $mainMenu->title }}</a>
                                                    @endif
                                                @else
                                                    <a href="{{$mainMenu->link}}" target="_blank">{{ $mainMenu->title }}</a>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                            </ul>
                        </div>
                    </nav>
                    @endif
                    <!-- Main Menu End-->
                    @if (isset($generalSetting) && $generalSetting->main_nav_button_status == 1)
                        <div class="outer-box">
                            <a href="{{$generalSetting->main_nav_button_link}}" target="_blank" class="consult-btn theme-btn">
                                <span class="fa fa-angle-double-right"></span>{{$generalSetting->main_nav_button_button_text}}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!--End Header Lower-->

    <!--Sticky Header-->
    @if (isset($generalSetting) && $generalSetting->sticky_nav_status == 1)
        <div class="sticky-header">
        <div class="auto-container clearfix">
            <!--Logo-->
            <div class="logo pull-left">
                {{--<a href="{{route('web.home')}}" ><img src="{{ asset('web/general_settings/'. $generalSetting->site_logo) }}" alt="" title=""></a>--}}
                <a href="{{ route('web.home') }}">
                    @if (isset($generalSetting->site_logo))
                        <img class="img-responsive" src="{{ asset('web/general_settings/'. $generalSetting->site_logo) }}" alt="{{ $generalSetting->site_title }}" height="51px" alt="" title="">
                    @endif
                </a>
            </div>

            <!--Right Col-->
            <div class="right-col pull-right">
                <!-- Main Menu -->
                @if (isset($sticky_nav) && $sticky_nav->count() > 0)
                <nav class="main-menu">
                    <div class="navbar-header navbar-expand-lg">
                        <!-- Toggle Button -->
                        {{--<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>--}}
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <div class="navbar-collapse clearfix">
                        <ul class="navigation clearfix">
                            <li class="home btn-primary"><a href="{{route('web.home')}}"><span class="fa fa-home"></span></a></li>
                                @foreach($sticky_nav as $stickyMenu)
                                    @if($stickyMenu->children()->count() > 0)
                                        <li class="dropdown">
                                            @if($stickyMenu->page_type == 'content-page')
                                                <a href="{{route('web.page').'/'.$stickyMenu->slug}}">{{ $stickyMenu->title }}</a>
                                            @elseif($stickyMenu->page_type =="predefine-link")
                                                <a href="{{route('web.home').'/'.$stickyMenu->slug}}">{{ $stickyMenu->title }}</a>
                                            @else
                                                <a href="{{$stickyMenu->link}}" target="_blank">{{ $stickyMenu->title }}</a>
                                            @endif
                                            <ul class="submenu">
                                                @if($childMenues = $stickyMenu->children()->get())
                                                    @foreach($childMenues as $childMenue)
                                                        <li>
                                                            @if($childMenue->page_type == 'content-page')
                                                                <a href="{{route('page').'/'.$childMenue->slug}}">{{ $childMenue->title }}</a>
                                                            @elseif($childMenue->page_type =="predefine-link")
                                                                <a href="{{route('home').'/'.$childMenue->slug}}">{{ $childMenue->title }}</a>
                                                            @else
                                                                <a href="{{$childMenue->link}}" target="_blank">{{ $childMenue->title }}</a>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                            <div class="dropdown-btn"><span class="fa fa-angle-down"></span></div><div class="dropdown-btn"><span class="fa fa-angle-down"></span></div></li>
                                    @else
                                        <li class="">
                                            @if($stickyMenu->page_type == 'content-page')
                                                <a href="{{route('page').'/'.$stickyMenu->slug}}">{{ $stickyMenu->title }}</a>
                                            @elseif($stickyMenu->page_type =="predefine-link")
                                                <a href="{{route('home').'/'.$stickyMenu->slug}}">{{ $stickyMenu->title }}</a>
                                            @else
                                                <a href="{{$stickyMenu->link}}" target="_blank">{{ $stickyMenu->title }}</a>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                        </ul>
                    </div>
                </nav>
                @endif
            </div>
        </div>
    </div>
    @endif
    <!--End Sticky Header-->
</header>
