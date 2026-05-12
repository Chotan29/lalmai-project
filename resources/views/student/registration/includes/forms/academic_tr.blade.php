@if(isset($academicInfoRow) && $academicInfoRow->count() >0)
    @foreach($academicInfoRow as $row)
        @php
            $academicTitle = strtoupper(trim($row->title)) === 'BOARD' ? 'SSC/DAKHIL/EQUIVALENT' : $row->title;
        @endphp
        <tr class="option_value">
            <td>
                {!! Form::label('board', $academicTitle, ['class' => 'col-sm-12 control-label']) !!}
                {!! Form::hidden('board[]', $academicTitle, ["class" => "col-md-12"]) !!}
                {{--["class" => "col-xs-10 col-sm-11"]--}}
            </td>
            <td>
                {!! Form::text('pass_year[]', null, ["class" => "col-md-12"]) !!}
            </td>
            <td>
                {!! Form::text('institution[]', null, ["class" => "col-md-12"]) !!}
            </td>
            <td>
                {!! Form::text('roll_no[]', null, ["class" => "col-md-12"]) !!}
            </td>

            <td>
                {!! Form::text('major_subjects[]', null, ["class" => "col-md-12"]) !!}
            </td>

            <td>
                {!! Form::number('mark_obtained[]', 0, ["class" => "col-md-12 mark-obtained calculate-percent"]) !!}
            </td>

            <td>
                {!! Form::number('maximum_mark[]', 0, ["class" => "col-md-12 maximum-mark calculate-percent"]) !!}
            </td>

            <td>
                {!! Form::number('percentage[]', 0, ["class" => "col-md-12 percent-value","readonly"]) !!}
            </td>

            <td>
                {!! Form::text('grade_point[]', null, ["class" => "col-md-12"]) !!}
            </td>

            <td>
                {!! Form::text('grade_letter[]', null, ["class" => "col-md-12"]) !!}
            </td>
            <td></td>

        </tr>
    @endforeach
@endif

<script>
    $('.calculate-percent').change(function() {
        $obtainMark = $(this).closest('tr').find('.mark-obtained').val();
        $maximumMark = $(this).closest('tr').find('.maximum-mark').val();
        $percentage = (($obtainMark * 100) / $maximumMark).toFixed(2);
        $(this).closest('tr').find('.percent-value').val($percentage);
    });
</script>