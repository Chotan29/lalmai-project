@extends('layouts.master')

@section('css')
    <style>
        /* ============================================================
           Modern Registration Form Design - Online Registration Style
           ============================================================ */

        /* Form wrapper card */
        #validation-form {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.08);
            padding: 24px;
            margin-top: 10px;
        }

        /* Tab Navigation */
        #validation-form .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 28px;
            padding: 0;
        }

        #validation-form .nav-tabs > li > a {
            border: none !important;
            border-bottom: 3px solid transparent !important;
            color: #555;
            font-weight: 500;
            font-size: 13px;
            padding: 12px 16px;
            border-radius: 0 !important;
            margin-right: 0;
            transition: color 0.2s, border-color 0.2s;
        }

        #validation-form .nav-tabs > li > a:hover {
            color: #1e5799;
            background: transparent !important;
            border-bottom-color: #a8c4e8 !important;
        }

        #validation-form .nav-tabs > li.active > a,
        #validation-form .nav-tabs > li.active > a:focus,
        #validation-form .nav-tabs > li.active > a:hover {
            color: #1e5799 !important;
            background: transparent !important;
            border-bottom: 3px solid #1e5799 !important;
            font-weight: 700;
        }

        /* Fieldset → form-section card */
        #validation-form fieldset {
            margin-bottom: 24px;
            padding: 24px;
            border: 1px solid #e1eaf4 !important;
            border-radius: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        /* Legend → section-title style */
        #validation-form legend {
            width: 100%;
            float: none;
            margin-bottom: 20px;
            padding-bottom: 14px;
            padding-left: 0;
            padding-right: 0;
            border: 0;
            border-bottom: 1px solid #d9e4ef;
            font-size: 16px;
            font-weight: 700;
            color: #16324a;
            letter-spacing: 0.01em;
            display: block;
        }

        #validation-form legend::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 18px;
            background: linear-gradient(135deg, #1e5799 0%, #2f80c8 100%);
            border-radius: 3px;
            margin-right: 10px;
            vertical-align: middle;
            box-shadow: 0 2px 8px rgba(30, 87, 153, 0.25);
        }

        /* Form group spacing */
        #validation-form .form-group {
            margin-right: 0;
            margin-left: 0;
            margin-bottom: 18px;
        }

        /* Control label */
        #validation-form .control-label {
            padding-top: 11px;
            padding-bottom: 6px;
            font-weight: 600;
            color: #23384d;
            font-size: 13px;
            letter-spacing: 0.1px;
        }

        #validation-form .control-label .text-danger {
            margin-left: 3px;
            font-weight: 700;
        }

        /* Modern inputs */
        #validation-form .form-control {
            min-height: 44px !important;
            padding: 9px 12px;
            border: 1px solid #cdd6e0;
            border-radius: 8px !important;
            box-shadow: none !important;
            font-size: 13px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        #validation-form .form-control:focus {
            border-color: #2b6cb0 !important;
            box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.12) !important;
            outline: none;
        }

        #validation-form textarea.form-control {
            min-height: 76px !important;
            resize: vertical;
        }

        /* Chosen select modern look */
        #validation-form .chosen-container .chosen-single {
            min-height: 44px !important;
            line-height: 42px;
            border: 1px solid #cdd6e0 !important;
            border-radius: 8px !important;
            box-shadow: none !important;
            background: #fff !important;
            background-image: none !important;
            padding: 0 0 0 12px;
            color: #333;
        }

        #validation-form .chosen-container-active .chosen-single,
        #validation-form .chosen-container-active.chosen-with-drop .chosen-single {
            border-color: #2b6cb0 !important;
            box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.12) !important;
        }

        #validation-form .chosen-container .chosen-single span {
            line-height: 44px;
        }

        #validation-form .chosen-container .chosen-single b {
            background-position: 0 12px;
        }

        #validation-form .chosen-container .chosen-drop {
            border: 1px solid #cdd6e0;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Invalid field highlighting */
        #validation-form .form-control.field-invalid,
        #validation-form .chosen-container.field-invalid .chosen-single {
            border-color: #d9534f !important;
            box-shadow: 0 0 0 3px rgba(217, 83, 79, 0.1) !important;
        }

        #validation-form .validation-note {
            display: none;
            margin-top: 5px;
            color: #d9534f;
            font-size: 12px;
            font-weight: 600;
        }

        #validation-form .validation-note.is-visible {
            display: block;
        }

        /* arrowed labels */
        #validation-form .label.arrowed,
        #validation-form .label.arrowed-in {
            display: inline-block;
            margin-bottom: 10px;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 13px;
        }

        /* Next / Previous buttons inside tabs */
        #validation-form .tab-pane .btn-info,
        #validation-form .tab-pane .btn-primary {
            min-width: 120px;
            border-radius: 8px;
            padding: 9px 20px;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.2px;
        }

        #validation-form .tab-pane .btn-info {
            background: linear-gradient(135deg, #1e5799 0%, #2f80c8 100%);
            border-color: #1e5799;
            color: #fff;
        }

        #validation-form .tab-pane .btn-info:hover {
            background: linear-gradient(135deg, #174a87 0%, #2570b5 100%);
            border-color: #174a87;
        }

        #validation-form .tab-pane .btn-primary {
            background: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        #validation-form .tab-pane .btn-primary:hover {
            background: #5a6268;
            border-color: #545b62;
        }

        /* Register / Reset buttons at the bottom */
        .form-actions .btn {
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            font-size: 13px;
            min-width: 110px;
        }

        /* Photo preview box */
        #student-photo-preview {
            border-radius: 8px;
            border: 2px solid #e1eaf4;
            padding: 4px;
            background: #f8fbff;
        }

        @media (max-width: 767px) {
            #validation-form {
                padding: 14px;
            }

            #validation-form fieldset {
                padding: 16px;
                border-radius: 12px;
            }

            #validation-form .nav-tabs > li > a {
                padding: 9px 10px;
                font-size: 12px;
            }

            #validation-form .control-label {
                margin-bottom: 4px;
                text-align: left;
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
                    <h1>
                        @include($view_path.'.registration.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Registration
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                        <div class="align-right">
                            <a class="{!! request()->is('student/import*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('student.import') }}"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Bulk Student Registration</a>
                        </div>
                        {!! Form::open(['route' => $base_route.'.register', 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @include($view_path.'.registration.includes.form')
                            <div class="clearfix form-actions">
                                <div class="col-md-12 align-center">
                                    <button class="btn" type="reset">
                                        <i class="fa fa-undo bigger-110"></i>
                                        Reset
                                    </button>

                                    <button class="btn btn-primary" type="submit" value="Save" name="add_student" id="add-student">
                                        <i class="fa fa-save bigger-110"></i>
                                        Register
                                    </button>

                                    <button class="btn btn-success" type="submit" value="Save" name="add_student_another" id="add-student-another">
                                        <i class="fa fa-save bigger-110"></i>
                                        <i class="fa fa-plus bigger-110"></i>
                                        Register And Add Another
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
    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.inputMask_script')
    {{--@include('includes.scripts.jquery_validation_scripts')--}}
    @include('student.registration.includes.student-common-script')
@endsection

