@extends('user-student.layouts.master')

@section('css')


@endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        Application
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Edit
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                @include('user-student.application.includes.buttons')
                <div class="col-xs-12 ">
                @include('includes.flash_messages')
                @include('includes.validation_error_messages')
                <!-- PAGE CONTENT BEGINS -->
                    {!! Form::model($data['row'], ['route' => ['user-student.application.update', encrypt($data['row']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                                    'id' => 'validation-form', "enctype" => "multipart/form-data",'onSubmit'=>"return applicationValidation();"]) !!}
                    {!! Form::hidden('id', $data['row']->id) !!}
                    @include($view_path.'.application.includes.form')


                    <div class="clearfix form-actions">
                        <div class="col-md-12 align-right">
                            <button class="btn" type="reset">
                                <i class="fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-info" type="submit" id="assignment-btn">
                                <i class="fa fa-save bigger-110"></i>
                Update Assignment
                            </button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                    <div class="hr hr-18 dotted hr-double"></div>
                </div><!-- /.col -->

            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
    <!-- inline scripts related to this page -->
    @include('application.includes.application-common-script')
    @include('includes.scripts.datepicker_script')
@endsection