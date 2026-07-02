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
    
    .download-center {
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
    
    .filter-options {
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
    
    .download-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
    }
    
    .download-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .download-card {
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .download-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .file-type {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
    }
    
    .download-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 10px 0;
        color: var(--dark-color);
        padding-right: 30px;
    }
    
    .download-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .meta-item i {
        margin-right: 8px;
        color: var(--primary-color);
        min-width: 20px;
    }
    
    .download-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 10px;
        background: var(--primary-color);
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    
    .download-btn:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }
    
    .download-btn i {
        margin-right: 8px;
    }
    
    .no-downloads {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        grid-column: 1 / -1;
    }
    
    .no-downloads i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .no-downloads h4 {
        margin-bottom: 10px;
    }
    
    .file-icon {
        font-size: 1.5rem;
        margin-right: 15px;
        color: var(--primary-color);
    }
    
    @media (max-width: 768px) {
        .download-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-options {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="download-center">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="dashboard-header">
                <h1 style="margin: 0; color: var(--dark-color);">
                    Download Center
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        Access your course materials and resources
                    </small>
                </h1>
                
                <div class="filter-options">
                    <button class="filter-btn active">All Files</button>
                    <button class="filter-btn">Lecture Notes</button>
                    <button class="filter-btn">Assignments</button>
                    <button class="filter-btn">Study Guides</button>
                    <button class="filter-btn">Recent</button>
                </div>
            </div>

            <div class="download-container">
                @if (isset($data['download']) && $data['download']->count() > 0)
                <div class="download-grid">
                    @php($i=1)
                    @foreach($data['download'] as $download)
                    <div class="download-card">
                        <div class="file-type">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        
                        <h3 class="download-title">
                            <a href="{{ asset('downloads'.DIRECTORY_SEPARATOR.$download->file) }}" target="_blank" style="color: inherit; text-decoration: none;">
                                {{ $download->title }}
                            </a>
                        </h3>
                        
                        <div class="download-meta">
                            <div class="meta-item">
                                <i class="fas fa-layer-group"></i>
                                {{ isset($download->semesters_id) ? ViewHelper::getSemesterById($download->semesters_id) : 'General' }}
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-book"></i>
                                {{ isset($download->subjects_id) ? ViewHelper::getSubjectById($download->subjects_id) : 'All Subjects' }}
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                {{ $download->created_by_name }}
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                {{ \Carbon\Carbon::parse($download->created_at)->format('M d, Y h:i A') }}
                            </div>
                        </div>
                        
                        <a href="{{ asset('downloads'.DIRECTORY_SEPARATOR.$download->file) }}" target="_blank" class="download-btn">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    </div>
                    @php($i++)
                    @endforeach
                </div>
                @else
                <div class="no-downloads">
                    <i class="fas fa-cloud-download-alt"></i>
                    <h4>No Downloads Available</h4>
                    <p>There are currently no files available for download.</p>
                </div>
                @endif
            </div>
        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->
@endsection

@section('js')
    @include('includes.scripts.dataTable_scripts')
    <script>
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Here you would add filtering logic based on file type
            });
        });
        
        // Add hover effect to download cards
        document.querySelectorAll('.download-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.1)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
        });
        
        // Detect file type and change icon accordingly
        document.querySelectorAll('.file-type i').forEach(icon => {
            const card = icon.closest('.download-card');
            const title = card.querySelector('.download-title a').textContent.toLowerCase();
            
            if (title.includes('.pdf')) {
                icon.classList.remove('fa-file-alt');
                icon.classList.add('fa-file-pdf');
                icon.parentElement.style.color = '#e74c3c';
                icon.parentElement.style.background = 'rgba(231, 76, 60, 0.1)';
            } else if (title.includes('.doc') || title.includes('.docx')) {
                icon.classList.remove('fa-file-alt');
                icon.classList.add('fa-file-word');
                icon.parentElement.style.color = '#2c3e50';
                icon.parentElement.style.background = 'rgba(44, 62, 80, 0.1)';
            } else if (title.includes('.xls') || title.includes('.xlsx')) {
                icon.classList.remove('fa-file-alt');
                icon.classList.add('fa-file-excel');
                icon.parentElement.style.color = '#27ae60';
                icon.parentElement.style.background = 'rgba(39, 174, 96, 0.1)';
            } else if (title.includes('.ppt') || title.includes('.pptx')) {
                icon.classList.remove('fa-file-alt');
                icon.classList.add('fa-file-powerpoint');
                icon.parentElement.style.color = '#e67e22';
                icon.parentElement.style.background = 'rgba(230, 126, 34, 0.1)';
            } else if (title.includes('.zip') || title.includes('.rar')) {
                icon.classList.remove('fa-file-alt');
                icon.classList.add('fa-file-archive');
                icon.parentElement.style.color = '#f39c12';
                icon.parentElement.style.background = 'rgba(243, 156, 18, 0.1)';
            }
        });
    </script>
@endsection