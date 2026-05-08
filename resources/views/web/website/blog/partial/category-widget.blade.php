<aside class="sidebar right-sidebar">
    <!--Category Blog-->
    <div class="sidebar-widget categories-blog">
        <div class="sidebar-title">

            <div class="sec-title light centered">
                <h2>{{isset($data['home_setting']->blog_title)?$data['home_setting']->blog_title:'Blog'}} Category</h2>

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
                @if (isset($data['categories']) && $data['categories']->count() > 0)
                    @foreach($data['categories'] as $cat)
                        <li>
                            <a href="{{route('web.category.detail', ['slug' => $cat->slug])}}">{{ $cat->title }}</a>
                        </li>
                    @endforeach
                @else
                    <li>
                        <a href="#">....</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

</aside>
