@extends('web.website.layouts.master')
@section('css')
    @endsection
@section('content')
    @if (isset($data['client']) && $data['client']->count() > 0)
        <section class="clients-section">
            <div class="auto-container">
                <div class="title-box">
                    <div class="sec-title light centered">
                        <h2>{{isset($homeSetting->client_title)?$homeSetting->client_title:'Client/Award'}}</h2>
                        <!--div class="sub-title">Text goes here.</div-->
                        <div class="separator">
                            <span class="dott"></span>
                            <span class="dott"></span>
                            <span class="dott"></span>
                        </div>
                    </div>
                </div>
                <div class="sponsors-outer">
                    <!--Sponsors Carousel-->
                    <ul class="sponsors-carousel owl-carousel owl-theme">
                        @foreach($data['client'] as $client)
                            <li class="slide-item">
                                <figure class="image-box">
                                    <a href="{!! $client->link !!}" target="_blank">
                                        <img src="{{ asset('web/client/'.$client->image_name) }}" alt="{!! $client->title !!}">
                                    </a>
                                </figure>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif

@endsection
@section('js')
@endsection







