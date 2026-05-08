@extends('layouts.master')

@section('css')
    <style>
        #validation-form fieldset {
            margin-bottom: 24px;
            padding: 18px 16px 10px;
            border: 1px solid #e5e8ef;
            background: #fff;
        }

        #validation-form legend {
            width: auto;
            margin-bottom: 18px;
            padding: 0 10px;
            border: 0;
            font-size: 16px;
            font-weight: 600;
            color: #1f3b5b;
        }

        #validation-form .form-group {
            margin-right: 0;
            margin-left: 0;
            margin-bottom: 16px;
        }

        #validation-form .control-label {
            padding-top: 10px;
            padding-bottom: 10px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        #validation-form .form-control {
            min-height: 40px;
        }

        #validation-form .control-label .text-danger {
            margin-left: 3px;
            font-weight: 700;
        }

        #validation-form .form-control.field-invalid,
        #validation-form .chosen-container.field-invalid .chosen-single,
        #validation-form .chosen-container.field-invalid .chosen-choices {
            border-color: #d9534f !important;
            box-shadow: 0 0 0 2px rgba(217, 83, 79, 0.12);
        }

        #validation-form .validation-note {
            display: none;
            margin-top: 6px;
            color: #d9534f;
            font-size: 12px;
            font-weight: 600;
        }

        #validation-form .validation-note.is-visible {
            display: block;
        }

        #validation-form textarea.form-control {
            min-height: 72px;
            resize: vertical;
        }

        #validation-form .label.arrowed,
        #validation-form .label.arrowed-in {
            display: inline-block;
            margin-bottom: 10px;
            padding: 7px 12px;
        }

        @media (max-width: 767px) {
            #validation-form fieldset {
                padding: 14px 12px 8px;
            }

            #validation-form .control-label {
                margin-bottom: 6px;
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

