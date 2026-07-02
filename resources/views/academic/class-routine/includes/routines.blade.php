{{-- resources/views/academic/class-routine/includes/routines.blade.php --}}
@if (empty($routines) || (is_object($routines) && $routines->isEmpty()))
    <div class="alert alert-info">No routines found for the current selection.</div>
@else
    <style>
        /* Scoped styles for the routine list */
        .routine-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #fff
        }

        .routine-card-header {
            padding: 14px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .badge-time {
            background: #eef2ff;
            color: #4f46e5;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600
        }

        .routine-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #4f46e5;
            border-radius: 10px;
            padding: 16px;
            position: relative;
            min-height: 120px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .04)
        }

        .routine-item:hover {
            box-shadow: 0 8px 18px rgba(0, 0, 0, .06)
        }

        .ribbon {
            position: absolute;
            top: 5px;
            left: -8px;
            background: #ff9800;
            color: #fff;
            font-weight: 700;
            font-size: .8rem;
            padding: 4px 10px 4px 16px;
            border-radius: 0 6px 6px 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .18)
        }

        .ribbon:before {
            content: "";
            position: absolute;
            left: 0;
            top: 100%;
            border-width: 6px 8px;
            border-style: solid;
            border-color: #c77700 transparent transparent transparent;
            filter: brightness(0.95)
        }

        .btn-float-edit {
            position: absolute;
            top: 8px;
            right: 8px
        }

        .btn-float-delete {
            position: absolute;
            bottom: 8px;
            right: 8px
        }

        .btn-round {
            border-radius: 9999px;
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center
        }

        .text-muted.small i {
            width: 16px;
            text-align: center;
            color: #64748b;
            margin-right: 6px
        }
    </style>

    <div class="routines-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Class Schedule</h3>
                <div class="text-muted small mt-1">
                    {{ $current_department->department ?? '' }}
                    @if (!empty($current_faculty))
                        / {{ $current_faculty->faculty }}
                    @endif
                    @if (!empty($current_semester))
                        / {{ $current_semester->semester }}
                    @endif
                    @if (!empty($current_subject))
                        / {{ $current_subject->title }}
                    @endif
                    @if (!empty($current_batch))
                        / {{ $current_batch->title }}
                    @endif
                </div>
            </div>
        </div>

        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
            @if (isset($routines[$day]))
                <div class="routine-card mb-3">
                    <div class="routine-card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i> {{ $day }}</h5>
                        <span class="badge bg-primary">{{ count($routines[$day]) }} classes</span>
                    </div>

                    <div class="p-3">
                        <div class="row">
                            @foreach ($routines[$day] as $routine)
                                @php
                                    $teacherName = trim(
                                        trim(
                                            ($routine->teacher->first_name ?? '') .
                                                ' ' .
                                                ($routine->teacher->middle_name ?? ''),
                                        ) .
                                            ' ' .
                                            ($routine->teacher->last_name ?? ''),
                                    );
                                    $timeRange =
                                        date('h:i A', strtotime($routine->start_time)) .
                                        ' - ' .
                                        date('h:i A', strtotime($routine->end_time));
                                    $labelForDialog =
                                        ($routine->subject->title ?? 'Routine') . " ({$day}, {$timeRange})";
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="routine-item">
                                        {{-- Period Ribbon --}}
                                        @if (!empty($routine->period))
                                            <div class="ribbon">Period {{ $routine->period }}</div>
                                        @endif

                                        {{-- Floating Edit (Top Right) --}}
                                        {{-- <a href="{{ route('routine.edit', $routine->id) }}"
                                           class="btn btn-sm btn-primary btn-round btn-float-edit"
                                           data-bs-toggle="tooltip" title="Edit Routine">
                                            <i class="fas fa-edit"></i>
                                        </a> --}}
                                        <a href="{{ route('routine.manage', [
                                            'department_head_id' => $routine->department_head_id,
                                            'department_id' => $routine->department_id,
                                            'faculty_id' => $routine->faculty_id,
                                            'semester_id' => $routine->semester_id,
                                            'student_batch_id' => $routine->student_batch_id,
                                            'subject_id' => $routine->subject_id,
                                            // include department_head_id if you want/can
                                        ]) }}"
                                            class="btn btn-sm btn-primary btn-round btn-float-edit"
                                            data-bs-toggle="tooltip" title="Edit Routine">
                                            <i class="fas fa-edit"></i>
                                        </a>


                                        {{-- Floating Delete (Bottom Right) --}}
                                        <form action="{{ route('routine.destroy', $routine->id) }}" method="POST"
                                            class="btn-float-delete">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-danger btn-round bootbox-confirm"
                                                data-item-name="{{ $labelForDialog }}"
                                                data-custom-message="This action cannot be undone and the schedule will be removed permanently."
                                                data-bs-toggle="tooltip" title="Delete Routine">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                        {{-- Content --}}
                                        <div class="d-flex justify-content-between align-items-start mb-2 pe-5">
                                            <h4 class="mb-0">{{ $routine->subject->title ?? 'Subject' }}</h4>
                                            <span class="badge-time">{{ $timeRange }}</span>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-book"></i>{{ $routine->subject->code ?? '' }}
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <i
                                                class="fas fa-chalkboard-teacher"></i>{{ $teacherName !== '' ? $teacherName : '-' }}
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-door-open"></i>Room {{ $routine->room_number }}
                                        </div>
                                        @if (empty($current_batch))
                                            <div class="small text-muted">
                                                <i class="fas fa-users"></i>{{ $routine->batch->title ?? '' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- SweetAlert confirm for DELETE (submits the nearest form) --}}
    <script>
        jQuery(function($) {
            $(document).off('click', '.bootbox-confirm').on('click', '.bootbox-confirm', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const $form = $btn.closest('form');
                const itemName = $btn.data('item-name') || 'this routine';
                const customMessage = $btn.data('custom-message') || 'This action cannot be undone.';

                if (typeof Swal === 'undefined') {
                    // Fallback: native confirm if SweetAlert2 not loaded
                    if (confirm(`Delete ${itemName}?`)) {
                        $form.trigger('submit');
                    }
                    return;
                }

                Swal.fire({
                    title: '<i class="fa fa-exclamation-circle text-danger me-2"></i> Confirm Deletion',
                    html: `
                        <div class="swal-custom-alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            You are about to permanently delete <strong>${itemName}</strong>.
                        </div>
                        <div class="swal-custom-alert alert-light mt-3">
                            <i class="fa fa-info-circle text-info me-2"></i>
                            ${customMessage}
                        </div>
                        <p class="text-center mt-3 mb-0">
                            <i class="fa fa-question-circle text-primary me-2"></i>
                            Are you sure you want to proceed?
                        </p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-trash-alt me-2"></i> Delete Permanently',
                    cancelButtonText: '<i class="fa fa-times me-2"></i> Cancel',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    focusCancel: true,
                    preConfirm: () => {
                        // Submit the form
                        $btn.prop('disabled', true);
                        $form.trigger('submit');
                    },
                    customClass: {
                        popup: 'swal-custom-popup',
                        header: 'swal-custom-header',
                        title: 'swal-custom-title',
                        htmlContainer: 'swal-custom-content',
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 1.5rem
        }

        .swal-custom-header {
            padding: 0 0 1rem 0;
            border: none
        }

        .swal-custom-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: #343a40
        }

        .swal-custom-content {
            padding: 0;
            font-size: 1rem
        }

        .swal-custom-actions {
            margin: 1.5rem 0 0 0;
            padding: 0;
            justify-content: center
        }

        .swal-custom-confirm-btn,
        .swal-custom-cancel-btn {
            padding: .5rem 1.25rem;
            font-size: .9rem;
            border-radius: 4px;
            font-weight: 500
        }

        .swal-custom-alert {
            padding: .75rem 1rem;
            border-radius: 4px;
            margin-bottom: 0;
            border-left: 4px solid transparent
        }

        .alert-warning {
            background-color: #fff3cd;
            border-left-color: #ffc107;
            color: #856404
        }

        .alert-light {
            background-color: #f8f9fa;
            border-left-color: #e9ecef;
            color: #495057
        }

        .swal2-loader {
            border-color: #dc3545 transparent #dc3545 transparent !important
        }
    </style>
@endif
