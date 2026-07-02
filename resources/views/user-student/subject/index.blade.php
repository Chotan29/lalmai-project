@extends('user-student.layouts.master')

@section('css')
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --dark-color: #1a1a2e;
        --light-color: #f8f9fa;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --danger-color: #f87171;
    }
    
    .course-dashboard {
        background-color: #f5f7fb;
        min-height: 100vh;
    }
    
    .dashboard-header {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .semester-info {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }
    
    .semester-info i {
        margin-right: 12px;
        font-size: 1.8rem;
    }
    
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        margin-left: 15px;
    }
    
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.5rem;
    }
    
    .credits .stats-icon { background: rgba(67, 97, 238, 0.1); color: var(--primary-color); }
    .courses .stats-icon { background: rgba(76, 201, 240, 0.1); color: var(--accent-color); }
    
    .course-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }
    
    .course-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .course-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 20px;
        position: relative;
    }
    
    .course-code {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.2);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .course-body {
        padding: 20px;
    }
    
    .course-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--warning-color);
    }
    
    .course-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .meta-item i {
        margin-right: 8px;
        color: var(--primary-color);
    }
    
    .progress-container {
        margin-bottom: 20px;
    }
    
    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .progress-bar {
        height: 8px;
        border-radius: 4px;
        background: #e9ecef;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
        width: 65%; /* This would be dynamic in real implementation */
    }
    
    .marks-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 15px;
    }
    
    .marks-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
    }
    
    .marks-card h5 {
        margin: 0 0 5px 0;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .marks-value {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--dark-color);
    }
    
    .instructor-card {
        display: flex;
        align-items: center;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .instructor-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 12px;
    }
    
    .instructor-info h5 {
        margin: 0;
        font-size: 0.95rem;
        color: var(--dark-color);
    }
    
    .instructor-info p {
        margin: 3px 0 0;
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    .btn-outline {
        border: 1px solid #dee2e6;
        background: transparent;
        color: #6c757d;
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    
    .btn-outline:hover {
        background: #f8f9fa;
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 0.85rem;
        border: none;
        transition: all 0.2s ease;
    }
    
    .btn-primary:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }
    
    .no-courses {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    
    .no-courses i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .no-courses h4 {
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .no-courses p {
        color: #adb5bd;
        margin-bottom: 20px;
    }
    
    .btn-refresh {
        background: var(--light-color);
        color: var(--primary-color);
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-refresh i {
        margin-right: 8px;
    }
    
    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .stats-container {
            display: flex;
            width: 100%;
            margin-top: 15px;
        }
        
        .stats-card {
            margin-left: 0;
            margin-right: 10px;
            flex: 1;
        }
        
        .course-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="course-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="dashboard-header">
                <div class="semester-info">
                    <i class="fas fa-graduation-cap"></i>
                    <div>
                        <h4 style="margin: 0; font-weight: 600;">My Courses</h4>
                        <p style="margin: 5px 0 0; opacity: 0.9; font-size: 0.9rem;">
                            {{ \App\Facades\ViewHelperFacade::getFacultyTitle($data['semester']->faculty()->first()->id) }} |  
                            {{ \App\Facades\ViewHelperFacade::getSemesterTitle($data['semester']->id) }}
                        </p>
                    </div>
                </div>
                
                <div class="stats-container">
                    <div class="stats-card credits">
                        <div class="stats-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h5 style="margin: 0; color: #6c757d; font-size: 0.9rem;">Total Credits</h5>
                            <p style="margin: 5px 0 0; font-size: 1.2rem; font-weight: 600; color: var(--dark-color);">
                                {{ $data['subject']->sum('credit_hour') ?? 0 }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="stats-card courses">
                        <div class="stats-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <h5 style="margin: 0; color: #6c757d; font-size: 0.9rem;">Total Courses</h5>
                            <p style="margin: 5px 0 0; font-size: 1.2rem; font-weight: 600; color: var(--dark-color);">
                                {{ $data['subject']->count() ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if (isset($data['subject']) && $data['subject']->count() > 0)
                <div class="course-grid">
                    @foreach($data['subject'] as $subject)
                    <div class="course-card">
                        <div class="course-header">
                            <span class="course-code">{{ $subject->code }}</span>
                            <h3 class="course-title">{{ $subject->title }}</h3>
                        </div>
                        
                        <div class="course-body">
                            <div class="course-meta">
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $subject->credit_hour }} Credit Hours
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    {{ $subject->sub_type }}
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-chalkboard"></i>
                                    {{ $subject->class_type }}
                                </div>
                            </div>
                            
                            <div class="progress-container">
                                <div class="progress-label">
                                    <span>Course Progress</span>
                                    <span>65%</span> <!-- Dynamic value -->
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 65%"></div>
                                </div>
                            </div>
                            
                            <div class="marks-grid">
                                <div class="marks-card">
                                    <h5>Full Mark (Theory)</h5>
                                    <div class="marks-value">{{ $subject->full_mark_theory }}</div>
                                </div>
                                <div class="marks-card">
                                    <h5>Pass Mark (Theory)</h5>
                                    <div class="marks-value">{{ $subject->pass_mark_theory }}</div>
                                </div>
                                <div class="marks-card">
                                    <h5>Full Mark (Practical)</h5>
                                    <div class="marks-value">{{ $subject->full_mark_practical }}</div>
                                </div>
                                <div class="marks-card">
                                    <h5>Pass Mark (Practical)</h5>
                                    <div class="marks-value">{{ $subject->pass_mark_practical }}</div>
                                </div>
                            </div>
                            
                            <div class="instructor-card">
                                <div class="instructor-avatar">
                                    {{ substr(ViewHelper::getStaffNameById($subject->staff_id), 0, 1) }}
                                </div>
                                <div class="instructor-info">
                                    <h5>{{ ViewHelper::getStaffNameById($subject->staff_id) }}</h5>
                                    <p>Course Instructor</p>
                                </div>
                            </div>
                            
                            <div class="action-buttons">
                                <a class="btn-outline" href="{{ route('user-student.routine'). '?subject_id=' . $subject->id }}">
                                    <i class="fas fa-calendar-alt"></i> Schedule
                                </a>
                                <button class="btn-outline">
                                    <i class="fas fa-file-alt"></i> Syllabus
                                </button>
                                <button class="btn-primary">
                                    <i class="fas fa-door-open"></i> Enter Course
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="no-courses">
                    <i class="fas fa-book-open"></i>
                    <h4>No Courses Found</h4>
                    <p>You don't have any courses registered for this semester yet.</p>
                    <button class="btn-refresh">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            @endif
        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->
@endsection

@section('js')
    <script>
        // Add interactive elements here
        $(document).ready(function() {
            // Add hover effects
            $('.course-card').hover(
                function() {
                    $(this).css('box-shadow', '0 15px 30px rgba(0,0,0,0.1)');
                },
                function() {
                    $(this).css('box-shadow', '0 4px 24px rgba(0,0,0,0.06)');
                }
            );
            
            // Add click animation
            $('.btn-primary').click(function() {
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', 'scale(1)');
                }, 150);
            });
        });
    </script>
@endsection