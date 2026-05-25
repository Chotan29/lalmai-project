@extends('layouts.master')
@section('css')

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <style>
        #subjects_wrapper.live-invalid-container {
            border: 1px solid #d93025;
            border-radius: 8px;
            padding: 10px;
            background: #fff8f8;
        }

        .subject-selection-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            padding: 12px 14px;
            border: 1px solid #d6e2f0;
            border-radius: 10px;
            background: linear-gradient(135deg, #f5f9ff 0%, #eef5ff 100%);
        }

        .subject-selection-title {
            font-size: 15px;
            font-weight: 700;
            color: #17324c;
        }

        .subject-selection-limit {
            font-size: 13px;
            font-weight: 500;
            color: #2d5b8a;
        }

        .subject-structure-note {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: -6px 0 16px;
            padding: 10px 14px;
            border-left: 4px solid #1e5799;
            border-radius: 10px;
            background: #f8fbff;
            color: #4a5f75;
            font-size: 13px;
        }

        .subject-structure-note span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .subject-group-card {
            height: 100%;
            border: 1px solid #d8e4f0;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .subject-group-card__head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            border-bottom: 1px solid #e5edf6;
        }

        .subject-group-card__head h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #17324c;
        }

        .subject-group-tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 54px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #17324c;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .subject-group-tag--optional {
            background: #8a5b11;
        }

        .subject-group-card--compulsory .subject-group-card__head {
            background: #f2f9f5;
        }

        .subject-group-card--optional .subject-group-card__head {
            background: #f9f7f0;
        }

        .subject-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 8px;
            border-radius: 999px;
            background: #1e5799;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }

        .subject-group-card__body {
            padding: 12px 14px;
            max-height: 320px;
            overflow-y: auto;
        }

        .subject-option-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e3ebf4;
            font-size: 14px;
            color: #32485d;
            cursor: pointer;
        }

        .subject-option-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: 0;
        }

        .subject-option-row input[type="checkbox"] {
            margin-top: 2px;
        }

        .subject-empty-state {
            margin: 0;
            font-size: 13px;
            color: #6a7d91;
            font-style: italic;
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
                            Edit  Registration
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="row">
                    <div class="col-xs-12 ">
                    @include($view_path.'.includes.buttons')
                    @include('includes.flash_messages')
                    <!-- PAGE CONTENT BEGINS -->
                    @include('includes.validation_error_messages')
                        <div class="align-right hidden-print">
                            <a class="btn-primary btn-sm" href="{{ route($base_route.'.view', ['id' => encrypt($data['row']->id)]) }}"  >
                                <i class="ace-icon fa fa-eye"></i> View Student Profile
                            </a>
                        </div>
                        {!! Form::model($data['row'], ['route' => [$base_route.'.update', encrypt($data['row']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                   'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        {!! Form::hidden('id', encrypt($data['row']->id)) !!}
                        {{--{!! Form::hidden('guardians_id', $data['row']->guardians_id) !!}--}}
                        @include($view_path.'.registration.includes.form')
                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn btn-info" type="submit">
                                    <i class="fa fa-save bigger-110"></i>
                                    Update
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
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-common-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
    {{--@include('includes.scripts.table_tr_sort')--}}
@endsection


