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
                            List
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        @include('mcq.includes.buttons')
                        @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                        <div class="form-horizontal ">
                                @include($view_path.'.includes.form')
                        </div>
                    </div><!-- /.col -->

                    @include($view_path.'.includes.table')

                </div><!-- /.row -->
            </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection

@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">

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

            $("form").submit(function() {
                $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
                return true; // ensure form still submits
            });

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
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.dataTable_scripts')

    @endsection