@extends('layouts.master')

@section('css')
    <style>
        /* ============================================================
           Modern Staff Form Design - same as Student Registration
           ============================================================ */

        #validation-form {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.08);
            padding: 24px;
            margin-top: 10px;
        }

        /* Tab Navigation */
        #myTab4,
        #validation-form .nav-tabs,
        #validation-form ul.nav-tabs {
            background: #f0f4f9 !important;
            border-bottom: 3px solid #1e5799 !important;
            border-radius: 10px 10px 0 0;
            margin-bottom: 28px;
            padding: 6px 8px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            list-style: none;
        }
        #myTab4 > li,
        #validation-form .nav-tabs > li { margin-bottom: 0; }

        #myTab4 > li > a,
        #validation-form .nav-tabs > li > a {
            display: flex !important;
            align-items: center;
            gap: 7px;
            color: #4a6280 !important;
            background: #e4ecf5 !important;
            border: 1px solid #c8d9ec !important;
            border-bottom: none !important;
            border-radius: 8px 8px 0 0 !important;
            font-weight: 600;
            font-size: 13px;
            padding: 10px 16px;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
        }
        #myTab4 > li > a i,
        #validation-form .nav-tabs > li > a i { font-size: 14px; opacity: 0.85; }
        #myTab4 > li > a:hover,
        #validation-form .nav-tabs > li > a:hover {
            background: #d0e3f5 !important;
            color: #1e5799 !important;
            border-color: #a8c4e8 !important;
            text-decoration: none;
        }
        #myTab4 > li.active > a,
        #myTab4 > li.active > a:focus,
        #myTab4 > li.active > a:hover,
        #validation-form .nav-tabs > li.active > a,
        #validation-form .nav-tabs > li.active > a:focus,
        #validation-form .nav-tabs > li.active > a:hover {
            color: #fff !important;
            background: linear-gradient(135deg, #1e5799 0%, #2f80c8 100%) !important;
            border-color: #1e5799 !important;
            border-bottom-color: transparent !important;
            font-weight: 700;
            box-shadow: 0 -2px 10px rgba(30, 87, 153, 0.18);
        }
        #myTab4 > li.active > a i,
        #validation-form .nav-tabs > li.active > a i { opacity: 1; }

        /* Fieldset card */
        #validation-form fieldset {
            margin-bottom: 24px;
            padding: 24px;
            border: 1px solid #e1eaf4 !important;
            border-radius: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }
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

        /* Form group & labels */
        #validation-form .form-group { margin-right: 0; margin-left: 0; margin-bottom: 18px; }
        #validation-form .control-label {
            background: transparent !important;
            border: none !important;
            padding: 11px 8px 6px 0 !important;
            font-weight: 600;
            color: #23384d;
            font-size: 12px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            text-align: right;
        }
        @media (max-width: 767px) {
            #validation-form .control-label { text-align: left; padding-left: 0 !important; }
        }
        #validation-form .control-label .text-danger {
            margin-left: 3px;
            font-weight: 700;
            color: #e53e3e !important;
            font-size: 14px;
            line-height: 1;
        }

        /* Inputs */
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
        #validation-form textarea.form-control { min-height: 76px !important; resize: vertical; }

        /* Chosen select */
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
        #validation-form .chosen-container .chosen-single span { line-height: 44px; }
        #validation-form .chosen-container .chosen-single b { background-position: 0 12px; }
        #validation-form .chosen-container .chosen-drop {
            border: 1px solid #cdd6e0;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Invalid field */
        #validation-form .form-control.field-invalid,
        #validation-form .chosen-container.field-invalid .chosen-single {
            border-color: #d9534f !important;
            box-shadow: 0 0 0 3px rgba(217, 83, 79, 0.1) !important;
        }
        #validation-form .validation-note { display: none; margin-top: 5px; color: #d9534f; font-size: 12px; font-weight: 600; }
        #validation-form .validation-note.is-visible { display: block; }

        /* Arrowed labels */
        #validation-form .label.arrowed,
        #validation-form .label.arrowed-in {
            display: inline-block;
            margin-bottom: 10px;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 13px;
        }

        /* Next / Previous buttons */
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
        #validation-form .tab-pane .btn-primary { background: #6c757d; border-color: #6c757d; color: #fff; }
        #validation-form .tab-pane .btn-primary:hover { background: #5a6268; border-color: #545b62; }

        /* Bottom action buttons */
        .form-actions .btn {
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            font-size: 13px;
            min-width: 110px;
        }

        @media (max-width: 767px) {
            #validation-form { padding: 14px; }
            #validation-form fieldset { padding: 16px; border-radius: 12px; }
            #validation-form .nav-tabs > li > a { padding: 9px 10px; font-size: 12px; }
            #validation-form .control-label { margin-bottom: 4px; text-align: left; }
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
                            Registration
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        @include($view_path.'.includes.buttons')
                        @include('includes.validation_error_messages')
                        <div class="align-right">
                            <a class="{!! request()->is('staff/import*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('staff.import') }}"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Bulk Staff Registration</a>
                        </div>
                        {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                        @include($view_path.'.includes.form')

                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn" type="reset">
                                    <i class="fa fa-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-primary" type="submit" value="Save" name="add_staff" id="add-staff">
                                    <i class="fa fa-save bigger-110"></i>
                                    Save
                                </button>

                                <button class="btn btn-success" type="submit" value="Save" name="add_staff_another" id="add-staff-another">
                                    <i class="fa fa-save bigger-110"></i>
                                    <i class="fa fa-plus bigger-110"></i>
                                    Save And Add Another
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
    @include('includes.scripts.jquery_validation_scripts')
    <!-- inline scripts related to this page -->

    <script type="text/javascript">
        window.staffPhotoRequired = true;
    </script>

    @include('staff.includes.staff-common-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
    @include('staff.includes.date-format-script')

    @endsection

