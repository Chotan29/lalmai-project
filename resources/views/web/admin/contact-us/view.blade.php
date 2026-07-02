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
                Conact List Manager
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        View
                    </small>
                </h1>
            </div><!-- /.page-header -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="easy-link-menu align-center">
                        <a href="{{ route($base_route) }}" class="btn btn-xs btn-primary">
                            <i class="icon-list"></i>&nbsp;All {{$panel}}
                        </a>
                    </div>
                    <hr class="hr-4">
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
                                        <td>Name</td>
                                        <td>{{ $data['row']->name }}</td>
                                    </tr>

                                    <tr>
                                        <td>Date</td>
                                        <td>{{ $data['row']->created_at }}</td>
                                    </tr>

                                    <tr>
                                        <td>Email</td>
                                        <td>{{ $data['row']->email }}</td>
                                    </tr>

                                    <tr>
                                        <td>Contact Number</td>
                                        <td>{{ $data['row']->contact_number }}</td>
                                    </tr>

                                    <tr>
                                        <td>Address</td>
                                        <td>{{ $data['row']->address }}</td>
                                    </tr>

                                    <tr>
                                        <td>Message</td>
                                        <td>{!! $data['row']->message  !!} </td>
                                    </tr>

                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            @if ($data['row']->view_status == 0)
                                                <span class="label label-sm label-success">Un-Read</span>
                                            @else
                                                <span class="label label-sm label-warning">Read</span>
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

@endsection