@extends('user-student.layouts.master')

@section('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1f5aa6;
            --primary-light: #eaf1ff;
            --secondary-color: #12396b;
            --accent-color: #16a3b7;
            --light-color: #f4f6fb;
            --dark-color: #142033;
            --success-color: #2e9f6f;
            --warning-color: #c98a1c;
            --danger-color: #ce4b51;
            --border-radius: 14px;
            --box-shadow: 0 14px 36px rgba(16, 35, 62, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .page-content {
            background:
                radial-gradient(1200px 280px at -5% -20%, rgba(31, 90, 166, 0.14), transparent 70%),
                radial-gradient(900px 260px at 105% 0%, rgba(22, 163, 183, 0.14), transparent 70%),
                #f7f9fc;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .profile-header h2,
        .panel-title,
        .section-title h5,
        #profileTabs .nav-link {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: 0.2px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #1c4f91 0%, #133e74 55%, #0d2f57 100%);
            color: white;
            border-radius: var(--border-radius);
            padding: 2.2rem;
            margin-bottom: 1.2rem;
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.16);
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            transform: rotate(30deg);
        }

        .profile-header::after {
            content: '';
            position: absolute;
            inset: auto -40px -50px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            filter: blur(0.5px);
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
        
        #profileTabs {
            border-bottom: none;
            background: #ffffff;
            border-radius: var(--border-radius);
            padding: 0.75rem;
            box-shadow: var(--box-shadow);
            display: flex;
            overflow-x: auto;
            scrollbar-width: none;
        }
        
        #profileTabs::-webkit-scrollbar {
            display: none;
        }
        
        #profileTabs .nav-link {
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
        
        #profileTabs .nav-link.active {
            background: linear-gradient(135deg, #1f5aa6, #174a8b);
            color: white;
            box-shadow: 0 8px 20px rgba(21, 70, 132, 0.28);
        }
        
        #profileTabs .nav-link:hover:not(.active) {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }
        
        #profileTabs .nav-link .bi {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        
        #profileTabsContent {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem 2rem 1.2rem;
            box-shadow: var(--box-shadow);
            margin-top: 1rem;
            animation: fadeIn 0.5s ease;
            border: 1px solid #edf1f7;
        }

        .profile-quick-stats {
            margin-bottom: 1.3rem;
        }

        .quick-stat-card {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 251, 255, 0.96));
            border: 1px solid #dfe9f7;
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(18, 52, 92, 0.09);
            padding: 1rem 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            min-height: 88px;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .quick-stat-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #1f5aa6, #16a3b7);
            opacity: 0.8;
        }

        .quick-stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 55%;
            height: 100%;
            background: linear-gradient(110deg, transparent, rgba(255, 255, 255, 0.42), transparent);
            transition: left 0.55s ease;
        }

        .quick-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 34px rgba(16, 47, 90, 0.17);
            border-color: #c6d9f3;
        }

        .quick-stat-card:hover::after {
            left: 125%;
        }

        .quick-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
            transition: transform 0.25s ease;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
        }

        .quick-stat-card:hover .quick-stat-icon {
            transform: rotate(-8deg) scale(1.08);
        }

        .quick-stat-icon.primary { background: linear-gradient(135deg, #1f5aa6, #174a8b); }
        .quick-stat-icon.accent { background: linear-gradient(135deg, #0f9aa8, #147d8a); }
        .quick-stat-icon.warn { background: linear-gradient(135deg, #c98a1c, #ad7617); }
        .quick-stat-icon.dark { background: linear-gradient(135deg, #3c4a5f, #2d394b); }

        .quick-stat-meta {
            min-width: 0;
            width: 100%;
        }

        .quick-stat-meta .label {
            display: inline-flex;
            align-items: center;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #4f657f;
            background: #eaf1fb;
            border: 1px solid #d5e2f4;
            border-radius: 999px;
            padding: 0.28rem 0.7rem;
            margin-bottom: 0.42rem;
            font-weight: 800;
        }

        .quick-stat-meta .value {
            font-size: 1.2rem;
            color: #1f2e44;
            font-weight: 800;
            font-family: 'Space Grotesk', sans-serif;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.25s ease;
            display: block;
            letter-spacing: 0.15px;
        }

        .quick-stat-meta .value.value-id {
            letter-spacing: 0.45px;
        }

        .quick-stat-meta .value.value-mobile {
            font-size: 1.05rem;
            white-space: normal;
            line-height: 1.35;
        }

        .quick-stat-card:hover .quick-stat-meta .value {
            color: #143b6b;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .info-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.55rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--box-shadow);
            border-left: 4px solid var(--primary-color);
            border: 1px solid #edf1f7;
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
            background: linear-gradient(135deg, #1f5aa6, #174a8b);
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
            
            #profileTabs .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            #profileTabsContent {
                padding: 1.5rem;
            }

            .quick-stat-card {
                min-height: 76px;
                padding: 0.82rem 0.85rem;
                gap: 0.65rem;
            }

            .quick-stat-icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
            }

            .quick-stat-meta .label {
                font-size: 0.74rem;
                padding: 0.22rem 0.52rem;
            }

            .quick-stat-meta .value {
                font-size: 1.08rem;
            }

            .quick-stat-meta .value.value-mobile {
                font-size: 0.98rem;
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

            <div class="row profile-quick-stats">
                <div class="col-xs-12 col-sm-6 col-lg-3 mb-3">
                    <div class="quick-stat-card">
                        <span class="quick-stat-icon primary"><i class="bi bi-person-vcard"></i></span>
                        <div class="quick-stat-meta">
                            <span class="label">Registration No</span>
                            <span class="value value-id">{{ $data['student']->reg_no ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-lg-3 mb-3">
                    <div class="quick-stat-card">
                        <span class="quick-stat-icon accent"><i class="bi bi-collection"></i></span>
                        <div class="quick-stat-meta">
                            <span class="label">Batch</span>
                            <span class="value">{{ ViewHelper::getStudentBatchId($data['student']->batch) ?: 'Unknown' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-lg-3 mb-3">
                    <div class="quick-stat-card">
                        <span class="quick-stat-icon warn"><i class="bi bi-calendar2-week"></i></span>
                        <div class="quick-stat-meta">
                            <span class="label">Semester</span>
                            <span class="value">{{ ViewHelper::getSemesterTitle($data['student']->semester) ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-lg-3 mb-3">
                    <div class="quick-stat-card">
                        <span class="quick-stat-icon dark"><i class="bi bi-telephone-forward"></i></span>
                        <div class="quick-stat-meta">
                            <span class="label">Mobile</span>
                            <span class="value value-mobile">
                                @if($data['student']->mobile_1)
                                    {{ $data['student']->mobile_1 }}{{ $data['student']->mobile_2 ? ', '.$data['student']->mobile_2 : '' }}
                                @else
                                    N/A
                                @endif
                            </span>
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
    document.querySelectorAll('#profileTabs .nav-link').forEach(link => {
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