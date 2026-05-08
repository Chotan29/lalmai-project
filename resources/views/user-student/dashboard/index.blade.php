@extends('user-student.layouts.master')

@section('css')
    <style>
        /* Modern Notices Styles */
        .notices-container {
            margin-bottom: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .notices-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .notices-header h2 {
            margin: 0;
            font-size: 1.25rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notices-header h2 i {
            color: #4a6baf;
        }

        .notices-controls {
            display: flex;
            gap: 0.5rem;
        }

        .btn-notices-collapse,
        .btn-notices-expand {
            background: #f5f7fa;
            border: 1px solid #ddd;
            padding: 0.35rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s;
        }

        .btn-notices-collapse:hover,
        .btn-notices-expand:hover {
            background: #e9ecef;
        }

        .notices-list {
            padding: 0.5rem;
        }

        .notice-card {
            margin-bottom: 0.75rem;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e1e5eb;
            transition: all 0.3s ease;
        }

        .notice-card:last-child {
            margin-bottom: 0;
        }

        .notice-card:hover {
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .notice-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            background: #f8f9fa;
            cursor: pointer;
            user-select: none;
        }

        .notice-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-grow: 1;
        }

        .notice-title h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
            flex-grow: 1;
        }

        .notice-toggle {
            transition: transform 0.2s;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .notice-card.expanded .notice-toggle {
            transform: rotate(90deg);
        }

        .notice-date {
            font-size: 0.85rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .notice-dismiss {
            background: transparent;
            border: none;
            color: #6c757d;
            font-size: 1rem;
            cursor: pointer;
            padding: 0.25rem;
            margin-left: 0.5rem;
            transition: color 0.2s;
        }

        .notice-dismiss:hover {
            color: #dc3545;
        }

        .notice-card-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: #fff;
        }

        .notice-card.expanded .notice-card-content {
            max-height: 1000px;
            /* Adjust based on your content */
        }

        .notice-content {
            padding: 1.25rem;
            line-height: 1.6;
            color: #495057;
        }

        .notice-content p:last-child {
            margin-bottom: 0;
        }

        .notice-attachment {
            padding: 0 1.25rem 1.25rem;
        }

        .btn-attachment {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a6baf;
            text-decoration: none;
            font-size: 0.9rem;
            padding: 0.4rem 0.8rem;
            border: 1px solid #e1e5eb;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .btn-attachment:hover {
            background: #f1f5fd;
            border-color: #d0d9e8;
        }

        /* Animation for dismissed notices */
        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                max-height: 0;
                padding: 0;
                margin: 0;
            }
        }

        .notice-card.dismissed {
            animation: fadeOut 0.3s forwards;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('user-student.layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        Dashboard
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Student
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <!-- PAGE CONTENT BEGINS -->
                <div class="row">
                    <div class="col-xs-12">
                        @include('includes.flash_messages')

                        <!-- Modern Notices Section -->
                        <div class="notices-container">
                            @if ($data['notice_display'] && $data['notice_display']->count() > 0)
                                <div class="notices-header">
                                    <h2><i class="fas fa-bullhorn"></i> Important Notices</h2>
                                    <div class="notices-controls">
                                        <button class="btn-notices-collapse" id="collapseAllNotices">
                                            <i class="fas fa-chevron-up"></i> Collapse All
                                        </button>
                                        <button class="btn-notices-expand" id="expandAllNotices">
                                            <i class="fas fa-chevron-down"></i> Expand All
                                        </button>
                                    </div>
                                </div>

                                <div class="notices-list">
                                    @foreach ($data['notice_display'] as $notice)
                                        <div class="notice-card" data-notice-id="{{ $notice->id }}">
                                            <div class="notice-card-header">
                                                <div class="notice-title">
                                                    <i class="fas fa-chevron-right notice-toggle"></i>
                                                    <h3>{{ $notice->title }}</h3>
                                                    @if ($notice->created_at)
                                                        <span class="notice-date">
                                                            <i class="far fa-calendar-alt"></i>
                                                            {{ $notice->created_at->format('M d, Y') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <button type="button" class="notice-dismiss" aria-label="Dismiss notice">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="notice-card-content">
                                                <div class="notice-content">
                                                    {!! $notice->message !!}
                                                </div>
                                                @if ($notice->attachment)
                                                    <div class="notice-attachment">
                                                        <a href="{{ asset($notice->attachment) }}" target="_blank"
                                                            class="btn-attachment">
                                                            <i class="fas fa-paperclip"></i> View Attachment
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <!-- End Modern Notices Section -->

                        <!-- Rest of your content... -->
                        <div class="col-md-12">
                            @include('user-student.dashboard.includes.student-card')
                            @if (isset($data['current_unpaid_installment']['installmentAmount']) &&
                                    $data['current_unpaid_installment']['installmentAmount'] != null)
                                <h3 class="text-center">Payment amount must be exactly
                                    {{ $data['current_unpaid_installment']['installmentAmount'] }} for the current
                                    due.</h3>
                            @endif
                            <div class="hr-double hr-16"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div>{!! $data['feeCompare']->container() !!}</div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize notices
            $('.notice-card').each(function() {
                // Set initial state (all expanded by default)
                $(this).addClass('expanded');
            });

            // Toggle notice content
            $('.notice-card-header').click(function() {
                const card = $(this).closest('.notice-card');
                card.toggleClass('expanded');

                // Rotate the toggle icon
                const icon = card.find('.notice-toggle');
                if (card.hasClass('expanded')) {
                    icon.css('transform', 'rotate(90deg)');
                } else {
                    icon.css('transform', 'rotate(0)');
                }
            });

            // Dismiss notice
            $('.notice-dismiss').click(function(e) {
                e.stopPropagation();
                const card = $(this).closest('.notice-card');
                card.addClass('dismissed');

                // Remove from DOM after animation
                setTimeout(() => {
                    card.remove();

                    // If no notices left, hide the notices container
                    if ($('.notice-card').length === 0) {
                        $('.notices-container').hide();
                    }
                }, 300);

                // Here you could add an AJAX call to mark the notice as dismissed in the database
            });

            // Collapse all notices
            $('#collapseAllNotices').click(function() {
                $('.notice-card').removeClass('expanded');
                $('.notice-toggle').css('transform', 'rotate(0)');
            });

            // Expand all notices
            $('#expandAllNotices').click(function() {
                $('.notice-card').addClass('expanded');
                $('.notice-toggle').css('transform', 'rotate(90deg)');
            });

            // Rest of your scripts...
        });
    </script>

    <!-- page specific plugin scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" charset="utf-8"></script>
    {!! $data['feeCompare']->script() !!}
@endsection
