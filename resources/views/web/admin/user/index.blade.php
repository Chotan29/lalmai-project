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
                        List
                    </small>
                </h1>
            </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    @include('admin.user.includes.commanbuttons')
                    @include('admin.user.includes.buttons')
                    @include('includes.flash_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            @include($view_path.'.includes.search_form')
                            {{--@include($view_path.'.includes.search_form')--}}
                        </div>
                        @include($view_path.'.includes.table')
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
    <script>
        $(document).ready(function () {

            $('#filter-btn').click(function () {
                console.log('here');

                var url = '{{ $data['url'] }}';
                var flag = false;
                var name = $('input[name="name"]').val();
                var role = $('select[name="role"]').val();
                var status = $('select[name="status"]').val();

                if (name !== '') {
                    url += '?name=' + name;
                    flag = true;
                }

                if (role !== '' & role >0) {

                    if (flag) {

                        url += '&role=' + role;

                    } else {

                        url += '?role=' + role;
                        flag = true;

                    }
                }

                if (status !== '' ) {

                    if (status !== 'all') {

                        if (flag) {

                            url += '&status=' + status;

                        } else {

                            url += '?status=' + status;

                        }

                    }
                }

                location.href = url;

            });

        });
    </script>

    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('admin.includes.script.dataTable_scripts')
@endsection