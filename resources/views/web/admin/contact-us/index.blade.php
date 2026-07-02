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
               <li>
                    <i class="ace-icon fa fa-home"></i>
                    <a href="{{ route('web.admin.dashboard') }}">Home</a>
                </li>

                <li>
                    <a href="{{ route('web.admin.contact-us') }}">Contact List</a>
                </li>
            </ul><!-- .breadcrumb -->


        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Conact List Manager
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        List
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="row">
                        <div class="col-xs-12">
                            @include('includes.flash_messages')
                            {!! Form::open(['route' => $base_route, 'method' => 'GET', 'class' => 'form-horizontal',
                                'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            <div id="search_form">
                                @include($view_path.'.includes.search_form')
                            </div>
                            {!! Form::close() !!}
                                @include($view_path.'.includes.table')
                            </div><!-- /.table-responsive -->
                        </div><!-- /span -->
                    </div><!-- /row -->

                    <div class="hr hr-18 dotted hr-double"></div>


                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div><!-- /.main-content -->

@endsection


@section('js')

    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.delete_confirm')

    <script>
        jQuery(document).ready(function($){
            $("form").submit(function() {
                $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
                return true; // ensure form still submits
            });

            // Un-disable form fields when page loads, in case they click back after submission
            $( "form" ).find( ":input" ).prop( "disabled", false );

        });
    </script>

@endsection