@extends('web.admin.layouts.master')
@section('css')
@endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
            <script type="text/javascript">
                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
            </script>

            <ul class="breadcrumb">
                @include($view_path.'.includes.breadcrumb-primary')
                <li class="active">List</li>
            </ul><!-- .breadcrumb -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    {{ $panel }} Manager
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        List
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="easy-link-menu align-center">
                        <a href="{{ route($base_route) }}" class="btn btn-xs btn-primary">
                            <i class="ace-icon fa fa-list"></i> All {{ $panel }}
                        </a>
                    </div>
                    <hr class="hr-double">
                    <div class="row">
                        <div class="col-xs-12">
                        @include('includes.flash_messages')
                         @include('includes.validation_error_messages')
                         <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            {!! Form::open(['route' => $base_route.'.bulk.import', 'method' => 'POST', 'class' => 'form-horizontal',
                             'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            <hr>
                            <a href="{{ asset('assets/csv-template/subscriber-import.csv') }}" target="_blank" class="easy-link-menu"><h3><i class="fa fa-download"></i> CSV Template for Bulk Subscriber Import</h3></a>
                            <hr>
                            <div class="form-group">
                                {!! Form::label('file', 'File', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-4">
                                    {!! Form::file('file', null, ["class" => "form-control border-form", "required"]) !!}
                                    @include('includes.form_fields_validation_message', ['name' => 'file'])
                                </div>
                            </div>

                            <div class="clearfix form-actions">
                                <div class="col-md-12 align-center">
                                    <button class="btn btn-info" type="submit" id="filter-btn">
                                        <i class="fa fa-upload"></i>
                                        Import
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

@endsection