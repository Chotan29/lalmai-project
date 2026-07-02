<div class="form-group table-responsive">
    <div id="entry-summary" class="alert alert-info" style="display:none; margin-bottom:8px;"></div>
    <table id="studentsTable" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Sort</th>
                <th>Reg.Number</th>
                <th>{{__('form_fields.student.fields.name_of_student')}}</th>
                <th>Absent (Theory)</th>
                <th id="th-theory">Obtain Mark (Theory)</th>
                <th id="th-mcq">Obtain Mark (MCQ)</th>
                <th>Absent (Practical)</th>
                <th id="th-practical">Obtain Mark (Practical)</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="student_wrapper">

        </tbody>

    </table>
</div>
@include('includes.scripts.inputMask_script')