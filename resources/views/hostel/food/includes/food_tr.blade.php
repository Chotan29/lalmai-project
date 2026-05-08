<tr class="option_value">
    <td>
        <input type="hidden" name="food_items_id[]" value="{{ $foodItem->id }}">
        <p>{{ $foodItem->title }}</p>
    </td>
    <td>
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                <i class="fa fa-trash bigger-120"></i>
            </button>
        </div>
    </td>
</tr>
