@extends('web.website.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/datepicker.css') }}" />

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
                @if($data['periodValid'])
                    @if(isset($data['reg_setting']->status) && $data['reg_setting']->status == 'active')

                    <div class="inner-column">
                        <div class="sec-title light centered">
                            <h2 class="text-uppercase">{{isset($data['reg_setting']->title)?$data['reg_setting']->title:$generalSetting->site_title}} </h2>
                            <h3 class="text-uppercase">{{isset($data['reg_setting']->sub_title)?$data['reg_setting']->sub_title:'ONLINE APPLICATION'}} </h3>
                            <div class="separator">
                                <span class="dott"></span>
                                <span class="dott"></span>
                                <span class="dott"></span>
                            </div>
                            <a href="{{ route('web.find-registration') }}" class="btn btn-sm btn-primary pull-right">
                                <i class="fa fa-print" aria-hidden="true"></i> Print Registration
                            </a>
                        </div>

                        <div class="text">
                            @include('includes.validation_error_messages')
                            @include('includes.flash_messages')
                            <hr class="hr-double">
                                {!! Form::open(['route' => 'web.registration.store', 'method' => 'POST', 'class' => 'form-horizontal',
                                'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                                @include($view_path.'.includes.form')

                                <div class="clearfix form-actions">
                                    <div class="align-right">
                                        <button class="btn" type="reset">
                                            <i class="icon-undo bigger-110"></i>
                                            Reset
                                        </button>
                                        <button class="btn btn-info" type="submit">
                                            <i class="icon-ok bigger-110"></i>
                                            Submit
                                        </button>
                                    </div>
                                </div>
                                <hr class="hr-double">
                            {!! Form::close() !!}
                        </div>
                        <div style="clear:both"><br></div>
                    </div>
                    @endif
                @else
                    @include('includes.flash_messages')
                @endif
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
            //date
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!

            var yyyy = today.getFullYear();
            if(dd<10){
                dd='0'+dd;
            }
            if(mm<10){
                mm='0'+mm;
            }
            var today = dd +'-'+mm+'-'+ yyyy;
            $("#reg_date").val( today );
            $(".reg_date").val( today );
            /*enddate*/


            $('#load-academicinfo-html').click(function () {

                $.ajax({
                    type: 'POST',
                    url: '{{ route('web.registration.academicInfo-html') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);

                        if (data.error) {
                            //$.notify(data.message, "warning");
                        } else {

                            $('#academicInfo_wrapper').append(data.html);
                            //$(document).find('option[value="0"]').attr("value", "");
                        }
                    }
                });

            });

            $('#load-workexperience-html').click(function () {

                $.ajax({
                    type: 'POST',
                    url: '{{ route('web.registration.workExperience-html') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);

                        if (data.error) {
                            //$.notify(data.message, "warning");
                        } else {

                            $('#workExperience_wrapper').append(data.html);
                            //$(document).find('option[value="0"]').attr("value", "");
                        }
                    }
                });

            });


        });

        /*copy permanent address on temporary address*/
        function CopyAddress(f) {
            if(f.mailing_address_copier.checked == true) {
                f.mailing_address.value = f.address.value;
                f.mailing_tel.value = f.tel.value;
                f.mailing_cell_1.value = f.cell_1.value;
                f.mailing_cell_2.value = f.cell_2.value;
                f.mailing_email.value = f.email.value;
            }
        }

    </script>
@endsection







