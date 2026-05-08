@if(isset($data['home_setting']) && $data['home_setting']->welcome_status == 1)
    <section class="welcome-section">
        <div class="auto-container">
            <div class="title-box">
                <div class="about-text-container">
                    <div class="sec-title light centered">
                        <h2>{{isset($data['home_setting']->welcome_title)?$data['home_setting']->welcome_title:'Wel - Come'}}</h2>
                        <div class="sub-title">{{isset($data['home_setting']->welcome_sub_title)?$data['home_setting']->welcome_sub_title:''}}</div>
                        <div class="separator">
                            <span class="dott"></span>
                            <span class="dott"></span>
                            <span class="dott"></span>
                        </div>
                    </div>
                    @if(isset($data['home_setting']->welcome_image))
                        <img class="float-right hvr-glow" src="{{asset('web/home_page_settings').'/'.$data['home_setting']->welcome_image}}" alt="{{$data['home_setting']->welcome_title}}">
                    @endif
                    <div class="message">
                        {!! isset($data['home_setting']->welcome_description)?$data['home_setting']->welcome_description:'' !!}
                    </div>
                    @if(isset($data['home_setting']->welcome_button_text))
                        <div class="btns-box">
                            <a href="{{isset($data['home_setting']->welcome_link)?$data['home_setting']->welcome_link:'#'}}" class="theme-btn btn-style-four">
                                {{isset($data['home_setting']->welcome_button_text)?$data['home_setting']->welcome_button_text:''}}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif