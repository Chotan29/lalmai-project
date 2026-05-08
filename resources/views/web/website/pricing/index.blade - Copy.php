@extends('web.website.layouts.master')

@section('css')
    @include('web.website.includes.css.pricing')
@endsection

@section('content')

    <section class="pricing-table">
        <div class="block-heading">
            <h2>Pricing Table</h2>
            <div class="separator">
                <span class="dott"></span>
                <span class="dott"></span>
                <span class="dott"></span>
            </div>
            {{--<p>descripton descripton descripton descripton descripton descripton</p>--}}
        </div>

        <div class="row justify-content-md-center">
                <div class="container">
                    <div class="row">
                        @if (isset($data['rows']) && $data['rows']->count() > 0)
                            @foreach($data['rows'] as $row)
                                <div class="col-md-5 col-lg-4 hvr-float">
                                    <div class="item" style="border-top: 2px solid {{isset($row->tag_color)?$row->tag_color:'#ff0000'}};">
                                        <div class="ribbon" style="background: {{isset($row->tag_color)?$row->tag_color:''}};">{{$row->tag}}</div>
                                        <div class="heading">
                                            <h3>{{$row->title}}</h3>
                                        </div>
                                        @if (isset($row->image) && $row->image !=='')
                                           {{-- <div class="heading">
                                            </div>--}}
                                            <img style="border: 1px {{isset($row->tag_color)?$row->tag_color:''}} solid;" src="{{ asset('web/pricing/'. $row->image) }}" alt="{{$row->title}}" title="{{$row->title}}" class="center">
                                        @endif
                                        <div class="features">
                                            {!! $row->description!!}
                                        </div>

                                        <div class="price">
                                            <h4>{{isset($generalSetting->currency_symbol)?$generalSetting->currency_symbol:'$'}} {{{$row->new_price}}} </h4>
                                            <h6 class="center">{{isset($row->per_term)?$row->per_term:''}}</h6>
                                            <h5 class="old">{{isset($generalSetting->currency_symbol)?$generalSetting->currency_symbol:'$'}} {{{$row->old_price}}}</h5>
                                        </div>
                                        <a class="btn btn-block btn-primary" href="{{$row->link}}" target="{{$row->open_in}}">{{isset($row->button_text)?$row->button_text:'BUY NOW'}}</a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

        <div class="block-heading">

        </div>
    </section>

@endsection