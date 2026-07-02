<!-- Modal -->
<div class="modal fade" id="feeMasterAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        {!! Form::open(['route' => 'account.fees.master.store', 'method' => 'POST', 'class' => 'form-horizontal',
                       'id' => 'validation-form', "enctype" => "multipart/form-data", 'novalidate' => 'novalidate']) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close top-close" data-dismiss="modal" id="close-button">×</button>
                <h4 class="modal-title title text-center fees_title" id="MasterTitle"><b>Add:</b> Fees</h4>
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal">
                    <div class="box-body">
                        <input type="hidden" name="chkIds[]" id="StudentsId" value="{{encrypt($data['student']->id)}}"/>
                        
                        <!-- Due Date -->
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Due Date</label>
                            <div class="col-sm-9">
                                {!! Form::text('fee_due_date[]', null, [
                                    "placeholder" => "YYYY-MM-DD", 
                                    "class" => "col-xs-10 col-sm-11 input-mask-date date-picker due-date", 
                                    "id" => "date1",
                                    "required",
                                    "min" => \Carbon\Carbon::today()->format('Y-m-d')
                                ]) !!}
                                <div class="text-danger due-date-error" style="display:none;">
                                    Please enter a valid due date
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fee Head -->
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Head</label>
                            <div class="col-sm-9">
                                {!! Form::select('fee_head[]', $data['fee_heads'], null, [
                                    'class' => 'form-control col-xs-10 col-sm-11 chosen-select', 
                                    'required',
                                    'onChange' => 'setAmount(this);', 
                                    'style'=>'width:420px;'
                                ], $data['fee_head_attributes']) !!}
                                <div class="text-danger fee-head-error" style="display:none;">
                                    Please select a fee head
                                </div>
                            </div>
                        </div>
                        
                        <!-- Amount -->
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Amount</label>
                            <div class="col-sm-9">
                                {!! Form::text('fee_amount[]', null, [
                                    "class" => "col-xs-10 col-sm-11 feeAmount", 
                                    "required",
                                    "pattern" => "[0-9]+(\.[0-9]{1,2})?",
                                    "title" => "Please enter a valid amount (e.g. 100 or 100.50)"
                                ]) !!}
                                <div class="text-danger fee-amount-error" style="display:none;">
                                    Please enter a valid amount
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="box-body">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn cfees btn-success" id="load" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Fees
                    </button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize datepicker
    $('.date-picker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        startDate: new Date()
    });

    // Form validation
    $('#validation-form').on('submit', function(e) {
        let isValid = true;
        
        const dueDate = $('.due-date').val();
        if (!dueDate || new Date(dueDate) < new Date(new Date().toDateString())) {
            $('.due-date-error').show();
            isValid = false;
        } else {
            $('.due-date-error').hide();
        }
        
        // Validate Fee Head
        if (!$('select[name="fee_head[]"]').val()) {
            $('.fee-head-error').show();
            isValid = false;
        } else {
            $('.fee-head-error').hide();
        }
        
        // Validate Amount
        const amount = $('.feeAmount').val();
        if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
            $('.fee-amount-error').show();
            isValid = false;
        } else {
            $('.fee-amount-error').hide();
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        return true;
    });

    // Real-time validation
    $('.date-picker').on('change', function() {
        const dateValue = $(this).val();
            const isFutureDate = dateValue && new Date(dateValue) >= new Date(new Date().toDateString());

            $('.due-date-error').toggle(!isFutureDate);
    });
    
    $('select[name="fee_head[]"]').on('change', function() {
        $('.fee-head-error').toggle(!$(this).val());
    });
    
    $('.feeAmount').on('input', function() {
        const amount = $(this).val();
        $('.fee-amount-error').toggle(!amount || isNaN(amount) || parseFloat(amount) <= 0);
    });
});
</script>
@endpush