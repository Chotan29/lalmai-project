@extends('layouts.master')

@section('css')
<style>
    /* Certificate Issuance Page Styles */
    .certificate-issue-container {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .search-student-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e9ecef;
    }
    
    .search-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .search-header i {
        font-size: 24px;
        color: #3498db;
        margin-right: 10px;
    }
    
    .search-header h4 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .student-select-container .select2-container {
        width: 100% !important;
    }
    
    .verify-btn {
        margin-top: 15px;
        padding: 8px 20px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    
    .verify-btn i {
        margin-right: 8px;
    }
    
    .student-detail-wrapper {
        transition: all 0.3s ease;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .search-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .search-header i {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>
                        @include($view_path.'.issue.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Certificate Issuance
                        </small>
                    </h1>
                    @include('certificate.includes.buttons')
                </div>
            </div><!-- /.page-header -->

            <div class="certificate-issue-container">
                @include('includes.flash_messages')
                
                <!-- Student Search Section -->
                <div class="search-student-card">
                    <div class="search-header">
                        <i class="fa fa-search"></i>
                        <h4>Search & Verify Student Before Issuing Certificate</h4>
                    </div>
                    
                    <div class="form-group student-select-container">
                        {!! Form::select('students_id', [], null, [
                            "placeholder" => "Type Student Name | Reg.No. | Mobile | Email...", 
                            "class" => "form-control select2", 
                            "style" => "width: 100%;",
                            "id" => "student-select"
                        ]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'students_id'])
                        
                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-primary verify-btn" id="load-html-btn">
                                <i class="fa fa-user-check"></i> Verify Student
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Student Details Section -->
                <div id="student_wrapper" class="student-detail-wrapper"></div>
                
                <!-- Certificate Form Section -->
                @include('certificate.issue.includes.form')
            </div>
        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->
@endsection

@section('js')
<!-- Load jQuery first -->
{{-- <script src="{{ asset('assets/js/jquery.min.js') }}"></script> --}}
<!-- Then load Select2 -->
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<!-- Load Toastr if not already loaded in master layout -->
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>

<script>
    // Ensure jQuery is properly loaded
    if (typeof jQuery == 'undefined') {
        console.error('jQuery is not loaded');
    } else {
        console.log('jQuery version:', jQuery.fn.jquery);
    }

    $(document).ready(function () {
        console.log('Document ready - initializing Select2');
        
        // Initialize Select2 with better configuration
        $('#student-select').select2({
            placeholder: 'Search Student...',
            allowClear: true,
            minimumInputLength: 3,
            ajax: {
                url: '{{ route('student.student-name-autocomplete') }}',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            templateResult: formatStudent,
            templateSelection: formatStudentSelection
        }).on('select2:open', function () {
            console.log('Select2 opened');
        });
        
        // Format student results in dropdown
        function formatStudent(student) {
            if (student.loading) return student.text;
            
            var $container = $(
                '<div class="student-result">' +
                    '<span class="student-name">' + student.name + '</span>' +
                    '<span class="student-meta">' +
                        '<span class="reg-no">' + student.reg_no + '</span>' +
                        (student.faculty ? '<span class="faculty">' + student.faculty + '</span>' : '') +
                    '</span>' +
                '</div>'
            );
            
            return $container;
        }
        
        // Format selected student
        function formatStudentSelection(student) {
            return student.name || student.text;
        }
        
        // Student Verification
        $('#load-html-btn').click(function () {
            var studentId = $('#student-select').val();
            var $studentWrapper = $('#student_wrapper');
            var $btn = $(this);
            
            if (!studentId) {
                toastr.warning("Please select a student first", "Warning");
                return;
            }
            
            // Show loading state
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Verifying...')
                .prop('disabled', true);
            
            // Clear previous results
            $studentWrapper.empty().hide();
            
            $.ajax({
                type: 'POST',
                url: '{{ route('certificate.student-detail-html') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: studentId
                },
                success: function (response) {
                    try {
                        var data = $.parseJSON(response);
                        
                        if (data.error) {
                            toastr.error(data.message, "Error");
                        } else {
                            $studentWrapper.html(data.html).slideDown();
                            toastr.success("Student verified successfully", "Success");
                        }
                    } catch (e) {
                        toastr.error("Error processing student data", "Error");
                        console.error(e);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Failed to verify student. Please try again.", "Error");
                    console.error("AJAX Error:", status, error);
                },
                complete: function() {
                    $btn.html('<i class="fa fa-user-check"></i> Verify Student')
                        .prop('disabled', false);
                }
            });
        });
    });
</script>

@include('includes.scripts.inputMask_script')
@include('includes.scripts.datepicker_script')
@endsection