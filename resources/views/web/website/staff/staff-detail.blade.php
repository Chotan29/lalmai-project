@extends('web.website.layouts.master')
@section('header_js')
    <script src="https://www.google.com/recaptcha/api.js"></script>
@endsection
@section('css')
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('website/plugins/ContactFrom_v17/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('website/plugins/ContactFrom_v17/css/main.css') }}">
    <!--===============================================================================================-->
    <style>
        .contact-section {
            position: relative;
            padding: 40px 0px 40px;
        }

        .container-contact100{
            margin-bottom: 25px;
        }
        img.staff-detail-profile-image {
            width: 200px;
            height: 200px;
            border: 3px #ff9a04 solid;
            box-shadow: 8px 6px 10px black;
        }
        .info-name{
            color: #ff9a04;
            font-size: 22px;
            font-weight: 700;
            padding: 15px 0px 5px 0px;
        }
        .info-position{
            color: white;
            font-size: 18px;
            font-weight: 700;
            padding: 5px 0px 50px 0px;
        }

        .sec-title.light h2.services-heading {
            color: #8b9192!important;
            font-size: 50px !important;
            text-decoration: overline;
            text-transform: capitalize;
            border-block-end: 4px #8b9192 solid;
            margin-bottom: 50px !important;
        }

        .sec-title.light h2.services-heading:hover {
            color: white !important;
            font-size: 50px !important;
            text-decoration: overline;
            text-transform: capitalize;
            border-block-end: 4px #8b9192 solid;
            margin-bottom: 50px !important;
        }

        .hvr-bounce-to-top:before {
            background: #ff9a04 ;
        }
        .list-style-two li:first-child {
            padding-bottom: 28px;
        }

        .list-style-two li {
            position: relative;
            padding-left: 60px;
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 0.5px solid #eeeeee9e;
        }

        .list-style-two li .icon{
            color: #f8f9fa;
        }
        .list-style-two li .text-info {
            position: relative;
            color: #525252;
            line-height: 1em;
            margin-bottom: 8px;
            font-size: 18px;
            font-weight: 400 !important;
        }

        .text-info {
            color: white !important;
            font-size: 24px;
            font-weight: 400;
        }

        .list-style-two li h3 {
            position: relative;
            color: #8b9192;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.6em;
            margin-bottom: 4px;
        }

        .list-style-two li .info-number {
            color: #ff9a04;
            font-size: 18px;
            font-weight: 700;
        }

        .social-icon-two li a {
            position: relative;
            color: #d2d2d2;
            font-size: 25px;
        }

        .contact100-form-btn {
            background: #2460B9;
        }

        .contact100-form-btn:hover {
            background: #ff9a04;
        }

        @media only screen and (max-width: 767px) {
            .services-block-three .inner, .list-style-two li {
                padding-left: 0px !important;
                text-align: center;
            }

            [class^="flaticon-"]:before, [class*=" flaticon-"]:before, [class^="flaticon-"]:after, [class*=" flaticon-"]:after {
                margin-left: 0px;
            }
        }
    </style>
@endsection
@section('content')

    <div class="contact-section">
        <div class="auto-container">
            <div class="row clearfix">
               {{-- <div class="sec-title light centered">
                    <h2 class="services-heading">
                        {{isset($homeSetting->services_title)?$homeSetting->services_title:'Send Us A Message'}}
                    </h2>
                </div>--}}
                <!--Form Column-->
                <div class="container-contact100">
                    <div class="wrap-contact100">
                        {{--<form class="contact100-form validate-form">--}}
                        {!! Form::open(['route' => 'web.staffs.message', 'method' => 'POST', 'class' => 'contact100-form validate-form',
                          'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            <span class="contact100-form-title">
                                Send A Message
                            </span>
                        <div>
                            @include('includes.validation_error_messages')
                            @include('includes.flash_messages')
                        </div>

                            <input id="id" class="input100" type="hidden" name="staff" value="{{encrypt($data['staffs']->id)}}">
                            <input id="staff_name" class="input100" type="hidden" name="staff_name" value="{{ isset($data['staffs']->name)?$data['staffs']->name:"Sir" }}">
                            <input id="email" class="input100" type="hidden" name="send_to_email" value="{{$data['staffs']->email}}">
                            <label class="label-input100" for="first-name"> Tell us your name *  </label>
                            <div class="wrap-input100 rs1-wrap-input100 validate-input" data-validate="Type first name">
                                <input id="first-name" class="input100" type="text" name="first_name" placeholder="First name" required>
                                <span class="focus-input100"></span>
                            </div>
                            <div class="wrap-input100 rs2-wrap-input100 validate-input" data-validate="Type last name">
                                <input class="input100" type="text" name="last_name" placeholder="Last name" required>
                                <span class="focus-input100"></span>
                            </div>
                            <div>
                                @include('includes.form_fields_validation_message', ['name' => 'first_name'])
                                @include('includes.form_fields_validation_message', ['name' => 'last_name'])
                            </div>
                            <label class="label-input100" for="email">Enter your email * </label>
                            <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                                <input id="email" class="input100" type="email" name="email" placeholder="Eg. example@email.com" required>
                                <span class="focus-input100"></span>
                            </div>
                            @include('includes.form_fields_validation_message', ['name' => 'email'])

                            <label class="label-input100" for="phone">Enter phone number</label>
                            <div class="wrap-input100">
                                <input id="phone" class="input100" type="text" name="phone" placeholder="Eg. +1 800 000000">
                                <span class="focus-input100"></span>
                            </div>

                            <label class="label-input100" for="message">Message * </label>
                            <div class="wrap-input100 validate-input" data-validate = "Message is required">
                                <textarea id="message" class="input100" name="message" placeholder="Write us a message" required></textarea>
                                <span class="focus-input100"></span>
                            </div>
                            @include('includes.form_fields_validation_message', ['name' => 'message'])

                            <label class="g-recaptcha" data-sitekey="{{env('RECAPTCHA_SITE_KEY')}}"> </label>

                            <div class="container-contact100-form-btn">
                                <button class="contact100-form-btn">
                                    Send Message
                                </button>
                            </div>
                        {!! Form::close() !!}
                        {{--</form>--}}

                        <div class="contact100-more flex-col-c-m" style="background-image: url({{ asset('website/plugins/ContactFrom_v17/images/bg-01.jpg') }});">
                           {{-- @if(isset($data['staffs']->contact_title))
                            <div class="sec-title light centered">
                                --}}{{--<div class="sec-title light centered hvr-grow">--}}{{--
                                <h2 class="services-heading  hvr-bounce-to-top">
                                    {{$data['staffs']->contact_title}}
                                </h2>
                            </div>
                            @endif--}}
                            @if ($data['staffs']->profile_image)
                                <img src="{{ asset('web/staff/'.$data['staffs']->profile_image) }}" class="staff-detail-profile-image"  alt="{{ $data['staffs']->name }}">
                            @endif
                            <div class="info-name">{{ isset($data['staffs']->name)?$data['staffs']->name:"" }}</div>
                            <div class="info-position">{{ isset($data['staffs']->position)?$data['staffs']->position:"" }}</div>

                            <ul class="list-style-two">
                               <li>
                                    <span class="icon flaticon-pin"></span>
                                    <div class="text-info">Address</div>
                                    <h3>{{ isset($data['staffs']->address)?$data['staffs']->address:"" }}</h3>
                               </li>

                               <li>
                                    <span class="icon flaticon-phone"></span>
                                    <div class="text-info">Contact Number</div>
                                    <div class="info-number">{{ isset($data['staffs']->cell_1)?$data['staffs']->cell_1:"" }}{{ isset($data['staffs']->cell_2)?', '.$data['staffs']->cell_2:"" }}</div>
                               </li>

                               <li>

                                    <span class="icon flaticon-fax"></span>
                                    <div class="text-info">Telephone</div>
                                    <h3>{{ isset($data['staffs']->tel)?$data['staffs']->tel:"" }}</h3>
                               </li>
                               <li>
                                    <span class="icon flaticon-mail"></span>
                                    <div class="text-info">Send Email</div>
                                    <h3>{{ isset($data['staffs']->email)?$data['staffs']->email:"" }}</h3>
                               </li>
                            </ul>

                            <ul class="social-icon-two center">
                                <li>
                                    <a href="{{ isset($data['staffs']->twitter_url)?$data['staffs']->twitter_url:"#" }}" target="_blank">
                                        <span class="fa fa-twitter"></span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ isset($data['staffs']->linkedIn_url)?$data['staffs']->linkedIn_url:"#" }}" target="_blank">
                                        <span class="fa fa-linkedin"></span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ isset($data['staffs']->facebook_url)?$data['staffs']->facebook_url:"#" }}" target="_blank">
                                        <span class="fa fa-facebook"></span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ isset($data['staffs']->instagram_url)?$data['staffs']->instagram_url:"#" }}" target="_blank">
                                        <span class="fa fa-instagram"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ isset($data['staffs']->whatsApp_url)?$data['staffs']->whatsApp_url:"#" }}" target="_blank">
                                        <span class="fa fa-whatsapp"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ isset($data['staffs']->skype_url)?$data['staffs']->skype_url:"#" }}" target="_blank">
                                        <span class="fa fa-skype"></span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ isset($data['staffs']->pinterest_url)?$data['staffs']->pinterest_url:"#" }}" target="_blank">
                                        <span class="fa fa-pinterest"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Contact Section-->
    </div>


@endsection

@section('js')
    {{--<!--===============================================================================================-->
        <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
        <!--===============================================================================================-->
        <script src="vendor/animsition/js/animsition.min.js"></script>
        <!--===============================================================================================-->
        <script src="vendor/bootstrap/js/popper.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <!--===============================================================================================-->
        <script src="vendor/select2/select2.min.js"></script>
        <script>
            $(".selection-2").select2({
                minimumResultsForSearch: 20,
                dropdownParent: $('#dropDownSelect1')
            });
        </script>
        <!--===============================================================================================-->
        <script src="vendor/daterangepicker/moment.min.js"></script>
        <script src="vendor/daterangepicker/daterangepicker.js"></script>
        <!--===============================================================================================-->
        <script src="vendor/countdowntime/countdowntime.js"></script>
        <!--===============================================================================================-->
        <script src="js/main.js"></script>--}}
@endsection