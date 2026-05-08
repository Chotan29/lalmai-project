<div class="assignment-form-container">
    <div class="form-section">
        <h3 class="section-title">
            <i class="fas fa-edit"></i>
            Your Answer
        </h3>
        
        <div class="form-group">
            <label for="answer_text" class="form-label">Detailed Answer</label>
            <div class="rich-text-editor">
                {!! Form::textarea('answer_text', null, [
                    "class" => "form-control border-form", 
                    "id" => "summernote", 
                    "rows" => "8",
                    "placeholder" => "Write your detailed answer here..."
                ]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'answer_text'])
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="section-title">
            <i class="fas fa-paperclip"></i>
            Attachments
        </h3>
        
        <div class="form-group">
            <label for="attach_file" class="form-label">Upload File</label>
            <div class="file-upload-wrapper">
                <div class="file-upload-input">
                    {!! Form::file('attach_file', [
                        "class" => "form-control border-form",
                        "id" => "fileUpload"
                    ]) !!}
                    <label for="fileUpload" class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span class="file-upload-text">Choose a file or drag it here</span>
                        <span class="file-upload-hint">PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</span>
                    </label>
                </div>
                @include('includes.form_fields_validation_message', ['name' => 'attach_file'])
            </div>
        </div>

        @if (isset($data['row']) && $data['row']->file)
        <div class="form-group existing-file">
            <label class="form-label">Current Attachment</label>
            <div class="file-preview">
                <a href="{{ asset('assignments'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR.$data['row']->file) }}" 
                   target="_blank" class="file-link">
                    <i class="fas fa-file-alt"></i>
                    {{ $data['row']->file }}
                    <i class="fas fa-download download-icon"></i>
                </a>
                <div class="file-actions">
                    <span class="file-info">Uploaded on {{ \Carbon\Carbon::parse($data['row']->updated_at)->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="form-actions">
        <button type="reset" class="btn btn-reset">
            <i class="fas fa-undo"></i> Reset
        </button>
        <button type="submit" class="btn btn-submit">
            <i class="fas fa-paper-plane"></i> 
            {{ isset($data['row']) ? 'Update Submission' : 'Submit Assignment' }}
        </button>
    </div>
</div>

<style>
    .assignment-form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 12px;
        color: #4361ee;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #495057;
    }
    
    .rich-text-editor {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        overflow: hidden;
    }
    
    .file-upload-wrapper {
        margin-top: 10px;
    }
    
    .file-upload-input {
        position: relative;
    }
    
    #fileUpload {
        opacity: 0;
        position: absolute;
        z-index: -1;
    }
    
    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .file-upload-label:hover {
        border-color: #4361ee;
        background: rgba(67, 97, 238, 0.05);
    }
    
    .file-upload-text {
        font-size: 1rem;
        color: #495057;
        margin: 10px 0 5px;
    }
    
    .file-upload-hint {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .existing-file {
        margin-top: 20px;
    }
    
    .file-preview {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }
    
    .file-link {
        display: flex;
        align-items: center;
        color: #4361ee;
        text-decoration: none;
        font-weight: 500;
    }
    
    .file-link:hover {
        color: #3f37c9;
    }
    
    .file-link i:first-child {
        margin-right: 10px;
        font-size: 1.2rem;
    }
    
    .download-icon {
        margin-left: auto;
    }
    
    .file-actions {
        margin-top: 8px;
        display: flex;
        justify-content: space-between;
    }
    
    .file-info {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }
    
    .btn-reset {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #6c757d;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    
    .btn-reset:hover {
        background: #e9ecef;
    }
    
    .btn-submit {
        background: #4361ee;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-submit:hover {
        background: #3f37c9;
        transform: translateY(-2px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File upload preview
        const fileUpload = document.getElementById('fileUpload');
        const fileUploadLabel = document.querySelector('.file-upload-label');
        
        if (fileUpload) {
            fileUpload.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    fileUploadLabel.innerHTML = `
                        <i class="fas fa-file-upload"></i>
                        <span class="file-upload-text">${fileName}</span>
                        <span class="file-upload-hint">Click to change file</span>
                    `;
                }
            });
        }
        
        // Form validation
        const form = document.querySelector('#validation-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const answerText = document.querySelector('textarea[name="answer_text"]').value.trim();
                if (!answerText) {
                    e.preventDefault();
                    toastr.info("Please enter your answer before submitting", "Information");
                    document.querySelector('textarea[name="answer_text"]').focus();
                }
            });
        }
    });
</script>