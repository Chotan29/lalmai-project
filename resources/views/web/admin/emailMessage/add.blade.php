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
            </ul><!-- .breadcrumb -->

        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    {{ $panel }}
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Create
                    </small>
                </h1>
            </div><!-- /.page-header -->


            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->

                    @include('includes.validation_error_messages')

                    @include('web.admin.includes.buttons.add-page-button')
                    @include('includes.validation_error_messages')


                    {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                    @include($view_path.'.includes.form')

                    <div class="clearfix form-actions">
                        <div class="col-md-offset-3 col-md-9">
                            <button class="btn" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                            <button class="btn btn-info" type="submit">
                                <i class="icon-envelope bigger-110"></i>
                                Send & Store
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
    @include('admin.includes.script.jquery_validation_scripts')

    <script>

        $(document).ready(function () {


            );
        });
    </script>



    @endsection

