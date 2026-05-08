{{--'registrations_id','examination','institution','board_university','year_of_pass','percentage_grade','status'--}}

<tr class="option_value">
    <td>
        <table class="table table-striped table-bordered table-hover">
            <tr>
                <th class="align-right" style="border: none !important; background: none !important;">
                    Examination
                </th>
                <td>
                    {!! Form::text('examination[]', null, ["class" => "col-xs-10 col-sm-11"]) !!}
                </td>
            </tr>
            <tr>
                <th class="align-right" style="border: none !important; background: none !important;">
                    Institution
                </th>
                <td>
                    {!! Form::text('institution[]', null, ["class" => "col-xs-10 col-sm-11"]) !!}
                </td>
            </tr>
            <tr>
                <th class="align-right" style="border: none !important; background: none !important;">
                    Board/University
                </th>
                <td>
                    {!! Form::text('board_university[]', null, ["class" => "col-xs-10 col-sm-11"]) !!}
                </td>
            </tr>
            <tr>
                <th class="align-right" style="border: none !important; background: none !important;">
                    Year of Pass
                </th>
                <td>
                    {!! Form::text('year_of_pass[]', null, ["class" => "col-xs-10 col-sm-11"]) !!}
                </td>
            </tr>
            <tr>
                <th class="align-right" style="border: none !important; background: none !important;">
                    Percentage/Grade
                </th>
                <td>
                    {!! Form::text('percentage_grade[]', null, ["class" => "col-xs-10 col-sm-11"]) !!}
                </td>
            </tr>
        </table>
    </td>
    <td width="5%">
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                <i class="fa fa-trash bigger-130" title="Delete"></i> Delete
            </button>
        </div>

    </td>
</tr>

