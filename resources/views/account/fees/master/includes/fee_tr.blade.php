<tr class="option_value">
    <td width="5%">
        <div class="btn-group">
            <span class="btn btn-xs btn-primary">
                <i class="fa fa-arrows" aria-hidden="true"></i>
            </span>
        </div>
    </td>
    
    <!-- Due Date -->
    <td width="10%">
        {!! Form::text('fee_due_date[]', null, [
            "placeholder" => "YYYY-MM-DD", 
            "class" => "col-xs-10 col-sm-11 input-mask-date date-picker" . ($errors->has('fee_due_date.*') ? ' is-invalid' : ''), 
            "required"
        ]) !!}
        @if($errors->has('fee_due_date.*'))
            <span class="text-danger small">
                {{ str_replace('fee due date', 'Due Date', $errors->first('fee_due_date.*')) }}
            </span>
        @endif
    </td>
    
    <!-- Fee Head -->
    <td width="40%">
        {!! Form::select('fee_head[]', $fee_heads, null, ['class' => 'form-control chosen-select feeHead', 'required','onChange' => 'setAmount(this);'], $fee_head_attributes) !!}
        @if($errors->has('fee_head.*'))
            <span class="text-danger small">{{ $errors->first('fee_head.*') }}</span>
        @endif
    </td>
    
    <!-- Amount -->
    <td width="10%">
        {!! Form::text('fee_amount[]', null, ["id" => $randId, "class" => "col-xs-10 col-sm-11 feeAmount" , "required"]) !!}
        @if($errors->has('fee_amount.*'))
            <span class="text-danger small">{{ $errors->first('fee_amount.*') }}</span>
        @endif
    </td>
    
    <td width="10%">
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                <i class="fa fa-trash bigger-120"></i>
            </button>
        </div>
    </td>
</tr>

<style>
    .is-invalid {
        border-color: #dc3545;
    }
    .text-danger {
        color: #dc3545;
        font-size: 12px;
        display: block;
        margin-top: 5px;
    }
</style>

@include('account.fees.master.includes.common-script')
@include('includes.scripts.inputMask_script')
@include('includes.scripts.table_tr_sort')
@include('includes.scripts.datepicker_script')