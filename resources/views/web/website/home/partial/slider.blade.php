@if (isset($data['home_setting']) && $data['home_setting']->slider_status == 1)
    @if (isset($data['slider']) && $data['slider']->count() > 0)
        <div id="carouselExampleIndicators" class=" carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @php($i=1)
                @foreach($data['slider'] as $slider)
                    <li data-target="#carouselExampleIndicators" data-slide-to="{{$i}}" class={{$i==1?'active':''}}></li>
                @php($i++)
                @endforeach
            </ol>
            <div class="carousel-inner">
                @php($i=1)
                @foreach($data['slider'] as $slider)
                    <div class="carousel-item {{$i==1?'active':''}}" style="background-image: url({{ asset('web/slider/'.$slider->image_name) }});">
                        {{--<img class='img-fluid'  src="{{ asset('web/slider/'.$slider->image_name) }}" alt="{{$slider->title}}">--}}
                        @if (isset($data['home_setting']) && $data['home_setting']->slider_caption_status == 1)
                            <div class="carousel-caption">
                                <h5>{{$slider->title}}</h5>
                                <p>{{$slider->description}}</p>
                                @if(isset($slider->link))
                                    <div class="btns-box">
                                        <a href="{{isset($slider->link)?$slider->link:'#'}}" target="{{isset($slider->open_in)?$slider->open_in:'_self'}}" class="theme-btn btn-style-four">
                                            {{isset($slider->button_text)?$slider->button_text:''}}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    @php($i++)
                @endforeach
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    @endif
@endif

@if (isset($data['home_setting']) && $data['home_setting']->slider_call_to_action_status == 1)
    <section class="call-to-action">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="column col-md-12 col-sm-12 col-xs-12">
                    <div class="text">
                        <a href="{{$data['home_setting']->slider_call_to_action_link}}" target="_blank" style="color: white">{{$data['home_setting']->slider_call_to_action_title}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

{{--Slider With Notice--}}
{{--

@if (isset($data['home_setting']) && $data['home_setting']->slider_status == 1)
    @if (isset($data['slider']) && $data['slider']->count() > 0)
        <div class="inner-container">
            <div class="row">
                <div class="col-md-8">
                    <div id="carouselExampleIndicators" class=" carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @php($i=1)
                            @foreach($data['slider'] as $slider)
                                <li data-target="#carouselExampleIndicators" data-slide-to="{{$i}}" class={{$i==1?'active':''}}></li>
                                @php($i++)
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @php($i=1)
                            @foreach($data['slider'] as $slider)
                                <div class="carousel-item {{$i==1?'active':''}}">
                                    <img class='img-fluid'  src="{{ asset('web/slider/'.$slider->image_name) }}" alt="{{$slider->title}}">
                                    @if (isset($data['home_setting']) && $data['home_setting']->slider_caption_status == 1)
                                        <div class="carousel-caption">
                                            <h5>{{$slider->title}}</h5>
                                            <p>{{$slider->description}}</p>
                                            @if(isset($slider->link))
                                                <div class="btns-box">
                                                    <a href="{{isset($slider->link)?$slider->link:'#'}}" target="{{isset($slider->open_in)?$slider->open_in:'_self'}}" class="theme-btn btn-style-four">
                                                        {{isset($slider->button_text)?$slider->button_text:''}}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                @php($i++)
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    @if (isset($data['notice']) && $data['notice']->count() > 0)
                        <div class="content-side">
                            <div class="services-single">
                                <div class="inner-box">
                                    <div class="lower-content">
                                        <div class="text ">
                                            <div class="sec-title light centered">
                                                <h2>{{isset($homeSetting->notice_title)?$homeSetting->notice_title:'Notice'}}</h2>
                                                <!--div class="sub-title">Text goes here.</div-->
                                                <div class="separator">
                                                    <span class="dott"></span>
                                                    <span class="dott"></span>
                                                    <span class="dott"></span>
                                                </div>
                                            </div>
                                            <ul class="list-style-one">
                                                @foreach($data['notice'] as $notice)
                                                    <li>
                                                        <a href="{{ 'notice/'.$notice->slug }}">
                                                            {{ $notice->title }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <a class="pull-left" href="{{route('web.notice')}}">More {{isset($homeSetting->notice_title)?$homeSetting->notice_title:'Notice'}}...</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    @endif
@endif

@if (isset($data['home_setting']) && $data['home_setting']->slider_call_to_action_status == 1)
    <section class="call-to-action">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="column col-md-12 col-sm-12 col-xs-12">
                    <div class="text">
                        <a href="{{$data['home_setting']->slider_call_to_action_link}}" target="_blank" style="color: white">{{$data['home_setting']->slider_call_to_action_title}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif--}}

{{--Revolution Slider--}}

{{--
@if (isset($data['home_setting']) && $data['home_setting']->slider_status == 1)

    @if (isset($data['slider']) && $data['slider']->count() > 0)
        <section class="main-slider">
            <!--Main Slider-->
            <div class="rev_slider_wrapper fullwidthbanner-container"  id="rev_slider_one_wrapper" data-source="gallery">
                <div class="rev_slider fullwidthabanner" id="rev_slider_one" data-version="5.4.1">
                    <ul>
                        @foreach($data['slider'] as $slider)
                            <li data-description="sadfasd" data-easein="default" data-easeout="default" data-fsmasterspeed="1500" data-fsslotamount="7" data-fstransition="fade" data-hideafterloop="0" data-hideslideonmobile="off" data-index="rs-1687" data-masterspeed="default" data-param1="11111" data-param10="1000" data-param2="222" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-rotate="0" data-saveperformance="off" data-slotamount="default" data-thumb="{{ asset('web/slider/'.$slider->image_name) }}" data-title="{!! $slider->title !!}" data-transition="parallaxvertical">
                               <img alt="{!! $slider->title !!}" class="rev-slidebg" data-bgfit="cover" data-bgparallax="10" data-bgposition="center center" data-bgrepeat="no-repeat" data-no-retina="" src="{{ asset('web/slider/'.$slider->image_name) }}">
                            </li>
                            <a href="{{$slider->link}}"> </a>
                        @endforeach
                    </ul>
                </div>
            </div>
            <!--End Main Header -->
        </section>
    @endif
@endif

@if (isset($data['home_setting']) && $data['home_setting']->slider_call_to_action_status == 1)
    <section class="call-to-action">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="column col-md-12 col-sm-12 col-xs-12">
                    <div class="text">
                        <a href="{{$data['home_setting']->slider_call_to_action_link}}" target="_blank" style="color: white">{{$data['home_setting']->slider_call_to_action_title}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

--}}
