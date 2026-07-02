@if(isset($academicInfos) && $academicInfos->count() > 0)
    @foreach($academicInfos as $academicInfo)
        <tr class="option_value">
            <td>
                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th class="align-right" style="border: none !important; background: none !important;">
                            Examination
                        </th>
                        <td>
                            {!! Form::text('examination[]', $academicInfo->examination, ["class" => "col-xs-10 col-sm-11"]) !!}
                        </td>
                    </tr>
                    <tr>
                        <th class="align-right" style="border: none !important; background: none !important;">
                            Institution
                        </th>
                        <td>
                            {!! Form::text('institution[]', $academicInfo->institution, ["class" => "col-xs-10 col-sm-11"]) !!}
                        </td>
                    </tr>
                    <tr>
                        <th class="align-right" style="border: none !important; background: none !important;">
                            Board/University
                        </th>
                        <td>
                            {!! Form::text('board_university[]', $academicInfo->board_university, ["class" => "col-xs-10 col-sm-11"]) !!}
                        </td>
                    </tr>
                    <tr>
                        <th class="align-right" style="border: none !important; background: none !important;">
                            Year of Pass
                        </th>
                        <td>
                            {!! Form::text('year_of_pass[]', $academicInfo->year_of_pass, ["class" => "col-xs-10 col-sm-11"]) !!}
                        </td>
                    </tr>
                    <tr>
                        <th class="align-right" style="border: none !important; background: none !important;">
                            Percentage/Grade
                        </th>
                        <td>
                            {!! Form::text('percentage_grade[]', $academicInfo->percentage_grade, ["class" => "col-xs-10 col-sm-11"]) !!}
                        </td>
                    </tr>

                </table>
            </td>
            <td width="5%">
                <div class="btn-group">
                    <a href="{{ route('web.admin.registration.delete-academicInfo', ['id' => $academicInfo->id]) }}" class="btn-primary btn-sm btn-danger bootbox-confirm align-right" >
                        <i class="fa fa-trash bigger-130" title="Delete"></i> Delete
                    </a>
                </div>

            </td>
        </tr>
    @endforeach
@endif

