@extends('user-student.layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<style>
    .assignment-edit-page {
        background-color: #f5f7fb;
        min-height: 100vh;
    }
    
    .page-header-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .submission-status {
        background: #e8f5e9;
        border-left: 4px solid #4caf50;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }
    
    .status-icon {
        font-size: 1.5rem;
        color: #2e7d32;
        margin-right: 15px;
    }
    
    .status-text {
        font-size: 0.95rem;
    }
    
    .status-label {
        font-weight: 600;
        color: #1b5e20;
    }
    
    .question-preview {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
        margin-bottom: 30px;
    }
</style>
@endsection

@section('content')
<div class="assignment-edit-page">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="page-header-content">
                <h1 style="margin: 0; color: #2c3e50;">
                    Edit Submission
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        Update your assignment submission
                    </small>
                </h1>
                
                <div class="submission-status">
                    @if($data['row']->approve_status == 1)
                        <i class="fas fa-check-circle status-icon"></i>
                        <div class="status-text">
                            Status: <span class="status-label">Approved</span> - You may not be able to edit after approval
                        </div>
                    @elseif($data['row']->approve_status == 2)
                        <i class="fas fa-times-circle status-icon" style="color: #c62828;"></i>
                        <div class="status-text">
                            Status: <span class="status-label" style="color: #c62828;">Rejected</span> - Please revise and resubmit
                        </div>
                    @else
                        <i class="fas fa-hourglass-half status-icon" style="color: #fb8c00;"></i>
                        <div class="status-text">
                            Status: <span class="status-label" style="color: #fb8c00;">Pending Review</span> - You can update until reviewed
                        </div>
                    @endif
                </div>
            </div>

            @include($view_path.'.assignment.answer.includes.preview-question')

            <div class="form-container">
                {!! Form::model($data['row'], ['route' => ['user-student.assignment.answer.update', $data['row']->id], 
                    'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                {!! Form::hidden('id', $data['row']->id) !!}
                {!! Form::hidden('assignments_id', $data['assignment']->id) !!}
                
                @include('user-student.assignment.answer.includes.form')
                
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@include('includes.scripts.summarnote')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        
        // Disable form if approved
        @if($data['row']->approve_status == 1)
            $('form#validation-form :input').prop('disabled', true);
            $('.btn-submit').hide();
            $('.btn-reset').hide();
            toastr.info("This submission has been approved and can no longer be edited", "Information");
        @endif
    });
</script>
@endsection