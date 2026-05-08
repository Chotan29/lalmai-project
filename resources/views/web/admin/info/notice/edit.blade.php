@extends('web.admin.layouts.master')

@section('css')
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
                            Edit Notice
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="easy-link-menu align-center">
                            <a href="{{ route($base_route) }}" class="btn btn-xs btn-primary">
                                <i class="icon-list"></i>&nbsp;All {{$panel}}
                            </a>

                            <a href="{{ route($base_route.'.add') }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-plus bigger-120"></i>&nbsp;Add {{ $panel }}
                            </a>
                        </div>
                        <hr class="hr-4">
                        @include('includes.validation_error_messages')
                    {!! Form::model($data['row'], ['route' => [$base_route.'.update', encrypt($data['row']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                    {!! Form::hidden('id', $data['row']->id) !!}

                    @include($view_path.'.includes.form')

                    <div class="clearfix form-actions">
                        <div class="align-center">
                            <button class="btn btn-info" type="submit">
                                <i class="fa fa-save bigger-110"></i>
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
    @include('includes.scripts.summarnote')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection
