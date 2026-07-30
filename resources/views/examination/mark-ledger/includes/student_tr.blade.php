@foreach($students as $student)
    <tr class="option_value" data-reg="{{ $student->reg_no }}">
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
            @include('examination.mark-ledger.includes.mark-input', [
                'field' => 'obtain_mark_theory[]', 'value' => null,
                'limit' => $markLimits['theory'] ?? 0, 'label' => 'theory', 'locked' => false,
            ])
        </td>
        <td>
            @include('examination.mark-ledger.includes.mark-input', [
                'field' => 'obtain_mark_mcq[]', 'value' => null,
                'limit' => $markLimits['mcq'] ?? 0, 'label' => 'MCQ', 'locked' => false,
            ])
        </td>
        <td>
            {!! Form::checkbox('absent_practical[]', $student->id, false, array_merge(['class' => 'form-control'], (float)($markLimits['practical'] ?? 0) <= 0 ? ['disabled' => 'disabled'] : [])) !!}
        </td>
        <td>
            @include('examination.mark-ledger.includes.mark-input', [
                'field' => 'obtain_mark_practical[]', 'value' => null,
                'limit' => $markLimits['practical'] ?? 0, 'label' => 'practical', 'locked' => false,
            ])
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
