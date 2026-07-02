<tr class="option_value">
    <td width="5%">
        <div class="btn-group">
                <span class="btn btn-xs btn-primary" >
                    <i class="fa fa-arrows bigger-120"></i>
                </span>
        </div>
    </td>
    <td>
        {!! Form::file('gallery_image[]',["required"]) !!}
    </td>
    <td>
        {!! Form::text('gallery_caption[]', null, ["placeholder" => "Caption", "class" => "col-xs-10 col-sm-11", "required"]) !!}
    </td>
    <td>
        {!! Form::select('gallery_status[]', ['active' => 'Active', 'in-active' => 'Inactive'], request('status'), ['class' => 'from-control']) !!}
    </td>
    <td>
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                <i class="ace-icon fa fa-trash"></i>
            </button>
        </div>

    </td>
</tr>

