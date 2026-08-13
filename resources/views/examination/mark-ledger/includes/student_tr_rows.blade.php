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

        /* A "Not enrolled" row is a saved mark for a student this subject's list does not
           include. Their absent boxes were once pre-ticked so the office would not have to tick
           them one by one; the office has now asked for the opposite - a student who never took
           the subject is not "absent" from it, and carrying the tick makes them look like a
           candidate who failed to turn up.

           So the boxes are drawn clear, and the tick already saved against them goes when the
           office presses Save. That is deliberate: nothing is written by looking at the screen.

           Only where the row carries no real mark. A mark on a not-enrolled row means the
           student did sit the paper and it is their subject list that is incomplete - that row
           is left exactly as it stands, red flag and all, for the office to look at. */
        $isNotEnrolled = isset($notEnrolledIds) && in_array((int) $student->student_id, $notEnrolledIds, true);
        $hasRealMark = (float) $student->obtain_mark_theory > 0
            || (float) ($student->obtain_mark_mcq ?? 0) > 0
            || (float) $student->obtain_mark_practical > 0;
        $clearAbsent = $isNotEnrolled && !$hasRealMark && !$isLocked;
    @endphp
    <tr class="option_value {{ $isLocked ? 'ledger-locked-row' : '' }}" data-student-id="{{ $student->student_id }}" data-reg="{{ $student->reg_no }}" style="background: {{ $rowBg }}">
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
            @if(isset($optionalIds) && in_array((int) $student->student_id, $optionalIds, true))
                <span class="label label-info" title="Takes this subject as Optional (4th subject) - mark will be saved under the Optional subject automatically.">Optional</span>
            @endif
            @if(isset($notEnrolledIds) && in_array((int) $student->student_id, $notEnrolledIds, true))
                <span class="label label-danger" title="This student is not enrolled in this subject, but a mark is already saved for them. It is shown so it is not lost - check the student's subject list, then clear this mark if it was entered by mistake.">Not enrolled</span>
            @endif
        </td>
        <td>
            {!! Form::checkbox('absent_theory[]', $student->student_id, $clearAbsent ? false : in_array($student->student_id, $absent_theory), array_merge(['class' => 'form-control'], $isLocked ? ['disabled' => 'disabled'] : [])) !!}
        </td>
        <td>
            @include('examination.mark-ledger.includes.mark-input', [
                'field' => 'obtain_mark_theory[]', 'value' => $student->obtain_mark_theory,
                'limit' => $markLimits['theory'] ?? 0, 'label' => 'theory', 'locked' => $isLocked,
            ])
        </td>
        <td>
            @include('examination.mark-ledger.includes.mark-input', [
                'field' => 'obtain_mark_mcq[]', 'value' => $student->obtain_mark_mcq,
                'limit' => $markLimits['mcq'] ?? 0, 'label' => 'MCQ', 'locked' => $isLocked,
            ])
        </td>
        <td>
            {!! Form::checkbox('absent_practical[]', $student->student_id, $clearAbsent ? false : in_array($student->student_id, $absent_practical), array_merge(['class' => 'form-control'], ($isLocked || (float)($markLimits['practical'] ?? 0) <= 0) ? ['disabled' => 'disabled'] : [])) !!}
        </td>
        <td>
            @include('examination.mark-ledger.includes.mark-input', [
                'field' => 'obtain_mark_practical[]', 'value' => $student->obtain_mark_practical,
                'limit' => $markLimits['practical'] ?? 0, 'label' => 'practical', 'locked' => $isLocked,
            ])
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
