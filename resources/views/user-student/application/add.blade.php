@extends('user-student.layouts.master')

@section('css')


@endsection

@section('content')
    <div class="main-content">
        <div class="main-content">
            <div class="main-content-inner">
                <div class="page-content">
                    @include('layouts.includes.template_setting')
                    <div class="page-header">
                        <h1>
                            Application
                            <small>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                Add
                            </small>
                        </h1>
                    </div><!-- /.page-header -->

                <div class="row">
                    @include('user-student.application.includes.buttons')
                    <div class="col-xs-12 ">
                    @include('includes.flash_messages')
                    @include('includes.validation_error_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            {!! Form::open(['route' => 'user-student.application.store', 'method' => 'POST', 'class' => 'form-horizontal',
                    'id' => 'validation-form', "enctype" => "multipart/form-data",'onSubmit'=>"return applicationValidation();"]) !!}

                                @include('application.includes.form')

                            <div class="clearfix form-actions">
                                <div class="col-md-12 align-center">
                                    <button class="btn" type="reset">
                                        <i class="fa fa-undo bigger-110"></i>
                                        Reset
                                    </button>

                                    <button class="btn btn-primary" type="submit" value="Save" name="add_assignment" id="add-application">
                                        <i class="fa fa-save bigger-110"></i>
                                        Save
                                    </button>
                                </div>
                            </div>

                            <div class="hr hr-18 dotted hr-double"></div>
                            {!! Form::close() !!}
                        </div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->

            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    <!-- inline scripts related to this page -->
    @include('application.includes.application-common-script')
    @include('includes.scripts.datepicker_script')
@endsection