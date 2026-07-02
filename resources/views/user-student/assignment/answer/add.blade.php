@extends('user-student.layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<style>
    .assignment-submit-page {
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
    
    .assignment-deadline {
        background: #fff8e1;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }
    
    .deadline-icon {
        font-size: 1.5rem;
        color: #ff9800;
        margin-right: 15px;
    }
    
    .deadline-text {
        font-size: 0.95rem;
    }
    
    .deadline-date {
        font-weight: 600;
        color: #e65100;
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
<div class="assignment-submit-page">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="page-header-content">
                <h1 style="margin: 0; color: #2c3e50;">
                    Submit Assignment
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        Complete and submit your assignment
                    </small>
                </h1>
                
                @if($data['assignment']->end_date >= date('Y-m-d H:i:s'))
                <div class="assignment-deadline">
                    <i class="fas fa-clock deadline-icon"></i>
                    <div class="deadline-text">
                        Submission deadline: <span class="deadline-date">{{ $data['assignment']->end_date }}</span>
                    </div>
                </div>
                @else
                <div class="assignment-deadline" style="background: #ffebee; border-left-color: #f44336;">
                    <i class="fas fa-exclamation-triangle deadline-icon"></i>
                    <div class="deadline-text">
                        The submission deadline has passed on <span class="deadline-date">{{ $data['assignment']->end_date }}</span>
                    </div>
                </div>
                @endif
            </div>

            @include($view_path.'.assignment.answer.includes.preview-question')

            <div class="form-container">
                {!! Form::open(['route' => 'user-student.assignment.answer.store', 'method' => 'POST', 
                    'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
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
    });
</script>
@endsection