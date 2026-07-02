@extends('web.admin.layouts.master')
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
                                            <td> Profile Image</td>
                                            <td>
                                                @if ($data['row']->profile_image)
                                                    <img class="profile-picture" src="{{ asset('web/'.$folder_name.'/'.$data['row']->profile_image) }}" width="100">
                                                @else
                                                    <p>No image</p>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Name</td>
                                            <td>{{ $data['row']->name }}</td>
                                        </tr>

                                        <tr>
                                            <td>Position</td>
                                            <td>{{ $data['row']->position }}</td>
                                        </tr>

                                        <tr>
                                            <td>Address</td>
                                            <td>{{ $data['row']->address }}</td>
                                        </tr>

                                        <tr>
                                            <td>Contact</td>
                                            <td>{{ $data['row']->tel }}, {{ $data['row']->cell_1 }}, {{ $data['row']->cell_2 }}</td>
                                        </tr>

                                        <tr>
                                            <td>Email</td>
                                            <td>{!! $data['row']->email !!}</td>
                                        </tr>

                                        <tr>
                                            <td>Description</td>
                                            <td>{!! $data['row']->description !!}</td>
                                        </tr>

                                        <tr>
                                            <td>Social</td>
                                            <td>
                                                <ul class="social-icon-two list-inline">
                                                    <li>
                                                        <a href="{{ isset($data['row']->twitter_url)?$data['row']->twitter_url:"#" }}" target="_blank">
                                                            <span class="fa fa-twitter"></span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ isset($data['row']->linkedIn_url)?$data['row']->linkedIn_url:"#" }}" target="_blank">
                                                            <span class="fa fa-linkedin"></span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ isset($data['row']->facebook_url)?$data['row']->facebook_url:"#" }}" target="_blank">
                                                            <span class="fa fa-facebook"></span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ isset($data['row']->instagram_url)?$data['row']->instagram_url:"#" }}" target="_blank">
                                                            <span class="fa fa-instagram"></span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ isset($data['row']->whatsapp_url)?$data['row']->whatsapp_url:"#" }}" target="_blank">
                                                            <span class="fa fa-whatsapp"></span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ isset($data['row']->skype_url)?$data['row']->skype_url:"#" }}" target="_blank">
                                                            <span class="fa fa-skype"></span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ isset($data['row']->pinterest_url)?$data['row']->pinterest_url:"#" }}" target="_blank">
                                                            <span class="fa fa-pinterest"></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>Create at</td>
                                            <td>{{ $data['row']->created_at }}</td>
                                        </tr>

                                        <tr>
                                            <td>Last updated at</td>
                                            <td>{{ $data['row']->updated_at }}</td>
                                        </tr>

                                        <tr>
                                            <td>Created by</td>
                                            <td>{{ $data['row']->created_by_name }}</td>
                                        </tr>

                                        <tr>
                                            <td>Last updated by</td>
                                            <td>{{ $data['row']->updated_by_name }}</td>
                                        </tr>

                                        <tr>
                                            <td>Slug</td>
                                            <td>{!! $data['row']->slug !!}</td>
                                        </tr>

                                        <tr>
                                            <td>Rank</td>
                                            <td>{!! $data['row']->rank !!}</td>
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

@endsection