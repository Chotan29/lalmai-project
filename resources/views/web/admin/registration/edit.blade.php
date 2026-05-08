@extends('web.admin.layouts.master')
@section('content')
    <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
            <script type="text/javascript">
                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
            </script>
            <ul class="breadcrumb">
                @include($view_path.'.includes.breadcrumb-primary')
            </ul><!-- .breadcrumb -->
        </div>
        <div class="page-content">
            <div class="page-header">
                <h1>
                    {{ $panel }} Manager
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Edit Form
                    </small>
                </h1>
            </div><!-- /.page-header -->
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    @include('web.admin.includes.buttons.edit-page-button')
                    @include('includes.validation_error_messages')
                    @include('includes.flash_messages')

                    {!! Form::model($data['row'], ['route' => [$base_route.'.update', encrypt($data['row']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                    {!! Form::hidden('id', encrypt($data['row']->id)) !!}

                    @include($view_path.'.includes.form')

                    <div class="clearfix form-actions">
                        <div class="align-center">
                            <button class="btn" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                            <button class="btn btn-info" type="submit">
                                <i class="ace-icon fa fa-save bigger-110"></i>
                                Update
                            </button>
                        </div>
                    </div>

                    <div class="hr hr-24"></div>

                    {!! Form::close() !!}

                    <div class="hr hr-18 dotted hr-double"></div>

                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div><!-- /.main-content -->

@endsection
@section('js')
    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.summarnote')
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


            $('#load-academicinfo-html').click(function () {

                $.ajax({
                    type: 'POST',
                    url: '{{ route('web.admin.registration.academicInfo-html') }}',
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
                    url: '{{ route('web.admin.registration.workExperience-html') }}',
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
