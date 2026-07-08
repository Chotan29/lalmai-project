<style>
    .sched-panel { background: #fff; border: 1px solid #dce4ec; border-radius: 6px; margin-bottom: 18px; }
    .sched-panel .chosen-container { width: 100% !important; }
    .sched-panel-header { background: #f5f8fb; border-bottom: 2px solid #337ab7; padding: 10px 15px; }
    .sched-panel-header h4 { margin: 0; font-size: 15px; font-weight: 600; color: #2c3e50; display: inline-block; }
    .sched-panel-header h4 .fa { color: #337ab7; margin-right: 6px; }
    .sched-panel-body { padding: 15px; }
    .sched-field label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; color: #7f8c9a; margin-bottom: 4px; }
    .sched-field { margin-bottom: 10px; }
</style>

<div class="sched-panel">
    <div class="sched-panel-header">
        <h4><i class="fa fa-sliders"></i> Exam Selection</h4>
    </div>
    <div class="sched-panel-body">
        <div class="row">
            <div class="col-md-3 col-sm-6 sched-field">
                <label>{{ __('form_fields.student.fields.faculty') }}</label>
                {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control chosen-select', 'onChange' => 'loadSemesters(this);']) !!}
            </div>
            <div class="col-md-3 col-sm-6 sched-field">
                <label>{{ __('form_fields.student.fields.semester') }}</label>
                <select name="semester_select" class="form-control semester_select" onChange="loadSubject(this)">
                    <option> Select {{ __('form_fields.student.fields.semester') }} </option>
                </select>
            </div>
            <div class="col-md-2 col-sm-4 sched-field">
                <label>Year</label>
                {!! Form::select('years_id', $data['years'], null, ["class" => "form-control border-form", "required", "onChange" => "loadSubject(this)"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'years_id'])
            </div>
            <div class="col-md-2 col-sm-4 sched-field">
                <label>Month</label>
                {!! Form::select('months_id', $data['months'], null, ["class" => "form-control border-form", "required", "onChange" => "loadSubject(this)"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'months_id'])
            </div>
            <div class="col-md-2 col-sm-4 sched-field">
                <label>Exam</label>
                {!! Form::select('exams_id', $data['exams'], null, ["class" => "form-control border-form chosen-select", "onChange" => "loadSubject(this)", "required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'exams_id'])
            </div>
        </div>
    </div>
</div>

<!-- Option Values -->
@include($view_path.'.includes.subject')
