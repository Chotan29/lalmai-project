@extends('web.admin.layouts.master')
@section('css')

    <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/colorbox.min.css') }}" />
    @endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
            <script type="text/javascript">
                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
            </script>

            <ul class="breadcrumb">
                @include($view_path.'.includes.breadcrumb-primary')
                <li class="active">View</li>
            </ul><!-- .breadcrumb -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    {{ $panel }} Manager
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        View
                    </small>
                </h1>
            </div><!-- /.page-header -->
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    @include('web.admin.includes.buttons.view-page-button')

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="20%">Column</th>
                                        <th>Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Title</td>
                                        <td>{{ $data['row']->title }}</td>
                                    </tr>

                                    <tr>
                                        <td>Slug</td>
                                        <td>{{ $data['row']->slug }}</td>
                                    </tr>



                                    <tr>
                                        <td>Description</td>
                                        <td>{!! $data['row']->description !!}</td>
                                    </tr>

                                    <tr>
                                        <td>Rank</td>
                                        <td>{{ $data['row']->rank }}</td>
                                    </tr>

                                    <tr>
                                        <td>Create at</td>
                                        <td>{{ $data['row']->created_at }}</td>
                                    </tr>

                                    <tr>
                                        <td>Last updated at</td>
                                        <td>{{ $data['row']->last_updated_at }}</td>
                                    </tr>

                                    <tr>
                                        <td>Created by</td>
                                        <td>{{ $data['row']->created_by }}</td>
                                    </tr>
                                    <tr>
                                        <td>Last updated by</td>
                                        <td>{{ $data['row']->updated_by_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-minier dropdown-toggle {{ $data['row']->status == 'active'?"btn-info":"btn-warning" }}" >
                                                    {{ $data['row']->status == 'active'?"Active":"In Active" }}
                                                    <i class="ace-icon fa fa-caret-down"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route($base_route.'.active', ['id' => encrypt($data['row']->id)]) }}" title="Active"><i class="ace-icon fa fa-check"></i> Active</a>
                                                    </li>

                                                    <li>
                                                        <a href="{{ route($base_route.'.in-active', ['id' => encrypt($data['row']->id)]) }}" title="In-Active"><i class="ace-icon fa fa-remove"></i> InActive</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Images</td>
                                        <td>
                                            @if ($data['row']->image_name)
                                                <img src="{{ asset('web/'.$folder_name.'/'. $data['row']->image_name) }}" class="img-responsive" class="img-responsive">
                                            @else
                                                <p>No image</p>
                                            @endif
                                            @if(isset($data['row']->images))
                                                <hr class="hr-double">
                                                <div>
                                                    <ul class="ace-thumbnails clearfix">
                                                        @foreach($data['row']->images as $galleryImage)
                                                            <li>
                                                                <a href="{{ asset('web/'.$folder_name.'/'. $galleryImage->image) }}" title="{{ $galleryImage->caption }}" data-rel="colorbox">
                                                                    <img alt="{{ $galleryImage->caption }}" src="{{ asset('web/'.$folder_name.'/'. $galleryImage->image) }}" class="img-responsive" width="250"/>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
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
    <!-- page specific plugin scripts -->
    <script src="{{ asset('admin-panel/assets/js/jquery.colorbox.min.js') }}"></script>

    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        jQuery(function($) {
            var $overflow = '';
            var colorbox_params = {
                rel: 'colorbox',
                reposition:true,
                scalePhotos:true,
                scrolling:false,
                previous:'<i class="ace-icon fa fa-arrow-left"></i>',
                next:'<i class="ace-icon fa fa-arrow-right"></i>',
                close:'&times;',
                current:'{current} of {total}',
                maxWidth:'100%',
                maxHeight:'100%',
                onOpen:function(){
                    $overflow = document.body.style.overflow;
                    document.body.style.overflow = 'hidden';
                },
                onClosed:function(){
                    document.body.style.overflow = $overflow;
                },
                onComplete:function(){
                    $.colorbox.resize();
                }
            };

            $('.ace-thumbnails [data-rel="colorbox"]').colorbox(colorbox_params);
            $("#cboxLoadingGraphic").html("<i class='ace-icon fa fa-spinner orange fa-spin'></i>");//let's add a custom loading icon


            $(document).one('ajaxloadstart.page', function(e) {
                $('#colorbox, #cboxOverlay').remove();
            });
        })
    </script>
@endsection