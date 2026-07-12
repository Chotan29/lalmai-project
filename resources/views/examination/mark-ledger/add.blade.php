@extends('layouts.master')

@section('css')
    <style>
        /* Keep the mark-entry table header visible while scrolling */
        #studentsTable thead th {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        /* sticky needs its own opaque background so rows don't show through;
           JS below copies the theme's real header colors at load time. */

        /* .table-responsive's overflow:auto traps position:sticky inside the
           wrapper, so the header never sticks to the page. On desktop widths
           the table fits anyway - release the overflow so sticky works.
           Below 768px keep the horizontal scroll for small screens. */
        @media (min-width: 768px) {
            #ledger-table-wrap { overflow: visible !important; }
        }
    </style>
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

                        {{-- Truncation guard: placed at the TOP of the form so it always survives
                             PHP max_input_vars truncation. JS fills it with the row count at submit;
                             the server compares it against the received students_id[] count. --}}
                        <input type="hidden" name="expected_rows" id="expected-rows" value="0">

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

                                <button class="btn btn-warning" type="button" id="ledger-print-btn">
                                    <i class="fa fa-print bigger-110"></i>
                                    Print My Entries
                                </button>

                                <button class="btn btn-info" type="button" id="unlock-selected-btn" style="display:none;">
                                    <i class="fa fa-unlock bigger-110"></i>
                                    Unlock Selected
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
        /*Sticky table header: copy the theme's real header background onto the
          sticky THs (otherwise rows show through) and offset below any fixed navbar.*/
        jQuery(function ($) {
            var $ths = $('#studentsTable thead th');
            if (!$ths.length) return;
            var $src = $('#studentsTable thead tr');
            var bgColor = $src.css('background-color');
            var bgImage = $src.css('background-image');
            $ths.each(function () {
                var $th = $(this);
                var ownColor = $th.css('background-color');
                if (!ownColor || ownColor === 'rgba(0, 0, 0, 0)' || ownColor === 'transparent') {
                    $th.css('background-color', (bgColor && bgColor !== 'rgba(0, 0, 0, 0)') ? bgColor : '#eff3f8');
                    if (bgImage && bgImage !== 'none') $th.css('background-image', bgImage);
                }
            });
            var $nav = $('.navbar-fixed-top, #navbar').first();
            if ($nav.length && $nav.css('position') === 'fixed') {
                $ths.css('top', $nav.outerHeight() + 'px');
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
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (response) {
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                    if (data.error) {
                        toastr.warning(data.message || data.error, "Warning");
                    } else {
                        $('.semester_select').html('').append('<option value="0">Select Sem./Sec.</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.semester_select').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                        toastr.success(data.message || 'Semester Loaded.', "Success:");
                    }
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
                        var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                        if (data.error) {
                            $('.schedule_subject').html('')
                            toastr.warning(data.message || data.error, "Warning:");
                        } else {
                                $('.schedule_subject').html('').append('<option value="0">Select Subject</option>');
                                $.each(data.subjects, function (key, valueObj) {
                                    $('.schedule_subject').append('<option value="' + valueObj.id + '">' + valueObj.title + '</option>');
                                });
                                toastr.success(data.success, "Success:");
                        }
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
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                    if(data.error){
                        toastr.warning(data.message || data.error, "Warning:");
                    }else{
                        if(data.exist){
                            $('#student_wrapper').append(data.exist);
                            $('#studentsTable tr:last').after(data.students);
                        }else{
                            $('#student_wrapper').append(data.students);
                        }
                        updateMarkHeaders(data.limits);
                        applyAbsentState();
                        updateSummary(data.exist_count, data.new_count, data.locked_count);
                        /*Admin-only: show the bulk unlock button when owned rows are present*/
                        $('#unlock-selected-btn').toggle($('.unlock-chk').length > 0);
                        toastr.success(data.message, "Success:");
                    }
                }
            });
        }

        /*Show full mark in column headers*/
        function updateMarkHeaders(limits) {
            if (!limits) return;
            if (parseFloat(limits.theory) > 0) $('#th-theory').text('Obtain Mark (' + limits.theory + ')');
            if (parseFloat(limits.mcq) > 0) $('#th-mcq').text('MCQ (' + limits.mcq + ')');
            if (parseFloat(limits.practical) > 0) $('#th-practical').text('Practical (' + limits.practical + ')');
        }

        /*Entry summary: total / entered / remaining*/
        function updateSummary(existCount, newCount, lockedCount) {
            var total = $('#student_wrapper tr').length;
            if (total === 0) { $('#entry-summary').hide(); return; }
            var entered = 0;
            $('#student_wrapper tr').each(function () {
                var th = $(this).find('input[name="obtain_mark_theory[]"]');
                var thAbsent = $(this).find('input[name="absent_theory[]"]').is(':checked');
                if (thAbsent || (th.val() !== '' && th.val() !== null)) entered++;
            });
            var msg = 'Total Students: <b>' + total + '</b> | Mark Entered: <b>' + entered + '</b> | Remaining: <b>' + (total - entered) + '</b>';
            if (typeof existCount !== 'undefined' && typeof newCount !== 'undefined') {
                msg += ' | Previously Saved: <b>' + existCount + '</b> | New: <b>' + newCount + '</b>';
            }
            if (typeof lockedCount !== 'undefined' && lockedCount > 0) {
                msg += ' | <span style="color:#b7791f;"><i class="fa fa-lock"></i> Locked by other teacher: <b>' + lockedCount + '</b></span>';
            }
            $('#entry-summary').html(msg).show();
        }

        /*Absent checked => clear + lock the related mark input*/
        function toggleAbsentInput(checkbox, inputName) {
            var input = $(checkbox).closest('tr').find('input[name="' + inputName + '"]');
            if (checkbox.checked) {
                input.val('').prop('readonly', true).css('background', '#eee');
            } else {
                input.prop('readonly', false).css('background', '');
            }
        }

        function applyAbsentState() {
            $('#student_wrapper input[name="absent_theory[]"]').each(function () {
                toggleAbsentInput(this, 'obtain_mark_theory[]');
            });
            $('#student_wrapper input[name="absent_practical[]"]').each(function () {
                toggleAbsentInput(this, 'obtain_mark_practical[]');
            });
        }

        $('#student_wrapper').on('change', 'input[name="absent_theory[]"]', function () {
            toggleAbsentInput(this, 'obtain_mark_theory[]');
            updateSummary();
        });

        $('#student_wrapper').on('change', 'input[name="absent_practical[]"]', function () {
            toggleAbsentInput(this, 'obtain_mark_practical[]');
        });

        /*Live summary refresh while typing*/
        $('#student_wrapper').on('input', 'input[name="obtain_mark_theory[]"]', function () {
            updateSummary();
        });

        /*Enter / Arrow keys: jump to same column of next/previous row*/
        $('#studentsTable').on('keydown', 'input[type="number"]', function (e) {
            if (e.key === 'Enter' || e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                var name = $(this).attr('name');
                var inputs = $('#studentsTable input[name="' + name + '"]:not([readonly])');
                var idx = inputs.index(this);
                var next = (e.key === 'ArrowUp') ? idx - 1 : idx + 1;
                if (next >= 0 && next < inputs.length) inputs.eq(next).focus().select();
            }
        });

        /*Print own entries (teacher => only rows entered by him/her)*/
        $('#ledger-print-btn').click(function () {
            var year = $('select[name="years_id"]').val();
            var month = $('select[name="months_id"]').val();
            var exam = $('select[name="exams_id"]').val();
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semester_select"]').val();
            var subject = $('select[name="schedule_subject"]').val();

            if (!year || year == 0 || !month || month == 0 || !exam || exam == 0 ||
                !faculty || faculty == 0 || !semester || semester == 0 || !subject || subject == 0) {
                toastr.info("Please, Select Year, Month, Exam, Class, Sem./Sec. & Subject First", "Info:");
                return false;
            }

            var url = '{{ route('exam.mark-ledger.print') }}'
                + '?years_id=' + year
                + '&months_id=' + month
                + '&exams_id=' + exam
                + '&faculty_id=' + faculty
                + '&semester_id=' + semester
                + '&subject_id=' + subject;

            window.open(url, '_blank');
        });

        /*Block double submission + set expected row count for server-side truncation check*/
        $('#validation-form').on('submit', function () {
            $('#expected-rows').val($('#student_wrapper tr.option_value').length);
            var $btns = $(this).find('button[type="submit"]');
            setTimeout(function () { $btns.prop('disabled', true); }, 10);
            setTimeout(function () { $btns.prop('disabled', false); }, 8000);
        });

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

        /*Auto-restore filter & student list after save (old input)*/
        var oldFilter = {
            faculty: '{{ old('faculty') }}',
            semester: '{{ old('semester_select') }}',
            subject: '{{ old('schedule_subject') }}'
        };

        $(function () {
            if (!oldFilter.faculty || !oldFilter.semester || !oldFilter.subject) return;
            if (oldFilter.faculty == 0 || oldFilter.semester == 0 || oldFilter.subject == 0) return;

            /*Step 1: repopulate semester options*/
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                data: { _token: '{{ csrf_token() }}', faculty_id: oldFilter.faculty },
                success: function (response) {
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                    if (data.error) return;
                    $('.semester_select').html('').append('<option value="0">Select Sem./Sec.</option>');
                    $.each(data.semester, function (key, valueObj) {
                        $('.semester_select').append('<option value="' + valueObj.id + '">' + valueObj.semester + '</option>');
                    });
                    $('.semester_select').val(oldFilter.semester);

                    /*Step 2: repopulate subject options*/
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('exam.mark-ledger.find-subject') }}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            years_id: $('select[name="years_id"]').val(),
                            months_id: $('select[name="months_id"]').val(),
                            exams_id: $('select[name="exams_id"]').val(),
                            faculty_id: oldFilter.faculty,
                            semester_id: oldFilter.semester
                        },
                        success: function (response) {
                            var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                            if (data.error) return;
                            $('.schedule_subject').html('').append('<option value="0">Select Subject</option>');
                            $.each(data.subjects, function (key, valueObj) {
                                $('.schedule_subject').append('<option value="' + valueObj.id + '">' + valueObj.title + '</option>');
                            });
                            $('.schedule_subject').val(oldFilter.subject);

                            /*Step 3: reload student list*/