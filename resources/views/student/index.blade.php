@extends('layouts.master')

@section('css')
    <style>
        /* Status Dropdown */
        /* Extra Small Button Styling */
.btn-xs {
    padding: 2px 8px;
    font-size: 11px;
    line-height: 1.2;
    border-radius: 12px;
}

.status-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-weight: 500;
}

.dropdown-menu-status {
    min-width: 140px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 0.3rem 0;
}

.dropdown-menu-status .dropdown-item {
    font-size: 12px;
    padding: 5px 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.dropdown-menu-status .dropdown-item:hover {
    background-color: #f5f5f5;
}

.status-dropdown .dropdown-toggle {
    padding: 2px 8px;
    font-size: 11px;
    line-height: 1.2;
    border-radius: 12px;
}

.dropdown-menu-status {
    min-width: 140px;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    font-size: 13px;
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
                        @include($view_path . '.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        @include($view_path . '.includes.buttons')
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                        <div class="form-horizontal filter-form">
                            @include($view_path . '.includes.form')
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                @include($view_path . '.includes.table')
                @include($view_path . '.includes.hostel_model')
                @include($view_path . '.includes.transport_model')
            </div>
        </div><!-- /.page-content -->
    </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        $('.show-details-btn').on('click', function(e) {
            e.preventDefault();
            $(this).closest('tr').next().toggleClass('open');
            $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
        });

        $('#filter-btn').click(function() {
            @include('student.includes.common-script.student_filter_common_script')
            location.href = url;
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
                success: function(response) {
                    var data = (typeof response === 'string') ? $.parseJSON(response) : response;
                    if (!data) return;
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.semester_select').html('').append('<option value="0">Select Sem./Sec.</option>');
                        $.each(data.semester, function(key, valueObj) {
                            $('.semester_select').append('<option value="' + valueObj.id + '">' +
                                valueObj.semester + '</option>');
                        });
                    }
                }
            });
        }

        // chosen-select plugin fires jQuery change events — bind explicitly as fallback
        $(document).on('change', 'select[name="faculty"]', function() {
            loadSemesters(this);
        });

        //hostel
        $(document).on("click", ".open-ActiveAgain", function() {
            var Id = $(this).data('id');
            var regNum = $(this).data('reg');

            $(".modal-body #residentId").val(Id);
            //$(".modal-body #hostelTitle").val(hostelTitle);
            $(".modal-header").html(
                "<button type=\"button\" class=\"close top-close\" data-dismiss=\"modal\" id=\"close-button\">" +
                "×</button><h4 class=\"modal-title title text-center renew_title\" id=\"renew_title\"><b>" +
                regNum + "</b> | Manage Resident</h4>\n" +
                "     ");
        });

        function loadRooms($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.find-rooms') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    hostel_id: $this.value
                },
                success: function(response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.room_select').html('').append('<option value="0">Select Room...</option>');
                        $.each(data.rooms, function(key, valueObj) {
                            $('.room_select').append('<option value="' + valueObj.id + '">' + valueObj
                                .room_number + '</option>');
                        });
                    }
                }
            });

        }

        function loadBeds($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.find-beds') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    room_id: $this.value
                },
                success: function(response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.bed_select').html('').append('<option value="0">Select Beds...</option>');
                        $.each(data.beds, function(key, valueObj) {
                            $('.bed_select').append('<option value="' + valueObj.id + '">' + valueObj
                                .bed_number + '</option>');
                        });
                    }
                }
            });
        }

        //transport
        $(document).on("click", ".open-TransportActiveAgain", function() {
            var Id = $(this).data('id');
            var regNum = $(this).data('reg');

            $(".modal-body #userId").val(Id);
            $(".modal-header").html(
                "<button type=\"button\" class=\"close top-close\" data-dismiss=\"modal\" id=\"close-button\">" +
                "×</button><h4 class=\"modal-title title text-center renew_title\" id=\"renew_title\"><b>" +
                regNum + "</b> | Manage Transport User</h4>\n" +
                "     ");
        });

        function loadVehicle($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('transport.find-vehicles') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    route_id: $this.value
                },
                success: function(response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.vehicle_select').html('').append('<option value="0">Select Vehicle...</option>');
                        $.each(data.vehicles, function(key, valueObj) {
                            $('.vehicle_select').append('<option value="' + valueObj.id + '">' +
                                valueObj.number + ' | ' + valueObj.type + '</option>');
                        });
                    }
                }
            });

        }
    </script>
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.dataTable_scripts')
@endsection
