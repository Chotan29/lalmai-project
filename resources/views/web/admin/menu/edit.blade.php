@extends('web.admin.layouts.master')
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
                        Edit Form
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->

                    <div class="easy-link-menu align-center">
                        <a href="{{ route($base_route) }}" class="btn btn-xs btn-primary">
                            <i class="ace-icon fa fa-list-alt bigger-110"></i>&nbsp;All {{$panel}}
                        </a>
                    </div>
                    <hr class="hr-4">

                    @include('includes.validation_error_messages')

                    {!! Form::model($data['row'], ['route' => [$base_route.'.update', encrypt($data['row']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                    {!! Form::hidden('id', encrypt($data['row']->id)) !!}

                    @include($view_path.'.includes.form')

                    <div class="form-group">
                        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Page</th>
                                    <th>
                                        <button type="button" class="btn btn-xs btn-primary pull-right" id="menu-pages-html">
                                            <i class="fa fa-plus bigger-120"></i> Add Pages
                                        </button>
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="page_value_wrapper">
                                <!-- Option Values -->
                                @if($data['active_pages']->count() > 0)

                                    @foreach($data['active_pages'] as $page)

                                        @include($view_path.'.includes.page_row_edit',[
                                            'active_page' => $page,
                                            'pages' => $data['pages']

                                        ])

                                        @endforeach
                                    @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="clearfix form-actions">
                        <div class="align-center">
                            <button class="btn" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                            <button class="btn btn-info" type="submit">
                                <i class="ace-icon fa fa-save bigger-110"></i>
                                Update
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

    @include('includes.scripts.table_tr_sort')
    {{--@include('includes.scripts.summarnote')--}}

    @include($view_path.'.includes.scripts')

@endsection
