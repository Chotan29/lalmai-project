 <tr class="pages">
    <td>
        <div class="btn-group">
        <span class="btn btn-xs btn-primary" >
            <i class="fa fa-arrows bigger-120"></i>
        </span>
        </div>
    </td>

    <td>
        <select name="pages[]" id="">
            @foreach($pages as $page)
                <option value="{{ $page->id }}">{!! $page->title !!}</option>
                @endforeach
        </select>
        <div class="space-4"></div>
    </td>
    <td>
        <div class="btn-group">
            <button class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                <i class="ace-icon fa fa-trash"></i>
            </button>
        </div>

    </td>
</tr>