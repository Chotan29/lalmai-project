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
                    CMS User Guide
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Read The Documentation to Be a  Better CMS User
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="tabbable">
                        <ul class="nav nav-tabs padding-18 tab-size-bigger" id="myTab">
                            <li class="active">
                                <a data-toggle="tab" href="#faq-tab-1">
                                    <i class="blue ace-icon fa fa-question bigger-120"></i>
                                    General Info
                                </a>
                            </li>  
                            <li>
                                <a data-toggle="tab" href="#faq-tab-2">
                                    <i class="red ace-icon fa fa-youtube-play bigger-120"></i>
                                    CMS User Helper Video
                                </a>
                            </li>                              
                        </ul>

                        <div class="tab-content no-border padding-24">
                            <div id="faq-tab-1" class="tab-pane fade in active">
                                    <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <a href="#predefine-page" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                    <i class="ace-icon fa fa-arrow-down"></i>
                                                    Pre Define Page Links &amp;Layouts
                                                </a>
                                            </div>

                                            <div class="panel-collapse collapse" id="predefine-page" style="height: 0px;">
                                                <div class="panel-body">
                                                    @include($view_path.'.includes.general.page_links')
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <a href="#generalsetting" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                    <i class="ace-icon fa fa-arrow-down"></i>
                                                    &nbsp;
                                                    General Setting
                                                </a>
                                            </div>

                                            <div class="panel-collapse collapse" id="generalsetting" style="height: 0px;">
                                                <div class="panel-body">
                                                    @include($view_path.'.includes.image_size.generalsetting')
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <a href="#homepage" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                    <i class="ace-icon fa fa-arrow-down"></i>
                                                    &nbsp;
                                                    Page Setting
                                                </a>
                                            </div>

                                            <div class="panel-collapse collapse" id="homepage" style="height: 0px;">
                                                <div class="panel-body">
                                                    @include($view_path.'.includes.image_size.homepagesetting')
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <a href="#aboutus" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                    <i class="ace-icon fa fa-arrow-down"></i>
                                                    &nbsp;
                                                    Others
                                                </a>
                                            </div>

                                            <div class="panel-collapse collapse" id="aboutus" style="height: 0px;">
                                                <div class="panel-body">
                                                    @include($view_path.'.includes.image_size.others')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div id="faq-tab-2" class="tab-pane fade in">
                                <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/videoseries?list=PLCtD_CGPAQJ3MgY_iID3H9MZpLLbKRCb9" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                                                       
                        </div>
                    </div>

                    <!-- PAGE CONTENT ENDS -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->

@endsection


@section('js')
@endsection