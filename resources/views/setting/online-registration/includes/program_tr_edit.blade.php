<tr class="option_value">
    <td>
        {!! Form::hidden('faculties_id[]', $program->faculties_id, ['class' => 'form-control']) !!}
        {!! Form::text('faculties_title[]', $program->faculty, ['class' => 'form-control', "readonly"]) !!}
    </td>

    <td>
        {!! Form::hidden('semester_select[]', $program->semesters_id, ['class' => 'form-control']) !!}
        {!! Form::text('semesters_title[]', $program->semester, ['class' => 'form-control', "readonly"]) !!}
    </td>

    <td>
        <div class="input-group">
            {!! Form::text('program_start_date[]', $program->start_date, ["placeholder" => "Start Date", "class" => "form-control border-form date-timepicker1", "required"]) !!}
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fa fa-exchange"></i>
                </span>
            </div>
            {!! Form::text('program_end_date[]', $program->end_date, ["placeholder" => "End Date", "class" => "form-control border-form date-timepicker1", "required"]) !!}
        </div>
    </td>
    <td>
        {!! Form::number('program_new_fee[]', $program->new_student_fee, ["placeholder" => "Default", "class" => "form-control", "min" => "0", "step" => "0.01", "title" => "Leave empty to use the default fee"]) !!}
    </td>
    <td>
        {!! Form::number('program_old_fee[]', $program->old_student_fee, ["placeholder" => "Default", "class" => "form-control", "min" => "0", "step" => "0.01", "title" => "Leave empty to use the default fee"]) !!}
    </td>
    <td>
        {{-- Which Main Fee Head this admission charges. Empty keeps the old behaviour: one
             lump ADMISSION FEE row instead of the sub head split. --}}
        {!! Form::select('program_fee_head_group[]',
            [0 => 'None (single head)'] + (isset($fee_head_groups) ? $fee_head_groups : (isset($data['fee_head_groups']) ? $data['fee_head_groups'] : [])),
            $program->fee_head_group_id,
            ['class' => 'form-control select2', 'title' => 'Splits the payment across this fee\'s sub heads']) !!}
    </td>
    <td>
        {!! Form::select('program_status[]', ['active' => 'Active', 'in-active' => 'In-Active'], $program->status, ['class' => 'form-control select2']) !!}
    </td>
    <td class="text-center">
        <a href="#" class="btn btn-sm btn-danger btn-icon-only delete-program" data-id="{{$program->id}}" title="Delete program">
            <i class="fa fa-trash"></i>
        </a>
    </td>
</tr>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('.date-timepicker1').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: false
        });
    });
</script>