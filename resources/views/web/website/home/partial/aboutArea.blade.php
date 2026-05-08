@if(isset($data['home_setting']) && $data['home_setting']->introduction_status == 1)
    <section class="clients-section">
        <div class="auto-container">
            <div class="title-box">
                <h2>{{isset($data['home_setting']->introduction_title)?$data['home_setting']->introduction_title:'About Us'}}</h2>
                <div class="text">
                    {!! isset($data['home_setting']->introduction_description)?$data['home_setting']->introduction_description:'' !!}
                </div>
                @if(isset($data['home_setting']->introduction_button_text))
                    <div class="btns-box">
                        <a href="{{isset($data['home_setting']->introduction_link)?$data['home_setting']->introduction_link:'#'}}" class="theme-btn btn-style-four">{{isset($data['home_setting']->introduction_button_text)?$data['home_setting']->introduction_button_text:''}}</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
