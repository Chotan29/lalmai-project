@if($data['aboutUs_setting']->counter_status == 1)
    <div class="skill-and-experience-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title-wrapper">
                    <div class="section-title">
                        <h3>{{ $data['aboutUs_setting']->counter_title }}</h3>
                        <p>{{ $data['aboutUs_setting']->counter_salogan }}</p>
                    </div>
                </div>
            </div>
        </div>
        @if (isset($data['counter']) && $data['counter']->count() > 0)
            <div class="row">
                @foreach($data['counter'] as $counter)
                    <div class="col-md-6 col-sm-6" style="margin-top: 15px;">
                        <div class="experience-skill-wrapper">
                            <div class="skill-bar-item">
                                <span>{{ $counter->title }}</span>
                                <div class="progress">
                                    <div class="progress-bar wow fadeInLeft" data-progress="{{ $counter->counter }}%" style="width: {{ $counter->counter }}%;" data-wow-duration="1.5s" data-wow-delay="1.2s">
                                        <span class="text-top">{{ $counter->counter }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No post found.</p>
        @endif

    </div>
</div>
@endif