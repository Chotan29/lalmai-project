{{-- One sub head inside a Main Fee Head.

     The row can be dragged, and its position is saved as the order money fills these heads in -
     college heads above, department heads below - so a student who pays only the college share
     leaves the department heads untouched instead of part-filling them at random. --}}
<tr class="option_value sub-head-row">
    <td width="5%">
        <div class="btn-group">
            <span class="btn btn-xs btn-primary">
                <i class="fa fa-arrows" aria-hidden="true"></i>
            </span>
        </div>
    </td>

    {{-- Sub Head --}}
    <td width="55%">
        <select name="fee_head_id[]" class="form-control chosen-select feeHead" required
                onChange="setSubHeadAmount(this);" {{ isset($template) ? 'disabled' : '' }}>
            <option value="">Select Sub Head</option>
            @foreach($data['fee_heads'] as $head)
                <option value="{{ $head->id }}"
                        data-feeHead-amount="{{ $head->fee_head_amount }}"
                        {{ (isset($item) && $item->fee_head_id == $head->id) ? 'selected' : '' }}>
                    {{ $head->fee_head_title }}@if($head->collected_by == 'department') (Department)@endif
                </option>
            @endforeach
        </select>
        @if($errors->has('fee_head_id.*'))
            <span class="text-danger small">{{ $errors->first('fee_head_id.*') }}</span>
        @endif
    </td>

    {{-- Amount --}}
    <td width="30%">
        <input type="text" name="amount[]" class="col-xs-10 col-sm-11 subHeadAmount"
               value="{{ isset($item) ? ($item->amount + 0) : '' }}" required
               {{ isset($template) ? 'disabled' : '' }}>
        @if($errors->has('amount.*'))
            <span class="text-danger small">{{ $errors->first('amount.*') }}</span>
        @endif
    </td>

    <td width="10%">
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-danger remove-sub-head">
                <i class="fa fa-trash bigger-120"></i>
            </button>
        </div>
    </td>
</tr>
