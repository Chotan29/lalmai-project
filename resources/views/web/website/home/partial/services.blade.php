@if (isset($data['home_setting']) && $data['home_setting']->services_status == 1)
    <section class="services-section">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12">
                {{--<div class="sec-title light centered animated infinite pulse delay-2s slower">--}}
                <div class="sec-title light centered hvr-grow">
                    <h2 class="services-heading">
                        {{isset($data['home_setting']->services_title)?$data['home_setting']->services_title:''}}
                    </h2>
                </div>
                @if(isset($data['services']) && $data['services']->count() >0)
                    @php($i=1)
                    @foreach($data['services'] as $services)
                        <div class="about-text-container">
                            <div class="sec-title light centered">
                                <h2>{{isset($services->title)?$services->title:'About Edu Firm'}}</h2>
                                <div class="sub-title">{{isset($services->sub_title)?$services->sub_title:''}}</div>
                                <div class="separator">
                                    <span class="dott"></span>
                                    <span class="dott"></span>
                                    <span class="dott"></span>
                                </div>
                            </div>
                            @if(isset($services->image))
                                <img class="{{($i%2 != 0)?'float-right':'float-left'}}" src="{{ asset('web/services/'.$services->image) }}" alt="{{$services->title}}" width="585" height="385">
                            @endif
                           {{-- @if(isset($services->video))
                                <span class="video {{($i%2 != 0)?'float-right':'float-left'}}">
                                    {!! $services->video !!}
                                </span>
                            @endif--}}
                            <p>
                                {!! $services->description !!}
                            </p>
                            @if(isset($services->link))
                                <div class="btns-box">
                                    <a href="{{isset($services->link)?$services->link:'#'}}" target="{{isset($services->open_in)?$services->open_in:'_self'}}" class="theme-btn btn-style-four">
                                        {{isset($services->button_text)?$services->button_text:''}}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @php($i++)
                    @endforeach
                @endif

            </div>
        </div>
    </div>
</section>
@endif
