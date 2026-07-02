@extends('layouts.master')

@section('css')

@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Create
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('application.includes.buttons')
                    <div class="col-xs-12 ">
                    @include('includes.flash_messages')
                    @include('includes.validation_error_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                    'id' => 'validation-form', "enctype" => "multipart/form-data",'onSubmit'=>"return applicationValidation();"]) !!}

                                @include($view_path.'.includes.form')

                                <div class="clearfix form-actions">
                                    <div class="col-md-12 align-center">
                                        <button class="btn" type="reset">
                                            <i class="fa fa-undo bigger-110"></i>
                                            Reset
                                        </button>

                                        <button class="btn btn-primary" type="submit" value="Save" name="add_application" id="add-application">
                                            <i class="fa fa-save bigger-110"></i>
                                            Submit
                                        </button>

                                        <button class="btn btn-success" type="submit" value="Save" name="add_application_another" id="add-application-another">
                                            <i class="fa fa-save bigger-110"></i>
                                            <i class="fa fa-plus bigger-110"></i>
                                            Submit And Add Another
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