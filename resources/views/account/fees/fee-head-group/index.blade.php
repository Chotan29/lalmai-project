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

            /* Chosen is what turns a sub head list of thirty-odd into something you can type
               into. It is set up once on page load, so a row added afterwards never had it -
               the old code even stripped the class off the copy, which is why searching worked
               on the rows that were already there and not on the ones you added. */
            function makeSearchable($select) {
                if (!$select.length || !$.fn.chosen) { return; }
                $select.addClass('chosen-select').chosen({
                    allow_single_deselect: true,
                    search_contains: true,     /* match anywhere, not only at the start */
                    width: '100%'
                });
            }

            $('#add-sub-head').click(function () {
                var $row = $('#sub_head_template').find('tr').first().clone();
                /* The template's fields are disabled so they never post; the copy is a real row
                   and has to be switched back on. */
                $row.find('select, input').prop('disabled', false);
                $row.find('select').removeClass('chosen-select').val('');
                $row.find('input.subHeadAmount').val('');
                $row.find('.chosen-container').remove();
                $('#sub_head_wrapper').append($row);
                makeSearchable($row.find('select.feeHead'));
                recalculateSubHeadTotal();
            });

            /* The rows drawn by the server: Chosen ran on them at page load, but only where the
               class was already there. Any that were missed are picked up here, so every row on
               the screen behaves the same way. */
            $('#sub_head_wrapper').find('select.feeHead').each(function () {
                if (!$(this).next('.chosen-container').length) {
                    makeSearchable($(this));
                }
            });

            /* ---- creating a sub head without leaving the page ---- */

            $('#toggle-new-sub-head').click(function () {
                $('#new-sub-head-panel').slideToggle(120);
                $('#new_sub_head_title').focus();
            });

            /* Put a newly created head into every dropdown at once, so the office can use it on
               any row, not only the one they happened to be looking at. */
            function addHeadEverywhere(head) {
                var label = head.title + (head.collected_by === 'department' ? ' (Department)' : '');

                $('select.feeHead').each(function () {
                    var $sel = $(this);
                    if ($sel.find('option[value="' + head.id + '"]').length) { return; }
                    $sel.append(
                        $('<option>').val(head.id).text(label)
                            .attr('data-feeHead-amount', head.amount)
                    );
                });

                /* The hidden template too, so the next Add Sub Head row carries it as well. */
                $('#sub_head_template').find('select').trigger('chosen:updated');
                $('#sub_head_wrapper').find('select.feeHead').trigger('chosen:updated');
            }

            /* The row to drop it into: the first one still empty, or a fresh one. */
            function rowForNewHead() {
                var $empty = $('#sub_head_wrapper').find('select.feeHead').filter(function () {
                    return !$(this).val();
                }).first();

                if ($empty.length) { return $empty; }

                $('#add-sub-head').click();
                return $('#sub_head_wrapper').find('select.feeHead').last();
            }

            function useHead(head) {
                var $select = rowForNewHead();
                $select.val(head.id).trigger('chosen:updated');

                var $amount = $select.closest('tr').find('input.subHeadAmount').first();
                if ($amount.length && $amount.val() === '') {
                    $amount.val(head.amount || '');
                }

                recalculateSubHeadTotal();
            }

            $('#save-new-sub-head').click(function () {
                var $button = $(this);
                var $error = $('#new_sub_head_error').text('');

                var payload = {
                    _token: '{{ csrf_token() }}',
                    fee_head_title: $.trim($('#new_sub_head_title').val()),
                    collected_by: $('#new_sub_head_collected_by').val(),
                    fee_head_amount: $('#new_sub_head_amount').val() || 0
                };

                if (payload.fee_head_title === '') {
                    $error.text('Please type the sub head name.');
                    return;
                }

                $button.prop('disabled', true);

                $.post('{{ route('account.fees.fee-head-group.sub-head.store') }}', payload)
                    .done(function (res) {
                        if (!res || !res.ok) { return; }

                        addHeadEverywhere(res.head);
                        useHead(res.head);

                        $('#new_sub_head_title').val('');
                        $('#new_sub_head_amount').val('');
                        $('#new-sub-head-panel').slideUp(120);
                        toastr.success(res.head.title + ' created and added.');
                    })
                    .fail(function (xhr) {
                        var res = xhr.responseJSON || {};

                        /* Already there: say so, and point the row at the one that exists rather
                           than making the office go and look for it. */
                        if (xhr.status === 409 && res.existing) {
                            addHeadEverywhere({
                                id: res.existing.id,
                                title: res.existing.title,
                                amount: res.existing.amount,
                                collected_by: res.existing.collected_by
                            });
                            useHead(res.existing);
                            $error.text(res.message + ' It has been selected for you.');
                            return;
                        }

                        $error.text(res.message || 'Could not save that sub head. Please try again.');
                    })
                    .always(function () {
                        $button.prop('disabled', false);
                    });
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
