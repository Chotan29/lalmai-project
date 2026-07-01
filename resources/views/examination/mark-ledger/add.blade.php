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
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Add
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('examination.includes.buttons')
                    @include('includes.flash_messages')
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                        @include($view_path.'.includes.buttons')
                        {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                        @include($view_path.'.includes.form')

                        <div class="clearfix form-actions">
                            <div class="align-right">
                                <button class="btn" type="reset">
                                    <i class="fa fa-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-primary" type="submit" value="Save" name="add_markledger" id="add-markledger">
                                    <i class="fa fa-save bigger-110"></i>
                                    Save
                                </button>

                                <button class="btn btn-success" type="submit" value="Save" name="add_markledger_another" id="add-student-another">
                                    <i class="fa fa-save bigger-110"></i>
                                    <i class="fa fa-plus bigger-110"></i>
                                    Save And Add Another
                                </button>
                            </div>
                        </div>

                        <div class="hr hr-24"></div>

                        {!! Form::close() !!}
                    </div>

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


@endsection

@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">

        // Auto-restore semester & subject after save (uses old() flashed values)
        $(document).ready(function () {
            var oldFaculty  = '{{ old("faculty") }}';
            var oldSemester = '{{ old("semester_select") }}';
            var oldSubject  = '{{ old("schedule_subject") }}';

            if (oldFaculty && oldFaculty !== '0') {
                $.ajax({
                    type: 'GET',
                    url: '{{ route("get-semesters") }}',
                    data: { faculty_id: oldFaculty },
                    success: function (semData) {
                        $('.semester_select').html('').append('<option value="0">Select Sem./Sec.</option>');
                        $.each(semData, function (id, name) {
                            var sel = (String(id) === String(oldSemester)) ? ' selected' : '';
                            $('.semester_select').append('<option value="' + id + '"' + sel + '>' + name + '</option>');
                        });

                        if (oldSemester && oldSemester !== '0') {
                            $.ajax({
                                type: 'POST',
                                url: '{{ route("exam.mark-ledger.find-subject") }}',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    years_id:   $('select[name="years_id"]').val(),
                                    months_id:  $('select[name="months_id"]').val(),
                                    exams_id:   $('select[name="exams_id"]').val(),
                                    faculty_id: oldFaculty,
                                    semester_id: oldSemester
                                },
                                success: function (response) {
                                    var data = $.parseJSON(response);
                                    if (!data.error) {
                                        $('.schedule_subject').html('').append('<option value="0">Select Subject</option>');
                                        $.each(data.subjects, function (key, obj) {
                                            $('.schedule_subject').append('<option value="' + obj.id + '">' + obj.title + '</option>');
                                        });
                                        // Clear student wrapper — teacher picks subject fresh
                                        $('#student_wrapper').html('');
                                        toastr.success('Mark saved! Select subject for next entry.', 'Success');
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });

        function loadSemesters($this) {
            var year = $('select[name="years_id"]').val();
            var month = $('select[name="months_id"]').val();
            var exam = $('select[name="exams_id"]').val();
            var faculty = $('select[name="faculty"]').val();

            if (year == 0) {
                toastr.info("Please, Select Year", "Info:");
                return false;
            }

            if (month == 0) {
                toastr.info("Please, Select Month", "Info:");
                return false;
            }

            if (exam == 0) {
                toastr.info("Please, Select Exam Type", "Info:");
                return false;
            }

            if (faculty == 0) {
                toastr.info("Please, Select Faculty/Program/Class", "Info:");
                return false;
            }

            $.ajax({
                type: 'GET',
                url: '{{ route('get-semesters') }}',
                data: { faculty_id: $this.value },
                success: function (data) {
                    if ($.isEmptyObject(data)) {
                        toastr.warning("No semester found for this faculty.", "Warning");
                    } else {
                        $('.semester_select').html('').append('<option value="0">Select Sem./Sec.</option>');
                        $.each(data, function(id, name){
                            $('.semester_select').append('<option value="'+id+'">'+name+'</option>');
                        });
                        toastr.success("Semester loaded.", "Success:");
                    }
                },
                error: function() {
                    toastr.error("Failed to load semesters.", "Error");
                }
            });

        }

        function loadSubject($this) {
            var year = $('select[name="years_id"]').val();
            var month = $('select[name="months_id"]').val();
            var exam = $('select[name="exams_id"]').val();
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semester_select"]').val();

            if (year == 0) {
                toastr.info("Please, Select Year", "Info:");
                return false;
            }

            if (month == 0) {
                toastr.info("Please, Select Month", "Info:");
                return false;
            }

            if (exam == 0) {
                toastr.info("Please, Select Exam Type", "Info:");
                return false;
            }

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
                    url: '{{ route('exam.mark-ledger.find-subject') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        years_id: year,
                        months_id: month,
                        exams_id: exam,
                        faculty_id: faculty,
                        semester_id: semester
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);
                        if (data.error) {
                            $('.schedule_subject').html('')
                            toastr.warning(data.error, "Warning:");
                        } else {
                                $('.schedule_subject').html('').append('<option value="0">Select Subject</option>');
                                $.each(data.subjects, function (key, valueObj) {
                                    $('.schedule_subject').append('<option value="' + valueObj.id + '">' + valueObj.title + '</option>');
                                });
                                toastr.success(data.success, "Success:");
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Subject load failed (HTTP ' + xhr.status + '). Please refresh and try again.', 'Error');
                    }
                });
            }

        }

        function loadStudent($this) {
            var year = $('select[name="years_id"]').val();
            var month = $('select[name="months_id"]').val();
            var exam = $('select[name="exams_id"]').val();
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semester_select"]').val();
            var subject = $('select[name="schedule_subject"]').val();

            if (year == 0) {
                toastr.info("Please, Select Year", "Info:");
                return false;
            }

            if (month == 0) {
                toastr.info("Please, Select Month", "Info:");
                return false;
            }

            if (exam == 0) {
                toastr.info("Please, Select Exam Type", "Info:");
                return false;
            }

            if (faculty == 0) {
                toastr.info("Please, Select Faculty/Program/Class", "Info:");
                return false;
            }

            if (semester == 0) {
                toastr.info("Please, Select Sem./Sec.", "Info:");
                return false;
            }

            if (subject == 0) {
                toastr.info("Please, Select Subject", "Info:");
                return false;
            }else{
                $('#student_wrapper').find("tr").remove();
            }

            $.ajax({
                type: 'POST',
                url: '{{ route('exam.mark-ledger.student-html') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    years_id: year,
                    months_id: month,
                    exams_id: exam,
                    faculty_id: faculty,
                    semester_id: semester,
                    subject_id: subject
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if(data.error){
                        toastr.warning(data.error, "Warning:");
                    }else{
                        if(data.exist){
                            $('#student_wrapper').append(data.exist);
                            $('#studentsTable tr:last').after(data.students);
                        }else{
                            $('#student_wrapper').append(data.students);
                        }
                        toastr.success(data.message, "Success:");
                    }
                }
            });
        }

        /*Schedule Now*/
        $('#ledger-save-btn').click(function () {
            var year = $('select[name="years_id"]').val();
            var month = $('select[name="months_id"]').val();
            var exam = $('select[name="exams_id"]').val();
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semester_select"]').val();
            var subject = $('select[name="schedule_subject"]').val();

            if (year == 0) {
                toastr.info("Please, Select Year", "Info:");
                return false;
            }

            if (month == 0) {
                toastr.info("Please, Select Month", "Info:");
                return false;
            }

            if (exam == 0) {
                toastr.info("Please, Select Exam Type", "Info:");
                return false;
            }

            if (faculty == 0) {
                toastr.info("Please, Select Faculty/Program/Class", "Info:");
                return false;
            }

            if (semester == 0) {
                toastr.info("Please, Select Sem./Sec.", "Info:");
                return false;
            }

            if (subject == 0) {
                toastr.info("Please, Select Subject", "Info:");
                return false;
            }

            location.href = url;

        });
        /*End Schedule Now*/

    </script>

    @include('includes.scripts.table_tr_sort')

@endsection

