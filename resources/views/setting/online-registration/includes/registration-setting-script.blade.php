<script>
    $(document).ready(function () {
        // Initialize select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Initialize datepicker
        $('.date-picker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
        });

        // Add program row
        $('#add-program-html').click(function () {
            $.ajax({
                type: 'POST',
                url: '{{ route('setting.online-registration.program-html') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);

                    if (data.error) {
                        toastr.warning(data.message, "Warning");
                    } else {
                        $('#program_wrapper').append(data.html);
                        $('#add-program-html').show();
                        $('.select2').select2({
                            theme: 'bootstrap4',
                            width: '100%'
                        });
                        $('.date-timepicker1').datetimepicker({
                            format: 'YYYY-MM-DD',
                            useCurrent: false
                        });
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while adding program.', "Error");
                }
            });
        });

        // Remove program row
        $(document).on('click', '.remove-program-row', function() {
            $(this).closest('tr').remove();
        });

        // Delete program
        $(document).on('click', '.delete-program', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var $row = $(this).closest('tr');

            if (!id) {
                toastr.warning('Invalid program id.', "Warning");
                return;
            }

            if (!window.confirm('Are you sure you want to delete this program?')) {
                return;
            }

            $.ajax({
                type: 'GET',
                url: '{{ route('setting.online-registration.remove-program') }}',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function (response) {
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                    if (data.error) {
                        toastr.warning(data.message || 'Unable to delete program.', "Warning");
                    } else {
                        $row.remove();
                        toastr.success(data.message || 'Program removed successfully.', "Success");
                    }
                },
                error: function(xhr) {
                    var message = 'An error occurred while deleting program.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message, "Error");
                }
            });
        });
    });

    function loadSemesters($this) {
        var facultyId = $this.value;
        var $semesterSelect = $($this).closest('tr').find('.semester_select');
        
        if (!facultyId) {
            $semesterSelect.empty().append('<option value="0">Select Sem./Sec.</option>');
            $semesterSelect.select2();
            return;
        }

        $.ajax({
            type: 'POST',
            url: '{{ route('setting.online-registration.find-semester') }}',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                faculty_id: facultyId
            },
            beforeSend: function() {
                $semesterSelect.empty().append('<option value="">Loading...</option>');
            },
            success: function (data) {
                if (data.error) {
                    toastr.warning(data.message || 'No semesters available for the selected faculty.', "Warning");
                    $semesterSelect.empty().append('<option value="0">Select Sem./Sec.</option>');
                } else {
                    $semesterSelect.empty().append('<option value="0">Select Sem./Sec.</option>');
                    $.each(data.semester || [], function(key, valueObj) {
                        $semesterSelect.append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                    });
                }
                $semesterSelect.trigger('change');
                $semesterSelect.select2();
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading semesters.', "Error");
                $semesterSelect.empty().append('<option value="0">Select Sem./Sec.</option>');
                $semesterSelect.select2();
            }
        });
    }
</script>