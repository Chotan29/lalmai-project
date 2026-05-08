
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"
        integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.min.css') }}" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .top-navbar {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .top-navbar .navbar-menu {
            display: flex;
            width: 100%;
        }

        .top-navbar .nav.navbar-nav {
            display: flex;
            flex-direction: row;
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
            justify-content: center;
        }

        .top-navbar .nav-item {
            display: inline-block;
            margin-left: 15px;
        }

        .top-navbar .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .top-navbar .nav-link:hover {
            background-color: #f8f9fa;
            color: #1e5799;
        }

        .top-navbar .nav-link i {
            margin-right: 5px;
        }

        .registration-container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .registration-header {
            padding: 30px;
            background: linear-gradient(135deg, #1e5799 0%, #207cca 51%, #2989d8 100%);
            color: white;
            text-align: center;
            position: relative;
        }

        .logo-container {
            margin-bottom: 20px;
        }

        .logo-container img {
            max-height: 100px;
            max-width: 100%;
        }

        .institution-info h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .institution-info h2 {
            font-size: 22px;
            font-weight: 500;
            margin-top: 20px;
            color: #fff;
        }

        .institution-details {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .guidelines-container {
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .accordion {
            margin-bottom: 0;
        }

        .accordion-toggle {
            display: none;
        }

        .accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background-color: #e9ecef;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .accordion-header:hover {
            background-color: #dee2e6;
        }

        .accordion-icon {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-right: 2px solid #333;
            border-bottom: 2px solid #333;
            transform: rotate(45deg);
            transition: transform 0.2s ease;
        }

        .accordion-toggle:checked~.accordion-header .accordion-icon {
            transform: rotate(-135deg);
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 15px;
        }

        .accordion-toggle:checked~.accordion-content {
            max-height: 1000px;
            padding: 15px;
        }

        .registration-form-container {
            padding: 30px;
        }

        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 25px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #495057;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 0;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #007bff;
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            background-color: transparent;
            border-bottom: 2px solid #007bff;
        }

        .tab-pane {
            padding: 15px 0;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .form-navigation {
            margin-top: 30px;
        }

        .preview-image-container {
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .preview-image {
            max-width: 100%;
            max-height: 100%;
        }

        .registration-closed,
        .registration-unavailable {
            text-align: center;
            padding: 50px 20px;
        }

        .registration-closed h1,
        .registration-unavailable h1 {
            color: #dc3545;
            margin-bottom: 30px;
        }

        .closed-message {
            max-width: 800px;
            margin: 0 auto 30px;
            padding: 20px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        .contact-info {
            margin-top: 20px;
            font-size: 14px;
        }

        .btn-next,
        .btn-prev {
            min-width: 120px;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875em;
        }

        .custom-file-label::after {
            content: "Browse";
        }

        .upper {
            text-transform: uppercase;
        }

        .label-warning {
            background-color: #f39c12;
            padding: 5px 10px;
            outa border-radius: 4px;
            color: white;
            display: inline-block;
            margin-bottom: 10px;
        }

        .hr-8 {
            height: 1px;
            margin: 8px 0;
            background-color: #ddd;
        }

        @media (max-width: 768px) {
            .registration-header {
                padding: 20px;
            }

            .institution-info h1 {
                font-size: 22px;
            }

            .institution-info h2 {
                font-size: 18px;
            }

            .registration-form-container {
                padding: 20px;
            }

            .nav-tabs .nav-link {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="top-navbar">
        <div class="container">
            <nav role="navigation" class="navbar-menu">
                <ul class="nav navbar-nav">
                    @if (isset($generalSetting) && $generalSetting->public_registration == 1)
                        <li class="nav-item">
                            <a href="{{ route('online-registration.registration') }}" class="nav-link">
                                <i class="fas fa-user"></i> Online Registration
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('online-registration.find') }}" class="nav-link">
                                <i class="fas fa-search"></i> Find & Print Registration
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('verification.certificate') }}" class="nav-link">
                                <i class="fas fa-search"></i> Certificate Verification
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('login') }}" target="_blank" class="nav-link">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('web.home') }}" target="_blank" class="nav-link">
                            <i class="fas fa-globe"></i> WebPortal
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="registration-container">
        <div class="registration-header">
            <div class="logo-container">
                <img src="{{ asset('images/setting/general/' . $generalSetting->logo) }}" alt="Institution Logo">
            </div>
            <div class="institution-info">
                <h1>{{ $generalSetting->institute ?? 'EduFirm Web Portal Online Registration' }}</h1>
                <p class="institution-details">
                    {{ $generalSetting->address ?? '' }},
                    {{ $generalSetting->phone ?? '' }},
                    {{ $generalSetting->email ?? '' }}
                </p>
                <h2>Find Registration Detail</h2>
            </div>
        </div>

        <div class="row">

            <div class="col-md-2"></div>
            <div class="col-md-8">
                @if (isset($data['student']) && $data['student']->count() > 0)
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
                @endif
                @include('includes.flash_messages')

                <div class="well">
                    {!! Form::open([
                        'route' => 'online-registration.find',
                        'method' => 'GET',
                        'class' => 'form-horizontal',
                        'id' => 'validation-form',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <div class="tab-content">
                        <!-- General Info Tab -->
                        <div class="tab-pane fade show active" id="generalInfo" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-info-circle"></i> Find your registration with details:
                                </h3>
                            </div>
                            <div class="form-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Faculty/Program <span class="text-danger">*</span></label>
                                            <select name="faculty" class="form-control" required>
                                                @foreach ($data['faculties'] as $key => $faculty)
                                                    <option value="{{ $key }}" {{ (string) request('faculty') === (string) $key ? 'selected' : '' }}>{{ $faculty }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" name="date_of_birth"
                                                value="{{ request('date_of_birth') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Reg No. <span class="text-danger">*</span></label>
                                            <input type="text" name="reg_no" value="{{ request('reg_no') }}" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <div></div>
                                <button type="submit" class="btn btn-primary btn-next">
                                    <i class="fa fa-search ml-2"></i> Find
                                </button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
                @if (isset($data['student']) && $data['student']->count() > 0)
                    @foreach ($data['student'] as $student)
                        <table>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <a href="{{ route('online-registration.print', ['id' => encrypt($student->id)]) }}"
                                        class="btn btn-info" target="_blank">
                                        <i class="ace-icon fa fa-print align-top bigger-125 icon-on-right"></i> Print
                                        Registration
                                    </a>
                                    <a href="{{ route('online-registration.pdf', ['id' => encrypt($student->id)]) }}"
                                        class="btn btn-success">
                                        <i class="fa fa-file-pdf align-top bigger-125 icon-on-right"></i> Download
                                        Registration
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Faculty/Sem:</b>{{ ViewHelper::getFacultyTitle($student->faculty) }}
                                    /{{ ViewHelper::getSemesterTitle($student->semester) }}</td>
                                <td><b>Session:</b>{{ ViewHelper::getStudentBatchById($student->batch) }}</td>
                            </tr>

                            <tr>
                                <td><b>Date: </b>{{ \Carbon\Carbon::parse($student->reg_date)->format('d/m/Y') }}</td>
                                <td><b>Reg Sr.No: </b> {{ $student->reg_no }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>Name of Student:</b>
                                    {{ $student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name }}
                                </td>
                            </tr>
                        </table>
                    @endforeach
                @endif

            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $('#filter-btn').click(function() {
            var url = '{{ $data['url'] }}';
            var flag = false;
            var reg_no = $('input[name="reg_no"]').val();
            var faculty = $('select[name="faculty"]').val();
            var date_of_birth = $('input[name="date_of_birth"]').val();
            var national_id_1 = $('input[name="national_id_1"]').val();
            var semester_select = $('select[name="semester_select"]').val();
            var batch = $('select[name="batch"]').val();


            if (reg_no !== '') {
                url += '?reg_no=' + reg_no;
                flag = true;
            }

        
            if (faculty > 0) {
                if (flag) {
                    url += '&faculty=' + faculty;
                } else {
                    url += '?faculty=' + faculty;
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
    {{-- @include($view_path . '.includes.student-common-script') --}}
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
</body>

</html>

{{-- @extends('layouts.master')

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
                                    @if (isset($generalSetting->logo))
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
                        @if (isset($data['student']) && $data['student']->count() > 0)
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
                        @endif
                        @include('includes.flash_messages')

                        <div class="well">
                       {!! Form::open(['route' => 'online-registration.find', 'method' => 'GET', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            <div class="clearfix">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.faculty')}}</label>
                                    <div class="col-sm-10">
                                        <select name="faculty" class="form-control chosen-select"  data-placeholder="Choose a Faculty..."  onChange ="loadSemesters(this)" required>
                                            @foreach ($data['faculties'] as $key => $faculty)
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

                                    {!! Form::label('national_id_1', 'NID/Birth', ['class' => 'col-sm-2 control-label']) !!}
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
                        @if (isset($data['student']) && $data['student']->count() > 0)
                            @foreach ($data['student'] as $student)
                                <table>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <a href="{{ route('online-registration.print', ['id' => encrypt($student->id)]) }}" class="btn btn-info" target="_blank">
                                            <i class="ace-icon fa fa-print align-top bigger-125 icon-on-right"></i> Print Registration
                                        </a>
                                        <a href="{{ route('online-registration.pdf', ['id' => encrypt($student->id)]) }}" class="btn btn-success">
                                            <i class="fa fa-file-pdf align-top bigger-125 icon-on-right"></i> Download Registration
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
                                    <td colspan="2"><b>Name of Student:</b> {{ $student->first_name.' '.
                    $student->middle_name.' '.$student->last_name }}</td>
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
@endsection --}}
