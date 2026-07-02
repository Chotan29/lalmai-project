@extends('layouts.master')

@section('css')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="row">

                            <div class="col-md-12 col-print-12 align-center">
                                <div class="text-center">
                                    @if(isset($generalSetting->logo))
                                        <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" width="100px">
                                    @endif
                                    <h2 class="no-margin-top text-uppercase" style="font-family: 'Raleway'; font-size: 35px;font-weight: 600;">
                                        {{isset($generalSetting->institute)?$generalSetting->institute:"EduFirm Web Portal Online Registration"}}
                                    </h2>
                                    <h4 class="no-margin-top">
                                        {{isset($generalSetting->address)?$generalSetting->address:""}}, {{isset($generalSetting->phone)?$generalSetting->phone:""}}, {{isset($generalSetting->email)?$generalSetting->email:""}}
                                    </h4>
                                        <h4 class="text-uppercase no-margin-top text-center" style="font-family: 'Raleway'; font-size: 25px;font-weight: 600;">
                                            Find & Print Registration Detail
                                        </h4>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.col -->
                </div><!-- /.row -->
                <hr class="hr-8">

                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">

                        @include('includes.flash_messages')

                            <div id="accordion" class="accordion-style1 panel-group hidden-print">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false">
                                                <h3 class="header large lighter blue">
                                                    <i class="bigger-110 ace-icon fa fa-angle-double-right" data-icon-hide="ace-icon fa fa-angle-double-down" data-icon-show="ace-icon fa fa-angle-double-right"></i>
                                                    Click here, To Find the student registration detail.
                                                    <i class="fa fa-search" aria-hidden="true"></i>&nbsp;
                                                </h3>
                                            </a>
                                        </h4>
                                    </div>

                                    <div class="panel-collapse collapse" id="collapseOne" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body">
                                            <div class="well">
                                                {!! Form::open(['route' => 'online-registration.find', 'method' => 'GET', 'class' => 'form-horizontal',
                                                 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                                                <div class="clearfix">
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.faculty')}}</label>
                                                        <div class="col-sm-10">
                                                            <select name="faculty" class="form-control chosen-select"  data-placeholder="Choose a Faculty..."  onChange ="loadSemesters(this)" required>
                                                                @foreach( $data['faculties'] as $key => $faculty)
                                                                    <option value="{{ $key }}">{{ $faculty }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label">{{__('form_fields.student.fields.semester')}}</label>
                                                        <div class="col-sm-5">
                                                            <select id="semester" name="semester" required class="form-control border-form semester" >
                                                            </select>
                                                        </div>

                                                        <label class="col-sm-2 control-label">{{__('form_fields.student.fields.batch')}}</label>
                                                        <div class="col-sm-3">
                                                            {!! Form::select('batch', $data['batch'], null, ['class' => 'form-control']) !!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        {!! Form::label('reg_no', __('form_fields.student.fields.reg_no'), ['class' => 'col-sm-2 control-label']) !!}
                                                        <div class="col-sm-2">
                                                            {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration","required"]) !!}
                                                            @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
                                                        </div>

                                                        {!! Form::label('national_id_1', __('form_fields.student.fields.national_id_1'), ['class' => 'col-sm-2 control-label']) !!}
                                                        <div class="col-sm-2">
                                                            {!! Form::text('national_id_1', null, ["placeholder" => "", "class" => "form-control border-form input-mask-aadhaar-id",/*'maxlength' => 12,*/]) !!}
                                                        </div>

                                                        {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
                                                        <div class="col-sm-2">
                                                            {!! Form::text('date_of_birth', null, ["class" => "form-control border-form date-picker input-mask-date","required"]) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix form-actions">
                                                    <div class="center">
                                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                                            <i class="fa fa-search bigger-110"></i>
                                                            Find
                                                        </button>
                                                    </div>
                                                </div>
                                                {{  Form::close() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @if(isset($data['student']) && $data['student']->count() > 0)
                            @foreach($data['student'] as $student)
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">
                                        <i class="ace-icon fa fa-times"></i>
                                    </button>

                                    <strong>
                                        <i class="ace-icon fa fa-check"></i>
                                        Found !
                                    </strong>
                                    Registration with your detail match. Please, check and Print.
                                    <br>
                                </div>
                                <table>
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <a href="{{ route('online-registration.print', ['id' => encrypt($student->id)]) }}" class="btn btn-info" target="_blank">
                                                <i class="ace-icon fa fa-print align-top bigger-125 icon-on-right"></i> Print Registration
                                            </a>
                                            <a href="{{ route('online-registration.pdf', ['id' => encrypt($student->id)]) }}" class="btn btn-success">
                                                <i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i> Download Registration
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Faculty/Sem:</b>{{  ViewHelper::getFacultyTitle( $student->faculty ) }} /{{  ViewHelper::getSemesterTitle( $student->semester ) }}</td>
                                        <td><b>Session:</b>{{ViewHelper::getStudentBatchById($student->batch)}}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Date: </b>{{ \Carbon\Carbon::parse($student->reg_date)->format('d/m/Y')}}</td>
                                        <td><b>Reg Sr.No: </b> {{$student->reg_no}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Name of Student:</b> {{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</td>
                                    </tr>
                                </table>
                            @endforeach
                        @endif

                    </div>
                    <div class="col-md-2"></div>
                </div>


            </div><!-- /.page-content -->
        </div>
    </div>

@endsection

@section('js')
    <script type="text/javascript">
        $('#filter-btn').click(function () {
            var url = '{{ $data['url'] }}';
            var flag = false;
            //var reg_no = $('input[name="reg_no"]').val();
            var national_id_1 = $('input[name="national_id_1"]').val();
            var date_of_birth = $('input[name="date_of_birth"]').val();
            var faculty = $('select[name="faculty"]').val();
            var semester_select = $('select[name="semester_select"]').val();
            var batch = $('select[name="batch"]').val();


            /*if (reg_no !== '') {
                url += '?reg_no=' + reg_no;
                flag = true;
            }*/

            if (national_id_1 !== '') {
                url += '?national_id_1=' + national_id_1;
                flag = true;
            }

            if (faculty >0) {
                if (flag) {
                    url += '&faculty=' + faculty;
                } else {
                    url += '?faculty=' + faculty;
                    flag = true;
                }
            }

            if (semester_select > 0) {
                if (flag) {
                    url += '&semester_select=' + semester_select;
                } else {
                    url += '?semester_select=' + semester_select;
                    flag = true;
                }
            }

            if (batch > 0) {
                if (flag) {
                    url += '&batch=' + batch;
                } else {
                    url += '?batch=' + batch;
                    flag = true;
                }
            }

            if (date_of_birth !== '') {
                if (flag) {
                    url += '&batch=' + batch;
                } else {
                    url += '?batch=' + batch;
                    flag = true;
                }
            }



            location.href = url;
        });

    </script>
    @include('includes.scripts.jquery_validation_scripts')
    @include($view_path.'.includes.student-common-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection

