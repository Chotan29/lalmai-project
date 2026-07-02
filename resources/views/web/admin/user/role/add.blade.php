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
                        Add
                    </small>
                </h1>
            </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    @include('admin.user.includes.commanbuttons')
                    @include($view_path.'.includes.buttons')
                    <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')

                        {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                        @include($view_path.'.includes.form')

                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-info" type="submit">
                                    <i class="ace-icon fa fa-undo bigger-110"></i>
                                    Create
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

    @include('admin.includes.script.jquery_validation_scripts')
    <script src="{{ asset('assets/js/notify.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('table th input:checkbox').on('click' , function(){
                var that = this;
                $(this).closest('table').find('input:checkbox')
                    .each(function(){
                        this.checked = that.checked;
                        $(this).closest('tr').toggleClass('selected');
                    });

            });

            $('.group').on('click' , function(){
                var that = this;
                $(this).closest('tr').find('input:checkbox')
                    .each(function(){
                        this.checked = that.checked;
                        $(this).closest('tr').toggleClass('selected');
                    });

            });


        });
    </script>

@endsection
