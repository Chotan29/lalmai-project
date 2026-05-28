@foreach($exist as $student)
    <tr class="option_value" style="background:lightgrey">
        <td>
            <div class="btn-group">
                <label class="btn btn-xs btn-primary">
                    <i class="ace-icon fa fa-arrows bigger-120"></i>
                </label>
            </div>
        </td>
        <td>
            <input type="hidden" name="students_id[]" value="{{ $student->student_id }}">
            {{ $student->reg_no }}
        </td>
        <td>
            {{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}
        </td>
        <td>
            {!! Form::checkbox('absent_theory[]', $student->student_id, in_array($student->student_id, $absent_theory), ['class' => 'form-control']) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_theory[]', $student->obtain_mark_theory, ["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['theory'] ?? 0)]) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_mcq[]', $student->obtain_mark_mcq, ["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['mcq'] ?? 0)]) !!}
        </td>
        <td>
            {!! Form::checkbox('absent_practical[]', $student->student_id, in_array($student->student_id, $absent_practical), ['class' => 'form-control']) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_practical[]', $student->obtain_mark_practical, ["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['practical'] ?? 0)]) !!}
        </td>

        <td>
            <div class="btn-group">
                <label class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                    <i class="fa fa-trash bigger-120"></i>
                </label>
            </div>
        </td>
    </tr>
@endforeach
