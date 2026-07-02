@extends('layouts.master')

@section('css')
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --active: #10b981;
        --inactive: #f59e0b;
        --danger: #ef4444;
        --gray-600: #4b5563;
        --gray-500: #6b7280;
        --gray-400: #9ca3af;
        --gray-100: #f3f4f6;
        --border: #e5e7eb;
        --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
    }

    .settings-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        background-color: white;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .header-title small {
        font-size: 1rem;
        color: var(--gray-500);
        font-weight: 400;
    }

    /* Tab Styles */
    .settings-tabs {
        border-bottom: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }

    .settings-tabs .nav-tabs {
        border-bottom: none;
    }

    .settings-tabs .nav-tabs > li {
        margin-bottom: -1px;
    }

    .settings-tabs .nav-tabs > li > a {
        border: none;
        border-radius: 6px 6px 0 0;
        padding: 0.75rem 1.5rem;
        color: var(--gray-600);
        font-weight: 500;
        transition: all 0.2s;
        margin-right: 0.25rem;
    }

    .settings-tabs .nav-tabs > li > a:hover {
        background-color: var(--gray-100);
        border-color: transparent;
        color: var(--primary);
    }

    .settings-tabs .nav-tabs > li.active > a,
    .settings-tabs .nav-tabs > li.active > a:hover,
    .settings-tabs .nav-tabs > li.active > a:focus {
        background-color: white;
        border: 1px solid var(--border);
        border-bottom-color: transparent;
        color: var(--primary);
        font-weight: 600;
    }

    .settings-tabs .tab-content {
        padding: 1.5rem 0;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.2s;
        background-color: white;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        outline: none;
    }

    .form-control[disabled] {
        background-color: var(--gray-100);
        cursor: not-allowed;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
        gap: 1rem;
    }

    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-default {
        background-color: white;
        color: var(--gray-600);
        border: 1px solid var(--border);
    }

    .btn-default:hover {
        background-color: var(--gray-100);
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
    }

    /* Switch Styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e9ecef;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: var(--primary);
    }

    input:checked + .slider:before {
        transform: translateX(24px);
    }

    /* File Upload Styles */
    .file-upload {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .file-upload-preview {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        border: 1px dashed var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .file-upload-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .file-upload-control {
        flex-grow: 1;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .settings-container {
            padding: 1.5rem 1rem;
        }
        
        .settings-tabs .nav-tabs > li {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .settings-tabs .nav-tabs > li > a {
            border-radius: 6px;
            display: block;
        }
        
        .settings-tabs .nav-tabs > li.active > a {
            border: 1px solid var(--border);
        }
        
        .form-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="settings-container">
    <div class="header-section">
        <h1 class="header-title">
            <i class="fa fa-cog"></i> General Settings
            <small>Configure your system preferences and branding</small>
        </h1>
        
        @include('setting.includes.buttons')
    </div>

    @include('includes.validation_error_messages')
    @include('includes.flash_messages')

    {{-- {!! Form::open(['route' => isset($data['row']) ? [$base_route.'.update', encrypt($data['row']->id)] : $base_route.'.store', 
                   'method' => isset($data['row']) ? 'PUT' : 'POST', 
                   'class' => 'form-horizontal',
                   'id' => 'validation-form', 
                   "enctype" => "multipart/form-data"]) !!} --}}
     {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

    <div class="settings-tabs">
        <ul class="nav nav-tabs" id="settingsTab">
            <li class="active">
                <a data-toggle="tab" href="#general">
                    <i class="fa fa-list-alt"></i> General & Branding
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#module">
                    <i class="fa fa-th-large"></i> Modules & Layout
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#tracking">
                    <i class="fa fa-bar-chart"></i> Analytics
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#print">
                    <i class="fa fa-print"></i> Print Settings
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#social">
                    <i class="fa fa-share-alt"></i> Social Media
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="general" class="tab-pane fade in active">
                @include('setting.general.includes.forms.general')
                @include('setting.general.includes.forms.timezone')
            </div>
            <div id="module" class="tab-pane fade">
                @include('setting.general.includes.forms.layout')
            </div>
            <div id="tracking" class="tab-pane fade">
                @include('setting.general.includes.forms.tracking')
            </div>
            <div id="print" class="tab-pane fade">
                @include('setting.general.includes.forms.print')
            </div>
            <div id="social" class="tab-pane fade">
                @include('setting.general.includes.forms.social')
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-default" type="reset">
            <i class="fa fa-undo"></i> Reset
        </button>
        <button class="btn btn-primary" type="submit" id="save-btn">
            <i class="fa fa-save"></i> Save Settings
        </button>
    </div>

    {!! Form::close() !!}
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize file upload previews
        document.querySelectorAll('.file-upload-input').forEach(input => {
            input.addEventListener('change', function(e) {
                const preview = this.closest('.file-upload').querySelector('.file-upload-preview');
                const file = this.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        // Initialize all tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Show active tab on page load based on URL hash
        if (window.location.hash) {
            const hash = window.location.hash;
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }

        // Change URL hash when tab changes
        $('.nav-tabs a').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.hash;
        });
    });
</script>
@endsection