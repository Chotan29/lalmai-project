<script type="text/javascript">
    jQuery(function($) {
        // Select/Deselect all checkboxes
        $('table th input:checkbox').on('click', function() {
            var that = this;
            $(this).closest('table').find('tr > td:first-child input:checkbox')
                .each(function() {
                    this.checked = that.checked;
                    $(this).closest('tr').toggleClass('selected', this.checked);
                });
        });

        // Bulk action button handler
        $('.bulk-action-btn').click(function(e) {
            e.preventDefault();

            const $this = $(this);
            const bulk_action = $this.attr('attr-action-type');
            
            // Count checked items
            const $chkIds = $('input[name="chkIds[]"]:checked');
            const $chkCount = $chkIds.length;

            // Validate selection
            if ($chkCount <= 0 && bulk_action !== 'delete-all') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    html: '<i class="fa fa-exclamation-circle mr-2"></i> Please select at least one record',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'custom-swal-warning'
                    }
                });
                return false;
            }

            const actionLabel = bulk_action.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            const form = $('#bulk_action_form');
            const selectedCount = bulk_action === 'delete-all'
                ? 'all student records'
                : ($chkCount === 1 ? '1 record' : `${$chkCount} records`);

            // Customize confirmation message based on action type
            let actionIcon, actionClass, actionDescription, swalIcon;
            switch(bulk_action) {
                case 'delete':
                    actionIcon = 'fa-trash-alt';
                    actionClass = 'danger';
                    swalIcon = 'error';
                    actionDescription = 'Permanent deletion cannot be undone.';
                    break;
                case 'delete-all':
                    actionIcon = 'fa-exclamation-triangle';
                    actionClass = 'danger';
                    swalIcon = 'error';
                    actionDescription = 'This will permanently delete ALL students. This action cannot be undone.';
                    break;
                case 'active':
                    actionIcon = 'fa-check-circle';
                    actionClass = 'success';
                    swalIcon = 'success';
                    actionDescription = 'Selected records will be activated.';
                    break;
                case 'in-active':
                    actionIcon = 'fa-ban';
                    actionClass = 'warning';
                    swalIcon = 'warning';
                    actionDescription = 'Selected records will be deactivated.';
                    break;
                default:
                    actionIcon = 'fa-cog';
                    actionClass = 'info';
                    swalIcon = 'info';
                    actionDescription = 'This action will affect selected records.';
            }

            // SweetAlert confirmation dialog
            Swal.fire({
                title: `<i class="fa ${actionIcon} mr-2"></i>Confirm ${actionLabel}`,
                html: `
                    <div class="swal-custom-alert alert-${actionClass}">
                        <div class="d-flex align-items-center">
                            <i class="fa ${actionIcon} mr-2"></i>
                            <div>
                                <strong>${selectedCount}</strong> selected
                                <div class="small">${actionDescription}</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center text-muted small mt-2">
                        <i class="fa fa-info-circle mr-1"></i>
                        Are you sure you want to continue?
                    </div>
                `,
                icon: swalIcon,
                showCancelButton: true,
                confirmButtonText: `<i class="fa ${actionIcon} mr-1"></i> Confirm`,
                cancelButtonText: '<i class="fa fa-times mr-1"></i> Cancel',
                confirmButtonColor: `#${(bulk_action === 'delete' || bulk_action === 'delete-all') ? 'dc3545' : bulk_action === 'active' ? '28a745' : bulk_action === 'in-active' ? 'ffc107' : '17a2b8'}`,
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Submit the form
                    $(form).prepend(`<input type="hidden" name="bulk_action" value="${bulk_action}">`);
                    return form.submit();
                },
                customClass: {
                    popup: 'swal-custom-popup',
                    header: 'swal-custom-header',
                    content: 'swal-custom-content',
                    actions: 'swal-custom-actions',
                    confirmButton: 'swal-custom-confirm-btn',
                    cancelButton: 'swal-custom-cancel-btn'
                }
            });

            return false;
        });
    });
</script>

<style>
    /* SweetAlert Custom Styles */
    .swal-custom-popup {
        width: 400px;
        max-width: 95%;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 1.5rem;
    }
    
    .swal-custom-header {
        padding: 0 0 1rem 0;
        border: none;
    }
    
    .swal-custom-content {
        padding: 0;
    }
    
    .swal-custom-actions {
        margin: 1.5rem 0 0 0;
        padding: 0;
    }
    
    .swal-custom-confirm-btn,
    .swal-custom-cancel-btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 4px;
    }
    
    .swal-custom-alert {
        padding: 0.75rem 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-left: 4px solid #28a745;
        color: #155724;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        color: #856404;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border-left: 4px solid #17a2b8;
        color: #0c5460;
    }
    
    /* Table selection styling */
    table tr.selected {
        background-color: rgba(0, 123, 255, 0.08) !important;
    }
    
    /* SweetAlert icon colors */
    .swal2-icon.swal2-error {
        color: #dc3545;
        border-color: #dc3545;
    }
    
    .swal2-icon.swal2-success {
        color: #28a745;
        border-color: #28a745;
    }
    
    .swal2-icon.swal2-warning {
        color: #ffc107;
        border-color: #ffc107;
    }
    
    .swal2-icon.swal2-info {
        color: #17a2b8;
        border-color: #17a2b8;
    }
</style>