@if($data['aboutUs_setting']->staffs_status == 1)
    <div class="staffs-area padding-top">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title-wrapper">
                    <div class="section-title">
                        <h3>{{ $data['aboutUs_setting']->staffs_title }}</h3>
                        <p>{{ $data['aboutUs_setting']->staffs_salogan }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($data['our_staffs']) && $data['our_staffs']->count() > 0)
            <div class="row">

                @foreach($data['our_staffs'] as $staff)

                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="single-staff-item">
                            <div class="single-staff-image">
                                <a href="#">
                                    @if ($staff->profile_image)
                                        <img src="{{ asset('web/staff/270_253_'.$staff->profile_image) }}"  alt="{{ $staff->name }}">
                                    @else
                                         <img src="{{ asset('website/img/course/1.jpg') }}" alt="{{ $staff->name }}">
                                    @endif
                                </a>    
                            </div>
                            <div class="single-staff-text">
                                <h3><a href="#">{{ $staff->name }}</a></h3>
                                <h4>{{ $staff->position }}</h4>
                                <p>{!! $staff->description !!}</p>
                                <div class="social-links">
                                    <a href="{{ $staff->facebook_url }}"><i class="zmdi zmdi-facebook"></i></a>
                                    <a href="{{ $staff->twitter_url }}"><i class="zmdi zmdi-twitter"></i></a>
                                    <a href="{{ $staff->googlePlus_url }}#"><i class="zmdi zmdi-google-old"></i></a>
                                    <!-- <a href="{{ $staff->  linkedIn_url }}"><i class="zmdi zmdi-instagram"></i></a> -->
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="pagination-content">
                        {!! $data['our_staffs']->links() !!}
                    </div>
                </div>
            </div>
        @else
            <p>No post found.</p>
        @endif

    </div>
</div>
@endif