@if (isset($data['staffs']) && $data['staffs']->count() > 0)
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
                                        <h2>{{isset($homeSetting->staff_title)?$homeSetting->staff_title:'Staffs'}}</h2>
                                        <!--div class="sub-title">Text goes here.</div-->
                                        <div class="separator">
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                            <span class="dott"></span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        @foreach($data['staffs'] as $staff)
                                            <div class="team-member col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                <a href="{{ route('web.staffs.detail', ['id' => encrypt($staff->id)]) }}">
                                                    <div class="inner-box">
                                                        <div class="image">
                                                            @if ($staff->profile_image)
                                                                <img src="{{ asset('web/staff/'.$staff->profile_image) }}"  alt="{{ $staff->name }}">
                                                            @else
                                                                <img src="{{ asset('website/img/course/1.jpg') }}" alt="{{ $staff->name }}">
                                                            @endif
                                                        </div>
                                                        <div class="lower-box">
                                                            <h4>{{$staff->name}}</h4>
                                                            <div class="designation">{{$staff->position}}</div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
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