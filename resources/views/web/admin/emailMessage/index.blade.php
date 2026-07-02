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
                        List
                    </small>

                    <button class="btn btn-xs btn-danger pull-right bulk-action-btn" attr-action-type="delete">
                        <i class="icon-trash bigger-120">&nbsp;Delete</i>
                    </button>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">

                                @include('includes.flash_messages')

                                <a href="{{ route($base_route.'.add') }}" class="btn btn-xs btn-info">
                                    <i class="fa fa-plus bigger-120"></i>&nbsp;Add {{ $panel }}
                                </a>
                                <hr class="hr-double">
                                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="center">
                                            <label>
                                                <input type="checkbox" class="ace" />
                                                <span class="lbl"></span>
                                            </label>
                                        </th>
                                        <th>Title</th>
                                        <th width="40%">Description</th>
                                        <th width="20%">Link</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th class="center">&nbsp;</th>
                                        <th>{!! Form::text('title', request('title'), ['class' => 'from-control']) !!}</th>
                                        <th>{!! Form::text('description', request('description'), ['class' => 'from-control']) !!}</th>
                                        <th>{!! Form::text('link', request('link'), ['class' => 'from-control']) !!}</th>
                                        <th>
                                            <button class="btn btn-xs btn-info" id="filter-btn">
                                                <i class="icon-filter bigger-120">&nbsp;Filter</i>
                                            </button>
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody>


                                    @if (isset($data['rows']) && $data['rows']->count() > 0)

                                        {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}

                                        @foreach($data['rows'] as $row)

                                            <tr>
                                                <td class="center">
                                                    <label>
                                                        <input type="checkbox" name="chkIds[]" value="{{ encrypt($row->id) }}" class="ace" />
                                                        <span class="lbl"></span>
                                                    </label>
                                                </td>

                                                <td>
                                                    {{ $row->title }}<br>
                                                    <span class="label label-sm label-primary">{{ date('jS M, Y', strtotime($row->created_at)) }}</span>
                                                    <hr>
                                                    @if ($row->image)
                                                        <img src="{{ asset('web/'.$folder_name.'/'.$row->image) }}" width="150">
                                                    @else
                                                        <p>No image</p>
                                                    @endif
                                                </td>
                                                <td>  {!! $row->description !!}<br></td>
                                                <td>  <a href="{{ $row->link }}" target="_blank">{{ $row->link }}</a></td>
                                                <td>
                                                    <div class=" btn-group">

                                                        <a href="{{ route($base_route.'.view', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-success">
                                                            <i class="ace-icon fa fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route($base_route.'.delete', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-danger bootbox-confirm">
                                                            <i class="ace-icon fa fa-trash"></i>
                                                        </a>

                                                    </div>

                                                </td>
                                            </tr>

                                        @endforeach

                                        {!! Form::close() !!}

                                        <tr>
                                            <td colspan="7">{!! $data['rows']->appends($data['filter_query'])->links() !!}</td>
                                        </tr>

                                    @else

                                        <tr><td colspan="7">No data fount.</td></tr>

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

    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.delete_confirm')

    <script>
        $(document).ready(function () {

            $('#filter-btn').click(function () {

                var url = '{{ $data['url'] }}';
                var flag = false;
                var title = $('input[name="title"]').val();
                var description = $('input[name="description"]').val();
                var link = $('input[name="link"]').val();

                if (title !== '') {
                    url += '?title=' + title;
                    flag = true;
                }

                if (description !== '') {

                    if (flag) {

                        url += '&description=' + description;

                    } else {

                        url += '?description=' + description;
                        flag = true;
                    }
                }

                if (link !== '') {

                    if (flag) {

                        url += '&link=' + link;

                    } else {

                        url += '?link=' + link;
                        flag = true;
                    }
                }

                location.href = url;

            });

        });
    </script>

@endsection