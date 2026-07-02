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
    
    .exam-dashboard {
        background-color: #f5f7fb;
        min-height: 100vh;
    }
    
    .dashboard-header {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .exam-filter {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        background: white;
        border: 1px solid #dee2e6;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .filter-btn.active, .filter-btn:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .exam-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
    }
    
    .exam-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }
    
    .exam-card {
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .exam-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .exam-status {
        position: absolute;
        top: 0;
        right: 0;
        padding: 5px 15px;
        border-bottom-left-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-upcoming {
        background: var(--warning-color);
        color: white;
    }
    
    .status-ongoing {
        background: var(--success-color);
        color: white;
    }
    
    .status-completed {
        background: var(--accent-color);
        color: white;
    }
    
    .exam-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0 0 10px 0;
        color: var(--dark-color);
    }
    
    .exam-meta {
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
    
    .exam-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }
    
    .action-btn {
        flex: 1;
        min-width: 120px;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn i {
        margin-right: 8px;
    }
    
    .btn-schedule {
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(67, 97, 238, 0.3);
    }
    
    .btn-schedule:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-admit {
        background: rgba(76, 201, 240, 0.1);
        color: var(--accent-color);
        border: 1px solid rgba(76, 201, 240, 0.3);
    }
    
    .btn-admit:hover {
        background: var(--accent-color);
        color: white;
    }
    
    .btn-grade {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
        border: 1px solid rgba(74, 222, 128, 0.3);
    }
    
    .btn-grade:hover {
        background: var(--success-color);
        color: white;
    }
    
    .no-exams {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        grid-column: 1 / -1;
    }
    
    .no-exams i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .no-exams h4 {
        margin-bottom: 10px;
    }
    
    .exam-date {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 10px 20px;
        background: #f8f9fa;
        font-size: 0.85rem;
        color: #6c757d;
        display: flex;
        justify-content: space-between;
    }
    
    @media (max-width: 768px) {
        .exam-grid {
            grid-template-columns: 1fr;
        }
        
        .exam-actions {
            flex-direction: column;
        }
        
        .action-btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="exam-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="dashboard-header">
                <h1 style="margin: 0; color: var(--dark-color);">
                    Exam Center
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        View your exam schedules and results
                    </small>
                </h1>
                
                <div class="exam-filter">
                    <button class="filter-btn active">All Exams</button>
                    <button class="filter-btn">Upcoming</button>
                    <button class="filter-btn">Ongoing</button>
                    <button class="filter-btn">Completed</button>
                </div>
            </div>

            <div class="exam-container">
                @if (isset($data['schedule_exams']) && $data['schedule_exams']->count() > 0)
                <div class="exam-grid">
                    @php($i=1)
                    @foreach($data['schedule_exams'] as $exam)
                    <div class="exam-card">
                        <span class="exam-status status-{{ $exam->status ?? 'upcoming' }}">
                            {{ ucfirst($exam->status ?? 'upcoming') }}
                        </span>
                        
                        <h3 class="exam-title">{{ ViewHelper::getExamById($exam->exams_id) }}</h3>
                        
                        <div class="exam-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                {{ ViewHelper::getYearById($exam->years_id) }}
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-week"></i>
                                {{ ViewHelper::getMonthById($exam->months_id) }}
                            </div>
                        </div>
                        
                        <div class="exam-actions">
                            <a href="{{ route('user-student.exam-schedule', ['year' => $exam->years_id, 'month' => $exam->months_id, 'exam' => $exam->exams_id,'faculty' => $exam->faculty_id, 'semester' => $exam->semesters_id]) }}" 
                               class="action-btn btn-schedule" target="_blank">
                                <i class="fas fa-list-alt"></i> Schedule
                            </a>
                            <a href="{{ route('user-student.exam-admit-card', ['year' => $exam->years_id, 'month' => $exam->months_id, 'exam' => $exam->exams_id,'faculty' => $exam->faculty_id, 'semester' => $exam->semesters_id]) }}" 
                               class="action-btn btn-admit" target="_blank">
                                <i class="fas fa-id-card"></i> Admit Card
                            </a>
                            <a href="{{ route('user-student.exam-score', ['year' => $exam->years_id, 'month' => $exam->months_id, 'exam' => $exam->exams_id,'faculty' => $exam->faculty_id, 'semester' => $exam->semesters_id]) }}" 
                               class="action-btn btn-grade" target="_blank">
                                <i class="fas fa-chart-line"></i> Grades
                            </a>
                        </div>
                        
                        <div class="exam-date">
                            <span><i class="far fa-calendar-check"></i> Start: {{ $exam->start_date ?? 'TBD' }}</span>
                            <span><i class="far fa-calendar-times"></i> End: {{ $exam->end_date ?? 'TBD' }}</span>
                        </div>
                    </div>
                    @php($i++)
                    @endforeach
                </div>
                @else
                <div class="no-exams">
                    <i class="fas fa-clipboard-list"></i>
                    <h4>No Exams Scheduled</h4>
                    <p>There are currently no exams to display.</p>
                </div>
                @endif
            </div>
        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->
@endsection

@section('js')
<script>
    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // Here you would add filtering logic based on exam status
        });
    });
    
    // Add hover effect to exam cards
    document.querySelectorAll('.exam-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.1)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });
</script>
@endsection