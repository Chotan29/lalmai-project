@if (isset($data['event']) && $data['event']->count() > 0)
    <div class="content-side col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="services-single">
            <div class="inner-box">
                <div class="lower-content">
                    <div class="text">
                        <div class="sec-title light centered">
                            <h2>{{isset($homeSetting->event_title)?$homeSetting->event_title:'Events'}}</h2>
                            <!--div class="sub-title">Text goes here.</div-->
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>
                        <ul class="list-style-one">
                            @foreach($data['event'] as $event)
                                <li>
                                    <a href="{{ 'web.events/'.$event->slug }}">
                                        {{ $event->title }}
                                    </a>
                                    <em>{{ $event->category_title}}</em>
                                </li>
                            @endforeach
                        </ul>
                        <a class="pull-left" href="{{route('web.events')}}">More {{isset($homeSetting->event_title)?$homeSetting->event_title:'Blog'}}...</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif