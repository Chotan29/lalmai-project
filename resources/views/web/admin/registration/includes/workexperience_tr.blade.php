{{--'registrations_id','examination','institution','board_university','year_of_pass','percentage_grade','status'--}}

<tr class="option_value">
    <td>
        {!! Form::text('experience_info[]', null, ["class" => "col-xs-10 col-sm-12"]) !!}
    </td>
    <td width="5%">
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                <i class="fa fa-trash bigger-130" title="Delete"></i> Delete
            </button>
        </div>

    </td>
</tr>

