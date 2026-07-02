@extends('layouts.master')

@section('css')
    <style>
        /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f5f5;
    color: #333;
    line-height: 1.6;
}

/* Card Styling */
.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 25px;
    background-color: #fff;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eee;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0 !important;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #444;
}

.card-body {
    padding: 25px;
}

/* Form Section Styling */
.form-section {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #eee;
}

.form-section-title {
    color: #3c8dbc;
    border-bottom: 1px solid #f4f4f4;
    padding-bottom: 12px;
    margin-bottom: 25px;
    font-size: 18px;
    font-weight: 600;
}

/* Form Elements */
.form-control {
    border-radius: 4px;
    border: 1px solid #ddd;
    padding: 8px 12px;
    height: auto;
    box-shadow: none;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    color: #495057;
}

/* Select2 Customization */
.select2-container--bootstrap4 .select2-selection {
    border: 1px solid #ddd !important;
    height: auto !important;
    padding: 6px 12px !important;
}

.select2-container--bootstrap4 .select2-selection--single {
    height: 38px !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 24px !important;
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
    background-color: #e9ecef !important;
    border: 1px solid #ddd !important;
    color: #495057 !important;
}

/* Buttons */
.btn {
    border-radius: 4px;
    padding: 8px 16px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary {
    background-color: #3c8dbc;
    border-color: #367fa9;
}

.btn-primary:hover {
    background-color: #367fa9;
    border-color: #2c6a8f;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-light {
    background-color: #f8f9fa;
    border-color: #f8f9fa;
    color: #212529;
}

.btn-light:hover {
    background-color: #e2e6ea;
    border-color: #dae0e5;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

/* Tables */
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    border-collapse: collapse;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
    padding: 12px;
}

.program-management-panel {
    border: 1px solid #e6edf5;
    border-radius: 10px;
    background: #ffffff;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
}

.program-management-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: linear-gradient(90deg, #f7fbff 0%, #eef5ff 100%);
    border-bottom: 1px solid #e6edf5;
}

.program-management-title {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
}

.program-management-subtitle {
    margin: 3px 0 0;
    font-size: 12px;
    color: #6c7a89;
}

.program-table {
    margin-bottom: 0;
}

.program-table thead th {
    background: #f9fbfd;
    color: #2f3b4c;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-top: 0;
}

.program-table tbody tr:hover {
    background: #fcfdff;
}

.btn-icon-only {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border-radius: 8px;
}

.btn-soft-primary {
    background: #2f80ed;
    border-color: #2f80ed;
    color: #fff;
}

.btn-soft-primary:hover,
.btn-soft-primary:focus {
    background: #216fda;
    border-color: #216fda;
    color: #fff;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.03);
}

.thead-light th {
    background-color: #f8f9fa;
    color: #495057;
    border-color: #dee2e6;
    font-weight: 600;
}

/* Custom File Input */
.custom-file-input:focus ~ .custom-file-label {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.custom-file-label {
    height: 38px;
    padding: 8px 12px;
    line-height: 1.5;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.custom-file-label::after {
    height: 36px;
    padding: 8px 12px;
    line-height: 1.5;
    border-radius: 0 4px 4px 0;
    background-color: #e9ecef;
    border-left: 1px solid #ddd;
}

/* Existing Image Container */
.existing-image-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.existing-image-container img {
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 5px;
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.image-actions {
    margin-top: 10px;
}

/* Summernote Editor */
.note-editor.note-frame {
    border: 1px solid #ddd !important;
    border-radius: 4px;
}

.note-editor.note-frame .note-toolbar {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #ddd !important;
}

/* Toastr Notifications Customization */
.toast {
    border-radius: 4px !important;
}

.toast-success {
    background-color: #28a745 !important;
}

.toast-warning {
    background-color: #ffc107 !important;
}

.toast-error {
    background-color: #dc3545 !important;
}

/* Bootbox Modal Customization */
.bootbox.modal {
    z-index: 1060 !important;
}

.bootbox .modal-content {
    border-radius: 8px;
}

.bootbox .modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eee;
    padding: 15px 20px;
}

.bootbox .modal-body {
    padding: 20px;
}

.bootbox .modal-footer {
    border-top: 1px solid #eee;
    padding: 15px 20px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .form-group.row > .col-sm-2,
    .form-group.row > .col-sm-10 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .form-group.row label {
        margin-bottom: 8px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .form-section {
        padding: 15px;
    }
}

/* Animation for AJAX operations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.ajax-loading {
    animation: fadeIn 0.3s ease-in-out;
}

/* Custom spacing utilities */
.mb-3 {
    margin-bottom: 1rem !important;
}

.mt-2 {
    margin-top: 0.5rem !important;
}

.float-right {
    float: right !important;
}

.text-muted {
    color: #6c757d !important;
}

.text-center {
    text-align: center !important;
}

/* Datepicker Customization */
.datepicker {
    border-radius: 4px !important;
    border: 1px solid #ddd !important;
    padding: 10px !important;
}

.datepicker table tr td.active.active,
.datepicker table tr td.active.disabled,
.datepicker table tr td.active.disabled.active,
.datepicker table tr td.active.disabled.disabled,
.datepicker table tr td.active.disabled:active,
.datepicker table tr td.active.disabled:hover,
.datepicker table tr td.active.disabled:hover.active,
.datepicker table tr td.active.disabled:hover.disabled,
.datepicker table tr td.active.disabled:hover:active,
.datepicker table tr td.active.disabled:hover:hover,
.datepicker table tr td.active:active,
.datepicker table tr td.active:hover,
.datepicker table tr td.active:hover.active,
.datepicker table tr td.active:hover.disabled,
.datepicker table tr td.active:hover:active,
.datepicker table tr td.active:hover:hover {
    background-color: #3c8dbc !important;
    background-image: none !important;
}

.datepicker table tr td.today {
    background-color: #fcf8e3 !important;
    background-image: none !important;
}

/* Input Group Addon Styling */
.input-group-append .input-group-text,
.input-group-prepend .input-group-text {
    padding: 8px 12px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    color: #495057;
}

/* Easy Link Menu */
.easy-link-menu {
    margin-bottom: 20px;
}

.easy-link-menu a {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Form Validation Messages */
.invalid-feedback {
    color: #dc3545;
    font-size: 80%;
    margin-top: 0.25rem;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.is-invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* Page Header Styling */
.page-header {
    padding-bottom: 15px;
    margin-bottom: 25px;
    border-bottom: 1px solid #eee;
}

.page-header h1 {
    font-size: 24px;
    margin: 0;
    font-weight: 600;
    color: #333;
}

.page-header h1 small {
    font-size: 14px;
    color: #6c757d;
    font-weight: 400;
}

/* Form Actions */
.form-actions {
    margin-top: 25px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

/* Custom checkbox and radio */
.custom-control-input:checked ~ .custom-control-label::before {
    border-color: #3c8dbc;
    background-color: #3c8dbc;
}

.custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    {{-- <div class="d-flex justify-content-between align-items-center">
                        <h1>
                            @include($view_path.'.includes.breadcrumb-primary')
                            <small>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                Edit Registration Setting
                            </small>
                        </h1>
                        <div class="form-actions">
                            <a href="#" class="btn btn-sm btn-secondary">
                          
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div> --}}
                    @include('setting.includes.buttons')
                                @include('includes.flash_messages')
                                @include('includes.validation_error_messages')
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Edit Registration Settings</h3>
                            </div>
                            <div class="card-body">                                
                                
                                {!! Form::model($data['row'], ['route' => [$base_route.'.update', $data['row']->id], 'method' => 'POST', 'class' => 'form-horizontal',
                                    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                                    {!! Form::hidden('id', $data['row']->id) !!}

                                    @include($view_path.'.includes.form')

                                    <div class="clearfix form-actions">
                                        <div class="col-md-offset-2 col-md-10">
                                            <button class="btn btn-light" type="reset">
                                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                                Reset
                                            </button>
                                            <button class="btn btn-primary" type="submit">
                                                <i class="ace-icon fa fa-save bigger-110"></i>
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    @include('includes.scripts.summarnote')
    @include('includes.scripts.datepicker_script')
    @include('setting.online-registration.includes.registration-setting-script')
@endsection