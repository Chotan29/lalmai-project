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
    
    .assignment-detail-view {
        background-color: #f5f7fb;
        min-height: 100vh;
    }
    
    .detail-header {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .detail-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .section-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 12px;
        color: var(--primary-color);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .info-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }
    
    .info-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
    }
    
    .info-label i {
        margin-right: 8px;
    }
    
    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: var(--dark-color);
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-active {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
    }
    
    .status-inactive {
        background: rgba(248, 113, 113, 0.1);
        color: var(--danger-color);
    }
    
    .status-approved {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
    }
    
    .status-rejected {
        background: rgba(248, 113, 113, 0.1);
        color: var(--danger-color);
    }
    
    .status-pending {
        background: rgba(251, 191, 36, 0.1);
        color: var(--warning-color);
    }
    
    .file-download {
        display: inline-flex;
        align-items: center;
        color: var(--primary-color);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .file-download:hover {
        color: var(--secondary-color);
    }
    
    .file-download i {
        margin-left: 8px;
    }
    
    .content-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
    }
    
    .divider {
        height: 1px;
        background: #e9ecef;
        margin: 30px 0;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn-edit {
        background: var(--primary-color);
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    
    .btn-edit:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }
    
    .btn-edit i {
        margin-right: 8px;
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="assignment-detail-view">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="detail-header">
                <h1 style="margin: 0; color: var(--dark-color);">
                    Assignment Details
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        View and manage your assignment
                    </small>
                </h1>
            </div>

            <div class="detail-section">
                <h2 class="section-title">
                    <i class="fas fa-question-circle"></i>
                    Assignment Question
                </h2>
                
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-calendar-alt"></i>
                            Academic Year
                        </div>
                        <div class="info-value">
                            {{ ViewHelper::getYearById($data['assignment']->years_id) }}
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-layer-group"></i>
                            Semester/Section
                        </div>
                        <div class="info-value">
                            {!! ViewHelper::getSemesterById($data['assignment']->semesters_id) !!}
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-book"></i>
                            Subject
                        </div>
                        <div class="info-value">
                            {{ ViewHelper::getSubjectById($data['assignment']->subjects_id) }}
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-info-circle"></i>
                            Status
                        </div>
                        <div class="info-value">
                            <span class="status-badge status-{{ $data['assignment']->status == 'active' ? 'active' : 'inactive' }}">
                                {{ $data['assignment']->status == 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-calendar-check"></i>
                            Publish Date
                        </div>
                        <div class="info-value">
                            {{ $data['assignment']->publish_date }}
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-calendar-times"></i>
                            Due Date
                        </div>
                        <div class="info-value">
                            {{ $data['assignment']->end_date }}
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-paperclip"></i>
                            Attachments
                        </div>
                        <div class="info-value">
                            @if($data['assignment']->file)
                            <a href="{{ asset('assignments'.DIRECTORY_SEPARATOR.'questions'.DIRECTORY_SEPARATOR.$data['assignment']->file) }}" 
                               class="file-download" target="_blank">
                                Download File
                                <i class="fas fa-download"></i>
                            </a>
                            @else
                            No attachments
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="content-box">
                    <h3 style="margin-top: 0; color: var(--dark-color);">{{ $data['assignment']->title }}</h3>
                    {!! $data['assignment']->description !!}
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="detail-section">
                <h2 class="section-title">
                    <i class="fas fa-reply"></i>
                    Your Submission
                </h2>
                
                @if(isset($data['answers']))
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-info-circle"></i>
                            Submission Status
                        </div>
                        <div class="info-value">
                            @if($data['answers']->approve_status == 1)
                                <span class="status-badge status-approved">Approved</span>
                            @elseif($data['answers']->approve_status == 2)
                                <span class="status-badge status-rejected">Rejected</span>
                            @else
                                <span class="status-badge status-pending">Pending Review</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-paperclip"></i>
                            Attachments
                        </div>
                        <div class="info-value">
                            @if($data['answers']->file)
                            <a href="{{ asset('assignments'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR.$data['answers']->file) }}" 
                               class="file-download" target="_blank">
                                Download File
                                <i class="fas fa-download"></i>
                            </a>
                            @else
                            No attachments
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Submitted By
                        </div>
                        <div class="info-value">
                            @if($data['answers']->created_by)
                                {{ ViewHelper::getUserNameId($data['answers']->created_by) }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-user-edit"></i>
                            Last Updated By
                        </div>
                        <div class="info-value">
                            @if($data['answers']->last_updated_by)
                                {{ ViewHelper::getUserNameId($data['answers']->last_updated_by) }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-calendar-plus"></i>
                            Created On
                        </div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($data['answers']->created_at)->format('M d, Y') }}
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-calendar-edit"></i>
                            Updated On
                        </div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($data['answers']->updated_at)->format('M d, Y') }}
                        </div>
                    </div>
                </div>
                
                <div class="content-box">
                    <h3 style="margin-top: 0; color: var(--dark-color);">Your Answer</h3>
                    {!! $data['answers']->answer_text !!}
                </div>
                
                <div class="action-buttons">
                    @if($data['assignment']->end_date >= date('Y-m-d H:i:s'))
                        @if($data['answers']->approve_status != 1)
                            <a href="{{ route('user-student.assignment.answer.edit', ['id' => $data['assignment']->id]) }}" 
                               class="btn-edit">
                                <i class="fas fa-edit"></i> Edit Submission
                            </a>
                        @endif
                    @endif
                </div>
                @else
                <div class="content-box" style="text-align: center; padding: 40px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #dee2e6; margin-bottom: 15px;"></i>
                    <h3 style="color: #6c757d;">No Submission Found</h3>
                    <p style="color: #adb5bd;">You haven't submitted this assignment yet.</p>
                    
                    @if($data['assignment']->end_date >= date('Y-m-d H:i:s'))
                    <a href="{{ route('user-student.assignment.answer.add', ['id' => $data['assignment']->id]) }}" 
                       class="btn-edit" style="margin-top: 20px;">
                        <i class="fas fa-paper-plane"></i> Submit Assignment
                    </a>
                    @else
                    <p style="color: var(--danger-color); margin-top: 20px;">
                        <i class="fas fa-clock"></i> The submission deadline has passed
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->
@endsection

@section('js')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    
    <script>
        // Add any interactive elements here
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to download links
            document.querySelectorAll('.file-download').forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(3px)';
                });
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'none';
                });
            });
        });
    </script>
@endsection