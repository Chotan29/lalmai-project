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
                        @include($view_path.'.book-request.student.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            List
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('library.includes.buttons')
                    <div class="col-xs-12 ">
                    @include('includes.flash_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            @include($view_path.'.book-request.student.includes.search_form')
                            <div class="hr hr-18 dotted hr-double"></div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>

            @include($view_path.'.book-request.student.includes.table')
            </div><!-- /.page-content -->
        </div>

    </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        $(document).ready(function () {

            $('#filter-btn').click(function () {
                @include('student.includes.common-script.student_filter_common_script')
                    location.href = url;
            });

        });

        function loadSemesters($this) {
            var facultyId = typeof $this === 'object' && $this.value !== undefined ? $this.value : $this;
            if (!facultyId) return;
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: facultyId
                },
                success: function (response) {
                    var data = (typeof response === 'string') ? $.parseJSON(response) : response;
                    if (!data) return;
                    $('select[name="semester_select"]').html('').append('<option value="0">Select Sem./Sec.</option>');
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $.each(data.semester, function(key,valueObj){
                            $('select[name="semester_select"]').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });
        }

        $(document).on('change', 'select[name="faculty"]', function() {
            loadSemesters(this);
        });
    </script>
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')

@endsection