<div class="col-md-6">
    <div class="single-latest-item">
        <div class="single-latest-image">
			<div class="single-latest-image">
				<a href="{{ 'blog/'.$row->slug }}">
					@if ($row->image)
					    <img src="{{ asset('web/blog/236_234_'.$row->image) }}" alt="{{ $row->title }}">
					@else
					    <img src="../website/img/latest/1.jpg" alt="{{ $row->title }}">
					@endif
			    </a>
			</div>
			<div class="single-latest-text">
			    <h3><a href="{{ 'blog/'.$row->slug }}">{{ $row->title }}</a></h3>
			    <div class="single-item-comment-view">
			        <span><i class="zmdi zmdi-calendar-check"></i>{{ Carbon\Carbon::parse($row->publish_date)->format('d M Y') }}</span>
			        <span><i class="zmdi zmdi-eye"></i>&nbsp;{{ $row->view_count }}</span>
			        <!-- <span><i class="zmdi zmdi-comments"></i>19</span> -->
			    </div>
			    <p>{!! \Illuminate\Support\Str::words($row->short_desc, 15,'....')  !!}</p>
			    <a href="{{ 'blog/'.$row->slug }}" class="button-default">Read More</a>
			</div>
		</div>
	</div>
</div>
