@extends('web.website.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/datepicker.css') }}" />

    <link href="https://fonts.googleapis.com/css?family=Fugaz+One|Lobster|Merienda|Righteous|Black+Ops+One|Gilda+Display" rel="stylesheet">
    <style>
        .form-horizontal .control-label {
            /*padding-top: 5px;
            padding-bottom: 5px;*/
            text-align: right;
            background: #d2d2d696;
            color: #020202;
            /*font-weight: 600;*/
        }

        .label-warning {
            background-color: #0d860b !important;
            font-size: 13px !important;
        }
        input:required {
            border-left: 2px red solid;
        }

        .table .table {
            background-color: transparent !important;
        }

        .table-hover tbody tr {
            background-color: rgba(0, 0, 0, 0.075);
        }

        th {
            font-weight: 600 !important;
            background: #e4e4e7;
        }

        .align-right {
            float: right;
        }

        hr {
            margin-top: 10px;
            margin-bottom: 10px;
            border: 0;
            border-top: 1px solid #eee;
        }

        span.error {
            color: red;
        }

        div.group-header {
            background: #224c8a;
            color: white;
            padding: 3px 18px;
            width: fit-content;
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
                            <h2 class="text-uppercase">{{isset($data['reg_setting']->title)?$data['reg_setting']->title:$generalSetting->site_title}} </h2>
                            <h3 class="text-uppercase">SEARCH / PRINT YOUR APPLICATION </h3>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                            <a href="{{ route('web.online-registration') }}" class="btn btn-sm btn-primary pull-right">
                                <i class="fa fa-plus" aria-hidden="true"></i> New Registration
                            </a>

                        </div>

                        <div class="text">
                            <hr class="hr-double">
                            @include('includes.validation_error_messages')
                            @include('includes.flash_messages')

                            {!! Form::open(['route' => 'web.registration-print', 'method' => 'POST', 'class' => 'form-horizontal',
                            'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                                 @include($view_path.'.includes.find-registration')

                            {!! Form::close() !!}
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>

                        <div style="clear:both"><br></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.inputMask_script')
    <script>
        $(document).ready(function () {
            /*Change Field Value on Capital Letter When Keyup*/
            $(function() {
                $('.upper').keyup(function() {
                    this.value = this.value.toUpperCase();
                });
            });

            /*end capital function*/

        });

    </script>
@endsection







