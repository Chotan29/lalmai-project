
<script>
    jQuery(function($) {
        $(".bootbox-confirm").on('click', function(e) {
            e.preventDefault();
            const $this = $(this);
            const itemName = $this.data('item-name') || 'these items';
            const customMessage = $this.data('custom-message') || 'This action cannot be undone.';
            
            Swal.fire({
                title: '<i class="fa fa-exclamation-circle text-danger mr-2"></i> Confirm Deletion',
                html: `
                    <div class="swal-custom-alert alert-warning">
                        <i class="fa fa-exclamation-triangle mr-2"></i>
                        You are about to permanently delete &nbsp; <strong>${itemName}</strong>.
                    </div>
                    <div class="swal-custom-alert alert-light mt-3">
                        <i class="fa fa-info-circle text-info mr-2"></i>
                        ${customMessage}
                    </div>
                    <p class="text-center mt-3 mb-0">
                        <i class="fa fa-question-circle text-primary mr-2"></i>
                        Are you sure you want to proceed?
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-trash-alt mr-2"></i> Delete Permanently',
                cancelButtonText: '<i class="fa fa-times mr-2"></i> Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: () => {
                    return new Promise((resolve) => {
                        setTimeout(() => {
                            window.location.href = $this.attr('href');
                            resolve();
                        }, 500);
                    });
                },
                customClass: {
                    popup: 'swal-custom-popup',
                    header: 'swal-custom-header',
                    title: 'swal-custom-title',
                    content: 'swal-custom-content',
                    actions: 'swal-custom-actions',
                    confirmButton: 'swal-custom-confirm-btn',
                    cancelButton: 'swal-custom-cancel-btn'
                }
            });
        });
    });
</script>

<style>
    /* SweetAlert Custom Styles */
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
    
    .swal-custom-confirm-btn,
    .swal-custom-cancel-btn {
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
    
    /* SweetAlert loader */
    .swal2-loader {
        border-color: #dc3545 transparent #dc3545 transparent !important;
    }
</style>