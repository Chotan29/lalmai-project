@if (isset($data['client']) && $data['client']->count() > 0)
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
                                        <h2>{{isset($homeSetting->client_title)?$homeSetting->client_title:'Client/Award'}}</h2>
                                        <!--div class="sub-title">Text goes here.</div-->
                                        <div class="separator">
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                        </div>
                                    </div>
                                    <div class="sponsors-outer">
                                        <!--Sponsors Carousel-->
                                        <ul class="sponsors-carousel owl-carousel owl-theme">
                                        @foreach($data['client'] as $client)
                                            <li class="slide-item">
                                                <figure class="image-box">
                                                    <a href="{!! $client->link !!}" target="_blank">
                                                        <img src="{{ asset('web/client/'.$client->image_name) }}" alt="{!! $client->title !!}">
                                                    </a>
                                                </figure>
                                            </li>
                                        @endforeach
                                        </ul>
                                        {{-- <div class="four col-md-3">
                                             <div class="counter-box"> <i class="fa fa-group"></i> <span class="counter">3275</span>
                                                 <p>Registered Members</p>
                                             </div>
                                         </div>--}}
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
