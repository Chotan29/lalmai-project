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
            padding: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid #e1eaf4;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        .form-section .row {
            --bs-gutter-y: 10px;
        }

        .form-section .form-group {
            margin-bottom: 0;
        }

        .form-section .form-group label {
            display: inline-block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #23384d;
        }

        .registration-form-container .form-control,
        .registration-form-container select.form-control {
            min-height: 46px;
            padding: 10px 12px;
            border: 1px solid #d5dde7;
            border-radius: 8px;
            box-shadow: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .registration-form-container .form-control:focus,
        .registration-form-container select.form-control:focus {
            border-color: #2b6cb0;
            box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.12);
        }

        .registration-form-container .is-invalid,
        .registration-form-container .form-control.is-invalid,
        .registration-form-container .custom-file-input.is-invalid~.custom-file-label {
            border-color: #d93025 !important;
            box-shadow: 0 0 0 3px rgba(217, 48, 37, 0.1);
        }

        .registration-form-container .live-validation-error {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.35;
            color: #d93025;
        }

        #subjects_wrapper.live-invalid-container {
            border: 1px solid #d93025;
            border-radius: 8px;
            padding: 10px;
            background: #fff8f8;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 700;
            color: #16324a;
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 1px solid #d9e4ef;
            letter-spacing: 0.01em;
        }

        .section-title i {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #1e5799 0%, #2f80c8 100%);
            color: #fff;
            font-size: 15px;
            box-shadow: 0 8px 18px rgba(30, 87, 153, 0.22);
            flex: 0 0 38px;
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

        .photo-guideline-list {
            margin: 10px 0 0;
            padding-left: 18px;
            color: #5b6570;
            font-size: 13px;
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

        .address-subsection-label {
            margin-top: 18px;
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

            .form-section {
                padding: 18px;
                border-radius: 14px;
            }

            .nav-tabs .nav-link {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    @php
        $passportPhotoMinWidth = 300;
        $passportPhotoMinHeight = 400;
        $passportPhotoMaxSizeMb = 5;
        $passportPhotoRatio = 35 / 45;
        $passportPhotoRatioTolerance = 0.08;
    @endphp
    <div class="top-navbar">
        <div class="container">
            <nav role="navigation" class="navbar-menu">
                <ul class="nav navbar-nav">
                    @php
                        $showRegistrationLink = isset($generalSetting) && $generalSetting->public_registration == 1;
                        if (!$showRegistrationLink && isset($data['registration_setting']) && $data['registration_setting']->status == 'active' && now()->between($data['registration_setting']->start_date, $data['registration_setting']->end_date)) {
                            $showRegistrationLink = true;
                        }
                    @endphp
                    @if ($showRegistrationLink)
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

    @php
        $showRegistrationForm = isset($data['show_registration_form'])
            ? $data['show_registration_form']
            : ((isset($generalSetting) && $generalSetting->public_registration == 1) || (
                isset($data['registration_setting']) &&
                    $data['registration_setting']->status == 'active' &&
                    date('Y-m-d') >= $data['registration_setting']->start_date &&
                    $data['registration_setting']->end_date >= date('Y-m-d')
            ));
    @endphp

    @if ($showRegistrationForm)
        <div class="registration-container">
            <div class="registration-header">
                    <div class="logo-container">
                        @if ($data['registration_setting']->logo !== '')
                            <img src="{{ asset('images/setting/online-registration/' . $data['registration_setting']->logo) }}"
                                alt="Institution Logo">
                        @else
                            <img src="{{ asset('images/setting/general/' . $generalSetting->logo) }}"
                                alt="Institution Logo">
                        @endif
                    </div>
                    <div class="institution-info">
                        <h1>{{ $generalSetting->institute ?? 'EduFirm Web Portal Online Registration' }}</h1>
                        <p class="institution-details">
                            {{ $generalSetting->address ?? '' }},
                            {{ $generalSetting->phone ?? '' }},
                            {{ $generalSetting->email ?? '' }}
                        </p>
                        <h2>{{ $data['registration_setting']->title ?? 'ONLINE APPLICATION FOR ADMISSION' }}</h2>
                    </div>
                </div>

                @if (isset($data['registration_setting']->registration_guidelines))
                    <div class="guidelines-container">
                        <div class="accordion">
                            <input type="checkbox" id="guidelines-toggle" class="accordion-toggle">
                            <label for="guidelines-toggle" class="accordion-header">
                                <span>Registration Guidelines</span>
                                <i class="accordion-icon"></i>
                            </label>
                            <div class="accordion-content">
                                {!! $data['registration_setting']->registration_guidelines !!}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="registration-form-container">
                    @include('includes.validation_error_messages')

                    {!! Form::open([
                        'route' => $base_route . '.register',
                        'method' => 'POST',
                        'class' => 'registration-form',
                        'id' => 'validation-form',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <!-- Tab navigation -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" id="studentTypeTab">
                            <a class="nav-link active" data-toggle="tab" href="#studentType" role="tab">
                                <i class="fa fa-id-card"></i> Student Type
                            </a>
                        </li>
                        <li class="nav-item" id="generalInfoTab">
                            <a class="nav-link" data-toggle="tab" href="#generalInfo" role="tab">
                                <i class="fa fa-user"></i> General Information
                            </a>
                        </li>
                        <li class="nav-item" id="academicInfoTab">
                            <a class="nav-link" data-toggle="tab" href="#academicInfo" role="tab">
                                <i class="fa fa-certificate"></i> Academic Information
                            </a>
                        </li>
                        <li class="nav-item" id="profileImageTab">
                            <a class="nav-link" data-toggle="tab" href="#profileImage" role="tab">
                                <i class="fa fa-image"></i> Profile Image
                            </a>
                        </li>
                        @if (isset($data['annexures']) && count($data['annexures']) > 0)
                            <li class="nav-item" id="annexureTab">
                                <a class="nav-link" data-toggle="tab" href="#annexure" role="tab">
                                    <i class="fa fa-list"></i> Annexure
                                </a>
                            </li>
                        @endif
                        @if ($data['registration_setting']->rules_status == '1')
                            <li class="nav-item" id="rulesTab">
                                <a class="nav-link" data-toggle="tab" href="#rules" role="tab">
                                    <i class="fa fa-file-text"></i> Rules
                                </a>
                            </li>
                        @endif
                        @if ($data['registration_setting']->agreement_status == '1')
                            <li class="nav-item" id="agreementTab">
                                <a class="nav-link" data-toggle="tab" href="#agreement" role="tab">
                                    <i class="fa fa-file-contract"></i> Agreement
                                </a>
                            </li>
                        @endif
                        @if ($data['registration_setting']->payment_required == true)
                            <li class="nav-item" id="paymentTab">
                                <a class="nav-link" data-toggle="tab" href="#payment" role="tab">
                                    <i class="fa fa-credit-card"></i> Payment
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="tab-content">
                        <!-- Student Type Tab -->
                        <div class="tab-pane fade show active" id="studentType" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-id-card"></i> Select Your Student Type</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Student Type <span class="text-danger">*</span></label>
                                            <select name="student_type" id="studentTypeSelect" class="form-control" required>
                                                <option value="">Select Student Type</option>
                                                @if($data['registration_setting']->new_student_enabled)
                                                    <option value="new">New Student</option>
                                                @endif
                                                @if($data['registration_setting']->old_student_enabled)
                                                    <option value="old">Old Student (Returning)</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="studentTypeInfo" style="display:none; margin-top:20px;">
                                    <div class="alert alert-info">
                                        <strong id="studentTypeInfoText"></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-primary btn-next" onclick="navigateTab('next')" id="studentTypeNextBtn" disabled>
                                    <i class="fa fa-arrow-right mr-2"></i> Next
                                </button>
                            </div>
                        </div>

                        <!-- General Info Tab -->
                        <div class="tab-pane fade" id="generalInfo" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-info-circle"></i> Enrollment Information
                                </h3>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Session <span class="text-danger">*</span></label>
                                            {!! Form::select('batch', $data['batch'], null, ['class' => 'form-control', 'required', 'readonly']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Faculty/Program <span class="text-danger">*</span></label>
                                            <select name="faculty" class="form-control"
                                                onChange="loadSemesters(this)" required>
                                                @foreach ($data['faculties'] as $key => $faculty)
                                                    <option value="{{ $key }}">{{ $faculty }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Semester/Section <span class="text-danger">*</span></label>
                                            <select id="semester" name="semester" required
                                                onChange="loadSubject(this)" class="form-control semester">
                                                <option value="">Select Sem./Sec.</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-user"></i> Personal Information</h3>
                                <input type="hidden" name="nationality" value="Bangladeshi">
                                <input type="hidden" name="national_id_2" value="">
                                <input type="hidden" name="national_id_3" value="">
                                <input type="hidden" name="national_id_4" value="">
                                <input type="hidden" name="mother_tongue" value="">
                                <input type="hidden" name="caste" value="">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control upper"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Middle Name</label>
                                            <input type="text" name="middle_name" class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" class="form-control upper"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" name="date_of_birth"
                                                class="form-control" onfocus="if(this.showPicker){this.showPicker();}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="gender" class="form-control" required>
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Blood Group <span class="text-danger">*</span></label>
                                            <select name="blood_group" class="form-control" required>
                                                <option value="">Select Blood Group</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>NID/Birth</label>
                                            <input type="text" name="national_id_1" class="form-control upper"
                                                placeholder="Enter NID or Birth Certificate No.">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Religion <span class="text-danger">*</span></label>
                                            <select name="religion" class="form-control" required>
                                                <option value="">Select Religion</option>
                                                <option value="Islam">Islam</option>
                                                <option value="Hinduism">Hinduism</option>
                                                <option value="Buddhism">Buddhism</option>
                                                <option value="Christianity">Christianity</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-phone"></i> Contact Information</h3>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Home Phone</label>
                                            <input type="text" name="home_phone"
                                                class="form-control input-mask-phone">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile 1 <span class="text-danger">*</span></label>
                                            <input type="text" name="mobile_1"
                                                class="form-control input-mask-mobile" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile 2</label>
                                            <input type="text" name="mobile_2"
                                                class="form-control input-mask-mobile">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-map-marker"></i> Address Information</h3>
                                <div class="label label-warning arrowed-in arrowed-right arrowed">Permanent Address
                                </div>
                                <hr class="hr-8">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Address <span class="text-danger">*</span></label>
                                            <input type="text" name="address" class="form-control upper" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Division <span class="text-danger">*</span></label>
                                            <input type="text" name="state" class="form-control upper" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Postal Code <span class="text-danger">*</span></label>
                                            <input type="text" name="postal_code" class="form-control upper"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Country <span class="text-danger">*</span></label>
                                            <input type="text" name="country" class="form-control" value="Bangladesh" readonly required>
                                        </div>
                                    </div>
                                </div>

                                <div class="label label-warning arrowed-in arrowed-right arrowed address-subsection-label">Temporary Address
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="permanent_address_copier"
                                        id="permanent_address_copier" class="form-check-input"
                                        onclick="CopyAddress(this.form)">
                                    <label class="form-check-label" for="permanent_address_copier">Temporary Address
                                        Same As Permanent Address</label>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Temporary Address</label>
                                            <input type="text" name="temp_address" class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Temporary State</label>
                                            <input type="text" name="temp_state" class="form-control upper">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Temporary Postal Code</label>
                                            <input type="text" name="temp_postal_code" class="form-control upper">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-users"></i> Parent Information</h3>

                                <div class="label label-warning arrowed-in arrowed-right arrowed">Father's Details
                                </div>
                                <hr class="hr-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="father_first_name" class="form-control upper"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Middle Name</label>
                                            <input type="text" name="father_middle_name"
                                                class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="father_last_name" class="form-control upper"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Eligibility</label>
                                            <input type="text" name="father_eligibility"
                                                class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Occupation</label>
                                            <input type="text" name="father_occupation"
                                                class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Office</label>
                                            <input type="text" name="father_office" class="form-control upper">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Office Number</label>
                                            <input type="text" name="father_office_number"
                                                class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Residence Number</label>
                                            <input type="text" name="father_residence_number"
                                                class="form-control input-mask-mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile 1</label>
                                            <input type="text" name="father_mobile_1"
                                                class="form-control input-mask-mobile">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile 2</label>
                                            <input type="text" name="father_mobile_2"
                                                class="form-control input-mask-mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="text" name="father_email" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="label label-warning arrowed-in arrowed-right arrowed">Mother's Details
                                </div>
                                <hr class="hr-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="mother_first_name" class="form-control upper"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Middle Name</label>
                                            <input type="text" name="mother_middle_name"
                                                class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="mother_last_name" class="form-control upper"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Eligibility</label>
                                            <input type="text" name="mother_eligibility"
                                                class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Occupation</label>
                                            <input type="text" name="mother_occupation"
                                                class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Office</label>
                                            <input type="text" name="mother_office" class="form-control upper">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Office Number</label>
                                            <input type="text" name="mother_office_number"
                                                class="form-control upper">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Residence Number</label>
                                            <input type="text" name="mother_residence_number"
                                                class="form-control input-mask-mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile 1</label>
                                            <input type="text" name="mother_mobile_1"
                                                class="form-control input-mask-mobile">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile 2</label>
                                            <input type="text" name="mother_mobile_2"
                                                class="form-control input-mask-mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="text" name="mother_email" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-user-shield"></i> Guardian Information</h3>
                                @php
                                    $guardianRelation = strtoupper(trim(old('guardian_relation', '')));
                                    $guardianIs = old('guardian_is');
                                    if (!$guardianIs) {
                                        if ($guardianRelation === 'FATHER') {
                                            $guardianIs = 'father_as_guardian';
                                        } elseif ($guardianRelation === 'MOTHER') {
                                            $guardianIs = 'mother_as_guardian';
                                        } elseif ($guardianRelation === 'SELF') {
                                            $guardianIs = 'self_guardian';
                                        } elseif ($guardianRelation !== '') {
                                            $guardianIs = 'other_guardian';
                                        } else {
                                            $guardianIs = 'self_guardian';
                                        }
                                    }
                                @endphp
                                <div class="form-group">
                                    <label>Guardian Is:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="guardian_is"
                                            id="father_as_guardian" value="father_as_guardian"
                                            {{ $guardianIs === 'father_as_guardian' ? 'checked' : '' }}
                                            onclick="FatherAsGuardian(this.form)">
                                        <label class="form-check-label" for="father_as_guardian">Father</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="guardian_is"
                                            id="mother_as_guardian" value="mother_as_guardian"
                                            {{ $guardianIs === 'mother_as_guardian' ? 'checked' : '' }}
                                            onclick="MotherAsGuardian(this.form)">
                                        <label class="form-check-label" for="mother_as_guardian">Mother</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="guardian_is"
                                            id="self_guardian" value="self_guardian"
                                            {{ $guardianIs === 'self_guardian' ? 'checked' : '' }}
                                            onclick="SelfGuardian(this.form)">
                                        <label class="form-check-label" for="self_guardian">Self</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="guardian_is"
                                            id="other_guardian" value="other_guardian"
                                            {{ $guardianIs === 'other_guardian' ? 'checked' : '' }}
                                            onclick="OtherGuardian(this.form)">
                                        <label class="form-check-label" for="other_guardian">Other</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="guardian_is"
                                            id="link_guardian" value="link_guardian"
                                            {{ $guardianIs === 'link_guardian' ? 'checked' : '' }}
                                            onclick="linkGuardian(this.form)">
                                        <label class="form-check-label" for="link_guardian">Link Guardian</label>
                                    </div>
                                </div>

                                <div id="guardian-detail">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>First Name <span class="text-danger">*</span></label>
                                                <input type="text" name="guardian_first_name"
                                                    class="form-control upper" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Middle Name</label>
                                                <input type="text" name="guardian_middle_name"
                                                    class="form-control upper">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Last Name <span class="text-danger">*</span></label>
                                                <input type="text" name="guardian_last_name"
                                                    class="form-control upper" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Eligibility</label>
                                                <input type="text" name="guardian_eligibility"
                                                    class="form-control upper">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Occupation</label>
                                                <input type="text" name="guardian_occupation"
                                                    class="form-control upper">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Office</label>
                                                <input type="text" name="guardian_office"
                                                    class="form-control upper">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Office Number</label>
                                                <input type="text" name="guardian_office_number"
                                                    class="form-control upper">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Residence Number</label>
                                                <input type="text" name="guardian_residence_number"
                                                    class="form-control input-mask-mobile">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Mobile 1 <span class="text-danger">*</span></label>
                                                <input type="text" name="guardian_mobile_1"
                                                    class="form-control input-mask-mobile" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Mobile 2</label>
                                                <input type="text" name="guardian_mobile_2"
                                                    class="form-control input-mask-mobile">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" name="guardian_email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Relation <span class="text-danger">*</span></label>
                                                <input type="text" name="guardian_relation"
                                                    class="form-control upper" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Address <span class="text-danger">*</span></label>
                                                <input type="text" name="guardian_address"
                                                    class="form-control upper" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="link-guardian-detail" style="display: none;">
                                    <div class="form-group">
                                        <label>Find Guardian Using Name | Mobile Number | Email & Click on Link
                                            Now</label>
                                        <select name="guardian_link_id" class="form-control" style="width: 100%;">
                                            <option value="">Type Student Reg.No. or Guardians Name...</option>
                                        </select>
                                        <div class="text-right mt-2">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                id="load-guardian-html-btn">
                                                <i class="fa fa-link"></i> Link Now
                                            </button>
                                        </div>
                                    </div>
                                    <div id="guardian_wrapper"></div>
                                </div>
                            </div>

                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <div></div>
                                <button type="button" class="btn btn-primary btn-next"
                                    onclick="navigateTab('next')">
                                    Next <i class="fa fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Academic Info Tab -->
                        <div class="tab-pane fade" id="academicInfo" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-graduation-cap"></i> Academic Information</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Level</th>
                                                <th>Pass Year</th>
                                                <th>Board Name</th>
                                                <th>Reg No.</th>
                                                <th>Major Subjects</th>
                                                <th class="d-none academic-score-col">Mark Obtained</th>
                                                <th class="d-none academic-score-col">Maximum Mark</th>
                                                <th class="d-none academic-score-col">Percentage</th>
                                                <th>Grade Point</th>
                                                <th>Grade</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="academicInfo_wrapper">
                                            <!-- Academic Information rows will be loaded here via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-book"></i> Subjects</h3>
                                <div id="subjects_wrapper">
                                    <!-- Subjects will be loaded here via AJAX -->
                                </div>
                            </div>

                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-prev"
                                    onclick="navigateTab('prev')">
                                    <i class="fa fa-arrow-left mr-2"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary btn-next"
                                    onclick="navigateTab('next')">
                                    Next <i class="fa fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Profile Image Tab -->
                        <div class="tab-pane fade" id="profileImage" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-camera"></i> Upload Student Photo</h3>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Upload Photo</label>
                                    <div class="col-md-6">
                                        <div class="custom-file">
                                            <input type="file" name="student_main_image" id="student_main_image"
                                                class="custom-file-input" onchange="previewImage(this)"
                                                accept=".jpg,.jpeg,.png,.bmp,image/jpeg,image/png,image/bmp" required>
                                            <label class="custom-file-label" for="student_main_image">Choose
                                                file</label>
                                        </div>
                                        <small class="form-text text-muted">Max file size: 5MB | Minimum resolution: 300x400px | Passport ratio: 35x45</small>
                                        <ul class="photo-guideline-list">
                                            <li>Use a recent passport-size portrait photo.</li>
                                            <li>Keep the face straight and clearly visible.</li>
                                            <li>Use a plain white background and make both ears visible when possible.</li>
                                        </ul>
                                    </div>

                                    <div class="col-md-3">
                                        <div id="imagePreview" class="preview-image-container">
                                            <img id="photoPreviewImg" src="{{ asset('images/default-user.png') }}"
                                                class="img-thumbnail preview-image">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-prev" onclick="navigateTab('prev')">
                                    <i class="fa fa-arrow-left mr-2"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary btn-next" onclick="navigateTab('next')">
                                    Next <i class="fa fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Annexure Tab -->
                        @if(isset($data['annexures']) && count($data['annexures']) > 0)
                        <div class="tab-pane fade" id="annexure" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-list"></i> Annexure Documents</h3>
                                <div class="label label-warning arrowed-in arrowed-right arrowed">
                                    Details of Annexure & photo copy :
                                </div>
                                <hr class="hr-8">
                                <div class="row">
                                    @foreach($data['annexures'] as $annexure)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="annexure[]" id="annexure_{{ $annexure->id }}"
                                                value="{{ $annexure->id }}" class="form-check-input">
                                            <label class="form-check-label" for="annexure_{{ $annexure->id }}">
                                                {{ $annexure->title }}
                                            </label>
                                        </div>
                                        <hr class="hr-2">
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-prev" onclick="navigateTab('prev')">
                                    <i class="fa fa-arrow-left mr-2"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary btn-next" onclick="navigateTab('next')">
                                    Next <i class="fa fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Rules Tab -->
                        @if($data['registration_setting']->rules_status == '1')
                        <div class="tab-pane fade" id="rules" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-file-text"></i> Registration Rules</h3>
                                <div class="rules-container"
                                     style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px;">
                                    {!! $data['registration_setting']->rules !!}
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree_rules" name="agree_rules" required>
                                    <label class="form-check-label" for="agree_rules">
                                        I agree to abide by the rules and regulations mentioned above
                                    </label>
                                </div>
                            </div>

                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-prev" onclick="navigateTab('prev')">
                                    <i class="fa fa-arrow-left mr-2"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary btn-next" onclick="navigateTab('next')">
                                    Next <i class="fa fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Agreement Tab -->
                        @if($data['registration_setting']->agreement_status == '1')
                        <div class="tab-pane fade" id="agreement" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-file-contract"></i> Registration Agreement</h3>
                                <div class="agreement-container"
                                     style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px;">
                                    {!! $data['registration_setting']->agreement !!}
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                    <label class="form-check-label" for="agree_terms">
                                        I agree to the terms and conditions mentioned above
                                    </label>
                                </div>
                            </div>

                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-prev" onclick="navigateTab('prev')">
                                    <i class="fa fa-arrow-left mr-2"></i> Previous
                                </button>
                                @if ($data['registration_setting']->payment_required == true)
                                    <button type="button" class="btn btn-primary btn-next" onclick="navigateTab('next')">
                                        <i class="fa fa-arrow-right mr-2"></i> Continue to Payment
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-success" name="add_student" id="add-student">
                                        <i class="fa fa-check"></i> Submit Application
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Payment Tab -->
                        @if ($data['registration_setting']->payment_required == true)
                        <div class="tab-pane fade" id="payment" role="tabpanel">
                            <div class="form-section">
                                <h3 class="section-title"><i class="fa fa-credit-card"></i> Registration Payment</h3>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <strong>Registration Fee: </strong>
                                            <span id="registrationFeeAmount" class="h5">৳0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="alert alert-light border" id="selectedPaymentMethodInfo">
                                            <strong>Selected Method: </strong>
                                            <span id="selectedPaymentMethodText">Not selected</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-navigation d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-prev" onclick="navigateTab('prev')">
                                    <i class="fa fa-arrow-left mr-2"></i> Previous
                                </button>
                                <button type="button" class="btn btn-success" id="proceedPaymentBtn" onclick="openPaymentMethodModal()" disabled>
                                    <i class="fa fa-lock mr-2"></i> Proceed to Payment
                                </button>
                            </div>
                        </div>

                        <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="paymentMethodModalLabel">Choose Payment Method</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="d-grid gap-3">
                                            <button type="button" class="btn btn-outline-primary text-start p-3" onclick="choosePaymentMethod('ssl')">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-primary me-3" style="font-size:12px;">SSL</span>
                                                        <div>
                                                            <div class="fw-bold">SSL Commerz</div>
                                                            <small class="text-muted">Card, Mobile Banking, Internet Banking</small>
                                                        </div>
                                                    </div>
                                                    <i class="fa fa-chevron-right"></i>
                                                </div>
                                            </button>

                                            <button type="button" class="btn btn-outline-success text-start p-3" onclick="choosePaymentMethod('ucb')">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-success me-3" style="font-size:12px;">UCB</span>
                                                        <div>
                                                            <div class="fw-bold">United Commercial Bank</div>
                                                            <small class="text-muted">UCB Secure Online Payment</small>
                                                        </div>
                                                    </div>
                                                    <i class="fa fa-chevron-right"></i>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        </div>
                        @endif
                    {{-- Hidden submit button shown by JS when old student skips payment --}}
                    @if($data['registration_setting']->payment_required && $data['registration_setting']->hide_payment_for_old_student)
                    <button type="submit" class="btn btn-success" name="add_student"
                            id="add-student-skip-payment" style="display:none;">
                        <i class="fa fa-check"></i> Submit Application
                    </button>
                    @endif
                    {!! Form::close() !!}
                </div>
            </div>
    @else
        <div class="registration-unavailable">
            <h1>REGISTRATION NOT AVAILABLE AT THIS MOMENT</h1>
            {{-- Show flash message if exists --}}
            @if (session()->has('message_warning'))
                <div style="text-align: center; padding: 20px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; margin: 20px auto; max-width: 600px; color: #856404;">
                    {{ session()->get('message_warning') }}
                </div>
            @endif
            <div class="institution-info">
                @if (isset($generalSetting->logo))
                    <img src="{{ asset('images/setting/general/' . $generalSetting->logo) }}" alt="Institution Logo">
                @endif
                <h2>{{ $generalSetting->institute ?? '' }}</h2>
                <p>{{ $generalSetting->salogan ?? '' }}</p>
                <div class="contact-info">
                    <p>{{ $generalSetting->address ?? '' }}, {{ $generalSetting->phone ?? '' }}</p>
                    <p>{{ $generalSetting->email ?? '' }}, {{ $generalSetting->website ?? '' }}</p>
                </div>
            </div>
        </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Admin setting: hide payment when old student is selected
        const hidePaymentForOldStudent = {{ $data['registration_setting']->hide_payment_for_old_student ? 'true' : 'false' }};
        const paymentRequiredByDefault  = {{ $data['registration_setting']->payment_required ? 'true' : 'false' }};

        // Tab navigation order (mutable so we can remove 'payment' for old students)
        let tabOrder = [
            'studentType',
            'generalInfo',
            'academicInfo',
            'profileImage',
            @if(isset($data['annexures']) && count($data['annexures']) > 0)
                'annexure',
            @endif
            @if($data['registration_setting']->rules_status == '1')
                'rules',
            @endif
            @if($data['registration_setting']->agreement_status == '1')
                'agreement',
            @endif
            @if($data['registration_setting']->payment_required == true)
                'payment',
            @endif
        ];
        // Immutable copy used to restore tabOrder when switching back to new student
        const baseTabOrder = tabOrder.slice();
        const studentTypeFormSnapshots = { new: null, old: null };
        let lastStudentTypeSelection = null;
        const academicBoardOptions = [
            'Dhaka', 'Cumilla', 'Chattogram', 'Rajshahi', 'Jashore', 'Barishal', 'Sylhet', 'Dinajpur', 'Mymensingh',
            'Madrasah', 'Technical', 'Open University', 'Others'
        ];
        const gradeLetterOptions = ['A+', 'A', 'A-', 'B', 'C', 'D', 'F'];

        function buildSelectOptions(options, selectedValue, includePlaceholder, placeholderText) {
            let html = '';
            if (includePlaceholder) {
                html += '<option value="">' + placeholderText + '</option>';
            }

            options.forEach(function(option) {
                const selected = (String(selectedValue || '').toUpperCase() === String(option).toUpperCase()) ? ' selected' : '';
                html += '<option value="' + option + '"' + selected + '>' + option + '</option>';
            });

            return html;
        }

        function normalizeAcademicInfoRows() {
            const $rows = $('#academicInfo_wrapper').find('tr.option_value');
            if (!$rows.length) {
                return;
            }

            $rows.each(function() {
                const $row = $(this);
                const $cells = $row.children('td');

                const $boardCell = $cells.eq(2);
                const $institutionInput = $boardCell.find('input[name="institution[]"]');
                if ($institutionInput.length && !$boardCell.find('select[name="institution[]"]').length) {
                    const currentBoard = $institutionInput.val();
                    const boardSelectHtml = '<select name="institution[]" class="col-md-12">' +
                        buildSelectOptions(academicBoardOptions, currentBoard, true, 'Select Board Name') +
                        '</select>';
                    $institutionInput.replaceWith(boardSelectHtml);
                }

                [5, 6, 7].forEach(function(index) {
                    $cells.eq(index).addClass('d-none academic-score-col');
                });

                const $gradeCell = $cells.eq(9);
                const $gradeInput = $gradeCell.find('input[name="grade_letter[]"]');
                if ($gradeInput.length && !$gradeCell.find('select[name="grade_letter[]"]').length) {
                    const currentGrade = $gradeInput.val();
                    const gradeSelectHtml = '<select name="grade_letter[]" class="col-md-12">' +
                        buildSelectOptions(gradeLetterOptions, currentGrade, true, 'Select Grade') +
                        '</select>';
                    $gradeInput.replaceWith(gradeSelectHtml);
                }
            });
        }

        function collectCurrentFormState() {
            const state = {};
            $('#validation-form').find('input, select, textarea').each(function() {
                const element = this;
                const $element = $(element);
                const name = element.name;

                if (!name || name === '_token' || name === 'student_type' || element.type === 'file') {
                    return;
                }

                if (element.type === 'radio') {
                    if (!state[name]) {
                        state[name] = { type: 'radio', value: null };
                    }
                    if (element.checked) {
                        state[name].value = element.value;
                    }
                    return;
                }

                if (element.type === 'checkbox') {
                    if (name.endsWith('[]')) {
                        if (!state[name]) {
                            state[name] = { type: 'checkbox-array', values: [] };
                        }
                        if (element.checked) {
                            state[name].values.push(element.value);
                        }
                    } else {
                        state[name] = { type: 'checkbox', checked: element.checked };
                    }
                    return;
                }

                if (name.endsWith('[]')) {
                    if (!state[name]) {
                        state[name] = { type: 'array', values: [] };
                    }
                    state[name].values.push($element.val());
                    return;
                }

                state[name] = { type: 'value', value: $element.val() };
            });

            return state;
        }

        function applyFormState(state) {
            if (!state) {
                return;
            }

            Object.keys(state).forEach(function(name) {
                const fieldState = state[name];
                const $elements = $('[name="' + name + '"]');

                if (!$elements.length || !fieldState) {
                    return;
                }

                if (fieldState.type === 'radio') {
                    $elements.prop('checked', false);
                    if (fieldState.value !== null) {
                        $elements.filter('[value="' + String(fieldState.value) + '"]').prop('checked', true);
                    }
                    return;
                }

                if (fieldState.type === 'checkbox') {
                    $elements.prop('checked', !!fieldState.checked);
                    return;
                }

                if (fieldState.type === 'checkbox-array') {
                    const selectedValues = fieldState.values || [];
                    $elements.prop('checked', false);
                    selectedValues.forEach(function(value) {
                        $elements.filter('[value="' + String(value) + '"]').prop('checked', true);
                    });
                    return;
                }

                if (fieldState.type === 'array') {
                    const values = fieldState.values || [];
                    $elements.each(function(index) {
                        $(this).val(values[index] !== undefined ? values[index] : '');
                    });
                    return;
                }

                if (fieldState.type === 'value') {
                    $elements.first().val(fieldState.value !== undefined ? fieldState.value : '');
                }
            });

            initializeGuardianSelection(document.getElementById('validation-form'));
            normalizeAcademicInfoRows();
        }

        function cacheCurrentStudentTypeSnapshot(studentType) {
            if (studentType !== 'new' && studentType !== 'old') {
                return;
            }
            studentTypeFormSnapshots[studentType] = collectCurrentFormState();
        }

        function restoreStudentTypeSnapshot(studentType) {
            if (studentType !== 'new' && studentType !== 'old') {
                return;
            }
            applyFormState(studentTypeFormSnapshots[studentType]);
        }

        const generalFieldRules = [
            { field: 'input[name="first_name"]', message: "Please enter first name" },
            { field: 'input[name="last_name"]', message: "Please enter last name" },
            { field: 'input[name="date_of_birth"]', message: "Please select date of birth" },
            { field: 'select[name="gender"]', message: "Please select gender" },
            { field: 'input[name="nationality"]', message: "Please enter nationality" },
            { field: 'input[name="email"]', message: "Please enter email address" },
            { field: 'input[name="mobile_1"]', message: "Please enter mobile number" },
            { field: 'input[name="address"]', message: "Please enter address" },
            { field: 'input[name="state"]', message: "Please enter state" },
            { field: 'input[name="country"]', message: "Please enter country" },
            { field: 'input[name="father_first_name"]', message: "Please enter father\'s first name" },
            { field: 'input[name="father_last_name"]', message: "Please enter father\'s last name" },
            { field: 'input[name="mother_first_name"]', message: "Please enter mother\'s first name" },
            { field: 'input[name="mother_last_name"]', message: "Please enter mother\'s last name" },
            { field: 'select[name="batch"]', message: "Please select batch" },
            { field: 'select[name="faculty"]', message: "Please select faculty/program" },
            { field: 'select[name="semester"]', message: "Please select semester/section" }
        ];

        const guardianFieldRules = [
            { field: 'input[name="guardian_first_name"]', message: "Please enter guardian first name" },
            { field: 'input[name="guardian_last_name"]', message: "Please enter guardian last name" },
            { field: 'input[name="guardian_mobile_1"]', message: "Please enter guardian mobile number" },
            { field: 'input[name="guardian_relation"]', message: "Please enter guardian relation" },
            { field: 'input[name="guardian_address"]', message: "Please enter guardian address" }
        ];

        const realtimeFieldMessages = {
            first_name: "Please enter first name",
            last_name: "Please enter last name",
            date_of_birth: "Please select date of birth",
            gender: "Please select gender",
            nationality: "Please enter nationality",
            email: "Please enter email address",
            mobile_1: "Please enter mobile number",
            address: "Please enter address",
            state: "Please enter state",
            country: "Please enter country",
            father_first_name: "Please enter father's first name",
            father_last_name: "Please enter father's last name",
            mother_first_name: "Please enter mother's first name",
            mother_last_name: "Please enter mother's last name",
            batch: "Please select batch",
            faculty: "Please select faculty/program",
            semester: "Please select semester/section",
            guardian_first_name: "Please enter guardian first name",
            guardian_last_name: "Please enter guardian last name",
            guardian_mobile_1: "Please enter guardian mobile number",
            guardian_relation: "Please enter guardian relation",
            guardian_address: "Please enter guardian address"
        };

        const touchedFieldState = {};

        function getFieldKey($element) {
            return $element.attr('name') || $element.attr('id') || 'field';
        }

        function markFieldTouched($element) {
            touchedFieldState[getFieldKey($element)] = true;
        }

        function hasFieldBeenTouched($element) {
            return !!touchedFieldState[getFieldKey($element)];
        }

        function getErrorWrapper($element) {
            if ($element.attr('id') === 'subjects_wrapper') {
                return $element;
            }

            const $formGroup = $element.closest('.form-group');
            if ($formGroup.length) {
                return $formGroup;
            }

            const $customFile = $element.closest('.custom-file');
            if ($customFile.length) {
                return $customFile.parent();
            }

            return $element.parent();
        }

        function setFieldInvalid($element, message, showToast) {
            const fieldKey = getFieldKey($element);
            const $wrapper = getErrorWrapper($element);
            let $error = $wrapper.find('.live-validation-error[data-field="' + fieldKey + '"]');

            $element.addClass('is-invalid');
            if (!$element.is('input, select, textarea')) {
                $element.addClass('live-invalid-container');
            }
            if (!$error.length) {
                $error = $('<small class="text-danger live-validation-error"></small>').attr('data-field', fieldKey);
                $wrapper.append($error);
            }

            $error.text(message).show();

            if (showToast) {
                toastr.warning(message, 'Validation Error');
            }
        }

        function clearFieldInvalid($element) {
            const fieldKey = getFieldKey($element);
            const $wrapper = getErrorWrapper($element);

            $element.removeClass('is-invalid');
            $element.removeClass('live-invalid-container');
            $wrapper.find('.live-validation-error[data-field="' + fieldKey + '"]').remove();
        }

        function validateRequiredElement($element, message, showToast) {
            if (!$element.length) {
                return true;
            }

            let isValid = true;

            if ($element.is(':checkbox') || $element.is(':radio')) {
                isValid = $element.is(':checked');
            } else {
                const rawValue = $element.val();
                const value = rawValue === null ? '' : rawValue.toString().trim();
                isValid = value !== '' && value !== '0';
            }

            if (!isValid) {
                setFieldInvalid($element, message, showToast);
                return false;
            }

            clearFieldInvalid($element);
            return true;
        }

        function validateFieldFormat($element, showToast) {
            if (!$element.length) {
                return true;
            }

            const fieldName = $element.attr('name');
            const rawValue = $element.val();
            const value = rawValue === null ? '' : rawValue.toString().trim();

            if (value === '') {
                return true;
            }

            let message = '';

            if (fieldName === 'mobile_1' || fieldName === 'guardian_mobile_1') {
                if (!/^01\d{9}$/.test(value)) {
                    message = 'Mobile number must be 11 digits and start with 01.';
                }
            }

            if (fieldName === 'email') {
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    message = 'Please enter a valid email address.';
                }
            }

            if (fieldName === 'date_of_birth') {
                const selectedDate = new Date(value + 'T00:00:00');
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (Number.isNaN(selectedDate.getTime())) {
                    message = 'Please select a valid date of birth.';
                } else if (selectedDate > today) {
                    message = 'Date of birth cannot be in the future.';
                }
            }

            if (message) {
                setFieldInvalid($element, message, showToast);
                return false;
            }

            clearFieldInvalid($element);
            return true;
        }

        function shouldValidateGuardianFields() {
            const guardianIs = $('input[name="guardian_is"]:checked').val();
            return guardianIs === 'father_as_guardian' || guardianIs === 'mother_as_guardian' ||
                guardianIs === 'other_guardian' || guardianIs === 'self_guardian';
        }

        function validateSingleFieldByName(fieldName, showToast) {
            if (!realtimeFieldMessages[fieldName]) {
                return null;
            }

            if (guardianFieldRules.some(rule => rule.field === 'input[name="' + fieldName + '"]') && !shouldValidateGuardianFields()) {
                clearFieldInvalid($('input[name="' + fieldName + '"]'));
                return true;
            }

            const $element = $('[name="' + fieldName + '"]').first();
            const requiredValid = validateRequiredElement($element, realtimeFieldMessages[fieldName], showToast);
            if (!requiredValid) {
                return false;
            }

            return validateFieldFormat($element, showToast);
        }

        function getSubjectMaxCount() {
            const maxFromInput = parseInt($('input[name="max_subjects_count"]').val(), 10);
            if (!Number.isNaN(maxFromInput) && maxFromInput > 0) {
                return maxFromInput;
            }

            return $('#subjects_wrapper').find('input[name="subject[]"]').length;
        }

        function validateSubjectSelection(showToast) {
            const $wrapper = $('#subjects_wrapper');
            const $subjects = $wrapper.find('input[name="subject[]"]');

            if (!$subjects.length) {
                return false;
            }

            const selectedCount = $subjects.filter(':checked').length;
            const maxCount = getSubjectMaxCount();

            if (selectedCount < 1) {
                setFieldInvalid($wrapper, 'Please select at least 1 subject.', showToast);
                return false;
            }

            if (selectedCount > maxCount) {
                setFieldInvalid($wrapper, 'You can select maximum ' + maxCount + ' subjects.', showToast);
                return false;
            }

            clearFieldInvalid($wrapper);
            return true;
        }

        // Centralized navigation function
        function navigateTab(direction) {
            const currentTab = $('.tab-pane.active').attr('id');
            const currentIndex = tabOrder.indexOf(currentTab);

            if (direction === 'next' && validateTab(currentTab)) {
                if (currentIndex < tabOrder.length - 1) {
                    activateTab(tabOrder[currentIndex + 1]);
                }
            } else if (direction === 'prev') {
                if (currentIndex > 0) {
                    activateTab(tabOrder[currentIndex - 1]);
                }
            }
        }

        // Tab activation function
        function activateTab(tabName) {
            $('.tab-pane').removeClass('show active');
            $('#' + tabName).addClass('show active');
            $('.nav-tabs .nav-link').removeClass('active');
            $('a[href="#' + tabName + '"]').addClass('active');

            updateNavigationButtons(tabName);

            $('html, body').animate({
                scrollTop: $('.registration-form-container').offset().top - 20
            }, 300);
        }

        // Update navigation buttons visibility
        function updateNavigationButtons(currentTab) {
            const currentIndex = tabOrder.indexOf(currentTab);
            const prevButton = $('.btn-prev');
            const nextButton = $('.btn-next');

            prevButton.toggle(currentIndex !== 0);

            if (currentIndex === tabOrder.length - 1) {
                nextButton.hide();
                // Show skip-payment submit button when old student hides payment
                const oldStudentSkipsPayment = hidePaymentForOldStudent &&
                    $('#studentTypeSelect').val() === 'old';
                if (oldStudentSkipsPayment) {
                    $('#add-student-skip-payment').show();
                    $('#add-student').hide();
                } else {
                    $('#add-student').show();
                    $('#add-student-skip-payment').hide();
                }
            } else {
                nextButton.show();
                $('#add-student').hide();
                $('#add-student-skip-payment').hide();
            }
        }

        // Validation function
        function validateTab(tabName, options = {}) {
            const config = {
                showToast: options.showToast !== false,
                focusOnError: options.focusOnError !== false
            };

            let isValid = true;
            let firstInvalidField = null;
            let toastShown = false;

            const canShowToast = function() {
                if (!config.showToast || toastShown) {
                    return false;
                }

                toastShown = true;
                return true;
            };

            switch (tabName) {
                case 'generalInfo':
                    generalFieldRules.forEach(function(rule) {
                        const $element = $(rule.field);
                        const fieldValid = validateRequiredElement($element, rule.message, canShowToast());

                        if (!fieldValid) {
                            if (!firstInvalidField) {
                                firstInvalidField = $element;
                            }
                            isValid = false;
                            return;
                        }

                        const formatValid = validateFieldFormat($element, canShowToast());
                        if (!formatValid) {
                            if (!firstInvalidField) {
                                firstInvalidField = $element;
                            }
                            isValid = false;
                        }
                    });

                    if (shouldValidateGuardianFields()) {
                        guardianFieldRules.forEach(function(rule) {
                            const $element = $(rule.field);
                            const fieldValid = validateRequiredElement($element, rule.message, canShowToast());

                            if (!fieldValid) {
                                if (!firstInvalidField) {
                                    firstInvalidField = $element;
                                }
                                isValid = false;
                                return;
                            }

                            const formatValid = validateFieldFormat($element, canShowToast());
                            if (!formatValid) {
                                if (!firstInvalidField) {
                                    firstInvalidField = $element;
                                }
                                isValid = false;
                            }
                        });
                    } else {
                        guardianFieldRules.forEach(function(rule) {
                            clearFieldInvalid($(rule.field));
                        });
                    }
                    break;

                case 'academicInfo':
                    if (!$('#subjects_wrapper').children().length) {
                        const $semester = $('select[name="semester"]');
                        setFieldInvalid($semester, 'Please load subjects for the selected semester', canShowToast());
                        firstInvalidField = $semester;
                        isValid = false;
                    } else {
                        clearFieldInvalid($('select[name="semester"]'));

                        if (!validateSubjectSelection(canShowToast())) {
                            firstInvalidField = $('#subjects_wrapper');
                            isValid = false;
                        }
                    }
                    break;

                case 'profileImage':
                    if (profileImageValidationInProgress) {
                        setFieldInvalid($('#student_main_image'), 'Photo validation is in progress. Please wait a moment.', canShowToast());
                        firstInvalidField = $('#student_main_image');
                        isValid = false;
                    } else if ($('input[name="student_main_image"]').prop('required') && !$('#student_main_image').val()) {
                        setFieldInvalid($('#student_main_image'), 'Please upload a student photo', canShowToast());
                        firstInvalidField = $('#student_main_image');
                        isValid = false;
                    } else if (profileImageValidationError) {
                        setFieldInvalid($('#student_main_image'), profileImageValidationError, canShowToast());
                        firstInvalidField = $('#student_main_image');
                        isValid = false;
                    } else {
                        clearFieldInvalid($('#student_main_image'));
                    }
                    break;

                case 'rules':
                    if (!$('#agree_rules').is(':checked')) {
                        setFieldInvalid($('#agree_rules'), 'Please agree to the rules and regulations', canShowToast());
                        firstInvalidField = $('#agree_rules');
                        isValid = false;
                    } else {
                        clearFieldInvalid($('#agree_rules'));
                    }
                    break;

                case 'agreement':
                    if (!$('#agree_terms').is(':checked')) {
                        setFieldInvalid($('#agree_terms'), 'Please agree to the terms and conditions', canShowToast());
                        firstInvalidField = $('#agree_terms');
                        isValid = false;
                    } else {
                        clearFieldInvalid($('#agree_terms'));
                    }
                    break;
            }

            if (!isValid && firstInvalidField && config.focusOnError) {
                $('html, body').animate({
                    scrollTop: firstInvalidField.offset().top - 100
                }, 300);
            }

            return isValid;
        }

        const passportPhotoRules = {
            maxSizeBytes: {{ $passportPhotoMaxSizeMb * 1024 * 1024 }},
            minWidth: {{ $passportPhotoMinWidth }},
            minHeight: {{ $passportPhotoMinHeight }},
            ratio: {{ $passportPhotoRatio }},
            ratioTolerance: {{ $passportPhotoRatioTolerance }}
        };

        let profileImageValidationError = '';
        let profileImageValidationInProgress = false;

        function resetPhotoInput(input, message) {
            profileImageValidationInProgress = false;
            profileImageValidationError = message;
            input.value = '';
            setFieldInvalid($(input), message, false);
            $(input).next('.custom-file-label').removeClass('selected').html('Choose file');
            $('#photoPreviewImg').attr('src', '{{ asset('images/default-user.png') }}');
            toastr.warning(message, 'Photo Validation');
        }

        function validateSelectedPhoto(file, input, imageSource) {
            if (!file) {
                profileImageValidationInProgress = false;
                profileImageValidationError = '';
                return;
            }

            profileImageValidationInProgress = true;

            const allowedMimeTypes = ['image/jpeg', 'image/png', 'image/bmp'];
            if (allowedMimeTypes.indexOf(file.type) === -1) {
                resetPhotoInput(input, 'Student photo must be a JPG, JPEG, PNG, or BMP file.');
                return;
            }

            if (file.size > passportPhotoRules.maxSizeBytes) {
                resetPhotoInput(input, 'Student photo size must not be greater than 5 MB.');
                return;
            }

            const image = new Image();
            image.onload = function() {
                const ratio = image.width / image.height;

                if (image.width < passportPhotoRules.minWidth || image.height < passportPhotoRules.minHeight) {
                    resetPhotoInput(input, 'Photo resolution is too low. Minimum size is 300x400 pixels.');
                    return;
                }

                if (Math.abs(ratio - passportPhotoRules.ratio) > passportPhotoRules.ratioTolerance) {
                    resetPhotoInput(input, 'Photo must be passport style with a portrait ratio close to 35x45.');
                    return;
                }

                profileImageValidationInProgress = false;
                profileImageValidationError = '';
                clearFieldInvalid($(input));
                $('#photoPreviewImg').attr('src', imageSource);
            };

            image.onerror = function() {
                profileImageValidationInProgress = false;
                resetPhotoInput(input, 'Uploaded photo could not be read. Please choose another image.');
            };

            image.src = imageSource;
        }

        // Form submission handler
        $('#validation-form').on('submit', function(e) {
            if (!validateAllRequiredTabsBeforeSubmit()) {
                e.preventDefault();
                toastr.error("Please correct the errors before submitting", "Submission Error");
                return;
            }
            // Double-submit prevention: disable submit button after first valid submit
            $(this).find('[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
        });

        // Image preview function
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var file = input.files[0];

                clearFieldInvalid($(input));
                profileImageValidationError = '';

                reader.onload = function(e) {
                    validateSelectedPhoto(file, input, e.target.result);
                }

                reader.readAsDataURL(file);

                var fileName = file.name;
                $(input).next('.custom-file-label').addClass("selected").html(fileName);
            } else {
                profileImageValidationInProgress = false;
                profileImageValidationError = '';
                clearFieldInvalid($(input));
                $(input).next('.custom-file-label').removeClass('selected').html('Choose file');
                $('#photoPreviewImg').attr('src', '{{ asset('images/default-user.png') }}');
            }
        }

        function runRealtimeValidationForField($field, triggerType) {
            const fieldName = $field.attr('name');
            const shouldMarkTouched = triggerType === 'change' || triggerType === 'focusout';

            if (shouldMarkTouched) {
                markFieldTouched($field);
            }

            if (fieldName && realtimeFieldMessages[fieldName]) {
                if (hasFieldBeenTouched($field) || $field.hasClass('is-invalid') || triggerType === 'change') {
                    validateSingleFieldByName(fieldName, false);
                }
            }

            if (fieldName === 'guardian_is') {
                if (shouldValidateGuardianFields()) {
                    guardianFieldRules.forEach(function(rule) {
                        const $target = $(rule.field);
                        if (hasFieldBeenTouched($target) || $target.hasClass('is-invalid') || triggerType === 'change') {
                            validateRequiredElement($target, rule.message, false);
                            validateFieldFormat($target, false);
                        }
                    });
                } else {
                    guardianFieldRules.forEach(function(rule) {
                        clearFieldInvalid($(rule.field));
                    });
                }
            }

            if (fieldName === 'student_main_image' && !$field.val()) {
                setFieldInvalid($field, 'Please upload a student photo', false);
            } else if (fieldName === 'student_main_image' && triggerType === 'change' && $field.val()) {
                previewImage($field.get(0));
            }

            if (fieldName === 'agree_rules') {
                validateRequiredElement($('#agree_rules'), 'Please agree to the rules and regulations', false);
            }

            if (fieldName === 'agree_terms') {
                validateRequiredElement($('#agree_terms'), 'Please agree to the terms and conditions', false);
            }

            if (fieldName === 'subject[]') {
                validateSubjectSelection(false);
            }
        }

        function restoreStudentTypeFromState() {
            const queryParams = new URLSearchParams(window.location.search);
            const queryStudentType = queryParams.get('student_type');
            const storedStudentType = window.localStorage.getItem('online_registration_student_type');
            const preferredType = queryStudentType || storedStudentType;

            if (preferredType === 'new' || preferredType === 'old') {
                const $studentTypeSelect = $('#studentTypeSelect');
                if ($studentTypeSelect.length) {
                    $studentTypeSelect.val(preferredType);
                }
            }
        }

        // Initialize on page load
        $(document).ready(function() {
            restoreStudentTypeFromState();
            normalizeAcademicInfoRows();

            const $studentTypeSelect = $('#studentTypeSelect');
            lastStudentTypeSelection = $studentTypeSelect.val() || null;
            if (lastStudentTypeSelection === 'new' || lastStudentTypeSelection === 'old') {
                cacheCurrentStudentTypeSnapshot(lastStudentTypeSelection);
            }
            $studentTypeSelect.on('change', handleStudentTypeChange);
            updatePaymentInfo();

            const queryParams = new URLSearchParams(window.location.search);
            const requestedTab = queryParams.get('tab');
            const retryPayment = queryParams.get('retry_payment') === '1';
            const hasPaymentTab = $('#payment').length > 0;
            if (requestedTab === 'payment' && hasPaymentTab) {
                activateTab('payment');
                if (retryPayment) {
                    setTimeout(function() {
                        const studentType = $('#studentTypeSelect').val();
                        if (studentType) {
                            openPaymentMethodModal();
                        } else {
                            activateTab('studentType');
                            toastr.warning('Please select student type to continue payment.', 'Student Type Required');
                        }
                    }, 300);
                }
            } else {
                activateTab('studentType');
            }

            $('.nav-tabs .nav-link').on('click', function(e) {
                e.preventDefault();

                const currentTab = $('.tab-pane.active').attr('id');
                const targetHref = $(this).attr('href') || '';
                const targetTab = targetHref.replace('#', '');

                if (!targetTab || targetTab === currentTab) {
                    return;
                }

                const currentIndex = tabOrder.indexOf(currentTab);
                const targetIndex = tabOrder.indexOf(targetTab);

                if (targetIndex > currentIndex && !validateTab(currentTab)) {
                    return;
                }

                activateTab(targetTab);
            });

            $('#validation-form').on('input', 'input, textarea', function(e) {
                runRealtimeValidationForField($(this), e.type);
                cacheCurrentStudentTypeSnapshot($('#studentTypeSelect').val());
            });

            $('#validation-form').on('change focusout', 'input, select, textarea', function(e) {
                runRealtimeValidationForField($(this), e.type);
                cacheCurrentStudentTypeSnapshot($('#studentTypeSelect').val());
            });

            $('#subjects_wrapper').on('change', 'input[name="subject[]"]', function() {
                const maxCount = getSubjectMaxCount();
                const $subjects = $('#subjects_wrapper').find('input[name="subject[]"]');
                const selectedCount = $subjects.filter(':checked').length;

                if ($(this).is(':checked') && selectedCount > maxCount) {
                    this.checked = false;
                    setFieldInvalid($('#subjects_wrapper'), 'You can select maximum ' + maxCount + ' subjects.', true);
                    return;
                }

                validateSubjectSelection(false);
                cacheCurrentStudentTypeSnapshot($('#studentTypeSelect').val());
            });

            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1;
            var yyyy = today.getFullYear();

            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;

            $("input[name='reg_date']").val(yyyy + '-' + mm + '-' + dd);

            initializeGuardianSelection(document.getElementById('validation-form'));

            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);

                if ($(this).attr('id') === 'student_main_image') {
                    markFieldTouched($(this));
                    previewImage(this);
                }
            });

            $('select[name="guardian_link_id"]').select2({
                placeholder: 'Select Guardian...',
                ajax: {
                    url: '{{ route('student.guardian-name-autocomplete', [], false) }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            $('#load-guardian-html-btn').click(function() {
                var guardians_id = $('select[name="guardian_link_id"]').val();
                if (!guardians_id) {
                    toastr.warning("Please, Find Guardian First.", "Warning");
                } else {
                    $('#guardian_wrapper').empty();
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('student.guardianInfo-html', [], false) }}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: guardians_id
                        },
                        success: function(response) {
                            var data = $.parseJSON(response);
                            if (data.error) {
                                toastr.warning(data.message, "warning");
                            } else {
                                $('#guardian_wrapper').append(data.html);
                            }
                        }
                    });
                }
            });

            // Avoid mutating input value on each keyup (causes duplicate chars on some mobile keyboards).
            // Normalize uppercase right before submit for consistent stored values.
            $('#validation-form').on('submit', function() {
                $(this).find('.upper').each(function() {
                    if (typeof this.value === 'string') {
                        this.value = this.value.toUpperCase();
                    }
                });
            });

            // Initialize navigation buttons (studentType is the first tab)
            updateNavigationButtons('studentType');
        });

        /*copy permanent address on temporary address*/
        function CopyAddress(f) {
            if (f.permanent_address_copier.checked == true) {
                f.temp_address.value = f.address.value;
                f.temp_state.value = f.state.value;
                f.temp_postal_code.value = f.postal_code.value;
            }
        }

        /*copy Father Detail on Guardian Detail*/
        function FatherAsGuardian(f) {
            document.getElementById('guardian-detail').style.display = 'block';
            document.getElementById('link-guardian-detail').style.display = 'none';
            addRequiredFieldInGuardian();
            if (f.guardian_is.value == 'father_as_guardian') {
                f.guardian_first_name.value = f.father_first_name.value;
                f.guardian_middle_name.value = f.father_middle_name.value;
                f.guardian_last_name.value = f.father_last_name.value;
                f.guardian_eligibility.value = f.father_eligibility.value;
                f.guardian_occupation.value = f.father_occupation.value;
                f.guardian_office.value = f.father_office.value;
                f.guardian_office_number.value = f.father_office_number.value;
                f.guardian_residence_number.value = f.father_residence_number.value;
                f.guardian_mobile_1.value = f.father_mobile_1.value;
                f.guardian_mobile_2.value = f.father_mobile_2.value;
                f.guardian_email.value = f.father_email.value;
                f.guardian_relation.value = "FATHER";
            }
        }

        /*copy Mother Detail on Guardian Detail*/
        function MotherAsGuardian(f) {
            document.getElementById('guardian-detail').style.display = 'block';
            document.getElementById('link-guardian-detail').style.display = 'none';
            addRequiredFieldInGuardian();
            if (f.guardian_is.value == 'mother_as_guardian') {
                f.guardian_first_name.value = f.mother_first_name.value;
                f.guardian_middle_name.value = f.mother_middle_name.value;
                f.guardian_last_name.value = f.mother_last_name.value;
                f.guardian_eligibility.value = f.mother_eligibility.value;
                f.guardian_occupation.value = f.mother_occupation.value;
                f.guardian_office.value = f.mother_office.value;
                f.guardian_office_number.value = f.mother_office_number.value;
                f.guardian_residence_number.value = f.mother_residence_number.value;
                f.guardian_mobile_1.value = f.mother_mobile_1.value;
                f.guardian_mobile_2.value = f.mother_mobile_2.value;
                f.guardian_email.value = f.mother_email.value;
                f.guardian_relation.value = "MOTHER";
            }
        }

        /*copy Self Detail on Guardian Detail*/
        function SelfGuardian(f) {
            document.getElementById('guardian-detail').style.display = 'block';
            document.getElementById('link-guardian-detail').style.display = 'none';
            addRequiredFieldInGuardian();
            if (f.guardian_is.value == 'self_guardian') {
                f.guardian_first_name.value = f.first_name.value;
                f.guardian_middle_name.value = f.middle_name.value;
                f.guardian_last_name.value = f.last_name.value;
                f.guardian_residence_number.value = f.home_phone.value;
                f.guardian_mobile_1.value = f.mobile_1.value;
                f.guardian_mobile_2.value = f.mobile_2.value;
                f.guardian_email.value = f.email.value;
                f.guardian_address.value = f.address.value;
                f.guardian_relation.value = "SELF";
            }
        }

        /*Blank Guardian Detail to Enter New*/
        function OtherGuardian(f, keepExistingValues) {
            document.getElementById('guardian-detail').style.display = 'block';
            document.getElementById('link-guardian-detail').style.display = 'none';
            addRequiredFieldInGuardian();
            if (f.guardian_is.value == 'other_guardian' && !keepExistingValues) {
                f.guardian_first_name.value = "";
                f.guardian_middle_name.value = "";
                f.guardian_last_name.value = "";
                f.guardian_eligibility.value = "";
                f.guardian_occupation.value = "";
                f.guardian_office.value = "";
                f.guardian_office_number.value = "";
                f.guardian_residence_number.value = "";
                f.guardian_mobile_1.value = "";
                f.guardian_mobile_2.value = "";
                f.guardian_email.value = "";
                f.guardian_relation.value = "";
            }
        }

        function applyGuardianViewState(selectedGuardian) {
            if (selectedGuardian === 'link_guardian') {
                document.getElementById('guardian-detail').style.display = 'none';
                document.getElementById('link-guardian-detail').style.display = 'block';
                removeRequiredFieldInGuardian();
                return;
            }

            document.getElementById('guardian-detail').style.display = 'block';
            document.getElementById('link-guardian-detail').style.display = 'none';
            addRequiredFieldInGuardian();
        }

        function inferGuardianSelection() {
            var selectedGuardian = $('input[name="guardian_is"]:checked').val();
            if (selectedGuardian) {
                return selectedGuardian;
            }

            var relation = ($('input[name="guardian_relation"]').val() || '').trim().toUpperCase();
            if (relation === 'FATHER') {
                return 'father_as_guardian';
            }
            if (relation === 'MOTHER') {
                return 'mother_as_guardian';
            }
            if (relation === 'SELF') {
                return 'self_guardian';
            }
            if (relation !== '') {
                return 'other_guardian';
            }

            return 'self_guardian';
        }

        function initializeGuardianSelection(f) {
            if (!f) {
                return;
            }

            var selectedGuardian = inferGuardianSelection();
            $('input[name="guardian_is"]').prop('checked', false);
            $('input[name="guardian_is"][value="' + selectedGuardian + '"]').prop('checked', true);
            applyGuardianViewState(selectedGuardian);
        }

        function linkGuardian() {
            document.getElementById('guardian-detail').style.display = 'none';
            document.getElementById('link-guardian-detail').style.display = 'block';
            removeRequiredFieldInGuardian();
        }

        function addRequiredFieldInGuardian() {
            $('input[name="guardian_first_name"]').attr('required', 'required');
            $('input[name="guardian_last_name"]').attr('required', 'required');
            $('input[name="guardian_mobile_1"]').attr('required', 'required');
            $('input[name="guardian_relation"]').attr('required', 'required');
            $('input[name="guardian_address"]').attr('required', 'required');
        }

        function removeRequiredFieldInGuardian() {
            $('input[name="guardian_first_name"]').removeAttr('required');
            $('input[name="guardian_last_name"]').removeAttr('required');
            $('input[name="guardian_mobile_1"]').removeAttr('required');
            $('input[name="guardian_relation"]').removeAttr('required');
            $('input[name="guardian_address"]').removeAttr('required');
        }

        function loadSemesters($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('online-registration.find-semester', [], false) }}',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function(response) {
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                    if (data.error) {
                        $('.semester').html('').append('<option value="0">Select Sem./Sec.</option>');
                        toastr.warning(data.message || 'Semester list unavailable for this faculty/program.', "Warning");
                    } else {
                        $('.semester').html('').append('<option value="0">Select Sem./Sec.</option>');
                        if (data.semester && data.semester.length) {
                            $.each(data.semester, function(key, valueObj) {
                                $('.semester').append('<option value="' + valueObj.id + '">' + valueObj.semester + '</option>');
                            });
                        } else {
                            toastr.info(data.message || 'No semester found for this faculty/program.', "Info");
                        }
                    }
                }
            });
        }

        function loadSubject($this) {
            $('#subjects_wrapper').html('');
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semester"]').val();

            if (faculty == 0) {
                toastr.info("Please, Select Faculty/Program/Class", "Info:");
                return false;
            }

            if (semester == 0) {
                toastr.info("Please, Select Sem./Sec.", "Info:");
                return false;
            }

            if (!semester) {
                toastr.warning("Please, Choose Semester.", "Warning");
            } else {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('online-registration.find-subject', [], false) }}',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        faculty_id: faculty,
                        semester_id: semester
                    },
                    success: function(response) {
                        var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                        if (data.error) {
                            $('#subjects_wrapper').html('');
                            toastr.warning(data.error, "Warning:");
                        } else {
                            $('#subjects_wrapper').html('');
                            $('#subjects_wrapper').append(data.subjects);
                            clearFieldInvalid($('select[name="semester"]'));
                            clearFieldInvalid($('#subjects_wrapper'));
                            toastr.info(data.success, "Info:");
                        }
                    },
                    error: function() {
                        $('#subjects_wrapper').html('');
                        toastr.error('Subject list could not be loaded right now. Please try again.', 'Error');
                    }
                });
            }
            if (semester && $('#academicInfo_wrapper').length) {
                appendAcademicInfoRow(semester);
            }
        }

        function appendAcademicInfoRow($semester) {
            if (!$('#academicInfo_wrapper').length) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: '{{ route('student.academicInfo-html', [], false) }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    semester_id: $semester
                },
                success: function(response) {
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                    if (!data.error) {
                        $('#academicInfo_wrapper').empty();
                        $('#academicInfo_wrapper').append(data.html);
                        normalizeAcademicInfoRows();
                    }
                },
                error: function() {
                    $('#academicInfo_wrapper').empty();
                }
            });
        }

        // Student Type Selection Handler
        function setPaymentButtonState(enabled) {
            const $btn = $('#proceedPaymentBtn');
            if ($btn.length) {
                $btn.prop('disabled', !enabled);
            }
        }

        function updatePaymentInfo() {
            const studentType = $('#studentTypeSelect').val();
            const studentTypeNextBtn = $('#studentTypeNextBtn');
            const studentTypeInfoDiv = $('#studentTypeInfo');
            const studentTypeInfoText = $('#studentTypeInfoText');

            if (studentType === 'new' || studentType === 'old') {
                window.localStorage.setItem('online_registration_student_type', studentType);
            }

            // --- Payment tab visibility based on student type + admin setting ---
            if (studentType === 'old' && hidePaymentForOldStudent && paymentRequiredByDefault) {
                // Hide payment tab nav item and remove from tab order
                $('#paymentTab').hide();
                tabOrder = baseTabOrder.filter(function(t) { return t !== 'payment'; });
            } else if (paymentRequiredByDefault) {
                // Restore payment tab for new student (or when setting disabled)
                $('#paymentTab').show();
                tabOrder = baseTabOrder.slice();
            }
            // Refresh navigation buttons since tabOrder may have changed
            const currentTab = $('.tab-pane.active').attr('id');
            if (currentTab) { updateNavigationButtons(currentTab); }

            if (studentType === 'new') {
                studentTypeInfoText.text('As a new student, you will need to complete all required information and pay the registration fee.');
                studentTypeInfoDiv.show();
                studentTypeNextBtn.prop('disabled', false);
                $('#registrationFeeAmount').text('৳{{ $data['registration_setting']->new_student_registration_fee ?? 0 }}');
                setPaymentButtonState(true);
            } else if (studentType === 'old') {
                studentTypeInfoText.text('As a returning student, please ensure your information is up-to-date.');
                studentTypeInfoDiv.show();
                studentTypeNextBtn.prop('disabled', false);
                $('#registrationFeeAmount').text('৳{{ $data['registration_setting']->old_student_registration_fee ?? 0 }}');
                // No payment needed for old student when admin enables the hide setting
                setPaymentButtonState(!hidePaymentForOldStudent);
            } else {
                studentTypeInfoDiv.hide();
                studentTypeNextBtn.prop('disabled', true);
                $('#registrationFeeAmount').text('৳0');
                setPaymentButtonState(false);
                window.localStorage.removeItem('online_registration_student_type');
            }
        }

        function handleStudentTypeChange() {
            const selectedType = $('#studentTypeSelect').val();

            if (lastStudentTypeSelection === 'new' || lastStudentTypeSelection === 'old') {
                cacheCurrentStudentTypeSnapshot(lastStudentTypeSelection);
            }

            updatePaymentInfo();

            if (selectedType === 'new' || selectedType === 'old') {
                restoreStudentTypeSnapshot(selectedType);
            }

            lastStudentTypeSelection = selectedType || null;
        }

        function validateAllRequiredTabsBeforeSubmit() {
            const tabsToValidate = tabOrder.filter(function(tab) {
                return tab !== 'payment';
            });

            for (let i = 0; i < tabsToValidate.length; i++) {
                const tabName = tabsToValidate[i];
                const valid = validateTab(tabName, {
                    showToast: false,
                    focusOnError: false
                });

                if (!valid) {
                    activateTab(tabName);
                    validateTab(tabName, {
                        showToast: true,
                        focusOnError: true
                    });
                    return false;
                }
            }

            return true;
        }

        let selectedPaymentMethod = null;

        function openPaymentMethodModal() {
            const studentType = $('#studentTypeSelect').val();
            if (!studentType) {
                toastr.error('Please select student type first.', 'Payment Error');
                return;
            }

            const modalEl = document.getElementById('paymentMethodModal');
            if (!modalEl) {
                toastr.error('Payment modal is unavailable right now. Please refresh and try again.', 'Payment Error');
                return;
            }

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const paymentModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                paymentModal.show();
                return;
            }

            if (typeof $ !== 'undefined' && typeof $('#paymentMethodModal').modal === 'function') {
                $('#paymentMethodModal').modal('show');
                return;
            }

            toastr.error('Payment modal could not be opened. Please refresh and try again.', 'Payment Error');
        }

        function choosePaymentMethod(method) {
            selectedPaymentMethod = method;
            const methodText = method === 'ssl' ? 'SSL Commerz' : 'United Commercial Bank';
            $('#selectedPaymentMethodText').text(methodText);

            const modalEl = document.getElementById('paymentMethodModal');
            if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const paymentModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                paymentModal.hide();
            } else if (typeof $ !== 'undefined' && typeof $('#paymentMethodModal').modal === 'function') {
                $('#paymentMethodModal').modal('hide');
            }

            processPayment(method);
        }

        // Process Payment Handler
        function processPayment(methodFromPicker) {
            const studentType = $('#studentTypeSelect').val();
            const paymentMethod = methodFromPicker || selectedPaymentMethod;

            if (!paymentMethod) {
                toastr.error('Please select a payment method', 'Payment Method Required');
                return;
            }

            // Collect form data with proper array support (e.g. board[], institution[])
            const registrationData = {};
            const serialized = $('#validation-form').serializeArray();
            serialized.forEach(function(item) {
                if (!item.name) {
                    return;
                }

                const isArrayField = item.name.endsWith('[]');
                const key = isArrayField ? item.name.slice(0, -2) : item.name;

                if (isArrayField) {
                    if (!Array.isArray(registrationData[key])) {
                        registrationData[key] = [];
                    }
                    registrationData[key].push(item.value);
                } else {
                    registrationData[key] = item.value;
                }
            });

            const payload = new FormData();
            payload.append('student_type', studentType);
            payload.append('payment_method', paymentMethod);
            payload.append('amount', $('#registrationFeeAmount').text().replace('৳', ''));
            payload.append('registration_data', JSON.stringify(registrationData));
            payload.append('_token', '{{ csrf_token() }}');

            // Attach profile/parent images so backend can store real filenames instead of browser fakepath.
            ['student_main_image', 'father_main_image', 'mother_main_image', 'guardian_main_image'].forEach(function(field) {
                const fileInput = document.getElementById(field);
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    payload.append(field, fileInput.files[0]);
                }
            });

            // Submit payment request
            $.ajax({
                url: '{{ route("registration-payment.pay", [], false) }}',
                type: 'POST',
                data: payload,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success && response.gateway_url) {
                        window.location.href = response.gateway_url;
                    } else {
                        toastr.error(response.message || 'Payment initialization failed', 'Error');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'An error occurred while processing payment';
                    toastr.error(errorMsg, 'Payment Error');
                }
            });
        }

        // Validate student type before allowing next
        function validateStudentType() {
            const studentType = $('#studentTypeSelect').val();
            if (!studentType) {
                toastr.error('Please select a student type to continue', 'Student Type Required');
                return false;
            }
            return true;
        }

        // Override validateTab for student type
        const originalValidateTab = validateTab;
        window.validateTab = function(tabName, options) {
            if (tabName === 'studentType') {
                return validateStudentType();
            }
            return originalValidateTab(tabName, options);
        };
    </script>
</body>

</html>