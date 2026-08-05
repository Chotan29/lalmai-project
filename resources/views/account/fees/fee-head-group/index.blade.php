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
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('account.includes.buttons')
                    <div class="col-xs-12 ">
                        @include('account.fees.includes.buttons')
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')

                        @if (isset($data['row']))
                            @include($view_path.'.edit')
                        @else
                            @include($view_path.'.add')
                        @endif

                        @include($view_path.'.includes.table')
                    </div>
                </div><!-- /.row -->

            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->

@endsection

@section('js')
    <!-- page specific plugin scripts -->
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.jquery_validation_scripts')
    @include('includes.scripts.table_tr_sort')
    <script>
        /* Fill the amount from the head's own default when one is picked, the way the Add Fees
           screen does. The office can still type over it - the same head is worth different
           amounts in different fees. */
        function setSubHeadAmount($this) {
            var $select = $($this);
            var feeHeadAmount = $select.find(':selected').attr('data-feeHead-amount') || '';
            var $row = $select.closest('tr');
            var $amountInput = $row.find('input.subHeadAmount').first();

            if ($amountInput.length && $amountInput.val() === '') {
                $amountInput.val(feeHeadAmount);
            }

            recalculateSubHeadTotal();
        }

        /* The running total, and the reason a fee will or will not save. The sub heads have to
           add up to the total exactly - it is what keeps proportional shares and rounding out of
           the payment code later. */
        function recalculateSubHeadTotal() {
            var total = 0;

            $('#sub_head_wrapper').find('input.subHeadAmount').each(function () {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    total += Math.round(value * 100);
                }
            });

            var target = Math.round((parseFloat($('#totalAmount').val()) || 0) * 100);

            $('#subHeadTotal').text((total / 100).toFixed(2));

            var $note = $('#subHeadBalanceNote');

            if (target === 0) {
                $note.html('').removeClass('text-danger text-success');
            } else if (total === target) {
                $note.html(' <i class="fa fa-check"></i> matches the total')
                     .removeClass('text-danger').addClass('text-success');
            } else {
                $note.html(' <i class="fa fa-exclamation-triangle"></i> off by '
                        + (Math.abs(target - total) / 100).toFixed(2))
                     .removeClass('text-success').addClass('text-danger');
            }
        }

        $(document).ready(function () {
            $('.upper').keyup(function () {
                this.value = this.value.toUpperCase();
            });

            $('#add-sub-head').click(function () {
                var $row = $('#sub_head_template').find('tr').first().clone();
                /* The template's fields are disabled so they never post; the copy is a real row
                   and has to be switched back on. */
                $row.find('select, input').prop('disabled', false);
                $row.find('select').removeClass('chosen-select').val('');
                $row.find('input.subHeadAmount').val('');
                $row.find('.chosen-container').remove();
                $('#sub_head_wrapper').append($row);
                recalculateSubHeadTotal();
            });

            $(document).on('click', '.remove-sub-head', function () {
                if ($('#sub_head_wrapper').find('tr.sub-head-row').length <= 1) {
                    toastr.warning('A Main Fee Head needs at least one Sub Head.');
                    return false;
                }
                $(this).closest('tr').remove();
                recalculateSubHeadTotal();
            });

            $(document).on('keyup change', 'input.subHeadAmount, #totalAmount', function () {
                recalculateSubHeadTotal();
            });

            recalculateSubHeadTotal();
        });
    </script>
@endsection
