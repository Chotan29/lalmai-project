@if (isset($data['testimonial']) && $data['testimonial']->count() > 0)
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
                                    <h2><h2>{{isset($homeSetting->notice_title)?$homeSetting->testimonial_title:'Testimonial'}}</h2></h2>
                                    <!--div class="sub-title">Text goes here.</div-->
                                    <div class="separator">
                                        <span class="dott"></span>
                                        <span class="dott"></span>
                                        <span class="dott"></span>
                                    </div>
                                </div>
                                <div id="myCarousel" class="carousel slide" data-ride="carousel" style="width: 70%;">
                                    <!-- Carousel indicators -->
                                    <ol class="carousel-indicators">
                                        @foreach($data['testimonial'] as $key => $testimonial)
                                            <li data-target="#myCarousel" data-slide-to="{{$testimonial->id}}" class="{{($key==0)?'active':''}}"></li>
                                        @endforeach
                                    </ol>
                                    <!-- Wrapper for carousel items -->
                                    <div class="carousel-inner">
                                        @foreach($data['testimonial'] as $key => $testimonial)
                                            <div class="item carousel-item {{($key==0)?'active':''}}">
                                                <div class="img-box">
                                                    {{--<img src="/examples/images/clients/3.jpg" alt="">--}}
                                                    <img src="{{ asset('web/testimonial/'.$testimonial->profile_image) }}" alt="{{ $testimonial->name }}" />
                                                </div>
                                                <p class="testimonial">
                                                    {!! $testimonial->comment !!}
                                                </p>
                                                <p class="overview">
                                                    <b>{{ $testimonial->name }}</b>
                                                    {{ $testimonial->position }}<br>
                                                    <a href="{{isset($testimonial->link)?$testimonial->link:'#'}}">{{ $testimonial->office }}</a>
                                                </p>
                                                <div class="star-rating">
                                                    <ul class="list-inline">
                                                        <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                        <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                        <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                        <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                                        <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                                    </ul>
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>
                                    <!-- Carousel controls -->
                                    <a class="carousel-control left carousel-control-prev" href="#myCarousel" data-slide="prev">
                                        <i class="fa fa-angle-left"></i>
                                    </a>
                                    <a class="carousel-control right carousel-control-next" href="#myCarousel" data-slide="next">
                                        <i class="fa fa-angle-right"></i>
                                    </a>
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