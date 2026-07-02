@if (isset($data['others']) && $data['others']->count() > 0)
    <aside class="sidebar right-sidebar">
    <!--Others Pages-->
    <div class="sidebar-widget categories-blog">
        <div class="sidebar-title">
            <h2>Other Pages</h2>
            <div class="separator">
                <span class="dott"></span>
                <span class="dott"></span>
                <span class="dott"></span>
            </div>
        </div>
        <div class="inner-box">
            <ul class="list">
                    @foreach($data['others'] as $page)
                        <li>
                            @if($page->page_type == 'content-page')
                                <a href="{{route('web.page').'/'.$page->slug}}">{{ $page->title }}</a>
                            @elseif($page->page_type =="predefine-link")
                                <a href="{{route('web.home').'/'.$page->slug}}">{{ $page->title }}</a>
                            @else
                                <a href="{{$page->slug}}" target="_blank">{{ $page->title }}</a>
                            @endif
                        </li>
                    @endforeach
            </ul>
        </div>
    </div>

</aside>
@endif

