@if(isset($academicInfoRow) && $academicInfoRow->count() >0)
    @foreach($academicInfoRow as $row)
        <tr class="option_value">
            <td>
                <span class="academic-exam-title">{{ $row->title }}</span>
                {!! Form::hidden('board[]', $row->title, ["class" => "col-md-12"]) !!}
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
                {!! Form::number('mark_obtained[]', null, ["class" => "col-md-12 mark-obtained calculate-percent", "step" => "any", "min" => "0"]) !!}
            </td>

            <td>
                {!! Form::number('maximum_mark[]', null, ["class" => "col-md-12 maximum-mark calculate-percent", "step" => "any", "min" => "0"]) !!}
            </td>

            <td>
                {!! Form::number('percentage[]', null, ["class" => "col-md-12 percent-value","readonly", "step" => "any", "min" => "0"]) !!}
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
        var $row = $(this).closest('tr');
        var obtainMark = parseFloat($row.find('.mark-obtained').val());
        var maximumMark = parseFloat($row.find('.maximum-mark').val());

        if (!isNaN(obtainMark) && !isNaN(maximumMark) && maximumMark > 0) {
            var percentage = ((obtainMark * 100) / maximumMark).toFixed(2);
            $row.find('.percent-value').val(percentage);
        } else {
            $row.find('.percent-value').val('');
        }
    });
</script>