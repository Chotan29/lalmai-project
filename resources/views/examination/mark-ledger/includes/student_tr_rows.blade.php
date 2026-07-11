@foreach($exist as $student)
    @php
        $isLocked = isset($lockedIds) && in_array($student->student_id, $lockedIds);
        $ownerName = $isLocked && isset($ownerNames[$student->student_id]) ? $ownerNames[$student->student_id] : null;
        /* admin-side: a row that still has an owner (created_by) can be unlocked */
        $canUnlock = isset($canUnlock) ? $canUnlock : false;
        $hasOwner = !empty($student->created_by);
        $adminOwned = $canUnlock && $hasOwner;
        $adminOwnerName = $student->entered_by_name ?? 'A teacher';
        /* row background: teacher-locked = cream, still owned = light grey,
           unlocked / no owner (created_by = 0) = white */
        $rowBg = $isLocked ? '#f6f0e3' : ($hasOwner ? 'lightgrey' : '#ffffff');
    @endphp
    <tr class="option_value {{ $isLocked ? 'ledger-locked-row' : '' }}" data-student-id="{{ $student->student_id }}" style="background: {{ $rowBg }}">
        <td>
            <div class="btn-group">
                <label class="btn btn-xs {{ $isLocked ? 'btn-warning' : 'btn-primary' }}">
                    <i class="ace-icon fa {{ $isLocked ? 'fa-lock' : 'fa-arrows' }} bigger-120"></i>
                </label>
                @if($adminOwned)
                    <label class="btn btn-xs btn-warning" title="Select to unlock">
                        <input type="checkbox" class="unlock-chk" value="{{ $student->student_id }}" style="margin:0;">
                    </label>
                @endif
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
            {!! Form::checkbox('absent_theory[]', $student->student_id, in_array($student->student_id, $absent_theory), array_merge(['class' => 'form-control'], $isLocked ? ['disabled' => 'disabled'] : [])) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_theory[]', $student->obtain_mark_theory, array_merge(["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['theory'] ?? 0)], $isLocked ? ['readonly' => 'readonly', 'style' => 'background:#eee;'] : [])) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_mcq[]', $student->obtain_mark_mcq, array_merge(["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['mcq'] ?? 0)], $isLocked ? ['readonly' => 'readonly', 'style' => 'background:#eee;'] : [])) !!}
        </td>
        <td>
            {!! Form::checkbox('absent_practical[]', $student->student_id, in_array($student->student_id, $absent_practical), array_merge(['class' => 'form-control'], $isLocked ? ['disabled' => 'disabled'] : [])) !!}
        </td>
        <td>
            {!! Form::number('obtain_mark_practical[]', $student->obtain_mark_practical, array_merge(["class" => "form-control border-form","min"=>"0",'step'=>'any','max' => (float)($markLimits['practical'] ?? 0)], $isLocked ? ['readonly' => 'readonly', 'style' => 'background:#eee;'] : [])) !!}
        </td>

        <td>
            @if ($isLocked)
                <span class="label label-warning" title="Entered by {{ $ownerName }} — only that teacher or admin can change this mark.">
                    <i class="fa fa-lock"></i> {{ $ownerName }}
                </span>
            @elseif ($adminOwned)
                <span class="label label-info" title="Entered by {{ $adminOwnerName }}">
                    <i class="fa fa-user"></i> {{ $adminOwnerName }}
                </span>
                <button type="button" class="btn btn-xs btn-warning unlock-one-btn" data-student-id="{{ $student->student_id }}" title="Unlock this row so any teacher can edit">
                    <i class="fa fa-unlock"></i> Unlock
                </button>
            @else
                <div class="btn-group">
                    <label class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                        <i class="fa fa-trash bigger-120"></i>
                    </label>
                </div>
            @endif
        </td>
    </tr>
@endforeach
