@extends('layouts.master')

@section('css')
    @include('print.includes.print-layout')
    <style>
        .page-header{
            font-size: 16px;
        }
        .table {
            width: 98%;
            /* max-width: 100%; */
            margin-bottom: 10px;
            margin: 5px auto;
            font-size: 8px;
            /*font-weight: 600;*/
        }
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            line-height: 1.05;
            padding: 2px !important;
        }
        table tfoot {
            display: table-row-group;
        }
    </style>
@endsection

@section('content')
    <div class="main-content A4 landscape">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header hidden-print">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('account.includes.buttons')
                    <div class="col-xs-12 ">
                    @include('account.report.includes.buttons')
                    @include('includes.flash_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            @include($view_path.'.includes.search_form')
                            {{--<div class="hr hr-18 dotted hr-double"></div>--}}
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                <div class="col-sm-12 align-right hidden-print">
                    <a href="#" class="btn-primary btn-lg" onclick="window.print();">
                        <i class="ace-icon fa fa-print"></i> Print
                    </a>
                </div>
                <div class="space-32 hidden-print"></div>
                @include('print.includes.institution-detail')
                @if(isset($data))
                    <hr class="hr hr-2">
                    <div class="row align-center">
                        <span class="receipt-copy text-uppercase page-header" style="font-size: 16px;">{{  ViewHelper::getFacultyTitle( $data['faculty'] ) }} - Balance Statement</span>
                    </div>
                    <hr class="hr hr-2">


                @if($data['layout'] == 'feeHead' && $data['flag'] !=='overdue')
                        @include($view_path.'.includes.table-due-head')
                    @else
                        @include($view_path.'.includes.table')
                    @endif
                @endif
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('#filter-btn').click(function () {
                        @include('student.includes.common-script.student_filter_common_script')

                var village = $('select[name="village"]').val();

                if (village !== '' ) {

                    if (village !== '') {

                        if (flag) {

                            url += '&village=' + village;

                        } else {

                            url += '?village=' + village;

                        }

                    }
                }

                var due_status = $('select[name="due_status"]').val();

                if (due_status !== '' ) {

                    if (due_status !== 'all') {

                        if (flag) {

                            url += '&due_status=' + due_status;

                        } else {

                            url += '?due_status=' + due_status;

                        }

                    }
                }

                var layout = $('select[name="layout"]').val();

                if (layout !== '' ) {

                    if (layout !== 'all') {

                        if (flag) {

                            url += '&layout=' + layout;

                        } else {

                            url += '?layout=' + layout;

                        }

                    }
                }

                location.href = url;
            });


            $('#load-fee-html').click(function () {

                $.ajax({
                    type: 'POST',
                    url: '{{ route('account.fees.master.fee-html') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);

                        if (data.error) {
                            //$.notify(data.message, "warning");
                        } else {

                            $('#fee_wrapper').append(data.html);
                            $(document).find('option[value="0"]').attr("value", "");
                            //$(document).find('option[value="0"]').attr("disabled", "disabled");
                            //$.notify(data.message, "success");
                        }
                    }
                });

            });

        });

        function loadSemesters($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.semester_select').html('').append('<option value="0">Select Sem./Sec.</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.semester_select').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });

        }
    </script>
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection