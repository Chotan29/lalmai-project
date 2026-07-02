<div class="form-group">
    {!! Form::label('tc_num', 'TC.No.', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        @if(isset($data['row']) && !isset($data['tc_num']))
            {!! Form::text('tc_num', $data['row']->tc_num, ["class" => "form-control border-form","required","readonly"]) !!}
        @else
            {!! Form::text('tc_num', $data['tc_num'], ["class" => "form-control border-form","required","readonly"]) !!}
        @endif
        @include('includes.form_fields_validation_message', ['name' => 'tc_num'])
    </div>

    {!! Form::label('application_date', 'Application Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('application_date', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'application_date'])
    </div>

    {!! Form::label('date_of_issue', 'Issue Date', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_issue', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_issue'])
    </div>

    {!! Form::label('date_of_leaving', 'Leave Date', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_leaving', null, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_leaving'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('character', 'Character', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('character', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'character'])
    </div>

    {!! Form::label('join_time_class', 'Join Time Level/Class', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('join_time_class', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'join_time_class'])
    </div>

    {!! Form::label('leaving_time_class', 'Leaving Time Level/Class', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('leaving_time_class', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'leaving_time_class'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('dob_certificate', 'DOB Certificate', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('dob_certificate', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'dob_certificate'])
    </div>

    {!! Form::label('birth_place', 'Birth Place', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('birth_place', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'birth_place'])
    </div>

</div>

<div class="form-group">
    {!! Form::label('school_category', 'School Category', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('school_category', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'school_category'])
    </div>

    {!! Form::label('paid_fee_status', 'Fee Paid Status', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('paid_fee_status', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'paid_fee_status'])
    </div>

    {!! Form::label('fee_concession_detail', 'Concession', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('fee_concession_detail', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'fee_concession_detail'])
    </div>


</div>


<div class="form-group">
    {!! Form::label('last_taken_exam_with_result', 'Last Taken Examination', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('last_taken_exam_with_result', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'last_taken_exam_with_result'])
    </div>

    {!! Form::label('exam_fail_status', 'Student is Failed', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('exam_fail_status', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'exam_fail_status'])
    </div>

    {!! Form::label('qualified_to_promote', 'Qualified to Promote', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('qualified_to_promote', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'qualified_to_promote'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('promoted_class', 'Promoted Class', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('promoted_class', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'promoted_class'])
    </div>

    {!! Form::label('subject_studies', 'Subjects Offered', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::text('subject_studies',  null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'subject_studies'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('cadet_detail', 'Cadet Detail', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('cadet_detail', null, ["class" => "form-control border-form upper",]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'cadet_detail'])
    </div>

    {!! Form::label('reason_to_leave', 'Reason To Leave', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('reason_to_leave', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reason_to_leave'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('total_attendance', 'Last Date Total Attendance', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::number('total_attendance', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'total_attendance'])
    </div>
    {!! Form::label('school_college_open_total', 'School/College Open Days', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::number('school_college_open_total', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'school_college_open_total'])
    </div>

    {!! Form::label('extra_activity_detail', 'Extra Activity Of Student', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('extra_activity_detail', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'extra_activity_detail'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('any_other_remark', 'Any Other Remarks', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('any_other_remark', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'any_other_remark'])
    </div>
</div>

<hr class="hr-2">
@include('certificate.includes.student-detail')