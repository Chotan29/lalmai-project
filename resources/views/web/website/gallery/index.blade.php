@extends('web.website.layouts.master')

@section('css')
    @include('web.website.includes.css.search-box')
@endsection

@section('content')
    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!--Content Side / Blog Single-->
                <div class="content-column col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="inner-column">
                        <div class="sec-title light centered">
                            <h2>Gallery</h2>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                        </div>

                        <div class="text">
                            {!! Form::open(['route' => 'web.gallery', 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @include('web.website.includes.search-form')
                            {!! Form::close() !!}
                            @if (isset($data['rows']) && $data['rows']->count() > 0)
                            <div class="album py-5">
                                <div class="row">
                                    @foreach($data['rows'] as $gallery)
                                            <div class="col-md-4 hvr-float">
                                                <div class="card mb-4 box-shadow">
                                                    <a href="{{ 'gallery/'.$gallery->slug }}">
                                                        @if ($gallery-> image_name)
                                                            <img src="{{ asset('gallery/'.$gallery-> image_name) }}" alt="{{ $gallery->title }}" class="card-img-top" >
                                                        @else
                                                            <img  data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" style="height: 225px; width: 100%; display: block;" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22348%22%20height%3D%22225%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20348%20225%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_17022fa4014%20text%20%7B%20fill%3A%23eceeef%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A17pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_17022fa4014%22%3E%3Crect%20width%3D%22348%22%20height%3D%22225%22%20fill%3D%22%2355595c%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22116.71875%22%20y%3D%22120.3%22%3EThumbnail%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true">
                                                        @endif
                                                    </a>
                                                    <div class="card-body">
                                                        <h5><a href="{{ 'gallery/'.$gallery->slug }}" class="">{{$gallery->title}}</a></h5>
                                                        <p class="card-text">
                                                            {!! \Illuminate\Support\Str::words($gallery->description, 20,'....')  !!}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="btn-group">
                                                                <a href="{{ 'gallery/'.$gallery->slug }}" type="button" class="btn btn-sm btn-outline-secondary">View</a>
                                                            </div>
                                                            {{--<small class="text-muted">9 mins</small>--}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        @include('web.website.includes.pagination')
                                </div>
                            </div>
                            @endif

                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

