@extends('web.admin.layouts.master')

@section('css')

@endsection

@section('content')

    <div class="main-content">

        <div class="breadcrumbs" id="breadcrumbs">
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
                        List
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">

                                @include('includes.flash_messages')

                                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Rank</th>
                                            <th>{{ __('common.status')}}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($data['rows']) && $data['rows']->count() > 0)

                                            @foreach($data['rows'] as $row)

                                                <tr>
                                                    <td>
                                                        {{ $row->title }}
                                                    </td>

                                                    <td>{{ $row->rank }}</td>

                                                    <td>
                                                        @if ($row->status == 'active')
                                                            <span class="label label-sm label-success">Active</span>
                                                        @else
                                                            <span class="label label-sm label-warning">Inactive</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <div class=" btn-group">

                                                            <a href="{{ route($base_route.'.edit', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-info">
                                                                <i class="ace-icon fa fa-edit"></i>
                                                            </a>

                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach
                                            {!! Form::close() !!}
                                            <tr>
                                                <td colspan="7"><span class="pull-right">{!! $data['rows']->links() !!}</span></td>
                                            </tr>
                                        @else
                                            <tr><td colspan="7">No data found.</td></tr>
                                        @endif
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