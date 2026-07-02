<tr class="option_value">
    <td>
        {!! Form::select('faculties_id[]', $programs, null, ['class' => 'form-control select2', "required", 'onChange' => 'loadSemesters(this);']) !!}
    </td>
    <td>
        <select name="semester_select[]" class="form-control semester_select select2" required>
            <option value="0"> Select {{__('form_fields.student.fields.semester')}} </option>
        </select>
    </td>
    <td>
        <div class="input-group">
            {!! Form::text('program_start_date[]', null, ["placeholder" => "Start Date", "class" => "form-control border-form date-timepicker1", "required"]) !!}
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fa fa-exchange"></i>
                </span>
            </div>
            {!! Form::text('program_end_date[]', null, ["placeholder" => "End Date", "class" => "form-control border-form date-timepicker1", "required"]) !!}
        </div>
    </td>
    <td>
        {!! Form::select('program_status[]', ['active' => 'Active', 'in-active' => 'In-Active'], request('status'), ['class' => 'form-control select2']) !!}
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger btn-icon-only remove-program-row" title="Remove row">
            <i class="fa fa-trash"></i>
        </button>
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