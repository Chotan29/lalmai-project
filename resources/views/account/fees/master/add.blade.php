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
                            Add Fees
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('account.includes.buttons')
                    <div class="col-xs-12 ">
                        @include('account.fees.includes.buttons')
                        @include('includes.flash_messages')
                        @if (isset($data['row']) && $data['row']->count() > 0)
                            @include($base_route.'.includes.edit')
                        @else
                        <!-- PAGE CONTENT BEGINS -->
                            <div class="form-horizontal">
                                @include($view_path.'.includes.form')
                                <div class="hr hr-18 dotted hr-double"></div>
                            </div>
                            @include($base_route.'.includes.add')
                            @include($view_path.'.includes.table')
                        @endif
                    </div><!-- /.col -->
                </div><!-- /.row -->

            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        function initFeeDatePickers(scope) {
            var $scope = scope ? $(scope) : $(document);

            $scope.find('.date-picker').each(function () {
                var $input = $(this);

                if ($input.data('datepicker')) {
                    $input.datepicker('destroy');
                }

                $input.datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'yyyy-mm-dd',
                    orientation: 'bottom'
                });
            });
        }

        $(document).ready(function () {
            initFeeDatePickers(document);

            $(document).on('focus click', '.date-picker', function () {
                var $input = $(this);

                if (!$input.data('datepicker')) {
                    initFeeDatePickers($input.closest('tr').length ? $input.closest('tr') : document);
                }

                $input.datepicker('show');
            });

            $(document).on('change', 'select.feeHead', function () {
                setAmount(this);
            });

            $('#filter-btn').click(function () {
                @include('student.includes.common-script.student_filter_common_script')

                var facility = $('select[name="facility"]').val();

                if (facility >0) {
                    if (flag) {
                        url += '&facility=' + facility;
                    } else {
                        url += '?facility=' + facility;
                        flag = true;
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
                            initFeeDatePickers($('#fee_wrapper'));
                        }
                    }
                });

            });

            /*Add Fees */
            $('#fee-add-btn').click(function () {
                var fee_head = $('select[name="fee_head[]"]').val();

                if(fee_head !== undefined) {
                    $chkIds = document.getElementsByName('chkIds[]');
                    var $chkCount = 0;
                    $length = $chkIds.length;

                    for(var $i = 0; $i < $length; $i++){
                        if($chkIds[$i].type == 'checkbox' && $chkIds[$i].checked){
                            $chkCount++;
                        }
                    }

                    if($chkCount <= 0){
                        toastr.warning("Please, Select At Least One Student Record.");
                        return false;
                    }

                    var form = $('#fee_add_form');

                }else{
                    toastr.warning('Please, Add At Least One Fee Head.');
                    return false;
                }


            });
            /*Add Fees End*/

           /* $('select.feeHead').change(function() {
                var feeValue = $('select.feeHead').find(':selected').data('feeHeadAmount');
                $('.feeAmount').val(feeValue);
            });*/

        });

        function loadSemesters($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (data) {
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

        function setAmount($this){
            var $select = $($this);
            var feeHeadAmount = $select.find(':selected').attr('data-feeHead-amount') || '';
            var $row = $select.closest('tr');
            var $amountInput = $row.find('input.feeAmount').first();

            if ($amountInput.length) {
                $amountInput.val(feeHeadAmount);
            }
        }

    </script>
    @include('account.fees.master.includes.common-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')

@endsection