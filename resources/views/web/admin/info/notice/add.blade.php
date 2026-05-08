@extends('web.admin.layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/datepicker.css') }}" />
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Create User
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                   {{-- @include('info.includes.buttons')--}}
                    <div class="col-xs-12 ">
                    @include('web.admin.includes.buttons.add-page-button')
                    @include('includes.validation_error_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                        @include($view_path.'.includes.form')


                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn" type="reset">
                                    <i class="fa fa-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-primary" type="submit" value="Save" name="add_notice" id="add-notice">
                                    <i class="fa fa-save bigger-110"></i>
                                    Create
                                </button>

                                <button class="btn btn-success" type="submit" value="Create" name="add_notice_another" id="add-notice-another">
                                    <i class="fa fa-save bigger-110"></i>
                                    <i class="fa fa-plus bigger-110"></i>
                                    Create And Add Another
                                </button>
                            </div>
                        </div>

                        <div class="hr hr-24"></div>

                        {!! Form::close() !!}

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    @include('includes.scripts.summarnote')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection
