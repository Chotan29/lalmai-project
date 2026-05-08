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

    .payment-settings-container {
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

    .gateway-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .gateway-tag {
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

    .gateway-tag:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    .payment-gateways-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 1.5rem;
    }

    .gateway-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 10px;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .gateway-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .gateway-card.disabled {
        opacity: 0.85;
        background-color: var(--gray-100);
    }

    .gateway-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
    }

    .gateway-header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .gateway-logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
        border-radius: 6px;
        border: 1px solid var(--border);
        padding: 0.5rem;
        background: white;
    }

    .gateway-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .gateway-status {
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

    .no-gateways {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: var(--gray-500);
    }

    .no-gateways i {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--gray-400);
    }

    @media (max-width: 768px) {
        .payment-settings-container {
            padding: 1.5rem 1rem;
        }
        
        .payment-gateways-grid {
            grid-template-columns: 1fr;
        }
        
        .gateway-header-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="payment-settings-container">
    <div class="header-section">
        <h1 class="header-title">
            Payment Gateway Settings
            <small>Configure your payment processing options</small>
        </h1>
        
        @include('setting.includes.buttons')
    </div>

    @include('includes.flash_messages')

    <div class="integration-note">
        <p>
            <i class="fa fa-info-circle"></i> Need a new payment gateway integrated? 
            Contact <strong>support@yourdomain.com</strong> for assistance.
        </p>
    </div>

    @if (isset($data['paymentGateway']) && $data['paymentGateway']->count() > 0)
        <div class="gateway-tags">
            @foreach($data['paymentGateway'] as $gateway)
                <a href="#{{ $gateway->identity }}" class="gateway-tag">
                    <i class="fa fa-tag"></i> {{ $gateway->identity }}
                </a>
            @endforeach
        </div>

        <div class="payment-gateways-grid">
            @foreach($data['paymentGateway'] as $gateway)
                @php($config = json_decode($gateway->config, true))
                {!! Form::model($gateway, [
                    'route' => [$base_route.'.update', $gateway->id], 
                    'method' => 'POST', 
                    'enctype' => 'multipart/form-data',
                    'class' => 'gateway-card' . ($gateway->status != 'active' ? ' disabled' : '')
                ]) !!}

                <div class="gateway-header" onclick="toggleConfig('{{ $gateway->id }}')">
                    <div class="gateway-header-content">
                        <a href="{{ $gateway->link }}" target="_blank">
                            <img src="{{ asset('assets/images/paymenticon/' . $gateway->logo . '.png') }}" 
                                 alt="{{ $gateway->identity }}" 
                                 class="gateway-logo">
                        </a>
                        <h3 class="gateway-name" id="{{ $gateway->identity }}">
                            {{ ucfirst($gateway->identity) }}
                        </h3>
                    </div>
                    <div class="header-actions">
                        <span class="gateway-status {{ $gateway->status == 'active' ? 'status-active' : 'status-inactive' }}">
                            {{ ucfirst($gateway->status) }}
                        </span>
                        <button type="button" class="toggle-config" id="toggle-{{ $gateway->id }}">
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </div>
                </div>

                <div class="config-section" id="config-{{ $gateway->id }}">
                    @if($config)
                        @foreach($config as $key => $value)
                            <div class="form-group">
                                <label for="{{ $key }}-{{ $gateway->id }}">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </label>
                                <input type="{{ str_contains(strtolower($key), 'key') ? 'password' : 'text' }}" 
                                       id="{{ $key }}-{{ $gateway->id }}"
                                       name="{{ $key }}" 
                                       value="{{ $value }}" 
                                       class="form-control"
                                       {{ $gateway->status == 'active' ? '' : 'disabled' }}>
                            </div>
                        @endforeach
                    @endif

                    <div class="form-actions">
                        <div class="status-actions">
                            <a href="{{ route($base_route.'.active', ['id' => $gateway->id]) }}">
                                <i class="fa fa-check-circle"></i> Activate
                            </a>
                            <a href="{{ route($base_route.'.in-active', ['id' => $gateway->id]) }}">
                                <i class="fa fa-times-circle"></i> Deactivate
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary" {{ $gateway->status != 'active' ? 'disabled' : '' }}>
                            <i class="fa fa-save"></i> Save Configuration
                        </button>
                    </div>
                </div>

                {!! Form::close() !!}
            @endforeach
        </div>
    @else
        <div class="no-gateways">
            <i class="fa fa-credit-card"></i>
            <h3>No Payment Gateways Configured</h3>
            <p>You haven't set up any payment gateways yet.</p>
        </div>
    @endif
</div>
@endsection

@section('js')
    @include('includes.scripts.delete_confirm')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for gateway tags
            document.querySelectorAll('.gateway-tag').forEach(tag => {
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
        });

        function toggleConfig(gatewayId) {
            const configSection = document.getElementById(`config-${gatewayId}`);
            const toggleBtn = document.getElementById(`toggle-${gatewayId}`);
            
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