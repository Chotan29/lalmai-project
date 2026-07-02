@extends('user-student.layouts.master')

@section('css')
<style>
    :root {
        --primary-color: #4361ee;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --danger-color: #f87171;
        --info-color: #60a5fa;
        --dark-color: #1a1a2e;
        --light-color: #f8f9fa;
    }

    .library-dashboard {
        background-color: #f5f7fb;
        min-height: 100vh;
    }

    /* Header Styles */
    .library-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .library-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .library-title i {
        color: var(--primary-color);
    }

    /* Stats Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    }

    .stat-card.max-books { border-left: 4px solid var(--primary-color); }
    .stat-card.issued { border-left: 4px solid var(--warning-color); }
    .stat-card.eligible { border-left: 4px solid var(--success-color); }
    .stat-card.transactions { border-left: 4px solid var(--danger-color); }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Book History Table */
    .history-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .history-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .book-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }

    .book-table th {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .book-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .book-table tr:last-child td {
        border-bottom: none;
    }

    .book-table tr:hover td {
        background: #f8fafc;
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .status-active {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
    }

    .status-returned {
        background: rgba(96, 165, 250, 0.1);
        color: var(--info-color);
    }

    .status-overdue {
        background: rgba(248, 113, 113, 0.1);
        color: var(--danger-color);
    }

    /* Book Cover Styles */
    .book-cover {
        width: 40px;
        height: 60px;
        background: #e5e7eb;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 0.75rem;
        overflow: hidden;
    }

    .book-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr 1fr;
        }
        
        .history-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .book-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="library-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="library-header">
                <h1 class="library-title">
                    <i class="fas fa-book"></i>
                    My Library Records
                </h1>
                @include($view_path.'.library.includes.buttons')
            </div>

            @include('includes.flash_messages')

            @if($data['lib_member'])
                <!-- Stats Overview -->
                <div class="stats-container">
                    <div class="stat-card max-books">
                        <div class="stat-value">{{ $data['circulation']->issue_limit_books ?? 'N/A' }}</div>
                        <div class="stat-label">Maximum Books Allowed</div>
                        @if(!$data['circulation']->issue_limit_books)
                            <small class="text-muted">Please setup circulation settings</small>
                        @endif
                    </div>

                    <div class="stat-card issued">
                        <div class="stat-value">{{ $data['books_taken']->count() }}</div>
                        <div class="stat-label">Currently Issued</div>
                    </div>

                    <div class="stat-card eligible">
                        <div class="stat-value">{{ $data['circulation']->issue_limit_books - $data['books_taken']->count() }}</div>
                        <div class="stat-label">Eligible to Borrow</div>
                    </div>

                    <div class="stat-card transactions">
                        <div class="stat-value">{{ $data['books_history']->count() }}</div>
                        <div class="stat-label">Total Transactions</div>
                    </div>
                </div>

                <!-- Book History Table -->
                <div class="history-container">
                    <div class="history-header">
                        <h2 class="history-title">
                            <i class="fas fa-history"></i>
                            My Book History
                        </h2>
                        <div class="table-tools"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="book-table" id="dynamic-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Book</th>
                                    <th>Category</th>
                                    <th>Issued On</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Days</th>
                                    <th>Fine</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($data['books_history']) && $data['books_history']->count() > 0)
                                    @foreach($data['books_history'] as $i => $books_history)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 1rem;">
                                                    <div class="book-cover">
                                                        @if($books_history->cover_image)
                                                            <img src="{{ asset($books_history->cover_image) }}" alt="{{ $books_history->title }}">
                                                        @else
                                                            <i class="fas fa-book"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 600;">{{ $books_history->title }}</div>
                                                        <small style="color: #6b7280;">Ref: {{ $books_history->book_code }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ ViewHelper::getBookCategoryById($books_history->categories) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($books_history->issued_on)->format('M d, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($books_history->due_date)->format('M d, Y') }}</td>
                                            <td>
                                                @if(!isset($books_history->return_date) && $books_history->due_date >= \Carbon\Carbon::now()->format('Y-m-d'))
                                                    <span class="status-badge status-active">
                                                        <i class="fas fa-clock"></i> Due in {{ \Carbon\Carbon::parse($books_history->due_date)->diffForHumans(\Carbon\Carbon::now(), ['parts' => 2]) }}
                                                    </span>
                                                @elseif(isset($books_history->return_date))
                                                    <span class="status-badge status-returned">
                                                        <i class="fas fa-check-circle"></i> Returned on {{ \Carbon\Carbon::parse($books_history->return_date)->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="status-badge status-overdue">
                                                        <i class="fas fa-exclamation-circle"></i> Overdue by {{ \Carbon\Carbon::parse($books_history->due_date)->diffForHumans(\Carbon\Carbon::now(), ['parts' => 2]) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $day = \Carbon\Carbon::parse($books_history->return_date)->diffInDays(\Carbon\Carbon::parse($books_history->issued_on)) }}
                                            </td>
                                            <td>
                                                @if($day > $data['circulation']->issue_limit_days)
                                                    ₹{{ ($day - $data['circulation']->issue_limit_days) * $data['circulation']->fine_per_day }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 2rem;">
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                                <i class="fas fa-book-open" style="font-size: 3rem; color: #d1d5db;"></i>
                                                <h4 style="color: #6b7280;">No Book History Found</h4>
                                                <p>You haven't borrowed any books yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; background: white; border-radius: 0.75rem;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #fbbf24;"></i>
                    <h3 style="color: #6b7280; margin-top: 1rem;">Library Membership Required</h3>
                    <p>You need to register as a library member to access these features.</p>
                    <a href="#" class="btn btn-primary" style="margin-top: 1rem;">Become a Member</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
    @include('includes.scripts.dataTable_scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable with enhanced features
            // $('#dynamic-table').DataTable({
            //     responsive: true,
            //     dom: '<"top"f>rt<"bottom"lip><"clear">',
            //     language: {
            //         search: "_INPUT_",
            //         searchPlaceholder: "Search history...",
            //     },
            //     initComplete: function() {
            //         $('.dataTables_filter input').addClass('form-control');
            //     }
            // });
            
            // // Add tooltips for better UX
            // $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection