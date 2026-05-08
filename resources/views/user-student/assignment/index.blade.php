@extends('user-student.layouts.master')

@section('css')
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --danger-color: #f87171;
        --dark-color: #1a1a2e;
        --light-color: #f8f9fa;
    }
    
    .assignment-dashboard {
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
    
    .filter-container {
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
    
    .assignment-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
    }
    
    .assignment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }
    
    .assignment-card {
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .assignment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .assignment-status {
        position: absolute;
        top: 0;
        right: 0;
        padding: 5px 15px;
        border-bottom-left-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
    }
    
    .status-approved {
        background: var(--success-color);
    }
    
    .status-rejected {
        background: var(--danger-color);
    }
    
    .status-pending {
        background: var(--warning-color);
    }
    
    .status-not-submitted {
        background: #6c757d;
    }
    
    .status-overdue {
        background: var(--dark-color);
    }
    
    .assignment-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0 0 10px 0;
        color: var(--dark-color);
    }
    
    .assignment-subject {
        display: inline-block;
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-bottom: 15px;
    }
    
    .assignment-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
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
        min-width: 20px;
        text-align: center;
    }
    
    .date-range {
        display: flex;
        justify-content: space-between;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 0.85rem;
    }
    
    .date-item {
        display: flex;
        align-items: center;
    }
    
    .date-item i {
        margin-right: 5px;
    }
    
    .publish-date {
        color: var(--success-color);
    }
    
    .end-date {
        color: var(--danger-color);
    }
    
    .assignment-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
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
        cursor: pointer;
        text-decoration: none;
    }
    
    .action-btn i {
        margin-right: 8px;
    }
    
    .btn-submit {
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(67, 97, 238, 0.3);
    }
    
    .btn-submit:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-edit {
        background: rgba(76, 201, 240, 0.1);
        color: var(--accent-color);
        border: 1px solid rgba(76, 201, 240, 0.3);
    }
    
    .btn-edit:hover {
        background: var(--accent-color);
        color: white;
    }
    
    .btn-view {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
        border: 1px solid rgba(74, 222, 128, 0.3);
    }
    
    .btn-view:hover {
        background: var(--success-color);
        color: white;
    }
    
    .btn-disabled {
        background: #e9ecef;
        color: #6c757d;
        border: 1px solid #dee2e6;
        cursor: not-allowed;
    }
    
    .no-assignments {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        grid-column: 1 / -1;
    }
    
    .no-assignments i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .no-assignments h4 {
        margin-bottom: 10px;
    }
    
    .urgency-badge {
        position: absolute;
        top: 10px;
        left: -25px;
        background: var(--danger-color);
        color: white;
        padding: 3px 25px;
        transform: rotate(-45deg);
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .assignment-grid {
            grid-template-columns: 1fr;
        }
        
        .assignment-actions {
            flex-direction: column;
        }
        
        .action-btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="assignment-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="dashboard-header">
                <h1 style="margin: 0; color: var(--dark-color);">
                    Assignments
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        Manage your coursework and submissions
                    </small>
                </h1>
                
                <div class="filter-container">
                    <button class="filter-btn active">All Assignments</button>
                    <button class="filter-btn">Pending</button>
                    <button class="filter-btn">Submitted</button>
                    <button class="filter-btn">Graded</button>
                    <button class="filter-btn">Overdue</button>
                </div>
            </div>

            <div class="assignment-container">
                @if (isset($data['assignment']) && $data['assignment']->count() > 0)
                <div class="assignment-grid">
                    @php($i=1)
                    @foreach($data['assignment'] as $assignment)
                    @php($approveStatus = $assignment->answers()->where('students_id',$data['student']->id)->first())
                    <div class="assignment-card @if($assignment->end_date < date('Y-m-d H:i:s') && !isset($approveStatus)) overdue-card @endif">
                        @if($assignment->end_date < date('Y-m-d H:i:s') && !isset($approveStatus))
                            <div class="urgency-badge">OVERDUE</div>
                        @endif
                        
                        @if(isset($approveStatus))
                            @if($approveStatus->approve_status == 1)
                                <div class="assignment-status status-approved">Approved</div>
                            @elseif($approveStatus->approve_status == 2)
                                <div class="assignment-status status-rejected">Rejected</div>
                            @else
                                <div class="assignment-status status-pending">Pending Review</div>
                            @endif
                        @else
                            <div class="assignment-status status-not-submitted">Not Submitted</div>
                        @endif
                        
                        <h3 class="assignment-title">
                            <a href="{{ route('assignment.view', ['id' => encrypt($assignment->id)]) }}" style="color: inherit; text-decoration: none;">
                                {{ $assignment->title }}
                            </a>
                        </h3>
                        
                        <span class="assignment-subject">
                            {{ isset($assignment->subjects_id) ? ViewHelper::getSubjectById($assignment->subjects_id) : 'No Subject' }}
                        </span>
                        
                        <div class="assignment-meta">
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                {{ $assignment->created_by_name }}
                            </div>
                        </div>
                        
                        <div class="date-range">
                            <span class="date-item publish-date">
                                <i class="far fa-calendar-check"></i>
                                {{ $assignment->publish_date }}
                            </span>
                            <span class="date-item end-date">
                                <i class="far fa-calendar-times"></i>
                                {{ $assignment->end_date }}
                            </span>
                        </div>
                        
                        <div class="assignment-actions">
                            @if($assignment->end_date >= date('Y-m-d H:i:s'))
                                @if(!isset($approveStatus))
                                    <a href="{{ route('user-student.assignment.answer.add', ['id' => $assignment->id]) }}" 
                                       class="action-btn btn-submit">
                                        <i class="fas fa-paper-plane"></i> Submit
                                    </a>
                                @endif
                            @else
                                <span class="action-btn btn-disabled">
                                    <i class="fas fa-clock"></i> Time Over
                                </span>
                            @endif
                            
                            @if(isset($approveStatus))
                                @if($assignment->end_date >= date('Y-m-d H:i:s'))
                                    @if($approveStatus->approve_status != 1)
                                        <a href="{{ route('user-student.assignment.answer.edit', ['id' => $assignment->id]) }}" 
                                           class="action-btn btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                @endif
                                
                                <a href="{{ route('user-student.assignment.answer.view', ['id' => $assignment->id, 'answer'=> $approveStatus->id]) }}" 
                                   class="action-btn btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @endif
                        </div>
                    </div>
                    @php($i++)
                    @endforeach
                </div>
                @else
                <div class="no-assignments">
                    <i class="fas fa-clipboard-list"></i>
                    <h4>No Assignments Found</h4>
                    <p>There are currently no assignments to display.</p>
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
            // Here you would add filtering logic based on assignment status
        });
    });
    
    // Add hover effect to assignment cards
    document.querySelectorAll('.assignment-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.1)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });
    
    // Highlight overdue assignments
    document.querySelectorAll('.overdue-card').forEach(card => {
        card.style.borderLeft = '4px solid var(--danger-color)';
    });
</script>
@endsection