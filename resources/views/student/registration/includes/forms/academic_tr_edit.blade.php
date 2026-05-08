@foreach($academicInfos as $academicInfo)
<tr class="option_value">
    <td>
        {!! Form::hidden('academic_info_id[]', $academicInfo->id, ["class" => "col-md-12"]) !!}
        {!! Form::hidden('board[]', $academicInfo->board, ["class" => "col-md-12"]) !!}
        {!! Form::label('board', $academicInfo->title, ['class' => 'col-sm-12 control-label']) !!}
{{--        {!! Form::hidden('board[]', $row->title, ["class" => "col-md-12"]) !!}--}}
        {{--["class" => "col-xs-10 col-sm-11"]--}}
    </td>
    <td>
        {!! Form::text('pass_year[]', $academicInfo->pass_year, ["class" => "col-md-12"]) !!}
    </td>

    <td>
        {!! Form::text('institution[]', $academicInfo->institution, ["class" => "col-md-12"]) !!}
    </td>

    <td>
        {!! Form::text('roll_no[]', $academicInfo->roll_no, ["class" => "col-md-12"]) !!}
    </td>

    <td>
        {!! Form::text('major_subjects[]', $academicInfo->major_subjects, ["class" => "col-md-12"]) !!}
    </td>
    <td>
        {!! Form::number('mark_obtained[]',$academicInfo->mark_obtained, ["class" => "col-md-12 mark-obtained calculate-percent"]) !!}
    </td>

    <td>
        {!! Form::number('maximum_mark[]', $academicInfo->maximum_mark, ["class" => "col-md-12 maximum-mark calculate-percent"]) !!}
    </td>

    <td>
    {!! Form::number('percentage[]', $academicInfo->percentage, ["class" => "col-md-12 percent-value","readonly"]) !!}
    </td>

    <td>
        {!! Form::text('grade_point[]', $academicInfo->grade_point, ["class" => "col-md-12"]) !!}
    </td>

    <td>
        {!! Form::text('grade_letter[]', $academicInfo->grade_letter, ["class" => "col-md-12"]) !!}
    </td>

    <td width="5%">
        <div class="btn-group">
            @ability('super-admin', 'student-delete-academic-info')
            <a href="{{ route('student.delete-academicInfo', ['id' => $academicInfo->id]) }}" class="btn-danger btn-sm bootbox-confirm align-right" >
                <i class="fa fa-trash bigger-130"></i>
            </a>
            @endability
        </div>

    </td>
</tr>
@endforeach
