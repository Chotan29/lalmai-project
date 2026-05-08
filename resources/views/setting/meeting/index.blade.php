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

    .meeting-settings-container {
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

    .platform-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .platform-tag {
        display: inline-flex;
        align-items: center;
        background-color: var(--primary);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.2s;
    }

    .platform-tag:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    .meeting-platforms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 1.5rem;
    }

    .platform-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 10px;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .platform-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .platform-card.disabled {
        opacity: 0.85;
        background-color: var(--gray-100);
    }

    .platform-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
    }

    .platform-header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .platform-logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
        border-radius: 6px;
        border: 1px solid var(--border);
        padding: 0.5rem;
        background: white;
    }

    .platform-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .platform-status {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
    }

    .status-active {
        background-color: var(--active);
        color: white;
    }

    .status-inactive {
        background-color: var(--inactive);
        color: #111827;
    }

    .toggle-config {
        background: transparent;
        border: none;
        color: var(--gray-500);
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .toggle-config.active {
        transform: rotate(180deg);
    }

    .config-section {
        padding: 1.5rem;
        display: none;
    }

    .config-section.active {
        display: block;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--gray-600);
        margin-bottom: 0.35rem;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.2s;
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
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }

    .status-actions a {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--primary);
        margin-right: 0.75rem;
        text-decoration: none;
        transition: color 0.2s;
    }

    .status-actions a:hover {
        color: var(--primary-hover);
        text-decoration: underline;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
    }

    .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .integration-note {
        background-color: #f0f5ff;
        border-left: 4px solid var(--primary);
        padding: 1rem;
        margin-bottom: 2rem;
        border-radius: 0 6px 6px 0;
    }

    .integration-note p {
        margin: 0;
        font-size: 0.9rem;
        color: var(--gray-600);
    }

    .integration-note strong {
        color: var(--primary);
    }

    .no-platforms {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: var(--gray-500);
    }

    .no-platforms i {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--gray-400);
    }

    .connection-status {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-connected {
        background: #d4edda;
        color: #155724;
    }

    .status-disconnected {
        background: #f8d7da;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .meeting-settings-container {
            padding: 1.5rem 1rem;
        }
        
        .meeting-platforms-grid {
            grid-template-columns: 1fr;
        }
        
        .platform-header-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="meeting-settings-container">
    <div class="header-section">
        <h1 class="header-title">
            <i class="fa fa-video-camera"></i> Meeting Platform Settings
            <small>Configure your video conferencing solutions</small>
        </h1>
        
        @include('setting.includes.buttons')
    </div>

    @include('includes.flash_messages')

    <div class="integration-note">
        <p>
            <i class="fa fa-info-circle"></i> Need a new meeting platform integrated? 
            Contact <strong>support@yourdomain.com</strong> for assistance.
        </p>
    </div>

    @if (isset($data['meetingSetting']) && $data['meetingSetting']->count() > 0)
        <div class="platform-tags">
            @foreach($data['meetingSetting'] as $meeting)
                <a href="#{{ $meeting->identity }}" class="platform-tag">
                    <i class="fa fa-video"></i> &nbsp;{{ $meeting->identity }}
                </a>
            @endforeach
        </div>

        <div class="meeting-platforms-grid">
            @foreach($data['meetingSetting'] as $meeting)
                @php($config = json_decode($meeting->config, true))
                {!! Form::model($meeting, [
                    'route' => [$base_route.'.update', $meeting->id], 
                    'method' => 'POST', 
                    'enctype' => 'multipart/form-data',
                    'class' => 'platform-card' . ($meeting->status != 'active' ? ' disabled' : '')
                ]) !!}

                <div class="platform-header" onclick="toggleConfig('{{ $meeting->id }}')">
                    <div class="platform-header-content">
                        <a href="{{ $meeting->link }}" target="_blank">
                            <img src="{{ asset('assets/images/meetingicon/' . $meeting->logo . '.png') }}" 
                                 alt="{{ $meeting->identity }}" 
                                 class="platform-logo">
                        </a>
                        <h3 class="platform-name" id="{{ $meeting->identity }}">
                            {{ ucfirst($meeting->identity) }}
                        </h3>
                    </div>
                    <div class="header-actions">
                        <span class="platform-status {{ $meeting->status == 'active' ? 'status-active' : 'status-inactive' }}">
                            {{ ucfirst($meeting->status) }}
                        </span>
                        <button type="button" class="toggle-config" id="toggle-{{ $meeting->id }}">
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </div>
                </div>

                <div class="config-section" id="config-{{ $meeting->id }}">
                    @if($config)
                        @foreach($config as $key => $value)
                            <div class="form-group">
                                <label for="{{ $key }}-{{ $meeting->id }}">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </label>
                                <input type="{{ str_contains(strtolower($key), 'key') ? 'password' : 'text' }}" 
                                       id="{{ $key }}-{{ $meeting->id }}"
                                       name="{{ $key }}" 
                                       value="{{ $value }}" 
                                       class="form-control"
                                       {{ $meeting->status == 'active' ? '' : 'disabled' }}>
                            </div>
                        @endforeach
                    @endif

                    <div class="form-actions">
                        <div class="status-actions">
                            <a href="{{ route($base_route.'.active', ['id' => $meeting->id]) }}">
                                <i class="fa fa-check-circle"></i> Activate
                            </a>
                            <a href="{{ route($base_route.'.in-active', ['id' => $meeting->id]) }}">
                                <i class="fa fa-times-circle"></i> Deactivate
                            </a>
                            <a href="#" class="test-connection" data-platform="{{ $meeting->id }}">
                                <i class="fa fa-plug"></i> Test Connection
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary" {{ $meeting->status != 'active' ? 'disabled' : '' }}>
                            <i class="fa fa-save"></i> Save Configuration
                        </button>
                    </div>
                </div>

                {!! Form::close() !!}
            @endforeach
        </div>
    @else
        <div class="no-platforms">
            <i class="fa fa-video-slash"></i>
            <h3>No Meeting Platforms Configured</h3>
            <p>You haven't set up any meeting platforms yet.</p>
            <button class="btn btn-primary mt-2">
                <i class="fa fa-plus"></i> Add Platform
            </button>
        </div>
    @endif
</div>
@endsection

@section('js')
    @include('includes.scripts.delete_confirm')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for platform tags
            document.querySelectorAll('.platform-tag').forEach(tag => {
                tag.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    document.querySelector(targetId).scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });
            
            // Show/hide password fields
            document.querySelectorAll('input[type="password"]').forEach(input => {
                const parent = input.closest('.form-group');
                const toggle = document.createElement('span');
                toggle.innerHTML = '<i class="fa fa-eye"></i>';
                toggle.style.position = 'absolute';
                toggle.style.right = '10px';
                toggle.style.top = '32px';
                toggle.style.cursor = 'pointer';
                toggle.style.color = '#6b7280';
                toggle.addEventListener('click', function() {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                    } else {
                        input.type = 'password';
                        this.innerHTML = '<i class="fa fa-eye"></i>';
                    }
                });
                parent.style.position = 'relative';
                parent.appendChild(toggle);
            });

            // Test connection functionality
            document.querySelectorAll('.test-connection').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const platformId = this.getAttribute('data-platform');
                    const originalText = this.innerHTML;
                    
                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing...';
                    
                    // Simulate API test (replace with actual API call)
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        
                        // Random result for demo (replace with actual response handling)
                        const isSuccess = Math.random() > 0.3;
                        if (isSuccess) {
                            toastr.success('Connection successful! API is working properly.');
                        } else {
                            toastr.error('Connection failed. Please check your credentials.');
                        }
                    }, 1500);
                });
            });
        });

        function toggleConfig(platformId) {
            const configSection = document.getElementById(`config-${platformId}`);
            const toggleBtn = document.getElementById(`toggle-${platformId}`);
            
            configSection.classList.toggle('active');
            toggleBtn.classList.toggle('active');
            
            // Rotate chevron icon
            const icon = toggleBtn.querySelector('i');
            if (configSection.classList.contains('active')) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    </script>
@endsection