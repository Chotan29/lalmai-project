@foreach($schedules as $subject)
    <tr class="option_value" style="background: lightgrey">
        <td>
            <div class="btn-group">
                <label class="btn btn-xs btn-primary">
                    <i class="ace-icon fa fa-arrows bigger-120"></i>
                </label>
            </div>
        </td>
        <td>
            <input type="hidden" name="sem_subject_id[]" value="{{ $subject->sub_id }}">
            {!! Form::text('subjects_id[]', $subject->title, ['class' => 'form-control',"disabled"]) !!}
        </td>
        <td>
            {!! Form::text('date[]', \Carbon\Carbon::parse($subject->date)->format('Y-m-d'), ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "required"]) !!}
        </td>
        <td>
            {!! Form::time('start_time[]', $subject->start_time, ["class" => "form-control border-form", "required"]) !!}
        </td>
        <td>
            {!! Form::time('end_time[]', $subject->end_time, ["class" => "form-control border-form", "required"]) !!}
        </td>
        <td>
            {!! Form::text('full_mark_theory[]', $subject->full_mark_theory, ["class" => "form-control border-form upper"]) !!}
        </td>
        <td>
            {!! Form::text('pass_mark_theory[]', $subject->pass_mark_theory, ["class" => "form-control border-form upper"]) !!}
        </td>
        <td>
            <input type="text" class="form-control sched-mcq" value="{{ ($subject->mcq_number_theory ?? 0) > 0 ? $subject->mcq_number_theory + 0 : '' }}"
                   disabled title="MCQ full mark comes from Subject setup (Academic > Subject)">
        </td>
        <td>
            <input type="text" class="form-control sched-mcq" value="{{ ($subject->mcq_number_practical ?? 0) > 0 ? $subject->mcq_number_practical + 0 : '' }}"
                   disabled title="MCQ pass mark comes from Subject setup (Academic > Subject)">
        </td>
        <td>
            {!! Form::text('full_mark_practical[]', $subject->full_mark_practical, ["class" => "form-control border-form upper"]) !!}
        </td>
        <td>
            {!! Form::text('pass_mark_practical[]', $subject->pass_mark_practical, ["class" => "form-control border-form upper"]) !!}
        </td>
        <td>
            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                    <i class="fa fa-trash bigger-120"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
@include('includes.scripts.inputMask_script')
@include('includes.scripts.datepicker_script')