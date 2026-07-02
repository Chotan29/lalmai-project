@if (isset($data['notice']) && $data['notice']->count() > 0)
    <div class="content-side col-lg-4 col-md-4 col-sm-6 col-xs-12">
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