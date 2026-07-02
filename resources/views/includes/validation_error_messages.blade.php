@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissible animated fadeInDown" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="alert-content-wrapper">
        <i class="ace-icon fa fa-exclamation-triangle"></i>
        <div class="alert-message-content">
            <strong>Validation Errors Found</strong>
            <p class="mb-2">Please correct the following issues:</p>
            <hr class="hr-2">
            <ul class="validation-errors-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<style>
    /* Enhanced Validation Error Styling */
    .alert.alert-danger {
        padding: 15px 20px;
        border-radius: 4px;
        border-left: 4px solid #dc3545;
        background-color: #f8d7da;
        color: #721c24;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .alert-content-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .alert i.ace-icon {
        font-size: 22px;
        margin-top: 3px;
    }
    
    .alert-message-content {
        flex: 1;
    }
    
    .alert strong {
        display: block;
        margin-bottom: 5px;
        font-size: 16px;
    }
    
    .alert p.mb-2 {
        margin-bottom: 10px;
    }
    
    .hr-2 {
        margin: 10px 0;
        border: 0;
        border-top: 1px solid rgba(220, 53, 69, 0.3);
    }
    
    .validation-errors-list {
        margin: 0;
        padding-left: 20px;
    }
    
    .validation-errors-list li {
        margin-bottom: 5px;
        line-height: 1.4;
    }
    
    .validation-errors-list li:last-child {
        margin-bottom: 0;
    }
    
    /* Close Button */
    .alert-dismissible .close {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 0;
        background: transparent;
        border: 0;
        font-size: 22px;
        line-height: 1;
        opacity: 0.7;
        transition: opacity 0.2s;
        color: inherit;
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
</style>

<script>
    // Auto-dismiss after longer time for validation errors (10 seconds)
    document.addEventListener('DOMContentLoaded', function() {
        const validationAlert = document.querySelector('.alert.alert-danger');
        
        if (validationAlert) {
            const dismissTime = 10000; // 10 seconds for validation errors
            
            const dismissAlert = () => {
                validationAlert.style.transition = 'all 0.4s ease';
                validationAlert.style.opacity = '0';
                validationAlert.style.height = '0';
                validationAlert.style.padding = '0';
                validationAlert.style.margin = '0';
                setTimeout(() => validationAlert.remove(), 400);
            };
            
            // Start timer
            const timer = setTimeout(dismissAlert, dismissTime);
            
            // Pause timer on hover
            validationAlert.addEventListener('mouseenter', () => {
                clearTimeout(timer);
            });
            
            // Resume timer when mouse leaves
            validationAlert.addEventListener('mouseleave', () => {
                setTimeout(dismissAlert, dismissTime);
            });
            
            // Manual close
            validationAlert.querySelector('.close').addEventListener('click', function() {
                clearTimeout(timer);
                dismissAlert();
            });
        }
    });
</script>

@if (count($errors) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const errorMessages = {!! json_encode($errors->all()) !!};
        
        Swal.fire({
            title: '<i class="fa fa-exclamation-circle text-danger mr-2"></i> Validation Errors',
            html: `
                <div class="swal-custom-alert alert-warning">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    Please correct the following issues to continue:
                </div>
                <div class="swal-custom-alert alert-light mt-3">
                    <i class="fa fa-list-ol text-info mr-2"></i>
                    <ul class="text-left pl-3 mb-0" style="list-style-position: inside;">
                        ${errorMessages.map(error => `<li>${error}</li>`).join('')}
                    </ul>
                </div>
                <p class="text-center mt-3 mb-0">
                    <i class="fa fa-info-circle text-primary mr-2"></i>
                    Please fix these errors and try again
                </p>
            `,
            icon: 'error',
            confirmButtonText: '<i class="fa fa-check mr-2"></i> Understood',
            confirmButtonColor: '#6c757d',
            customClass: {
                popup: 'swal-custom-popup',
                header: 'swal-custom-header',
                title: 'swal-custom-title',
                content: 'swal-custom-content',
                actions: 'swal-custom-actions',
                confirmButton: 'swal-custom-confirm-btn'
            }
        });
    });
</script>
@endif

<style>
    /* SweetAlert Custom Styles - Matched with Delete Confirmation */
    .swal-custom-popup {
        width: 500px;
        max-width: 95%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        padding: 1.5rem;
    }
    
    .swal-custom-header {
        padding: 0 0 1rem 0;
        border: none;
    }
    
    .swal-custom-title {
        font-weight: 600;
        font-size: 1.25rem;
        color: #343a40;
    }
    
    .swal-custom-content {
        padding: 0;
        font-size: 1rem;
    }
    
    .swal-custom-actions {
        margin: 1.5rem 0 0 0;
        padding: 0;
        justify-content: center;
    }
    
    .swal-custom-confirm-btn {
        padding: 0.5rem 1.25rem;
        font-size: 0.9rem;
        border-radius: 4px;
        font-weight: 500;
    }
    
    .swal-custom-alert {
        padding: 0.75rem 1rem;
        border-radius: 4px;
        margin-bottom: 0;
        border-left: 4px solid transparent;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-left-color: #ffc107;
        color: #856404;
    }
    
    .alert-light {
        background-color: #f8f9fa;
        border-left-color: #e9ecef;
        color: #495057;
    }
    
    /* List styling */
    .swal-custom-content ul {
        margin: 0.5rem 0 0 0;
        padding-left: 1.5rem;
    }
    
    .swal-custom-content li {
        margin-bottom: 0.3rem;
        line-height: 1.4;
    }
    
    .swal-custom-content li:last-child {
        margin-bottom: 0;
    }
</style>