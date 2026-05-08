@extends('layouts.master')

@section('css')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
@endsection

@section('content')

    @if(isset($data['registration_setting']) & $data['registration_setting'] != null)

        @if($data['registration_setting']->status=='active' && date('Y-m-d') >= $data['registration_setting']->start_date && $data['registration_setting']->end_date >= date('Y-m-d'))
            <div class="main-content">
            <div class="main-content-inner">
                <div class="page-content">
                    <div class="row">
                        <div class="col-xs-12 ">
                        <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-md-2 col-print-2 align-left">
                                        @if($data['registration_setting']->logo !=='')
                                            <img id=""  src="{{ asset('images/setting/online-registration/'.$data['registration_setting']->logo) }}" height="100" >
                                        @else
                                            <img id=""  src="{{ asset('images/setting/general/'.$generalSetting->logo) }}" height="100" >
                                        @endif
                                </div>

                                <div class="col-md-10 col-print-10 align-center">
                                    <div class="text-center">
                                        <h2 class="no-margin-top text-uppercase" style="font-family: 'Raleway'; font-size: 35px;font-weight: 600;">
                                            {{isset($generalSetting->institute)?$generalSetting->institute:"EduFirm Web Portal Online Registration"}}
                                        </h2>
                                        <h4 class="no-margin-top">
                                            {{isset($generalSetting->address)?$generalSetting->address:""}}, {{isset($generalSetting->phone)?$generalSetting->phone:""}}, {{isset($generalSetting->email)?$generalSetting->email:""}}
                                        </h4>
                                        <h3 class="text-uppercase no-margin-top" style="font-family: 'Raleway'; font-size: 30px;font-weight: 600;">
                                            {{isset($data['registration_setting']->title)?$data['registration_setting']->title:'ONLINE APPLICATION FOR ADMISSION'}}
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            @include('includes.validation_error_messages')
                                @if(isset($data['registration_setting']->registration_guidelines))
                                    <div class="row">
                                        <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <a href="#registraton-guidelines" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                        <i class="ace-icon fa fa-arrow-down"></i> Registration Guidelines
                                                    </a>
                                                </div>

                                                <div class="panel-collapse collapse" id="registraton-guidelines" style="height: 0px;">
                                                    <div class="panel-body">
                                                        {!! $data['registration_setting']->registration_guidelines !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    {!! Form::open(['route' => 'online-registration.registration', 'method' => 'POST','onSubmit' => 'return registrationValidation()', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                                            {{--
                                            <div class="form-group">
                                                                                        {!! Form::label('mobile_1', __('form_fields.student.fields.mobile_1'), ['class' => 'col-sm-1 control-label']) !!}
                                                                                        <div class="col-sm-2">
                                                                                            {!! Form::text('mobile_1', null, ["class" => "form-control border-form input-mask-mobile","required"]) !!}
                                                                                            @include('includes.form_fields_validation_message', ['name' => 'mobile_1'])
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">

                                                                                        {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-1 control-label']) !!}
                                                                                        <div class="col-sm-2">
                                                                                            {!! Form::text('email', null, ["class" => "form-control border-form ","Required"]) !!}
                                                                                            @include('includes.form_fields_validation_message', ['name' => 'email'])
                                                                                        </div>

                                                                                    </div>
                                            --}}
                                    @include($view_path.'.includes.form')
                                        <div class="clearfix form-actions">
                                            <div class="col-md-12 align-center">
                                                <button class="btn btn-primary" type="submit" value="Save" name="add_student" id="add-student">
                                                    <i class="fa fa-save bigger-110"></i>
                                                    Submit
                                                </button>
                                            </div>
                                        </div>

                                    <div class="hr hr-24"></div>
                                {!! Form::close() !!}
                            </div>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div>
        @else
            <div class="main-content">
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row">
                            <div class=" col-md-12 center">
                                <h1 class="">
                                    {!! $data['registration_setting']->registration_close_message !!}
                                </h1>
                                <hr class="hr-2">
                                @if(isset($generalSetting->logo))
                                    <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" width="100px">
                                @endif

                                <h6 class="no-margin-top" style="font-size: 14px">
                                    {{isset($generalSetting->salogan)?$generalSetting->salogan:""}}
                                </h6>
                                <h2 class="no-margin-top text-uppercase" style="font-family: 'Bowlby+One+SC'; font-size: 30px; font-weight: 600">
                                    <strong>{{isset($generalSetting->institute)?$generalSetting->institute:""}}</strong>
                                </h2>
                                <h5 class="no-margin-top" style="font-size: 16px;">
                                    {{isset($generalSetting->address)?$generalSetting->address:""}}, {{isset($generalSetting->phone)?$generalSetting->phone:""}}
                                </h5>
                                <h5 class="no-margin-top" style="font-size: 16px;">
                                    {{isset($generalSetting->email)?$generalSetting->email:""}}, {{isset($generalSetting->website)?$generalSetting->website:""}}
                                </h5>
                            </div>
                        </div>
                    </div><!-- /.page-content -->
                </div>
            </div>
    @endif

    @else
        <div class="main-content">
            <div class="main-content-inner">
                <div class="page-content">
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="center">
                                    <h1 class="">REGISTRATION NOT AVAILABLE AT THIS MOMENT</h1>
                                    <hr class="hr-2">
                                    @if(isset($generalSetting->logo))
                                        <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" width="100px">
                                    @endif

                                        <h6 class="no-margin-top" style="font-size: 14px">
                                            {{isset($generalSetting->salogan)?$generalSetting->salogan:""}}
                                        </h6>
                                        <h2 class="no-margin-top text-uppercase" style="font-family: 'Bowlby+One+SC'; font-size: 30px; font-weight: 600">
                                            <strong>{{isset($generalSetting->institute)?$generalSetting->institute:""}}</strong>
                                        </h2>
                                        <h5 class="no-margin-top" style="font-size: 16px;">
                                            {{isset($generalSetting->address)?$generalSetting->address:""}}, {{isset($generalSetting->phone)?$generalSetting->phone:""}}
                                        </h5>
                                        <h5 class="no-margin-top" style="font-size: 16px;">
                                            {{isset($generalSetting->email)?$generalSetting->email:""}}, {{isset($generalSetting->website)?$generalSetting->website:""}}
                                        </h5>
                                </div>
                            </div>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div>
    @endif

@endsection

@section('js')

    @include('includes.scripts.jquery_validation_scripts')

    </script>
@endsection

