@extends('layouts.master')

@section('css')

@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            {{ $panel }} Add
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('account.includes.buttons')
                    <div class="col-xs-12 ">
                    @include('account.payroll.includes.buttons')
                    @include('includes.flash_messages')
                        <div class="form-horizontal">
                            @include($view_path.'.includes.form')
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>
                            @if (isset($data['staff']) && $data['staff']->count() > 0)
                                @if($data['payrollType'] == 'attendance_payroll')
                                    @include($view_path.'.includes.attendancebase-add')

                                @else
                                    @if (isset($data['row']) && $data['row']->count() > 0)
                                        @include($base_route.'.includes.edit')
                                    @else
                                        @include($base_route.'.includes.add')
                                        @include($view_path.'.includes.table')


                                    @endif
                                @endif
                            @else
                                <div class="alert alert-info">
                                    <button type="button" class="close" data-dismiss="alert">
                                        <i class="ace-icon fa fa-times"></i>
                                    </button>
                                    <strong>Info:</strong>
                                    Please Filter / Search Staff to Add Manual or Attendance Base Payroll.
                                    <br>
                                </div>
                            @endif


                    </div>
                </div><!-- /.row -->

            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')

    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('#filter-btn').click(function () {
                    @include('staff.includes.common.staff_filter_common_script')
                    payrollType = $('input[name="payroll_type"]:checked').val();
                if (payrollType !== '' ) {

                    if (payrollType !== 'all') {

                        if (flag) {

                            url += '&payrollType=' + payrollType;

                        } else {

                            url += '?payrollType=' + payrollType;

                        }

                    }
                }

                location.href = url;
            });


            /*Add Payroll */
            $('#payroll-add-btn').click(function () {
                flag = false;
                var payroll_type = $("#payroll_type").val();

                if(payroll_type == 'manual_payroll'){
                    var payroll_head = $('select[name="payroll_head[]"]').val();


                    if(payroll_head == undefined) {
                        toastr.warning('Please, Add At Least One Payroll Head With Amount Detail.');
                        flag = true;
                        return false;
                    }
                }

                $chkIds = document.getElementsByName('chkIds[]');
                var $chkCount = 0;
                $length = $chkIds.length;

                for(var $i = 0; $i < $length; $i++){
                    if($chkIds[$i].type == 'checkbox' && $chkIds[$i].checked){
                        $chkCount++;
                    }
                }

                if($chkCount <= 0){
                    toastr.warning("Please, Select At Least One Staff Record.");
                    flag = true;
                    return false;
                }

              //  return false;

                //var form = $('#salary_add_form');
                if(flag){
                    $('#salary_add_form').submit(function(){
                        return false;
                    });
                   // return false;
                }

               // return false;

            });
            /*Add Payroll End*/

            $('#load-payroll-html').click(function () {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('account.payroll.master.payroll-html') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);

                        if (data.error) {
                            //$.notify(data.message, "warning");
                        } else {

                            $('#payroll_wrapper').append(data.html);
                            $(document).find('option[value="0"]').attr("value", "");
                            //$(document).find('option[value="0"]').attr("disabled", "disabled");
                            //$.notify(data.message, "success");
                        }
                    }
                });
            });
        });
    </script>
    @include('includes.scripts.jquery_validation_scripts')
    @include('includes.scripts.inputMask_script')
{{--    @include('includes.scripts.table_tr_sort')--}}
    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.dataTable_scripts')
@endsection