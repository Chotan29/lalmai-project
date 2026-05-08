<aside class="sidebar right-sidebar">
    <!--Category Blog-->
    <div class="sidebar-widget categories-blog">
        <div class="sidebar-title">
            <div class="sec-title light centered">
                <h2>Latest {{isset($homeSetting->blog_title)?$homeSetting->blog_title:'Blog'}}</h2>
                <!--div class="sub-title">Text goes here.</div-->
                <div class="separator">
                    <span class="dott"></span>
                    <span class="dott"></span>
                    <span class="dott"></span>
                </div>
            </div>
        </div>
        <div class="inner-box">
            <ul class="list">
                @if (isset($data['recent']) && $data['recent']->count() > 0)
                    @foreach($data['recent'] as $row)
                        <li>
                            <a href="{{ route('web.blog').'/'.$row->slug }}">{{ $row->title }}</a>
                        </li>
                    @endforeach
                    <a class="pull-right" href="{{route('web.blog')}}">More {{isset($data['home_setting']->blog_title)?$data['home_setting']->blog_title:'Blog & Events'}}...</a>
                @else
                    <li>
                        <a href="#">....</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

</aside>
