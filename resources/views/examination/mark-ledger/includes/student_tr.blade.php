@foreach($students as $student)
    <tr class="option_value">
        <td>
            <div class="btn-group">
                <label class="btn btn-xs btn-primary">
                    <i class="ace-icon fa fa-arrows bigger-120"></i>
                </label>
            </div>
        </td>
        <td>
            <input type="hidden" name="students_id[]" value="{{ $student->id }}">
            {{ $student->reg_no }}
        </td>
        <td>
            {{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}
            @if(isset($optionalIds) && in_array((int) $student->id, $optionalIds, true))
                <span class="label label-info" title="Takes this subject as Optional (4th subject) - mark will be saved under the Optional subject automatically.">Optional</span>
            @endif
        </td>
        <td>
            {!! Form::checkbox('absent_theory[]', $student->id, false, ['class' => 'form-control']) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_theory[]', null, ["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['theory'] ?? 0)]) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_mcq[]', null, ["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['mcq'] ?? 0)]) !!}
        </td>
        <td>
            {!! Form::checkbox('absent_practical[]', $student->id, false, ['class' => 'form-control']) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_practical[]', null, ["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['practical'] ?? 0)]) !!}
        </td>

    