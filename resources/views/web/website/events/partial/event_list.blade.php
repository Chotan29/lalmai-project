<div class="col-md-4 col-sm-6">
    <div class="single-event-item">
        <div class="single-event-image">
            <a href="{{ 'events/'.$row->slug }}">
                @if ($row->image)
                    <img src="{{ asset('web/events/370_252_'.$row->image) }}" alt="{{ $row->title }}">
                @else
                    <img src="website/img/event/1.jpg" alt="">
                @endif
                <span><span>{{ Carbon\Carbon::parse($row->event_date)->format('d') }}</span>{{ Carbon\Carbon::parse($row->event_date)->format('M') }}</span>
            </a>
        </div>
        <div class="single-event-text">
            <h3><a href="{{ 'events/'.$row->slug }}">{{ $row->title }}</a></h3>
            <div class="single-item-comment-view">
                <span><i class="zmdi zmdi-time"></i>{{ $row->event_time }}</span> |
                <span><i class="zmdi zmdi-pin"></i>{{ $row->event_place }}</span> |
                <span><i class="zmdi zmdi-eye"></i>&nbsp;{{ $row->view_count }}</span>
            </div>
            <p>{!! \Illuminate\Support\Str::words($row->description, 20,'....')  !!}</p>
            <a class="button-default" href="{{ 'events/'.$row->slug }}">More Info</a>
        </div>
    </div>
</div>

