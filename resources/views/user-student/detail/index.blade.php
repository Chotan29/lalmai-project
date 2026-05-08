@extends('user-student.layouts.master')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #ebedfc;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(30deg);
        }
        
        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: var(--transition);
            z-index: 2;
            position: relative;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
        }
        
        .nav-tabs {
            border-bottom: none;
            background: white;
            border-radius: var(--border-radius);
            padding: 0.75rem;
            box-shadow: var(--box-shadow);
            display: flex;
            overflow-x: auto;
            scrollbar-width: none;
        }
        
        .nav-tabs::-webkit-scrollbar {
            display: none;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: var(--dark-color);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: var(--transition);
            white-space: nowrap;
            display: flex;
            align-items: center;
        }
        
        .nav-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .nav-tabs .nav-link:hover:not(.active) {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }
        
        .nav-tabs .nav-link .bi {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        
        .tab-content {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--box-shadow);
            margin-top: 1rem;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .info-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--box-shadow);
            border-left: 4px solid var(--primary-color);
            transition: var(--transition);
            height: 100%;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .info-card h5 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .info-card h5 .bi {
            margin-right: 10px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--dark-color);
            min-width: 150px;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #6c757d;
            word-break: break-word;
        }
        
        .status-badge {
            padding: 0.4rem 0.9rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }
        
        .badge-active {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .badge-inactive {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .document-card {
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            height: 100%;
        }
        
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }
        
        .document-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-right: 1.5rem;
            flex-shrink: 0;
        }
        
        .family-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: var(--transition);
        }
        
        .family-photo:hover {
            transform: scale(1.1);
        }
        
        .table-responsive {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .table tbody tr {
            transition: var(--transition);
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .alert {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-title h5 {
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }
        
        .section-title .bi {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        @media (max-width: 992px) {
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
            
            .info-label {
                min-width: 120px;
            }
        }
        
        @media (max-width: 768px) {
            .profile-header {
                text-align: center;
                padding: 1.5rem;
            }
            
            .profile-avatar {
                margin: 0 auto 1rem;
                width: 80px;
                height: 80px;
            }
            
            .info-item {
                flex-direction: column;
            }
            
            .info-label {
                margin-bottom: 0.25rem;
                min-width: auto;
            }
            
            .nav-tabs .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .tab-content {
                padding: 1.5rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <!-- Profile Header -->
            <div class="profile-header animate__animated animate__fadeIn">
                <div class="d-flex flex-column flex-md-row align-items-center">
                    <div class="mb-3 mb-md-0 mr-md-4 position-relative text-center">
                        @if($data['student']->student_image != '')
                            <img class="profile-avatar" src="{{ asset('images/studentProfile/'.$data['student']->student_image) }}" alt="Student Photo">
                        @else
                            <img class="profile-avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" alt="Student Photo">
                        @endif
                    </div>
                    <div class="text-center text-md-left">
                        <h2 class="mb-2">{{ $data['student']->first_name.' '.$data['student']->middle_name.' '.$data['student']->last_name }}</h2>
                        <div class="d-flex flex-wrap justify-content-center justify-content-md-start align-items-center mb-3">
                            <span class="status-badge {{ $data['student']->status == 'active' ? 'badge-active' : 'badge-inactive' }} mr-2 mb-2">
                                <i class="bi bi-circle-fill mr-1"></i> {{ ucfirst($data['student']->status) }}
                            </span>
                            @if($data['student']->academic_status)
                                <span class="badge bg-light text-dark mb-2">{{ ViewHelper::getAcademicStatus($data['student']->academic_status) }}</span>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap justify-content-center justify-content-md-start">
                            <div class="mr-4 mb-2">
                                <small class="text-white-50 d-block">Reg No.</small>
                                <p class="mb-0 font-weight-bold">{{ $data['student']->reg_no }}</p>
                            </div>
                            @if($data['student']->univ_reg)
                            <div class="mr-4 mb-2">
                                <small class="text-white-50 d-block">Univ. Reg</small>
                                <p class="mb-0 font-weight-bold">{{ $data['student']->univ_reg }}</p>
                            </div>
                            @endif
                            <div class="mb-2">
                                <small class="text-white-50 d-block">Faculty</small>
                                <p class="mb-0 font-weight-bold">{{ ViewHelper::getFacultyTitle($data['student']->faculty) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="row">
                <div class="col-md-12">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
                                    <i class="bi bi-person"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="academic-tab" data-toggle="tab" href="#academicInfo" role="tab" aria-controls="academicInfo" aria-selected="false">
                                    <i class="bi bi-book"></i> Academic
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">
                                    <i class="bi bi-file-earmark"></i> Documents
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="false">
                                    <i class="bi bi-sticky"></i> Notes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="login-tab" data-toggle="tab" href="#login-access" role="tab" aria-controls="login-access" aria-selected="false">
                                    <i class="bi bi-shield-lock"></i> Login Access
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="profileTabsContent">
                            <div class="tab-pane fade active in" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                @include('user-student.detail.includes.profile')
                            </div>
                            <div class="tab-pane fade" id="academicInfo" role="tabpanel" aria-labelledby="academic-tab">
                                @include('user-student.detail.includes.academicInfo')
                            </div>
                            <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                @include('user-student.detail.includes.documents')
                            </div>
                            <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                                @include('user-student.detail.includes.notes')
                            </div>
                            <div class="tab-pane fade" id="login-access" role="tabpanel" aria-labelledby="login-tab">
                                @include('user-student.detail.includes.login-access')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Smooth scroll for tabs on mobile
    document.querySelectorAll('.nav-tabs .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            const targetTab = document.querySelector(this.getAttribute('href'));
            targetTab.scrollIntoView({ behavior: 'smooth' });
        });
    });
    
    // Add animation when switching tabs
    $('#profileTabs a').on('shown.bs.tab', function() {
        const activePane = $($(this).attr('href'));
        activePane.addClass('animate__animated animate__fadeIn');
        setTimeout(() => {
            activePane.removeClass('animate__animated animate__fadeIn');
        }, 500);
    });
</script>
@endsection