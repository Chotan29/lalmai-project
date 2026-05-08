<div class="application-form-container">
    <div class="form-header">
        <h3><i class="fas fa-file-alt"></i> {{ $panel }}</h3>
    </div>
    
    <div class="form-body">
        <div class="form-row">
            <div class="form-group">
                <label>Application Type</label>
                {!! Form::select('application_type_id', $data['applicationType'], null, [
                    'class' => 'form-select',
                    'onChange' => 'checkApplicationType()'
                ]) !!}
            </div>
        </div>

        <div id="leave_application" class="form-row date-row">
            <div class="form-group">
                <label>Start Date</label>
                {!! Form::text('date', null, [
                    "class" => "form-control date-picker",
                    "placeholder" => "Select date"
                ]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'date'])
            </div>
            
            <div class="form-group">
                <label>End Date</label>
                {!! Form::text('end_date', null, [
                    "class" => "form-control date-picker",
                    "placeholder" => "Select end date"
                ]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'end_date'])
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Subject</label>
                {!! Form::text('subject', null, [
                    "class" => "form-control",
                    "placeholder" => "Enter subject"
                ]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'subject'])
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Message/Reason</label>
                {!! Form::textarea('message', null, [
                    "class" => "form-control",
                    "rows" => "5",
                    "placeholder" => "Enter your message or reason..."
                ]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'message'])
            </div>
        </div>

        <div class="form-row">
            <div class="form-group file-upload">
                <label>Attachment</label>
                <div class="upload-container">
                    <label class="upload-btn">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Choose file</span>
                        {!! Form::file('attach_file', [
                            "id" => "file-upload",
                            "class" => "d-none"
                        ]) !!}
                    </label>
                    <div class="file-name" id="file-name">No file chosen</div>
                </div>
                @include('includes.form_fields_validation_message', ['name' => 'attach_file'])
            </div>
        </div>

        @if (isset($data['row']) && $data['row']->file)
            <div class="form-row">
                <div class="form-group">
                    <label>Current Attachment</label>
                    <div class="current-file">
                        <a href="{{ asset('assignments'.DIRECTORY_SEPARATOR.'questions'.DIRECTORY_SEPARATOR.$data['row']->file) }}" target="_blank">
                            <i class="fas fa-paperclip"></i> 
                            {{ basename($data['row']->file) }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .application-form-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .form-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f0f0f0;
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
    }
    
    .form-header h3 {
        margin: 0;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .form-body {
        padding: 1.5rem;
    }
    
    .form-row {
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        width: 100%;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #4a5568;
    }
    
    .form-control, .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 3px rgba(58, 123, 213, 0.1);
    }
    
    .date-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .file-upload .upload-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .upload-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: #f8fafc;
        border: 1px dashed #cbd5e0;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .upload-btn:hover {
        background: #f0f5ff;
        border-color: #3a7bd5;
    }
    
    .file-name {
        color: #718096;
        font-size: 0.9rem;
    }
    
    .current-file a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #3a7bd5;
        text-decoration: none;
    }
    
    .current-file a:hover {
        text-decoration: underline;
    }
    
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }
</style>

<script>
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        document.getElementById('file-name').textContent = fileName;
    });
</script>