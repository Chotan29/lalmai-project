@extends('layouts.master')

@section('css')


    @endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        @include($view_path.'.registration.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Add
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        @include($view_path.'.includes.buttons')
                        <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')

                        {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                        @include($view_path.'.registration.includes.form')

                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn" type="reset">
                                    <i class="fa fa-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-primary add-exam" type="submit" value="Save" name="add_exam" id="add-exam">
                                    <i class="fa fa-save bigger-110"></i>
                                    Save
                                </button>

                                <button class="btn btn-success add-exam" type="submit" value="Save" name="add_exam_another" id="add-exam-another">
                                    <i class="fa fa-save bigger-110"></i>
                                    <i class="fa fa-plus bigger-110"></i>
                                    Save & Add Another
                                </button>
                            </div>
                        </div>

                        <div class="hr hr-24"></div>

                        {!! Form::close() !!}

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


@endsection

@section('js')
    <!-- page specific plugin scripts -->

    <script>
        $('#exam_type').change(function() {
            var type = $(this).val();
            if(type == '') {
                $('#startdatetimeDiv').hide();
                $('#enddatetimeDiv').hide();
                $('#startdateDiv').hide();
                $('#enddateDiv').hide();
            } else if(type == 'duration') {
                $('#startdatetimeDiv').hide();
                $('#enddatetimeDiv').hide();
                $('#startdateDiv').hide();
                $('#enddateDiv').hide();
            } else if(type == 'date_duration') {
                $('#startdateDiv').show();
                $('#enddateDiv').show();

                $('#startdatetimeDiv').hide();
                $('#enddatetimeDiv').hide();

                $('#startdate').datetimepicker({
                    viewMode: 'years',
                    format: 'YYYY-MM-DD'
                });
                $('#enddate').datetimepicker({
                    viewMode: 'years',
                    format: 'YYYY-MM-DD'
                });
            } else if(type == 'date_time_duration') {
                $('#startdatetimeDiv').show();
                $('#enddatetimeDiv').show();

                $('#enddateDiv').hide();
                $('#startdateDiv').hide();

                $('#startdatetime').datetimepicker({
                    viewMode: 'years',
                    format: 'YYYY-MM-DD hh:mm a'
                });
                $('#enddatetime').datetimepicker({
                    viewMode: 'years',
                    format: 'YYYY-MM-DD hh:mm a'
                });
            }
        });



        $(document).ready(function ($) {

            var type = "";
            if(type == '') {
                $('#startdatetimeDiv').hide();
                $('#enddatetimeDiv').hide();
                $('#startdateDiv').hide();
                $('#enddateDiv').hide();
            } else if(type == 'duration') {
                $('#startdatetimeDiv').hide();
                $('#enddatetimeDiv').hide();
                $('#startdateDiv').hide();
                $('#enddateDiv').hide();
            } else if(type == 'date_duration') {
                $('#startdateDiv').show();
                $('#enddateDiv').show();

                $('#startdatetimeDiv').hide();
                $('#enddatetimeDiv').hide();

                $('#startdate').datetimepicker({
                    viewMode: 'years',
                    format: 'DD-MM-YYYY'
                });
                $('#enddate').datetimepicker({
                    viewMode: 'years',
                    format: 'DD-MM-YYYY'
                });
            } else if(type == 'date_time_duration') {
                $('#startdatetimeDiv').show();
                $('#enddatetimeDiv').show();

                $('#enddateDiv').hide();
                $('#startdateDiv').hide();

                $('#startdatetime').datetimepicker({
                    viewMode: 'years',
                    format: 'DD-MM-YYYY hh:mm a'
                });
                $('#enddatetime').datetimepicker({
                    viewMode: 'years',
                    format: 'DD-MM-YYYY hh:mm a'
                });
            } else {
                $('#startdateDiv').hide();
                $('#enddateDiv').hide();
                $('#startdatetimeDiv').hide();
                $('#enddatetimeDiv').hide();
                $('#validDaysDiv').hide();
                $('#costDiv').hide();
            }


            //validation
            $(".add-exam").click(function () {
                var flag = false;

                var faculty = $('select[name="faculty"]').val();
                    if (faculty > 0 && faculty !=='') {

                    }else{
                        toastr.warning("Please,Find and Choose Faculty", "Info:");
                        flag = true;
                        return false;
                    }

                var semesters_id = $('select[name="semesters_id"]').val();
                    if (semesters_id > 0 && semesters_id !=='') {

                    }else{
                        toastr.warning("Please,Find and Choose Semester", "Info:");
                        flag = true;
                        return false;
                    }

                var subjects_id = $('select[name="subjects_id"]').val();
                    if (subjects_id > 0 && subjects_id !=='') {

                    }else{
                        toastr.warning("Please,Find and Choose Subject", "Info:");
                        flag = true;
                        return false;
                    }

                var title = $('input[name="title"]').val();
                    if (title !=='') {

                    }else{
                        toastr.warning("Exam Title Required", "Info:");
                        flag = true;
                        return false;
                    }

                var mcq_instructions_id = $('select[name="mcq_instructions_id"]').val();
                    if (mcq_instructions_id > 0 && mcq_instructions_id !=='') {

                    }else{
                        toastr.warning("Exam Instruction Required", "Info:");
                        flag = true;
                        return false;
                    }

                var exam_status = $('select[name="exam_status"]').val();
                    if (exam_status !=='') {

                    }else{
                        toastr.warning("Exam Status Required", "Info:");
                        flag = true;
                        return false;
                    }

                var mark_type = $('select[name="mark_type"]').val();
                    if (mark_type !=='') {

                    }else{
                        toastr.warning("Mark Type Required", "Info:");
                        flag = true;
                        return false;
                    }

                var question_type = $('select[name="question_type"]').val();
                    if (question_type !=='') {

                    }else{
                        toastr.warning("Question Type Required", "Info:");
                        flag = true;
                        return false;
                    }

                var no_of_question = $('input[name="no_of_question"]').val();
                    if (no_of_question !=='') {

                    }else{
                        toastr.warning("Number of Questions Required", "Info:");
                        flag = true;
                        return false;
                    }

                var result_status = $('select[name="result_status"]').val();
                    if (result_status !=='') {

                    }else{
                        toastr.warning("Result Status Required", "Info:");
                        flag = true;
                        return false;
                    }

                var exam_type = $('select[name="exam_type"]').val();
                    if (exam_type !=='') {

                    }else{
                        toastr.warning("Exam Type Required", "Info:");
                        flag = true;
                        return false;
                    }

                //'date_duration'=>'Date & Duration','date_time_duration'=>'Date,Time & Duration'

                if( exam_type = "duration") {
                    var duration = $('input[name="duration"]').val();
                    if (duration !=='') {

                    }else{
                        toastr.warning("Duration Required", "Info:");
                        flag = true;
                        return false;
                    }
                }else if( exam_type = "date_duration"){
                    var duration = $('input[name="duration"]').val();
                    if (duration !=='') {

                    }else{
                        toastr.warning("Duration Required", "Info:");
                        flag = true;
                        return false;
                    }

                    var start_date = $('input[name="start_date"]').val();
                    if (start_date > 0 && start_date !=='') {

                    }else{
                        toastr.warning("Start Date Required", "Info:");
                        flag = true;
                        return false;
                    }

                    var end_date = $('input[name="end_date"]').val();
                    if (end_date > 0 && end_date !=='') {

                    }else{
                        toastr.warning("End Date Required", "Info:");
                        flag = true;
                        return false;
                    }
                }else if(date_time_duration = "date_duration"){
                    var duration = $('input[name="duration"]').val();
                    if (duration !=='') {

                    }else{
                        toastr.warning("Duration Required", "Info:");
                        flag = true;
                        return false;
                    }

                    var start_date_time = $('input[name="start_date_time"]').val();
                    if (start_date_time > 0 && start_date_time !=='') {

                    }else{
                        toastr.warning("Start Date & Time Required", "Info:");
                        flag = true;
                        return false;
                    }

                    var end_date_time = $('input[name="end_date_time"]').val();
                    if (end_date_time > 0 && end_date_time !=='') {

                    }else{
                        toastr.warning("End Date & Time Required", "Info:");
                        flag = true;
                        return false;
                    }
                }else{

                }


                if(flag){
                    toastr.warning("Something is Wrong, Please Check", "Info:");
                    $('#validation-form').submit(function(){
                        return false;
                    });
                }
            });

        });




        function loadSemesters($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.semesters_id').html('').append('<option value="0">Select Sem./Sec.</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.semesters_id').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });

        }

        function loadSubject($this) {
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semesters_id"]').val();


            if (faculty == 0) {
                toastr.info("Please, Select Faculty/Program/Class", "Info:");
                return false;
            }

            if (semester == 0) {
                toastr.info("Please, Select Sem./Sec.", "Info:");
                return false;
            }

            if (!semester)
                toastr.warning("Please, Choose Semester.", "Warning");
            else {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('mcq.exam.find-subject') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        faculty_id: faculty,
                        semester_id: semester
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);
                        if (data.error) {
                            $('.semester_subject').html('')
                            toastr.warning(data.error, "Warning:");
                        } else {
                            $('.semester_subject').html('').append('<option value="0">Select Subject</option>');
                            $.each(data.subjects, function (key, valueObj) {
                                $('.semester_subject').append('<option value="' + key + '">' + valueObj + '</option>');
                            });
                            toastr.success(data.success, "Success:");
                        }
                    }
                });
            }

        }

    </script>
{{--
    @include('includes.scripts.summarnote')
    @include('includes.scripts.jquery_validation_scripts')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')--}}
    @include('includes.scripts.datepicker_script')
@endsection

