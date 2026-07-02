@if (isset($data['blog']) && $data['blog']->count() > 0)
    <div class="content-side col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="services-single">
            <div class="inner-box">
                <div class="lower-content">
                    <div class="text">
                        <div class="sec-title light centered">
                            <h2>{{isset($homeSetting->blog_title)?$homeSetting->blog_title:'Blog'}}</h2>
                            <!--div class="sub-title">Text goes here.</div-->
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>
                        <ul class="list-style-one">
                            @foreach($data['blog'] as $blog)
                                <li>
                                    <a href="{{ 'blog/'.$blog->slug }}">
                                        {{ $blog->title }}
                                    </a>
                                    {{--<em>{{ $blog->category_title}}</em>--}}
                                </li>
                            @endforeach
                        </ul>
                        <a class="pull-left" href="{{route('web.blog')}}">More {{isset($homeSetting->blog_title)?$homeSetting->blog_title:'Blog'}}...</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif