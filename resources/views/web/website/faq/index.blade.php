@extends('web.website.layouts.master')

@section('css')
    @include('web.website.includes.css.search-box')
    <style>
        .card-header {
            font-weight: 600 !important;
        }

        .card-body {
            border: 1px rgba(0, 0, 0, 0.09) solid !important;
        }
    </style>
@endsection

@section('content')

        <div class="sidebar-page-container">
            <div class="auto-container">
                <div class="row clearfix">
                    <!--Content Side / Blog Single-->
                    <div class="content-column col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inner-column">
                            <div class="sec-title light centered">
                                <h2>Frequently Asked Question</h2>
                                <div class="separator">
                                    <span class="dott"></span>
                                    <span class="dott"></span>
                                    <span class="dott"></span>
                                </div>
                            </div>

                            <div class="text">
                               {{-- {!! Form::open(['route' => 'faqs', 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                                @include('web.website.includes.search-form')
                                {!! Form::close() !!}--}}
                                @if (isset($data['rows']) && $data['rows']->count() > 0)
                                    @foreach($data['rows'] as $key => $faq)
                                        <div class="card-header">
                                            <a class="card-link" data-toggle="collapse" href="#collapse-{{ $faq->id }}">
                                                {{--<i class="fa fa-question-circle"></i>--}}{{$key+1}}. {{ $faq->question }}
                                            </a>
                                        </div>
                                        <div id="collapse-{{ $faq->id }}" class="collapse" data-parent="#accordion">
                                            <div class="card-body">
                                                {!! $faq->answer !!}
                                            </div>
                                        </div>
                                    @endforeach
                                        {{--@include('web.website.includes.pagination')--}}
                                @endif

                            </div>

                            <div style="clear:both"><br></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection
@section('js')
@endsection







