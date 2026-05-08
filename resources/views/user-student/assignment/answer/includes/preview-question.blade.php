<div class="assignment-question-preview">
    <div class="preview-header">
        <h2 class="preview-title">
            <i class="fas fa-question-circle"></i>
            Assignment Question Preview
        </h2>
        <div class="preview-subtitle">Review the assignment details before submitting your work</div>
    </div>

    <div class="preview-metadata-grid">
        <div class="metadata-card">
            <div class="metadata-label">
                <i class="fas fa-calendar-alt"></i>
                Academic Year
            </div>
            <div class="metadata-value">
                {{ ViewHelper::getYearById($data['assignment']->years_id) }}
            </div>
        </div>

        <div class="metadata-card">
            <div class="metadata-label">
                <i class="fas fa-layer-group"></i>
                Semester/Section
            </div>
            <div class="metadata-value">
                {!! ViewHelper::getSemesterById($data['assignment']->semesters_id) !!}
            </div>
        </div>

        <div class="metadata-card">
            <div class="metadata-label">
                <i class="fas fa-book"></i>
                Subject
            </div>
            <div class="metadata-value">
                {{ ViewHelper::getSubjectById($data['assignment']->subjects_id) }}
            </div>
        </div>

        <div class="metadata-card">
            <div class="metadata-label">
                <i class="fas fa-calendar-check"></i>
                Publish Date
            </div>
            <div class="metadata-value">
                {{ $data['assignment']->publish_date }}
            </div>
        </div>

        <div class="metadata-card">
            <div class="metadata-label">
                <i class="fas fa-calendar-times"></i>
                Due Date
            </div>
            <div class="metadata-value">
                {{ $data['assignment']->end_date }}
            </div>
        </div>

        <div class="metadata-card">
            <div class="metadata-label">
                <i class="fas fa-paperclip"></i>
                Attachments
            </div>
            <div class="metadata-value">
                @if($data['assignment']->file)
                <a href="{{ asset('assignments'.DIRECTORY_SEPARATOR.'questions'.DIRECTORY_SEPARATOR.$data['assignment']->file) }}" 
                   target="_blank" class="file-download">
                    <i class="fas fa-file-download"></i> Download File
                </a>
                @else
                No attachments
                @endif
            </div>
        </div>

        <div class="metadata-card">
            <div class="metadata-label">
                <i class="fas fa-info-circle"></i>
                Status
            </div>
            <div class="metadata-value">
                <span class="status-badge status-{{ $data['assignment']->status == 'active' ? 'active' : 'inactive' }}">
                    {{ $data['assignment']->status == 'active' ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <div class="question-content">
        <div class="question-title-section">
            <h3 class="question-title">
                <i class="fas fa-question"></i>
                {{ $data['assignment']->title }}
            </h3>
        </div>

        <div class="question-detail">
            {!! $data['assignment']->description !!}
        </div>
    </div>
</div>

<style>
    .assignment-question-preview {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 30px;
        margin-bottom: 30px;
    }

    .preview-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .preview-title {
        font-size: 1.5rem;
        color: #2c3e50;
        margin: 0 0 5px 0;
        display: flex;
        align-items: center;
    }

    .preview-title i {
        margin-right: 12px;
        color: #4361ee;
    }

    .preview-subtitle {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .preview-metadata-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .metadata-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        transition: all 0.2s ease;
    }

    .metadata-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .metadata-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .metadata-label i {
        margin-right: 8px;
        color: #4361ee;
    }

    .metadata-value {
        font-size: 1rem;
        font-weight: 500;
        color: #2c3e50;
    }

    .file-download {
        color: #4361ee;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .file-download:hover {
        color: #3f37c9;
    }

    .file-download i {
        margin-right: 8px;
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
        color: #2e7d32;
    }

    .status-inactive {
        background: rgba(248, 113, 113, 0.1);
        color: #c62828;
    }

    .question-content {
        margin-top: 25px;
    }

    .question-title-section {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
    }

    .question-title {
        font-size: 1.3rem;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .question-title i {
        margin-right: 12px;
        color: #4361ee;
    }

    .question-detail {
        line-height: 1.6;
        color: #495057;
    }

    @media (max-width: 768px) {
        .preview-metadata-grid {
            grid-template-columns: 1fr;
        }
        
        .preview-title {
            font-size: 1.3rem;
        }
    }
</style>