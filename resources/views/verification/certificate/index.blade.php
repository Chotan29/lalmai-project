<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
            color: #1e5799;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .top-navbar .nav-link:hover {
            background-color: #f8f9fa;
            color: #1e5799;
        }

        .top-navbar .nav-link.active {
            background-color: #1e5799;
            color: #fff;
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

        .verification-container {
            padding: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-navigation {
            margin-top: 30px;
            text-align: center;
        }

        .btn-verify {
            min-width: 150px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 500;
            background: linear-gradient(135deg, #1e5799 0%, #207cca 51%, #2989d8 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-another {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
        }

        .certificate-result {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            display: none;
        }

        .certificate-header {
            background-color: #e9f7ef;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .certificate-title {
            font-size: 24px;
            font-weight: 600;
            color: #28a745;
            margin-bottom: 5px;
        }

        .certificate-subtitle {
            font-size: 18px;
            font-weight: 500;
            color: #333;
        }

        .certificate-content {
            padding: 20px;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .certificate-details {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #eee;
        }

        .certificate-details:last-child {
            border-bottom: none;
        }

        .certificate-label {
            font-weight: 500;
            color: #666;
            min-width: 150px;
            display: inline-block;
        }

        .certificate-value {
            font-weight: 400;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 4px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #495057;
        }

        .required:after {
            content: " *";
            color: #dc3545;
        }

        .status-badge {
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-verified {
            background-color: #28a745;
            color: white;
        }

        .verification-instructions {
            background-color: #e9f0f7;
            border-left: 4px solid #1e5799;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
        }

        .form-horizontal .form-group {
            margin-right: 0;
            margin-left: 0;
        }
        
        .verification-form-container {
            transition: all 0.3s ease;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
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

            .verification-container {
                padding: 20px;
            }
            
            .top-navbar .nav-item {
                margin-left: 8px;
                margin-right: 8px;
            }
            
            .top-navbar .nav-link {
                padding: 6px 8px;
                font-size: 14px;
            }
            
            .col-sm-4, .col-sm-8 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="top-navbar">
        <div class="container">
            <nav role="navigation" class="navbar-menu">
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <a href="{{ route('online-registration.registration') }}" class="nav-link">
                            <i class="fas fa-user"></i> Online Registration
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('online-registration.find') }}" class="nav-link">
                            <i class="fas fa-search"></i> Find & Print
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('verification.certificate') }}" class="nav-link active">
                            <i class="fas fa-certificate"></i> Certificate Verification
                        </a>
                    </li>
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
                <h1>{{ $generalSetting->institute ?? 'EduFirm Web Portal' }}</h1>
                <p class="institution-details">
                    {{ $generalSetting->address ?? '' }},
                    {{ $generalSetting->phone ?? '' }},
                    {{ $generalSetting->email ?? '' }}
                </p>
                <h2>Certificate Verification</h2>
            </div>
        </div>

        <div class="verification-container">
          
            
            <!-- Verification Form Container -->
            <div id="verificationFormContainer" @if(isset($data['certificateContent'])) style="display: none;" @endif>
                  <div class="verification-instructions">
                <h4><i class="fas fa-info-circle text-primary"></i> How to Verify Your Certificate</h4>
                <p>Enter the certificate details exactly as they appear on your document. All fields marked with <span class="text-danger">*</span> are required for verification.</p>
            </div>
            
            <h3 class="section-title"><i class="fas fa-certificate"></i> Verify Certificate Authenticity</h3>
                <form action="{{ route('verification.certificate') }}" method="GET" class="form-horizontal" id="certificateVerificationForm">
                    <div class="form-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label required">Certificate Type</label>
                                    <div class="col-sm-8">
                                        <select name="certificate" class="form-control" required>
                                            <option value="">Select Certificate Type</option>
                                            @foreach($data['certificate-template'] as $key => $certificate)
                                                @if($key != '')
                                                    <option value="{{ $key }}" {{ request('certificate') == $key ? 'selected' : '' }}>{{ $certificate }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label required">Date of Issue</label>
                                    <div class="col-sm-8">
                                        <input type="date" name="issue_dte" class="form-control" value="{{ request('issue_dte') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label required">Registration No</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="reg_no" class="form-control" placeholder="Enter registration number" value="{{ request('reg_no') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label required">First Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="first_name" class="form-control" placeholder="Enter your first name" value="{{ request('first_name') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label required">Date of Birth</label>
                                    <div class="col-sm-8">
                                        <input type="date" name="date_of_birth" class="form-control" value="{{ request('date_of_birth') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-navigation">
                        <button type="submit" class="btn btn-primary btn-verify" id="filter-btn">
                            <i class="fa fa-search mr-2"></i> Verify Certificate
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Result Container -->
            <div id="certificateResultContainer" class="certificate-result" @if(isset($data['certificateContent'])) style="display: block;" @endif>
                @if(isset($data['certificateContent']))
                <div class="certificate-header">
                    <h3 class="certificate-title">
                        <i class="fas fa-shield-alt mr-2"></i> Valid Certificate Found
                    </h3>
                    <p class="certificate-subtitle">This certificate has been verified and authenticated by our system.</p>
                </div>
                
                <div class="certificate-content">
                    <div class="certificate-details">
                        <span class="certificate-label">Certificate Type:</span>
                        <span class="certificate-value">{{ $data['certificate_template']->certificate ?? 'N/A' }}</span>
                    </div>
                    <div class="certificate-details">
                        <span class="certificate-label">Issued To:</span>
                        <span class="certificate-value">{{ $student->first_name ?? '' }} {{ $student->middle_name ?? '' }} {{ $student->last_name ?? '' }}</span>
                    </div>
                    <div class="certificate-details">
                        <span class="certificate-label">Registration No:</span>
                        <span class="certificate-value">{{ $student->reg_no ?? 'N/A' }}</span>
                    </div>
                    <div class="certificate-details">
                        <span class="certificate-label">Date of Issue:</span>
                        <span class="certificate-value">
                            @if(isset($data['issue_detail']->date_of_issue))
                                {{ \Carbon\Carbon::parse($data['issue_detail']->date_of_issue)->format('d-m-Y') }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="certificate-details">
                        <span class="certificate-label">Certificate ID:</span>
                        <span class="certificate-value">CERT-{{ $student->id ?? '' }}{{ $student->certificate_id ?? '' }}</span>
                    </div>
                    <div class="certificate-details">
                        <span class="certificate-label">Verification Status:</span>
                        <span class="certificate-value">
                            <span class="status-badge badge-verified">Verified & Authentic</span>
                        </span>
                    </div>
                </div>
                
                <div class="text-center">
                    <button id="verifyAnotherBtn" class="btn btn-another">
                        <i class="fas fa-redo mr-2"></i> Verify Another Certificate
                    </button>
                </div>
                @endif
            </div>
            
            <!-- Message Container -->
            <div id="messageContainer" class="mt-4">
                @if(session()->has('message_warning'))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('message_warning') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle between form and result
            $('#verifyAnotherBtn').on('click', function() {
                $('#certificateResultContainer').slideUp(400, function() {
                    $('#verificationFormContainer').slideDown();
                    $('html, body').animate({
                        scrollTop: $('#verificationFormContainer').offset().top - 100
                    }, 500);
                });
            });
            
            // If there's an error message, ensure form is visible
            @if(session()->has('message_warning'))
                $('#verificationFormContainer').show();
                $('#certificateResultContainer').hide();
                toastr.warning("{{ session('message_warning') }}", "Verification Notice");
            @endif
            
            // If certificate content exists, ensure result is visible
            @if(isset($data['certificateContent']))
                $('#verificationFormContainer').hide();
                $('#certificateResultContainer').show();
                $('html, body').animate({
                    scrollTop: $('#certificateResultContainer').offset().top - 100
                }, 500);
            @endif
            
            // Form submission handler
            $('#certificateVerificationForm').on('submit', function() {
                $('#filter-btn').html('<i class="fas fa-spinner fa-spin mr-2"></i> Verifying...').prop('disabled', true);
            });
        });
    </script>
</body>
</html>