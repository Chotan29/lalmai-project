@if (isset($data['counter']) && $data['counter']->count() > 0)
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
                                        <h2>{{isset($homeSetting->counter_title)?$homeSetting->counter_title:'Counter'}}</h2>
                                        <!--div class="sub-title">Text goes here.</div-->
                                        <div class="separator">
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach($data['counter'] as $counter)
                                        <div class="four col-md-3 counter hvr-float">
                                            <div class="counter-box colored">
                                                <i class=" fa {{$counter->icon}}"></i>
                                                <span class="counter">{{ $counter->counter }}</span>
                                                <p>{{ $counter->title }}</p>
                                            </div>
                                        </div>
                                        @endforeach
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
