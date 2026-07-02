{{-- @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
        @if(session('error_details'))
            <ul class="mt-2 mb-0">
                @foreach(session('error_details') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif --}}
{{-- @if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif --}}

@if (session()->has('warning'))
<div class="alert alert-warning alert-dismissible animated fadeInDown" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="alert-content-wrapper">
        <i class="ace-icon fa fa-exclamation-triangle"></i>
        <div class="alert-message-content">
            <strong>Warning:</strong>
            <div>{!! session()->get('warning') !!}</div>
        </div>
    </div>
</div>
@endif

@if (session()->has('message_warning'))
<div class="alert alert-warning alert-dismissible animated fadeInDown" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="alert-content-wrapper">
        <i class="ace-icon fa fa-exclamation-triangle"></i>
        <div class="alert-message-content">
            <strong>Warning:</strong>
            <div>{!! session()->get('message_warning') !!}</div>
        </div>
    </div>
</div>
@endif

@if (session()->has('message_success'))
<div class="alert alert-success alert-dismissible animated fadeInDown" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="alert-content-wrapper">
        <i class="ace-icon fa fa-check-circle"></i>
        <div class="alert-message-content">
            <strong>Success:</strong>
            <div>{!! session()->get('message_success') !!}</div>
        </div>
    </div>
</div>
@endif

@if (session()->has('message_info'))
<div class="alert alert-info alert-dismissible animated fadeInDown" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="alert-content-wrapper">
        <i class="ace-icon fa fa-info-circle"></i>
        <div class="alert-message-content">
            <strong>Info:</strong>
            <div>{!! session()->get('message_info') !!}</div>
        </div>
    </div>
</div>
@endif

@if (session()->has('message_danger'))
<div class="alert alert-danger alert-dismissible animated fadeInDown" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="alert-content-wrapper">
        <i class="ace-icon fa fa-times-circle"></i>
        <div class="alert-message-content">
            <strong>Error:</strong>
            <div>{!! session()->get('message_danger') !!}</div>
        </div>
    </div>
</div>
@endif

<style>
    /* Enhanced Alert Styling */
    .alert {
        padding: 15px 20px;
        border-radius: 4px;
        border-left: 4px solid transparent;
        margin-bottom: 15px;
        position: relative;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .alert-content-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .alert i.ace-icon {
        font-size: 20px;
        margin-top: 2px;
    }
    
    .alert-message-content {
        flex: 1;
    }
    
    .alert strong {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    /* Alert Colors */
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffc107;
        color: #856404;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-color: #28a745;
        color: #155724;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border-color: #17a2b8;
        color: #0c5460;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #dc3545;
        color: #721c24;
    }
    
    /* Close Button */
    .alert-dismissible .close {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 0;
        background: transparent;
        border: 0;
        font-size: 20px;
        line-height: 1;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    
    .alert-dismissible .close:hover {
        opacity: 1;
    }
    
    /* Animations */
    .animated {
        animation-duration: 0.4s;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fadeInDown {
        animation-name: fadeInDown;
    }
    
    /* Auto-fade out */
    .alert.fade-out {
        animation: fadeOutUp 0.4s forwards;
    }
    
    @keyframes fadeOutUp {
        to {
            opacity: 0;
            transform: translateY(-20px);
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            height: 0;
        }
    }
</style>

<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        
        alerts.forEach(alert => {
            // Set timeout for auto-dismiss
            const dismissTime = 5000; // 5 seconds
            
            const dismissAlert = () => {
                alert.classList.add('fade-out');
                setTimeout(() => alert.remove(), 400);
            };
            
            // Start timer
            const timer = setTimeout(dismissAlert, dismissTime);
            
            // Pause timer on hover
            alert.addEventListener('mouseenter', () => {
                clearTimeout(timer);
            });
            
            // Resume timer when mouse leaves
            alert.addEventListener('mouseleave', () => {
                setTimeout(dismissAlert, dismissTime);
            });
            
            // Manual close
            alert.querySelector('.close').addEventListener('click', function() {
                clearTimeout(timer);
                dismissAlert();
            });
        });
    });
</script>

@if (session()->has('message_warning'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToastNotification('warning', 'Warning', `{!! session()->get('message_warning') !!}`);
    });
</script>
@endif

@if (session()->has('message_success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToastNotification('success', 'Success', `{!! session()->get('message_success') !!}`);
    });
</script>
@endif

@if (session()->has('message_info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToastNotification('info', 'Info', `{!! session()->get('message_info') !!}`);
    });
</script>
@endif

@if (session()->has('message_danger'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToastNotification('error', 'Error', `{!! session()->get('message_danger') !!}`);
    });
</script>
@endif
{{-- 

<script>
    /**
     * Shows a SweetAlert2 toast notification with styling matching delete confirmation
     * @param {string} type - Notification type (warning, success, info, error)
     * @param {string} title - Notification title
     * @param {string} message - HTML message content
     */
    function showToastNotification(type, title, message) {
        const iconMap = {
            warning: '<i class="fas fa-exclamation-triangle text-warning mr-2"></i>',
            success: '<i class="fas fa-check-circle text-success mr-2"></i>',
            info: '<i class="fas fa-info-circle text-info mr-2"></i>',
            error: '<i class="fas fa-times-circle text-danger mr-2"></i>'
        };

        const alertClassMap = {
            warning: 'alert-warning',
            success: 'alert-success',
            info: 'alert-info',
            error: 'alert-danger'
        };

        const defaultMessages = {
            warning: 'Please review this important information.',
            success: 'The operation was completed successfully.',
            info: 'Here is some information you should know.',
            error: 'An error occurred while processing your request.'
        };

        Swal.fire({
            title: `${iconMap[type]}<span>${title}</span>`,
            html: `
                <div class="swal-custom-alert ${alertClassMap[type]} mt-2">
                    <i class="fas ${type === 'warning' ? 'fa-exclamation-triangle' : 
                                      type === 'success' ? 'fa-check-circle' : 
                                      type === 'info' ? 'fa-info-circle' : 'fa-times-circle'} mr-2"></i>
                    ${message || defaultMessages[type]}
                </div>
                <p class="text-center mt-3 mb-0">
                    <i class="fas fa-clock text-secondary mr-2"></i>
                    This notification will dismiss automatically
                </p>
            `,
            icon: type,
            position: 'center',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            // showClass: {
            //     popup: 'animate__animated animate__fadeInRight'
            // },
            // hideClass: {
            //     popup: 'animate__animated animate__fadeOutRight'
            // },
            customClass: {
                popup: 'swal-custom-popup',
                title: 'swal-custom-title',
                htmlContainer: 'swal-custom-content',
                icon: 'swal-custom-icon'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
</script>

<style>
    /* SweetAlert Toast Styles - Matched with Delete Confirmation */
    .swal-custom-popup {
        width: 500px;
        max-width: 95%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        padding: 1.5rem;
        background: #fff;
        margin: 1rem;
    }
    
    .swal-custom-title {
        font-weight: 600;
        font-size: 1.25rem;
        color: #343a40;
        display: flex;
        align-items: center;
        padding: 0;
        margin: 0 0 0.5rem 0;
    }
    
    .swal-custom-content {
        padding: 0;
        font-size: 1rem;
        color: #495057;
    }
    
    /* Alert box styling - matches delete confirmation */
    .swal-custom-alert {
        padding: 0.75rem 1rem;
        border-radius: 4px;
        margin-bottom: 0;
        border-left: 4px solid transparent;
        display: flex;
        align-items: center;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-left-color: #ffc107;
        color: #856404;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-left-color: #28a745;
        color: #155724;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border-left-color: #17a2b8;
        color: #0c5460;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-left-color: #dc3545;
        color: #721c24;
    }
    
    /* Progress bar styling - matches delete confirmation colors */
    .swal2-timer-progress-bar {
        height: 3px;
        border-radius: 0 0 8px 8px;
    }
    
    .swal2-icon-warning ~ .swal2-timer-progress-bar {
        background: #ffc107;
    }
    .swal2-icon-success ~ .swal2-timer-progress-bar {
        background: #28a745;
    }
    .swal2-icon-info ~ .swal2-timer-progress-bar {
        background: #17a2b8;
    }
    .swal2-icon-error ~ .swal2-timer-progress-bar {
        background: #dc3545;
    }
    
    /* Icon sizing */
    .swal-custom-icon {
        margin: 0 10px 0 0 !important;
        width: auto !important;
        height: auto !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        .swal-custom-popup {
            width: 90%;
            padding: 1rem;
        }
        
        .swal-custom-title {
            font-size: 1.1rem;
        }
        
        .swal-custom-alert {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
    }
</style> --}}